<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InspectionStatus;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\SecurityReport;
use App\Services\SafetyScoreService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * Format a report model into a response array.
     */
    protected function formatReport(SecurityReport $report, bool $withHistories = false): array
    {
        $data = [
            'id' => $report->id,
            'report_code' => $report->report_code,
            'user_id' => $report->user_id,
            'reporter_name' => $report->user?->name ?? 'Anonim',
            'reporter_email' => $report->user?->email,
            'reporter_phone' => $report->user?->phone_number,
            'is_anonymous' => (bool) $report->is_anonymous,
            'category' => $report->category,
            'description' => $report->description,
            'severity' => $report->severity,
            'address' => $report->address,
            'latitude' => $report->latitude,
            'longitude' => $report->longitude,
            'status' => $report->status,
            'status_label' => $report->statusLabel(),
            'admin_note' => $report->admin_note,
            'forwarded_at' => $report->forwarded_at?->toIso8601String(),
            'forwarded_by' => $report->forwarded_by,
            'forwarded_by_name' => $report->forwardedBy?->name,
            'inspection_note' => $report->inspection_note,
            'needs_action' => $report->needs_action,
            'action_note' => $report->action_note,
            'inspected_by' => $report->inspected_by,
            'inspected_by_name' => $report->inspectedBy?->name,
            'security_confirmed_at' => $report->security_confirmed_at?->toIso8601String(),
            'handled_at' => $report->handled_at?->toIso8601String(),
            'incident_at' => $report->incident_at?->toIso8601String(),
            'reported_at' => $report->created_at?->toIso8601String(),
            'created_at' => $report->created_at?->toIso8601String(),
            'updated_at' => $report->updated_at?->toIso8601String(),
            'flags_count' => $report->flags_count ?? $report->flags()->count(),
        ];

        if ($withHistories) {
            $data['histories'] = $report->histories->map(function ($h) {
                return [
                    'id' => $h->id,
                    'status' => $h->status,
                    'status_label' => ReportStatus::tryFrom($h->status)?->label() ?? $h->status,
                    'note' => $h->note,
                    'actor_id' => $h->actor_id,
                    'actor_name' => $h->actor?->name,
                    'actor_role' => $h->actor_role,
                    'created_at' => $h->created_at?->toIso8601String(),
                ];
            });
        }

        return $data;
    }

    /**
     * GET /admin/data/reports
     */
    public function index(Request $request): JsonResponse
    {
        $query = SecurityReport::with(['user:id,name,email,phone_number', 'forwardedBy:id,name', 'inspectedBy:id,name'])
            ->withCount('flags');

        // Filter status
        if ($request->has('status') && $request->query('status') !== 'all') {
            $query->where('status', $request->query('status'));
        } elseif (! $request->has('status') && ! $request->has('since')) {
            $query->where('status', ReportStatus::PENDING_VALIDATION->value);
        }

        // Filter category
        if ($request->filled('category') && $request->query('category') !== 'all') {
            $query->where('category', $request->query('category'));
        }

        // Filter since (ISO 8601 time)
        if ($since = $request->query('since')) {
            try {
                $sinceTime = Carbon::parse($since);
                $query->where('updated_at', '>=', $sinceTime);
            } catch (\Exception $e) {
                // Ignore parse errors
            }
        }

        $perPage = (int) $request->input('per_page', 15);
        $paginated = $query->latest('updated_at')->paginate($perPage);

        $reports = collect($paginated->items())->map(fn ($r) => $this->formatReport($r));

        return response()->json([
            'success' => true,
            'server_time' => now()->setTimezone('Asia/Jakarta')->toIso8601String(),
            'data' => $reports,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * GET /admin/data/reports/{report}
     */
    public function show(SecurityReport $report): JsonResponse
    {
        $report->load(['user:id,name,email,phone_number', 'forwardedBy:id,name', 'inspectedBy:id,name', 'histories.actor:id,name', 'flags']);

        return response()->json([
            'success' => true,
            'server_time' => now()->setTimezone('Asia/Jakarta')->toIso8601String(),
            'data' => $this->formatReport($report, true),
        ]);
    }

    /**
     * POST /admin/data/reports/{report}/approve
     */
    public function approve(Request $request, SecurityReport $report): JsonResponse
    {
        if ($report->status !== ReportStatus::PENDING_VALIDATION->value) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya laporan dengan status Menunggu Validasi yang dapat disetujui.',
            ], 422);
        }

        $report->update([
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'forwarded_at' => now(),
            'forwarded_by' => $request->user()->id,
        ]);

        $report->histories()->create([
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'note' => $request->input('note') ?? 'Laporan disetujui oleh admin dan diteruskan ke petugas keamanan.',
            'actor_id' => $request->user()->id,
            'actor_role' => 'admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil disetujui dan diteruskan ke petugas keamanan.',
            'data' => $this->formatReport($report->fresh(['user', 'forwardedBy', 'inspectedBy', 'histories.actor']), true),
        ]);
    }

    /**
     * POST /admin/data/reports/{report}/revision
     */
    public function revision(Request $request, SecurityReport $report): JsonResponse
    {
        $validated = $request->validate([
            'admin_note' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:500',
        ]);

        $note = $validated['admin_note'] ?? $validated['note'] ?? null;
        if (empty($note)) {
            return response()->json([
                'success' => false,
                'message' => 'Catatan perbaikan wajib diisi.',
                'errors' => ['admin_note' => ['Catatan perbaikan wajib diisi.']],
            ], 422);
        }

        if (! in_array($report->status, [ReportStatus::PENDING_VALIDATION->value, ReportStatus::FORWARDED_TO_SECURITY->value], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Status laporan tidak dapat diubah ke Perlu Perbaikan.',
            ], 422);
        }

        $report->update([
            'status' => ReportStatus::NEEDS_REVISION->value,
            'admin_note' => $note,
        ]);

        $report->histories()->create([
            'status' => ReportStatus::NEEDS_REVISION->value,
            'note' => $note,
            'actor_id' => $request->user()->id,
            'actor_role' => 'admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan perbaikan laporan berhasil dikirim ke pelapor.',
            'data' => $this->formatReport($report->fresh(['user', 'forwardedBy', 'inspectedBy', 'histories.actor']), true),
        ]);
    }

    /**
     * POST /admin/data/reports/{report}/reject
     */
    public function reject(Request $request, SecurityReport $report): JsonResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
            'reason' => 'nullable|string|max:500',
            'admin_note' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:500',
        ]);

        $reason = $validated['rejection_reason'] ?? $validated['reason'] ?? $validated['admin_note'] ?? $validated['note'] ?? null;
        if (empty($reason)) {
            return response()->json([
                'success' => false,
                'message' => 'Alasan penolakan wajib diisi.',
                'errors' => ['rejection_reason' => ['Alasan penolakan wajib diisi.']],
            ], 422);
        }

        $report->update([
            'status' => ReportStatus::REJECTED->value,
            'validation_status' => 'rejected',
            'inspection_status' => InspectionStatus::UNPROVEN->value,
            'admin_note' => $reason,
        ]);

        $report->histories()->create([
            'status' => ReportStatus::REJECTED->value,
            'note' => $reason,
            'actor_id' => $request->user()->id,
            'actor_role' => 'admin',
        ]);

        try {
            app(SafetyScoreService::class)->recalculateAreaForReport(
                $report,
                'report_rejected',
                $request->user()->id,
                'admin'
            );
        } catch (\Throwable $e) {
            // Ignore
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil ditolak.',
            'data' => $this->formatReport($report->fresh(['user', 'forwardedBy', 'inspectedBy', 'histories.actor']), true),
        ]);
    }

    /**
     * POST /admin/data/reports/{report}/finalize
     */
    public function finalize(Request $request, SecurityReport $report): JsonResponse
    {
        if ($report->status !== ReportStatus::FORWARDED_TO_SECURITY->value || is_null($report->security_confirmed_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan belum dikonfirmasi oleh petugas keamanan atau status tidak sesuai untuk difinalisasi.',
            ], 422);
        }

        $report->update([
            'status' => ReportStatus::HANDLED->value,
            'validation_status' => 'verified',
            'inspection_status' => InspectionStatus::HANDLED->value,
            'handled_at' => now(),
        ]);

        $report->histories()->create([
            'status' => ReportStatus::HANDLED->value,
            'note' => $request->input('note') ?? 'Laporan difinalisasi oleh admin dan ditandai sebagai ditangani.',
            'actor_id' => $request->user()->id,
            'actor_role' => 'admin',
        ]);

        try {
            app(SafetyScoreService::class)->recalculateAreaForReport(
                $report,
                'report_handled',
                $request->user()->id,
                'admin'
            );
        } catch (\Throwable $e) {
            // Ignore
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil difinalisasi dan ditandai sebagai ditangani.',
            'data' => $this->formatReport($report->fresh(['user', 'forwardedBy', 'inspectedBy', 'histories.actor']), true),
        ]);
    }

    /**
     * GET /admin/data/reports/counts
     */
    public function counts(): JsonResponse
    {
        $serverTime = now()->setTimezone('Asia/Jakarta')->toIso8601String();

        $data = [
            'pending_validation' => SecurityReport::where('status', ReportStatus::PENDING_VALIDATION->value)->count(),
            'forwarded_to_security' => SecurityReport::where('status', ReportStatus::FORWARDED_TO_SECURITY->value)->count(),
            'needs_revision' => SecurityReport::where('status', ReportStatus::NEEDS_REVISION->value)->count(),
            'handled' => SecurityReport::where('status', ReportStatus::HANDLED->value)->count(),
            'rejected' => SecurityReport::where('status', ReportStatus::REJECTED->value)->count(),
            'ready_to_finalize' => SecurityReport::where('status', ReportStatus::FORWARDED_TO_SECURITY->value)
                ->whereNotNull('security_confirmed_at')
                ->count(),
            'server_time' => $serverTime,
        ];

        return response()->json([
            'success' => true,
            'server_time' => $serverTime,
            'data' => $data,
        ]);
    }
}
