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
use App\Http\Controllers\WomensZoneController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (! auth()->check()) {
        return app(AuthController::class)->showLogin();
    }

    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->role === 'keamanan') {
        return redirect()->route('keamanan.dashboard');
    }

    return redirect()->route('dashboard');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::redirect('/login/keamanan', '/login');
    Route::redirect('/login/admin', '/login');
    Route::redirect('/login/masyarakat', '/login');
});

Route::redirect('/keamanan/login', '/login/keamanan');
Route::redirect('/admin/login', '/login/admin');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
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

    // Area Khusus Role Keamanan
    Route::middleware('role:keamanan,admin')->prefix('keamanan')->name('keamanan.')->group(function () {
        Route::view('/dashboard', 'keamanan.dashboard')->name('dashboard');
        Route::view('/laporan', 'keamanan.dashboard')->name('laporan');
        Route::get('/data/laporan', [IncidentFollowUpController::class, 'index'])->name('data.laporan');
        Route::post('/laporan/{report}/tindak-lanjut', [IncidentFollowUpController::class, 'followUp'])->name('laporan.followUp');
    });

    // Area Khusus Role Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
        Route::view('/validasi', 'admin.validasi')->name('validasi');
        Route::view('/peta', 'admin.peta')->name('peta');
        Route::view('/statistik', 'admin.statistik')->name('statistik');
        Route::view('/users', 'admin.dashboard')->name('users');
        Route::view('/fasilitas', 'admin.peta')->name('fasilitas');

        Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('stats');
        Route::get('/data/users', [AdminUserController::class, 'index'])->name('data.users');
        Route::get('/data/users/{user}', [AdminUserController::class, 'show'])->name('data.users.show');
        Route::put('/data/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('data.users.role');

        Route::get('/verifikasi-identitas', [AdminVerificationController::class, 'index'])->name('verifikasi.index');
        Route::post('/verifikasi-identitas/{user}/approve', [AdminVerificationController::class, 'approve'])->name('verifikasi.approve');
        Route::post('/verifikasi-identitas/{user}/reject', [AdminVerificationController::class, 'reject'])->name('verifikasi.reject');

        Route::apiResource('facilities', AdminFacilityController::class);
        Route::apiResource('danger-zones', AdminDangerZoneController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Public Feature & Navigation Views
|--------------------------------------------------------------------------
*/
Route::view('/rute', 'rute')->name('rute');
Route::view('/rute/bandingkan', 'rute.bandingkan')->name('rute.bandingkan');

Route::view('/laporkan', 'laporkan')->name('laporkan');
Route::view('/laporan/riwayat', 'laporan.riwayat')->name('laporan.riwayat');

// SR-4: Women's Zone (Validasi Gender & Identitas Terverifikasi via Database)
Route::get('/womens-zone', [WomensZoneController::class, 'index'])->middleware('auth')->name('womens-zone');

// SR-9: Pusat Informasi Darurat
Route::view('/darurat', 'darurat')->name('darurat');
