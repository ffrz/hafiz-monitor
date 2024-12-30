<?php

namespace App\Http\Controllers;

use App\Models\Hafiz;
use App\Models\Memorization;
use App\Models\MemorizationDetail;
use App\Models\Surah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemorizationController extends Controller
{
    public function index()
    {
        return inertia('memorization/Index', [
            'hafizes' => Hafiz::where('user_id', auth()->id())
                ->orderBy('name', 'asc')->get(),
        ]);
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'created_at');
        $orderType = $request->get('order_type', 'desc');
        $filter = $request->get('filter', []);

        $q = Memorization::with('hafiz');
        $q->where('user_id', auth()->id());
        $q->orderBy($orderBy, $orderType);

        if (!empty($filter['hafiz_id'])) {
            $q->where('hafiz_id', '=', $filter['hafiz_id']);
        }

        if (!empty($filter['search'])) {
            $q->where(function ($query) use ($filter) {
                $query->where('hafizes.name', 'like', '%' . $filter['search'] . '%');
            });
        }

        $clients = $q->paginate($request->get('per_page', 10))->withQueryString();
        return response()->json($clients);
    }

    public function create(Request $request)
    {
        $memorization = new Memorization([
            'status' => 'open'
        ]);

        if ($request->method() === 'POST') {
            $rules = [
                'hafiz_id' => 'required',
                'title' => 'required|max:255',
                'start_surah_id' => 'required',
                'end_surah_id' => 'required',
            ];

            $message = '';
            $fields = ['title', 'hafiz_id', 'notes', 'start_surah_id', 'end_surah_id'];
            $request->validate($rules);
            $data = $request->only($fields);
            $memorization->fill($data);
            // swap dulu bos, supaya konsisten start surah harus dimulai dari surat nomor kecil
            if ($memorization->start_surah_id > $memorization->end_surah_id) {
                $tmp = $memorization->start_surah_id;
                $memorization->start_surah_id = $memorization->end_surah_id;
                $memorization->end_surah_id = $tmp;
            }
            $memorization->user_id = Auth::user()->id;
            $memorization->save();

            return redirect(route('memorization.run', ['id' => $memorization->id]))->with('success', $message);
        }

        return inertia('memorization/Create', [
            'data' => $memorization,
            'surahs' => Surah::all(),
            'hafizes' => Hafiz::where('user_id', auth()->id())
                ->orderBy('name', 'asc')->get(),
        ]);
    }

    public function save(Request $request)
    {
        $memorization = Memorization::findOrFail($request->id);
        $scores = $request->post('scores', []);
        DB::beginTransaction();
        MemorizationDetail::where('memorization_id', $memorization->id)->delete();
        $totalScore = 0;
        $count = 0;
        foreach ($scores as $ayah_id => $score) {
            if (!isset($score['score'])) {
                continue;
            }
            $totalScore += $score['score'];
            $count++;

            $detail = new MemorizationDetail([
                'memorization_id' => $memorization->id,
                'ayah_id' => $ayah_id,
                'score' => $score['score'] ?? 0,
                'notes' => trim($score['notes'] ?? ''),
            ]);
            $detail->save();
        }

        $memorization->score = $totalScore > 0 && $count > 0 ? $totalScore / $count : 0;
        $redirect = false;
        if ($request->closeSession) {
            $memorization->status = 'closed';
            $redirect = true;
        }
        $memorization->notes = $request->notes;
        $memorization->save();

        DB::commit();

        if ($redirect) {
            return redirect(route('memorization.view', ['id' => $memorization->id]))->with('success', 'Sesi telah selesai');
        }
        return response()->json(['message' => 'Berhasil disimpan']);
    }

    public function previewScore(Request $request)
    {
        $memorization = Memorization::with(['hafiz', 'details'])->findOrFail($request->id);
        return inertia('memorization/Preview', [
            'data' => $memorization
        ]);
    }

    public function run(Request $request)
    {
        $memorization = Memorization::with(['hafiz'])->findOrFail($request->get('id', null));

        $scores = [];

        foreach ($memorization->details as $detail) {
            $surahs[$detail->surah_id] = 1;
            if (!isset($scores[$detail->ayah_id])) {
                $scores[$detail->ayah_id] = [];
            }
            $scores[$detail->ayah_id]['score'] = $detail->score;
            $scores[$detail->ayah_id]['notes'] = $detail->notes;
        }

        $surahs = DB::table('surahs')
            ->whereIn('id', range($memorization->start_surah_id, $memorization->end_surah_id))
            ->get();

        $data = $memorization->toArray();

        return inertia('memorization/Run', [
            'data' => $data,
            'surahs' => $surahs,
            'scores' => $scores,
        ]);
    }

    public function view(Request $request)
    {
        $memorization = Memorization::select('*')
            ->with(['hafiz:id,name', 'details', 'details.ayah'])
            ->findOrFail($request->get('id', null));
        $juzes = [];
        $surah_ids = [];
        $details_by_surahs = [];
        foreach ($memorization->details as $detail) {
            $juzes[$detail->ayah->juz] = $detail->ayah->juz;
            $surah_ids[$detail->ayah->surah_id] = $detail->ayah->surah_id;
            $details_by_surahs[$detail->ayah->surah_id][] = [
                'ayah_id' => $detail->ayah_id,
                'ayah_number' => $detail->ayah->number,
                'ayah_text' => $detail->ayah->text,
                'score' => $detail->score,
                'notes' => $detail->notes,
            ];
        }

        $data = $memorization->toArray();
        $data['details'] = $details_by_surahs;
        $surah_ids = array_keys($surah_ids);
        $juzes = array_keys($juzes);

        $surahNames = DB::table('surahs')
            ->whereIn('id', $surah_ids)
            ->pluck('name', 'id');

        foreach ($surah_ids as $surah_id) {
            $data['details'][$surah_id] = [
                'id' => $surah_id,
                'name' => $surahNames[$surah_id],
                'score' => 0,
                'details' => $details_by_surahs[$surah_id],
            ];

            $total = 0;
            foreach ($details_by_surahs[$surah_id] as $detail) {
                $total += $detail['score'];
            }

            $count = count($details_by_surahs[$surah_id]);
            if ($count > 0) {
                $data['details'][$surah_id]['score'] = $total / $count;
            }
        }

        return inertia('memorization/View', [
            'data' => $data
        ]);
    }

    public function delete($id)
    {
        $memorization = Memorization::findOrFail($id);
        $memorization->delete();

        return response()->json([
            'message' => "Sesi {$memorization->title} telah dihapus!"
        ]);
    }
}
