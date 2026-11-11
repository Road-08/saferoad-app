<?php

namespace App\Http\Controllers;

use App\Models\SecurityReport;
use App\Models\SuspiciousReportFlag;
use Illuminate\Http\Request;

class SuspiciousReportFlagController extends Controller
{
    public function store(Request $request, SecurityReport $report)
    {
        $userId = $request->user()->getAuthIdentifier();

        $flag = SuspiciousReportFlag::firstOrCreate(
            [
                'security_report_id' => $report->id,
                'user_id' => $userId,
            ]
        );

        if (!$flag->wasRecentlyCreated) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah menandai laporan ini sebelumnya.',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil ditandai sebagai mencurigakan.',
            'data' => $flag,
        ], 201);
    }
}
