<?php

namespace App\Http\Controllers;

use App\Models\Ayah;
use App\Models\Hafiz;
use App\Models\Memorization;
use App\Models\Surah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AyahController extends Controller
{
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
