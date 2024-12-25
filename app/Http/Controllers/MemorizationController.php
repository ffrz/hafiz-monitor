<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemorizationController extends Controller
{
    public function index()
    {
        return inertia('memorization/Index', [
        ]);
    }
}
