<?php

namespace App\Http\Controllers;

use App\Models\SecurityReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SecurityReportController extends Controller
{
    // Menampilkan riwayat semua laporan milik pengguna yang sedang login
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

    // Mengirim laporan keamanan baru
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
        ]);

        $report = DB::transaction(function () use ($validated, $request) {
            $report = SecurityReport::create([
                'report_code' => 'SR-' . now()->format('ymd') . '-' . Str::upper(Str::random(4)),
                'user_id' => $request->user()->getAuthIdentifier(),
                'category' => $validated['category'],
                'description' => $validated['description'],
                'address' => $validated['address'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => 'submitted',
            ]);

            // Menyimpan riwayat awal laporan
            $report->histories()->create([
                'status' => 'submitted',
                'note' => 'Laporan berhasil dikirim dan menunggu tinjauan admin.',
            ]);

            return $report;
        });

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim.',
                'data' => $report,
            ], 201);
        }

        return back()->with('success', 'Laporan berhasil dikirim.');
    }

    // Melihat laporan terakhir milik pengguna
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

    // Melihat detail laporan tertentu
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
