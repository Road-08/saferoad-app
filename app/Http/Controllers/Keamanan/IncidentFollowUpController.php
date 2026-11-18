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
     * Petugas keamanan melakukan tindak lanjut terhadap laporan (dinonaktifkan).
     */
    public function followUp(Request $request, SecurityReport $report): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Tindak lanjut langsung dinonaktifkan. Silakan gunakan konfirmasi pemeriksaan petugas pada alur validasi.',
        ], 403);
    }
}
