<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IdentityVerificationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return match (Auth::user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'security' => redirect()->route('security.dashboard'),
        default => redirect()->route('dashboard'),
    };
})->name('home');

// Login dan Register hanya untuk pengguna yang belum login.
Route::middleware('guest')->group(function () {
    Route::get('/login', [
        AuthController::class,
        'showLogin',
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login',
    ])->name('login.store');

    Route::get('/register', [
        AuthController::class,
        'showRegister',
    ])->name('register');

    Route::post('/register', [
        AuthController::class,
        'register',
    ])->name('register.store');
});

// Logout.
Route::post('/logout', [
    AuthController::class,
    'logout',
])->middleware('auth')->name('logout');

// Dashboard berdasarkan role.
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role !== 'user') {
            return match (auth()->user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'security' => redirect()->route('security.dashboard'),
                default => abort(403),
            };
        }

        return view('dashboard', [
            'user' => auth()->user(),
        ]);
    })->name('dashboard');

    // Dashboard administrator.
    Route::get('/admin/dashboard', function () {
        abort_unless(
            auth()->user()->role === 'admin',
            403
        );

        return view('role-dashboard', [
            'heading' => 'Dashboard Administrator',
            'description' =>
                'Area administrator SafeRoad. ' .
                'Kelola antrean verifikasi identitas pengguna.',
        ]);
    })->name('admin.dashboard');

    // Daftar dan proses verifikasi identitas.
    Route::get('/admin/verifications', [
        IdentityVerificationController::class,
        'index',
    ])->name('admin.verifications.index');

    Route::get('/admin/verifications/{user}/ktp', [
        IdentityVerificationController::class,
        'downloadKtp',
    ])->name('admin.verifications.ktp');

    Route::post('/admin/verifications/{user}/approve', [
        IdentityVerificationController::class,
        'approve',
    ])->name('admin.verifications.approve');

    Route::post('/admin/verifications/{user}/reject', [
        IdentityVerificationController::class,
        'reject',
    ])->name('admin.verifications.reject');

    // Dashboard petugas keamanan.
    Route::get('/security/dashboard', function () {
        abort_unless(
            auth()->user()->role === 'security',
            403
        );

        return view('role-dashboard', [
            'heading' => 'Dashboard Petugas Keamanan',
            'description' =>
                'Area petugas keamanan SafeRoad.',
        ]);
    })->name('security.dashboard');
});
