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
            ];
            $messages = [
                'hafiz_id.required' => 'Pilih hafidz terlebih dahulu.',
                'title.required' => 'Judul sesi harus diisi.',
                'title.max' => 'Nama sesi maksimal 255 karakter.',
            ];

            $message = '';
            $fields = ['title', 'hafiz_id', 'notes'];
            $request->validate($rules, $messages);
            $data = $request->only($fields);
            $memorization->fill($data);
            $memorization->user_id = Auth::user()->id;
            $memorization->save();

            return redirect(route('memorization.run', ['id' => $memorization->id]))->with('success', $message);
        }

        return inertia('memorization/Create', [
            'data' => $memorization,
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

        $memorization->score = $totalScore > 0 && $count > 0? $totalScore / $count : 0;
        $redirect = false;
        if ($request->closeSession) {
            $memorization->status = 'closed';
            $redirect = true;
        }
        $memorization->save();

        DB::commit();

        if ($redirect) {
            return redirect(route('memorization.index'))->with('success', 'Sesi telah selesai');
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
        $memorization = Memorization::with(['hafiz', 'hafiz.memorizedSurahs', 'hafiz.memorizedSurahs.surah'])->findOrFail($request->get('id', null));
        $scores = [];
        foreach ($memorization->details as $detail) {
            if (!isset($scores[$detail->ayah_id])) {
                $scores[$detail->ayah_id] = [];
            }
            $scores[$detail->ayah_id]['score'] = $detail->score;
            $scores[$detail->ayah_id]['notes'] = $detail->notes;
        }

        $data = $memorization->toArray();
        $surahs = array_column($data['hafiz']['memorized_surahs'], 'surah');
        usort($surahs, function ($a, $b) {
            return $a['id'] <=> $b['id'];
        });
        unset($data['hafiz']['memorized_surahs']);

        return inertia('memorization/Run', [
            'data' => $data,
            'surahs' => $surahs ,
            'scores' => $scores,
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
