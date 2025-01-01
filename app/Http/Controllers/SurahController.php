<?php

namespace App\Http\Controllers;

use App\Models\Surah;
use Illuminate\Http\Request;

class SurahController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Surah::all());
    }

    public function show(Request $request, $id)
    {
        return response()->json(Surah::find($id));
    }
}
