<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SecurityReportController;
use App\Http\Controllers\SuspiciousReportFlagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : app(AuthController::class)->showLogin();
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/laporan', [SecurityReportController::class, 'store'])
        ->name('laporan.store');

    Route::get('/laporan/terakhir', [SecurityReportController::class, 'latest'])
        ->name('laporan.latest');

    // Fitur penandaan laporan mencurigakan (SR-61)
    Route::post(
        '/laporan/{report}/tandai-mencurigakan',
        [SuspiciousReportFlagController::class, 'store']
    )->name('laporan.flag');
});

// SR-11: Profile Management
Route::get('/profil', function () {
    return view('profile.index');
})->name('profile.index');

// SR-11: Identity Verification
Route::get('/verifikasi-identitas', function () {
    return view('profile.verifikasi-identitas');
})->name('profile.verifikasi');

Route::view('/rute', 'rute')->name('rute');
Route::view('/rute/bandingkan', 'rute.bandingkan')->name('rute.bandingkan');
