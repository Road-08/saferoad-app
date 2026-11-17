<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDangerZoneController;
use App\Http\Controllers\Admin\AdminFacilityController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IdentityVerificationController;
use App\Http\Controllers\Keamanan\IncidentFollowUpController;
use App\Http\Controllers\SecurityReportController;
use App\Http\Controllers\SuspiciousReportFlagController;
use App\Http\Controllers\UserProfileController;
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

    // Incident Reporting
    Route::get('/laporan', [SecurityReportController::class, 'index'])->name('laporan.index');
    Route::post('/laporan', [SecurityReportController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/terakhir', [SecurityReportController::class, 'latest'])->name('laporan.latest');
    Route::get('/laporan/{report}', [SecurityReportController::class, 'show'])->name('laporan.show');

    // Fitur penandaan laporan mencurigakan (SR-61)
    Route::post(
        '/laporan/{report}/tandai-mencurigakan',
        [SuspiciousReportFlagController::class, 'store']
    )->name('laporan.flag');

    // User Profile
    Route::get('/profil', [UserProfileController::class, 'index'])->name('profile.index');
    Route::put('/profil', [UserProfileController::class, 'update'])->name('profile.update');

    // Identity Verification (KTP & Gender)
    Route::get('/verifikasi-identitas', [IdentityVerificationController::class, 'show'])->name('profile.verifikasi');
    Route::post('/verifikasi-identitas', [IdentityVerificationController::class, 'submit'])->name('profile.verifikasi.store');

    // Area Khusus Role Keamanan (Web & API)
    Route::middleware('role:keamanan,admin')->prefix('keamanan')->name('keamanan.')->group(function () {
        Route::get('/laporan', [IncidentFollowUpController::class, 'index'])->name('laporan.index');
        Route::post('/laporan/{report}/tindak-lanjut', [IncidentFollowUpController::class, 'followUp'])->name('laporan.followUp');
    });

    // Area Khusus Role Admin (Web & API)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('stats');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::put('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');

        Route::get('/verifikasi-identitas', [AdminVerificationController::class, 'index'])->name('verifikasi.index');
        Route::post('/verifikasi-identitas/{user}/approve', [AdminVerificationController::class, 'approve'])->name('verifikasi.approve');
        Route::post('/verifikasi-identitas/{user}/reject', [AdminVerificationController::class, 'reject'])->name('verifikasi.reject');

        Route::apiResource('facilities', AdminFacilityController::class);
        Route::apiResource('danger-zones', AdminDangerZoneController::class);
    });
});

// Peta & Navigasi Rute
Route::view('/rute', 'rute')->name('rute');
Route::view('/rute/bandingkan', 'rute.bandingkan')->name('rute.bandingkan');
