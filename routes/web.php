<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDangerZoneController;
use App\Http\Controllers\Admin\AdminFacilityController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Keamanan\IncidentFollowUpController;
use App\Http\Controllers\Keamanan\KeamananReportController;
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
    if ($user->canAccessWomenZone()) {
        return redirect()->route('rute');
    }

    return redirect()->route('rute');
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
    Route::get('/laporan/data', [SecurityReportController::class, 'userReports'])->name('laporan.data');
    Route::post('/laporan', [SecurityReportController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/terakhir', [SecurityReportController::class, 'latest'])->name('laporan.latest');
    Route::get('/laporan/{report}', [SecurityReportController::class, 'show'])->whereNumber('report')->name('laporan.show');

    // Fitur penandaan laporan mencurigakan (SR-61)
    Route::post(
        '/laporan/{report}/tandai-mencurigakan',
        [SuspiciousReportFlagController::class, 'store']
    )->whereNumber('report')->name('laporan.flag');

    // User Profile
    Route::get('/profil', [UserProfileController::class, 'index'])->name('profile.index');
    Route::put('/profil', [UserProfileController::class, 'update'])->name('profile.update');

    // Backend Keamanan: Workflow Laporan
    Route::middleware('role:keamanan')->prefix('keamanan/data/reports')->name('keamanan.data.reports.')->group(function () {
        Route::get('/', [KeamananReportController::class, 'index'])->name('index');
        Route::get('/counts', [KeamananReportController::class, 'counts'])->name('counts');
        Route::get('/{report}', [KeamananReportController::class, 'show'])->whereNumber('report')->name('show');
        Route::post('/{report}/confirm', [KeamananReportController::class, 'confirm'])->whereNumber('report')->name('confirm');
        Route::post('/{report}/status', [KeamananReportController::class, 'updateStatus'])->whereNumber('report')->name('status');
    });

    // Area Khusus Role Keamanan
    Route::middleware('role:keamanan,admin')->prefix('keamanan')->name('keamanan.')->group(function () {
        Route::view('/dashboard', 'keamanan.dashboard')->name('dashboard');
        Route::view('/laporan', 'keamanan.dashboard')->name('laporan');
        Route::get('/data/laporan', [IncidentFollowUpController::class, 'index'])->name('data.laporan');
        Route::post('/laporan/{report}/tindak-lanjut', [IncidentFollowUpController::class, 'followUp'])->whereNumber('report')->name('laporan.followUp');
    });

    // Backend Admin: Workflow Laporan
    Route::middleware('role:admin')->prefix('admin/data/reports')->name('admin.data.reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');
        Route::get('/counts', [AdminReportController::class, 'counts'])->name('counts');
        Route::get('/{report}', [AdminReportController::class, 'show'])->whereNumber('report')->name('show');
        Route::post('/{report}/approve', [AdminReportController::class, 'approve'])->whereNumber('report')->name('approve');
        Route::post('/{report}/revision', [AdminReportController::class, 'revision'])->whereNumber('report')->name('revision');
        Route::post('/{report}/reject', [AdminReportController::class, 'reject'])->whereNumber('report')->name('reject');
        Route::post('/{report}/finalize', [AdminReportController::class, 'finalize'])->whereNumber('report')->name('finalize');
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

        Route::apiResource('facilities', AdminFacilityController::class);
        Route::apiResource('danger-zones', AdminDangerZoneController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Public Feature & Navigation Views
|--------------------------------------------------------------------------
*/
Route::get('/rute', function () {
    $user = auth()->user() ? auth()->user()->fresh() : null;
    $canAccessWomenZone = $user ? $user->canAccessWomenZone() : false;
    $routeMode = $canAccessWomenZone ? 'women' : 'general';

    return view('rute', [
        'currentUser' => $user,
        'canAccessWomenZone' => $canAccessWomenZone,
        'routeMode' => $routeMode,
        'userGender' => $user?->gender,
        'userIdentityStatus' => $user?->identity_status ?? 'unverified',
    ]);
})->name('rute');

Route::get('/rute/bandingkan', function () {
    $user = auth()->user() ? auth()->user()->fresh() : null;
    $canAccessWomenZone = $user ? $user->canAccessWomenZone() : false;
    $routeMode = $canAccessWomenZone ? 'women' : 'general';

    return view('rute.bandingkan', [
        'currentUser' => $user,
        'canAccessWomenZone' => $canAccessWomenZone,
        'routeMode' => $routeMode,
    ]);
})->name('rute.bandingkan');

Route::post('/api/routes/score', [\App\Http\Controllers\Api\RouteScoreApiController::class, 'score'])->name('api.routes.score');

Route::view('/laporkan', 'laporkan')->name('laporkan');
Route::view('/laporan/riwayat', 'laporan.riwayat')->name('laporan.riwayat');

// SR-4: Women's Zone (Validasi Gender & Identitas Terverifikasi via Database)
Route::get('/womens-zone', [WomensZoneController::class, 'index'])->middleware(['auth', 'women.zone'])->name('womens-zone');

// SR-9: Pusat Informasi Darurat
Route::view('/darurat', 'darurat')->name('darurat');
