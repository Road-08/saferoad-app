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
            'status' => ['required', Rule::in(['officer_check', 'map_updated', 'rejected'])],
            'note' => ['required', 'string', 'max:500'],
        ], [
            'status.required' => 'Status tindak lanjut wajib dipilih.',
            'note.required' => 'Catatan investigasi atau tindak lanjut petugas wajib diisi.',
        ]);

        $officer = $request->user();

        DB::transaction(function () use ($report, $validated, $officer) {
            $report->update([
                'status' => $validated['status'],
            ]);

            $report->histories()->create([
                'status' => $validated['status'],
                'note' => "[Petugas: {$officer->name}] {$validated['note']}",
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Status laporan berhasil diperbarui dan catatan tindak lanjut tersimpan.',
            'data' => $report->fresh(['histories', 'user:id,name,email']),
        ]);
    }
}
