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

    public function editor($id = 0)
    {
        $hafiz = $id ? Hafiz::findOrFail($id) : new Hafiz(['active' => true]);
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
        $userId = Auth::user()->id;
        $hafiz = !$request->id ? new Hafiz(['user_id' => $userId]) : Hafiz::findOrFail($request->id);

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
            $juz ->save();
        }

        HafizMemorizedSurah::where('hafiz_id', $hafiz->id)->delete();
        foreach ($surahs as $surah_id) {
            $surah = new HafizMemorizedSurah([
                'hafiz_id' => $hafiz->id,
                'surah_id' => $surah_id,
            ]);
            $surah ->save();
        }
        DB::commit();

        return redirect(route('hafiz.index'))->with('success', "Hafiz {$hafiz->name} telah disimpan.");
    }

    public function delete($id)
    {
        $hafiz = Hafiz::findOrFail($id);
        $hafiz->delete();

        return response()->json([
            'message' => "Hafiz {$hafiz->name} telah dihapus!"
        ]);
    }
}
