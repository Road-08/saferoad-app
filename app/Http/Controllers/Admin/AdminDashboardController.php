<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangerZone;
use App\Models\SafetyFacility;
use App\Models\SecurityReport;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'masyarakat' => User::where('role', 'masyarakat')->count(),
                'keamanan' => User::where('role', 'keamanan')->count(),
                'admin' => User::where('role', 'admin')->count(),
                'identity_verified' => User::where('identity_status', 'verified')->count(),
                'identity_pending' => User::where('identity_status', 'pending')->count(),
            ],
            'reports' => [
                'total' => SecurityReport::count(),
                'submitted' => SecurityReport::where('status', 'submitted')->count(),
                'admin_review' => SecurityReport::where('status', 'admin_review')->count(),
                'officer_check' => SecurityReport::where('status', 'officer_check')->count(),
                'map_updated' => SecurityReport::where('status', 'map_updated')->count(),
                'rejected' => SecurityReport::where('status', 'rejected')->count(),
            ],
            'facilities' => [
                'total' => SafetyFacility::count(),
                'active' => SafetyFacility::where('status', 'active')->count(),
                'cctv' => SafetyFacility::where('type', 'cctv')->count(),
                'street_light' => SafetyFacility::where('type', 'street_light')->count(),
                'security_post' => SafetyFacility::where('type', 'security_post')->count(),
            ],
            'danger_zones' => [
                'total' => DangerZone::count(),
                'active' => DangerZone::where('is_active', true)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
