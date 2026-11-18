<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Models\SecurityReport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SecurityReportController extends Controller
{
    /**
     * Menampilkan riwayat semua laporan milik pengguna yang sedang login (JSON paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $reports = SecurityReport::with('histories')
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    /**
     * Endpoint untuk riwayat laporan masyarakat dengan filter realtime ?since=.
     * GET /laporan/data
     */
    public function userReports(Request $request): JsonResponse
    {
        $query = SecurityReport::with(['histories' => function ($q) {
            $q->latest();
        }])
            ->where('user_id', $request->user()->getAuthIdentifier());

        if ($since = $request->query('since')) {
            try {
                $sinceTime = Carbon::parse($since);
                $query->where('updated_at', '>=', $sinceTime);
            } catch (\Exception $e) {
                // Ignore parse errors
            }
        }

        $reports = $query->latest('updated_at')->get()->map(function (SecurityReport $report) {
            return [
                'id' => $report->id,
                'report_code' => $report->report_code,
                'category' => $report->category,
                'description' => $report->description,
                'severity' => $report->severity,
                'address' => $report->address,
                'latitude' => $report->latitude,
                'longitude' => $report->longitude,
                'status' => $report->status,
                'status_label' => $report->statusLabel(),
                'admin_note' => $report->admin_note,
                'is_anonymous' => (bool) $report->is_anonymous,
                'incident_at' => $report->incident_at?->toIso8601String(),
                'reported_at' => $report->created_at?->toIso8601String(),
                'created_at' => $report->created_at?->toIso8601String(),
                'updated_at' => $report->updated_at?->toIso8601String(),
                'histories' => $report->histories->map(function ($h) {
                    return [
                        'id' => $h->id,
                        'status' => $h->status,
                        'status_label' => ReportStatus::tryFrom($h->status)?->label() ?? $h->status,
                        'note' => $h->note,
                        'actor_role' => $h->actor_role,
                        'created_at' => $h->created_at?->toIso8601String(),
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'server_time' => now()->setTimezone('Asia/Jakarta')->toIso8601String(),
            'data' => $reports,
        ]);
    }

    /**
     * Mengirim laporan keamanan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => [
                'required',
                'in:Pelecehan / Gangguan Keamanan,Pencurian,Kondisi Jalan,Fasilitas Keamanan,Lainnya',
            ],
            'description' => 'required|string|max:500',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'incident_time' => 'nullable|date|before_or_equal:now|after:-7 days',
            'incident_at' => 'nullable|date|before_or_equal:now|after:-7 days',
            'is_anonymous' => 'nullable|boolean',
        ], [
            'incident_time.before_or_equal' => 'Waktu kejadian tidak boleh di masa depan.',
            'incident_time.after' => 'Waktu kejadian maksimal 7 hari yang lalu.',
            'incident_at.before_or_equal' => 'Waktu kejadian tidak boleh di masa depan.',
            'incident_at.after' => 'Waktu kejadian maksimal 7 hari yang lalu.',
        ]);

        $rawIncidentTime = $validated['incident_time'] ?? $validated['incident_at'] ?? null;
        $incidentAt = $rawIncidentTime ? Carbon::parse($rawIncidentTime) : now();

        $report = DB::transaction(function () use ($validated, $request, $incidentAt) {
            $report = SecurityReport::create([
                'report_code' => 'SR-' . now()->format('ymd') . '-' . Str::upper(Str::random(4)),
                'user_id' => $request->user()->getAuthIdentifier(),
                'category' => $validated['category'],
                'description' => $validated['description'],
                'address' => $validated['address'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'incident_at' => $incidentAt,
                'is_anonymous' => $request->boolean('is_anonymous'),
                'status' => ReportStatus::PENDING_VALIDATION->value,
            ]);

            // Menyimpan riwayat awal laporan
            $report->histories()->create([
                'status' => ReportStatus::PENDING_VALIDATION->value,
                'note' => 'Laporan berhasil dikirim dan menunggu validasi admin.',
                'actor_id' => $request->user()->getAuthIdentifier(),
                'actor_role' => 'masyarakat',
            ]);

            return $report;
        });

        $timeWib = now()->setTimezone('Asia/Jakarta')->format('H:i');
        $message = "Laporan {$report->report_code} terkirim pukul {$timeWib} WIB";

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $report->id,
                    'report_code' => $report->report_code,
                    'category' => $report->category,
                    'description' => $report->description,
                    'status' => $report->status,
                    'status_label' => $report->statusLabel(),
                    'incident_at' => $report->incident_at?->toIso8601String(),
                    'reported_at' => $report->created_at?->toIso8601String(),
                    'is_anonymous' => (bool) $report->is_anonymous,
                ],
            ], 201);
        }

        return back()->with('success', $message);
    }

    /**
     * Melihat laporan terakhir milik pengguna
     */
    public function latest(Request $request)
    {
        $report = SecurityReport::with('histories')
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->latest()
            ->first();

        if (! $report) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada laporan keamanan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Melihat detail laporan tertentu
     */
    public function show(Request $request, SecurityReport $report): JsonResponse
    {
        $user = $request->user();

        // Hanya pemilik laporan, petugas keamanan, atau admin yang dapat melihat
        if ($report->user_id !== $user->getAuthIdentifier() && ! $user->hasRole(['admin', 'keamanan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak untuk melihat laporan ini.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $report->load(['histories', 'user:id,name,email']),
        ]);
    }
}
