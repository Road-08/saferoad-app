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

    // Route khusus portal login per role
    Route::get('/login/keamanan', function () {
        return view('auth.form', ['mode' => 'login', 'initialRole' => 'keamanan']);
    })->name('login.keamanan');

    Route::get('/login/admin', function () {
        return view('auth.form', ['mode' => 'login', 'initialRole' => 'admin']);
    })->name('login.admin');

    Route::get('/login/masyarakat', function () {
        return view('auth.form', ['mode' => 'login', 'initialRole' => 'masyarakat']);
    })->name('login.masyarakat');
});

Route::redirect('/keamanan/login', '/login/keamanan');
Route::redirect('/admin/login', '/login/admin');

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
Route::redirect('/rute/bandingkan', '/rute')->name('rute.bandingkan');

// Incident report page route
Route::view('/laporkan', 'laporkan')->name('laporkan');
Route::view('/laporan/riwayat', 'laporan.riwayat')->name('laporan.riwayat');

// SR-4: Women's Zone
Route::view('/womens-zone', 'womens-zone')->name('womens-zone');

// SR-9: Pusat Informasi Darurat
Route::view('/darurat', 'darurat')->name('darurat');

// SR-8: Role Keamanan (Security Officers)
Route::prefix('keamanan')->name('keamanan.')->group(function () {
    Route::view('/dashboard', 'keamanan.dashboard')->name('dashboard');
    Route::view('/laporan', 'keamanan.laporan')->name('laporan');
});

// SR-8: Role Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('/validasi', 'admin.validasi')->name('validasi');
    Route::view('/peta', 'admin.peta')->name('peta');
    Route::view('/statistik', 'admin.statistik')->name('statistik');
    Route::view('/users', 'admin.users')->name('users');
    Route::view('/fasilitas', 'admin.fasilitas')->name('fasilitas');
});
