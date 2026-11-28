<?php

namespace App\Http\Controllers\Keamanan;

use App\Http\Controllers\Controller;
use App\Models\SecurityReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IncidentFollowUpController extends Controller
{
    /**
     * Menampilkan daftar semua laporan masyarakat untuk ditindaklanjuti.
     */
    public function index(Request $request): JsonResponse
    {
        $query = SecurityReport::with(['user:id,name,email,phone_number', 'histories', 'flags'])
            ->withCount('flags');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        $reports = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    /**
     * Petugas keamanan melakukan tindak lanjut terhadap laporan.
     */
    public function followUp(Request $request, SecurityReport $report): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'note' => 'required|string|max:1000',
        ]);

        $statusStr = $validated['status'];
        $report->update([
            'status' => $statusStr,
            'inspection_note' => $validated['note'],
            'inspected_by' => $request->user()->id,
            'security_confirmed_at' => now(),
        ]);

        $report->histories()->create([
            'status' => $statusStr,
            'note' => $validated['note'],
            'actor_id' => $request->user()->id,
            'actor_role' => 'keamanan',
        ]);

        try {
            app(\App\Services\SafetyScoreService::class)->recalculateAreaForReport(
                $report,
                'follow_up_' . $statusStr,
                $request->user()->id,
                'keamanan'
            );
        } catch (\Throwable $e) {
            // Ignore
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui.',
            'data' => $report->fresh(['histories']),
        ]);
    }
}
