<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hafiz;
use App\Models\HafizMemorizedJuz;
use App\Models\HafizMemorizedSurah;
use App\Models\Surah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HafizController extends Controller
{
    public function index()
    {
        return inertia('hafiz/Index');
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'name');
        $orderType = $request->get('order_type', 'asc');
        $filter = $request->get('filter', []);

        $q = Hafiz::query();
        $q->where('user_id', Auth::user()->id);
        $q->orderBy($orderBy, $orderType);

        if (!empty($filter['status']) && ($filter['status'] == 'active' || $filter['status'] == 'inactive')) {
            $q->where('active', '=', $filter['status'] == 'active' ? true : false);
        }

        if (!empty($filter['search'])) {
            $q->where(function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('phone', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('address', 'like', '%' . $filter['search'] . '%');
            });
        }

        $hafizes = $q->paginate($request->get('per_page', 10))->withQueryString();
        return response()->json($hafizes);
    }

    public function detail($id = 0)
    {
        $hafiz = Hafiz::findOrFail($id);
        $memorized_surah_count = 0;
        $memorized_ayah_count = 0;
        $global_score = 0;
        $surah_ids = [];
        $result = DB::select('
        SELECT s.id as surah_id, a.number as ayah_number, md.score
        FROM memorization_details md
        JOIN (
            SELECT md.ayah_id, MAX(m.created_at) AS latest
            FROM memorization_details md
            JOIN memorizations m ON md.memorization_id = m.id
            WHERE m.hafiz_id = :hafiz_id
            GROUP BY md.ayah_id
        ) recent ON md.ayah_id = recent.ayah_id
        AND m.created_at = recent.latest
        JOIN memorizations m ON md.memorization_id = m.id
        JOIN ayahs a ON md.ayah_id = a.id
        JOIN surahs s ON s.id = a.surah_id
        WHERE m.hafiz_id = :hafiz_id
        ', [$hafiz->id]);
        $global_score_count = count($result);

        $details = [];
        $total_score = 0;
        foreach ($result as $row) {
            $surah_ids[$row->surah_id] = $row->surah_id;
            if (!isset($details[$row->surah_id])) {
                $total_score = 0;
                $details[$row->surah_id] = [
                    'surah_id' => $row->surah_id,
                    'memorized_ayah_count' => 0,
                    'average_score' => 0,
                    'details' => [],
                ];
            }

            $total_score += $row->score;

            $details[$row->surah_id]['details'][$row->ayah_number] = $row->score;
            $details[$row->surah_id]['memorized_ayah_count'] += 1;

            $global_score += $row->score;
        }
        ksort($details);

        $surahs = DB::table('surahs')
            ->whereIn('id', array_keys($surah_ids))
            ->orderBy('id', 'asc')
            ->get();

        foreach ($surahs as $surah) {
            $details[$surah->id]['ayah_count'] = $surah->total_ayahs;
            $details[$surah->id]['surah_name'] = $surah->name;
            $total_score = 0;
            foreach ($details[$surah->id]['details'] as $ayah_id => $score) {
                $total_score += $score;
                $memorized_ayah_count++;
            }
            $details[$surah->id]['average_score'] = $total_score / count($details[$surah->id]['details']);
            $memorized_surah_count++;
        }

        $hafiz->average_score = 0;
        if ($global_score_count > 0) {
            $hafiz->average_score = $global_score / $global_score_count;
        }

        $hafiz->memorized_surah_count = $memorized_surah_count;
        $hafiz->memorized_ayah_count = $memorized_ayah_count;

        return inertia('hafiz/Detail', [
            'data' => $hafiz,
            'details' => $details,
        ]);
    }

    public function editor($id = 0)
    {
        $hafiz = $id ? Hafiz::findOrFail($id) : new Hafiz(['active' => true]);

        if ($id && $hafiz->user_id != Auth::user()->id) {
            return response()->json(['error' => 'You are not authorized to edit this hafiz'], 403);
        }

        $juzs = HafizMemorizedJuz::where('hafiz_id', $hafiz->id)->get()->toArray();
        $surahs = HafizMemorizedSurah::where('hafiz_id', $hafiz->id)->get()->toArray();
        $data = $hafiz->toArray();
        $data['juzs'] = array_column($juzs, 'juz');
        $data['surahs'] = array_column($surahs, 'surah_id');
        return inertia('hafiz/Editor', [
            'data' => $data,
            'surahs' => Surah::all(),
        ]);
    }

    public function save(Request $request)
    {
        $hafiz = !$request->id ? new Hafiz(['user_id' => Auth::user()->id]) : Hafiz::findOrFail($request->id);
        if ($hafiz->user_id != Auth::user()->id) {
            return response()->json(['error' => 'You are not authorized to modify this hafiz'], 403);
        }

        $request->validate([
            'name' => 'required|max:255',
        ], [
            'name.required' => 'Nama hafidz harus diisi.',
            'name.max' => 'Nama hafidz maksimal 255 karakter.',
        ]);

        $juzs = $request->post('juzs', []);
        $surahs = $request->post('surahs', []);

        $data = $request->only(['name', 'gender', 'birth_date', 'phone', 'address', 'active', 'notes']);
        $hafiz->fill($data);

        DB::beginTransaction();
        $hafiz->save();

        HafizMemorizedJuz::where('hafiz_id', $hafiz->id)->delete();
        foreach ($juzs as $num) {
            $juz = new HafizMemorizedJuz([
                'hafiz_id' => $hafiz->id,
                'juz' => $num,
            ]);
            $juz->save();
        }

        HafizMemorizedSurah::where('hafiz_id', $hafiz->id)->delete();
        foreach ($surahs as $surah_id) {
            $surah = new HafizMemorizedSurah([
                'hafiz_id' => $hafiz->id,
                'surah_id' => $surah_id,
            ]);
            $surah->save();
        }
        DB::commit();
        return redirect(route('hafiz.index'))->with('success', "Hafiz {$hafiz->name} telah disimpan.");
    }

    public function delete($id)
    {
        $hafiz = Hafiz::findOrFail($id);
        if ($hafiz->user_id != Auth::user()->id) {
            return response()->json(['error' => 'You are not authorized to delete this hafiz'], 403);
        }
        $hafiz->delete();
        return redirect(route('hafiz.index'))->with('success', "Hafiz {$hafiz->name} telah dihapus.");
    }

    public function clearScore($id)
    {
        $hafiz = Hafiz::findOrFail($id);
        if ($hafiz->user_id != Auth::user()->id) {
            return response()->json(['error' => 'You are not authorized to reset this hafiz'], 403);
        }

        DB::delete('delete from memorizations where hafiz_id = ?', [$hafiz->id]);
        return redirect(route('hafiz.index'))->with('success', "Riwayat hafalan hafiz {$hafiz->name} telah dihapus.");
    }
}
