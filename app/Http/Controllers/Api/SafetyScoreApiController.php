<?php

namespace App\Services;

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SafetyScoreLog;
use App\Services\SafetyScoreService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SafetyScoreApiController extends Controller
{
    public function __construct(private SafetyScoreService $safetyScoreService) {}

    /**
     * Mengambil Safety Score terbaru untuk area titik tertentu.
     * GET /api/safety-score
     */
    public function score(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:0.05', 'max:5.0'],
            'mode' => ['nullable', 'string', 'in:general,women,all'],
        ]);

        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];
        $radiusKm = isset($validated['radius']) ? (float) $validated['radius'] : 0.50;

        // Tentukan mode berdasarkan user atau parameter
        $user = $request->user();
        $mode = $validated['mode'] ?? (($user && $user->canAccessWomenZone()) ? 'women' : 'general');

        $result = $this->safetyScoreService->calculateAreaScore($lat, $lng, $radiusKm, $mode);

        return response()->json([
            'success' => true,
            'message' => 'Safety score area berhasil dihitung.',
            'data' => $result,
        ]);
    }

    /**
     * Mengambil faktor-faktor yang memengaruhi skor di area tertentu.
     * GET /api/safety-score/factors
     */
    public function factors(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:0.05', 'max:5.0'],
            'mode' => ['nullable', 'string', 'in:general,women,all'],
        ]);

        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];
        $radiusKm = isset($validated['radius']) ? (float) $validated['radius'] : 0.50;

        $user = $request->user();
        $mode = $validated['mode'] ?? (($user && $user->canAccessWomenZone()) ? 'women' : 'general');

        $result = $this->safetyScoreService->calculateAreaScore($lat, $lng, $radiusKm, $mode);

        return response()->json([
            'success' => true,
            'message' => 'Faktor pembentuk safety score berhasil diambil.',
            'data' => [
                'score' => $result['score'],
                'level' => $result['level'],
                'level_label' => $result['level_label'],
                'components' => $result['components'],
                'factors' => $result['factors'],
                'facilities_summary' => $result['facilities_summary'],
            ],
        ]);
    }

    /**
     * Mengambil laporan kejadian pada area tertentu.
     * GET /api/safety-score/reports
     */
    public function reports(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:0.05', 'max:5.0'],
            'status' => ['nullable', 'string'],
        ]);

        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];
        $radiusKm = isset($validated['radius']) ? (float) $validated['radius'] : 0.50;
        $status = $validated['status'] ?? null;

        $reports = $this->safetyScoreService->getAreaReports($lat, $lng, $radiusKm, $status);

        return response()->json([
            'success' => true,
            'message' => 'Daftar laporan pada area berhasil diambil.',
            'count' => $reports->count(),
            'data' => $reports->map(function ($r) {
                return [
                    'id' => $r->id,
                    'report_code' => $r->report_code,
                    'category' => $r->category,
                    'description' => $r->description,
                    'severity' => $r->severity,
                    'latitude' => $r->latitude,
                    'longitude' => $r->longitude,
                    'address' => $r->address,
                    'status' => $r->status,
                    'status_label' => $r->statusLabel(),
                    'inspection_status' => $r->inspection_status,
                    'inspection_status_label' => $r->inspectionStatusLabel(),
                    'photo_url' => $r->photoUrl(),
                    'incident_at' => $r->incident_at?->toIso8601String(),
                ];
            }),
        ]);
    }

    /**
     * Mengambil riwayat audit perubahan skor.
     * GET /api/safety-score/logs
     */
    public function logs(Request $request): JsonResponse
    {
        $query = SafetyScoreLog::with(['securityReport:id,report_code,category', 'actor:id,name,role'])
            ->latest();

        if ($reportId = $request->query('report_id')) {
            $query->where('security_report_id', $reportId);
        }

        if ($since = $request->query('since')) {
            try {
                $sinceDate = Carbon::parse($since);
                $query->where('created_at', '>=', $sinceDate);
            } catch (\Exception $e) {
                // Ignore parse errors
            }
        }

        $limit = min(100, max(5, (int) $request->input('limit', 20)));
        $logs = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'server_time' => now()->setTimezone('Asia/Jakarta')->toIso8601String(),
            'data' => $logs->items(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ]);
    }
}
