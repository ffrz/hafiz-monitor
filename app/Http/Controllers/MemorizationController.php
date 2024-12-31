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

        if (!empty($filter['hafiz_id']) && $filter['hafiz_id'] != 'all') {
            $q->where('hafiz_id', '=', $filter['hafiz_id']);
        }

        if (!empty($filter['search'])) {
            $q->where(function ($query) use ($filter) {
                $query->where('hafizes.name', 'like', '%' . $filter['search'] . '%');
            });
        }

        $q->orderBy($orderBy, $orderType);

        $data = $q->paginate($request->get('per_page', 10))->withQueryString();
        return response()->json($data);
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
        $memorization->hafiz_id = $request->hafiz_id ?? $memorization->hafiz_id;
        $memorization->start_surah_id = $request->start_surah_id ?? $memorization->start_surah_id;
        $memorization->end_surah_id = $request->end_surah_id ?? $memorization->end_surah_id;
        $memorization->title = $request->title ?? $memorization->title;
        $memorization->notes = $request->notes ?? $memorization->notes;
        $memorization->save();

        DB::commit();

        if ($redirect) {
            return redirect(route('memorization.view', ['id' => $memorization->id]))->with('success', 'Sesi telah selesai');
        }
        return response()->json(['message' => 'Berhasil disimpan']);
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

        $surah_ids = range($memorization->start_surah_id, $memorization->end_surah_id);
        $surahs = DB::table('surahs')
            ->whereIn('id', $surah_ids)
            ->get();

        $data = $memorization->toArray();

        DB::select('
            select md.ayah_id, md.score, md.notes
            from memorization_details md
            join memorizations m on md.memorization_id = m.id
            join ayahs a on a.id = md.ayah_id
            where a.surah_id in (' . implode(',', $surah_ids) . ')
            order by m.created_at desc
        ');

        $recent_scores = DB::table('memorization_details')
        ->join('memorizations', 'memorization_details.memorization_id', '=', 'memorizations.id')
        ->join('ayahs', 'memorization_details.ayah_id', '=', 'ayahs.id')
        ->join('surahs', 'ayahs.surah_id', '=', 'surahs.id')
        ->where('memorizations.hafiz_id', $memorization->hafiz_id)
        ->where('memorizations.status', 'closed')
        ->whereIn('surahs.id', $surah_ids)
        ->select(
            'ayahs.id as ayah_id',
            'memorization_details.score',
            'memorization_details.notes',
            'memorizations.created_at'
        )
        ->orderBy('ayahs.id')
        ->orderBy('memorizations.created_at', 'desc')
        ->get()
        ->groupBy('ayah_id')
        ->mapWithKeys(function ($scores, $ayahId) {
            return [
                $ayahId => $scores->take(3)->map(function ($scoreDetail) {
                    return [
                        'score' => $scoreDetail->score,
                        'notes' => $scoreDetail->notes,
                    ];
                })->toArray(), // Include the top 3 scores with details
            ];
        })
        ->toArray();

        return inertia('memorization/Run', [
            'data' => $data,
            'surahs' => $surahs,
            'all_surahs' => Surah::all(),
            'scores' => $scores,
            'hafizes' => Hafiz::where('user_id', auth()->id())->orderBy('name', 'asc')->get(),
            'recent_scores' => $recent_scores,
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
