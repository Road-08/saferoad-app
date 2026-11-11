<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SecurityReportController;
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
});
