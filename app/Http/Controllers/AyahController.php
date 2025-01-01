<?php

namespace App\Http\Controllers;

use App\Models\Ayah;
use Illuminate\Http\Request;

class AyahController extends Controller
{
    public function index(Request $request, $surah_id = null)
    {
        return response()->json($surah_id ? Ayah::where('surah_id', $surah_id)->get() : Ayah::all());
    }

    public function data(Request $request)
    {
        $filter = $request->get('filter', []);

        $q = Ayah::query();
        $q->orderBy('id', 'asc');
        if (!empty($filter['surah_id'])) {
            $q->where('surah_id', $filter['surah_id']);
        }

        $ayahs = $q->paginate($request->get('per_page', 10))->withQueryString();
        return response()->json($ayahs);
    }
}
