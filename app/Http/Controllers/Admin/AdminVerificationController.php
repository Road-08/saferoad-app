<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminVerificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status', 'pending');

        $users = User::where('identity_status', $status)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function approve(Request $request, User $user): JsonResponse
    {
        if ($user->identity_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan dengan status pending yang dapat disetujui.',
            ], 422);
        }

        $user->update([
            'identity_status' => 'verified',
            'identity_verified_at' => now(),
            'identity_rejection_note' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Identitas pengguna {$user->name} berhasil diverifikasi.",
            'data' => $user,
        ]);
    }

    public function reject(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ], [
            'reason.required' => 'Alasan penolakan verifikasi wajib diisi.',
        ]);

        $user->update([
            'identity_status' => 'rejected',
            'identity_verified_at' => null,
            'identity_rejection_note' => $validated['reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Verifikasi identitas pengguna {$user->name} ditolak.",
            'data' => $user,
        ]);
    }
}
