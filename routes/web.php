<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AyahController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HafizController;
use App\Http\Controllers\MemorizationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\Auth;
use App\Http\Middleware\NonAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()) {
        return redirect()->route('dashboard');
    }
    return inertia('Welcome');
})->name('home');

Route::middleware(NonAuthenticated::class)->group(function () {
    Route::prefix('auth')->group(function () {
        Route::match(['get', 'post'], 'login', [AuthController::class, 'login'])->name('login');
        Route::match(['get', 'post'], 'register', [AuthController::class, 'register'])->name('register');
        Route::match(['get', 'post'], 'forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    });
});

Route::middleware([Auth::class])->group(function () {
    Route::match(['get', 'post'], 'auth/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('', function () {
        return redirect()->route('dashboard');
    });
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    Route::prefix('reports')->group(function () {
        Route::get('', [ReportController::class, 'index'])->name('report.index');
        Route::get('data', [ReportController::class, 'index'])->name('report.data');
    });

    Route::prefix('ayahs')->group(function () {
        Route::get('data', [AyahController::class, 'data'])->name('ayah.data');
    });

    Route::prefix('hafizes')->group(function () {
        Route::get('', [HafizController::class, 'index'])->name('hafiz.index');
        Route::get('data', [HafizController::class, 'data'])->name('hafiz.data');
        Route::get('add', [HafizController::class, 'editor'])->name('hafiz.add');
        Route::get('edit/{id}', [HafizController::class, 'editor'])->name('hafiz.edit');
        Route::get('detail/{id}', [HafizController::class, 'detail'])->name('hafiz.detail');
        Route::get('surah-history/{hafiz_id}/{surah_id}', [HafizController::class, 'surahHistory'])->name('hafiz.surah-history');
        Route::post('clear-score/{id}', [HafizController::class, 'clearScore'])->name('hafiz.clear-score');
        Route::post('save', [HafizController::class, 'save'])->name('hafiz.save');
        Route::post('delete/{id}', [HafizController::class, 'delete'])->name('hafiz.delete');
    });

    Route::prefix('memorizations')->group(function () {
        Route::get('', [MemorizationController::class, 'index'])->name('memorization.index');
        Route::get('data', [MemorizationController::class, 'data'])->name('memorization.data');
        Route::match(['get', 'post'], 'create', [MemorizationController::class, 'create'])->name('memorization.create');
        Route::post('save', [MemorizationController::class, 'save'])->name('memorization.save');
        Route::post('delete/{id}', [MemorizationController::class, 'delete'])->name('memorization.delete');
        Route::get('run', [MemorizationController::class, 'run'])->name('memorization.run');
        Route::get('view', [MemorizationController::class, 'view'])->name('memorization.view');
    });

    Route::prefix('settings')->group(function () {
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

        Route::prefix('users')->group(function () {
            Route::get('', [UserController::class, 'index'])->name('user.index');
            Route::get('data', [UserController::class, 'data'])->name('user.data');
            Route::get('add', [UserController::class, 'editor'])->name('user.add');
            Route::get('edit/{id}', [UserController::class, 'editor'])->name('user.edit');
            Route::post('save', [UserController::class, 'save'])->name('user.save');
            Route::post('delete/{id}', [UserController::class, 'delete'])->name('user.delete');
        });
    });
});
