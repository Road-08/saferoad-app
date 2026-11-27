<?php

namespace App\Http\Controllers\Keamanan;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\SecurityReport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KeamananReportController extends Controller
{
    /**
     * Format report for security officer perspective.
     * Anonymous reports will have reporter details stripped.
     */
    protected function formatReport(SecurityReport $report, bool $withHistories = false): array
    {
        $isAnonymous = (bool) $report->is_anonymous;

        $data = [
            'id' => $report->id,
            'report_code' => $report->report_code,
            'is_anonymous' => $isAnonymous,
            'reporter_name' => $isAnonymous ? 'Anonim' : ($report->user?->name ?? 'Anonim'),
            'reporter_phone' => $isAnonymous ? null : $report->user?->phone_number,
            'reporter_email' => $isAnonymous ? null : $report->user?->email,
            'user' => $isAnonymous ? null : [
                'id' => $report->user_id,
                'name' => $report->user?->name,
                'phone_number' => $report->user?->phone_number,
            ],
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
            'forwarded_by_name' => $report->forwardedBy?->name,
            'inspection_note' => $report->inspection_note,
            'needs_action' => $report->needs_action,
            'action_note' => $report->action_note,
            'inspected_by' => $report->inspected_by,
            'inspected_by_name' => $report->inspectedBy?->name,
            'security_confirmed_at' => $report->security_confirmed_at?->toIso8601String(),
            'is_confirmed' => ! is_null($report->security_confirmed_at),
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
                    'actor_name' => $h->actor?->name,
                    'actor_role' => $h->actor_role,
                    'created_at' => $h->created_at?->toIso8601String(),
                ];
            });
        }

        return $data;
    }

    /**
     * GET /keamanan/data/reports
     */
    public function index(Request $request): JsonResponse
    {
        // Hanya laporan dengan status forwarded_to_security
        $query = SecurityReport::with(['user:id,name,email,phone_number', 'forwardedBy:id,name', 'inspectedBy:id,name'])
            ->withCount('flags')
            ->where('status', ReportStatus::FORWARDED_TO_SECURITY->value);

        // Filter konfirmasi (belum diperiksa / sudah dikonfirmasi)
        $filter = $request->query('filter') ?? $request->query('status');
        if ($filter === 'unconfirmed' || $request->query('confirmed') === '0' || $request->query('confirmed') === 'false') {
            $query->whereNull('security_confirmed_at');
        } elseif ($filter === 'confirmed' || $request->query('confirmed') === '1' || $request->query('confirmed') === 'true') {
            $query->whereNotNull('security_confirmed_at');
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
     * GET /keamanan/data/reports/{report}
     */
    public function show(SecurityReport $report): JsonResponse
    {
        if ($report->status !== ReportStatus::FORWARDED_TO_SECURITY->value) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Petugas keamanan hanya dapat melihat laporan yang diteruskan ke keamanan.',
            ], 403);
        }

        $report->load(['user:id,name,email,phone_number', 'forwardedBy:id,name', 'inspectedBy:id,name', 'histories.actor:id,name', 'flags']);

        return response()->json([
            'success' => true,
            'server_time' => now()->setTimezone('Asia/Jakarta')->toIso8601String(),
            'data' => $this->formatReport($report, true),
        ]);
    }

    /**
     * POST /keamanan/data/reports/{report}/confirm
     */
    public function confirm(Request $request, SecurityReport $report): JsonResponse
    {
        if ($report->status !== ReportStatus::FORWARDED_TO_SECURITY->value) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Status laporan tidak sesuai untuk pemeriksaan keamanan.',
            ], 403);
        }

        if (! is_null($report->security_confirmed_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan sudah dikonfirmasi sebelumnya oleh petugas keamanan.',
            ], 422);
        }

        $validated = $request->validate([
            'inspection_note' => 'required|string|max:1000',
            'needs_action' => 'required|boolean',
            'action_note' => 'required_if:needs_action,true,1|nullable|string|max:1000',
        ], [
            'inspection_note.required' => 'Catatan pemeriksaan wajib diisi.',
            'needs_action.required' => 'Pilihan perlu tindakan lapangan wajib ditentukan.',
            'action_note.required_if' => 'Catatan tindakan wajib diisi jika memerlukan tindakan lapangan.',
        ]);

        $report->update([
            'inspection_note' => $validated['inspection_note'],
            'needs_action' => (bool) $validated['needs_action'],
            'action_note' => $validated['action_note'] ?? null,
            'inspected_by' => $request->user()->id,
            'security_confirmed_at' => now(),
        ]);

        $officerName = $request->user()->name ?? 'Petugas Keamanan';
        $note = "Pemeriksaan selesai oleh {$officerName}: {$validated['inspection_note']}";
        if ($validated['needs_action']) {
            $note .= " | Tindakan: " . ($validated['action_note'] ?? '-');
        }

        $report->histories()->create([
            'status' => ReportStatus::FORWARDED_TO_SECURITY->value,
            'note' => $note,
            'actor_id' => $request->user()->id,
            'actor_role' => 'keamanan',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hasil pemeriksaan berhasil dikonfirmasi dan dikirim ke admin.',
            'data' => $this->formatReport($report->fresh(['user', 'forwardedBy', 'inspectedBy', 'histories.actor']), true),
        ]);
    }

    /**
     * GET /keamanan/data/reports/counts
     */
    public function counts(): JsonResponse
    {
        $serverTime = now()->setTimezone('Asia/Jakarta')->toIso8601String();

        $unconfirmed = SecurityReport::where('status', ReportStatus::FORWARDED_TO_SECURITY->value)
            ->whereNull('security_confirmed_at')
            ->count();

        $confirmed = SecurityReport::where('status', ReportStatus::FORWARDED_TO_SECURITY->value)
            ->whereNotNull('security_confirmed_at')
            ->count();

        return response()->json([
            'success' => true,
            'server_time' => $serverTime,
            'data' => [
                'unconfirmed' => $unconfirmed,
                'confirmed' => $confirmed,
                'total' => $unconfirmed + $confirmed,
                'server_time' => $serverTime,
            ],
        ]);
    }
}
