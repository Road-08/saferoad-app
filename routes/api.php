<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDangerZoneController;
use App\Http\Controllers\Admin\AdminFacilityController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVerificationController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DangerZoneApiController;
use App\Http\Controllers\Api\RouteScoreApiController;
use App\Http\Controllers\Api\SafetyFacilityApiController;
use App\Http\Controllers\IdentityVerificationController;
use App\Http\Controllers\Keamanan\IncidentFollowUpController;
use App\Http\Controllers\RouteComparisonController;
use App\Http\Controllers\SecurityReportController;
use App\Http\Controllers\SuspiciousReportFlagController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Endpoints
|--------------------------------------------------------------------------
*/

// Authentication API
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);
});

// Route Comparison & Security Scoring (Dipanggil oleh rute.blade.php dan bandingkan.blade.php)
Route::post('/routes/score', [RouteScoreApiController::class, 'score']);
Route::get('/routes/compare', [RouteComparisonController::class, 'compare']);

// Data Peta (CCTV, Lampu Jalan, Pos Keamanan, Titik Rawan)
Route::get('/facilities', [SafetyFacilityApiController::class, 'index']);
Route::get('/danger-zones', [DangerZoneApiController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Authenticated API Endpoints
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthApiController::class, 'me']);
        Route::post('/logout', [AuthApiController::class, 'logout']);
    });

    // Profile Management
    Route::get('/profile', [UserProfileController::class, 'index']);
    Route::put('/profile', [UserProfileController::class, 'update']);
    Route::post('/profile/verify-identity', [IdentityVerificationController::class, 'submit']);

    // Incident Reporting
    Route::get('/reports', [SecurityReportController::class, 'index']);
    Route::post('/reports', [SecurityReportController::class, 'store']);
    Route::get('/reports/latest', [SecurityReportController::class, 'latest']);
    Route::get('/reports/{report}', [SecurityReportController::class, 'show']);
    Route::post('/reports/{report}/flag', [SuspiciousReportFlagController::class, 'store']);

    // Role Keamanan API
    Route::middleware('role:keamanan,admin')->prefix('keamanan')->group(function () {
        Route::get('/reports', [IncidentFollowUpController::class, 'index']);
        Route::post('/reports/{report}/follow-up', [IncidentFollowUpController::class, 'followUp']);
    });

    // Role Admin API
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/stats', [AdminDashboardController::class, 'stats']);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::put('/users/{user}/role', [AdminUserController::class, 'updateRole']);

        Route::get('/verifications', [AdminVerificationController::class, 'index']);
        Route::post('/verifications/{user}/approve', [AdminVerificationController::class, 'approve']);
        Route::post('/verifications/{user}/reject', [AdminVerificationController::class, 'reject']);

        Route::apiResource('facilities', AdminFacilityController::class);
        Route::apiResource('danger-zones', AdminDangerZoneController::class);
    });
});
