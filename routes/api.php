<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AyahController;
use App\Http\Controllers\SurahController;
use Illuminate\Support\Facades\Route;

// Public API Routes
Route::prefix('api/v1')->group(function () {
    Route::get('surahs', [SurahController::class, 'index']);
    Route::get('surahs/{id}', [SurahController::class, 'show']);

    Route::get('ayahs', [AyahController::class, 'index']);
    Route::get('surahs/{surah_id}/ayahs', [AyahController::class, 'index']);
});
