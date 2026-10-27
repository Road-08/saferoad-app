<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IdentityVerificationController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $users = User::query()
            ->where('role', 'user')
            ->where('verification_status', 'pending')
            ->orderBy('created_at')
            ->get();

        return view(
            'admin.verifications.index',
            compact('users')
        );
    }

    public function downloadKtp(User $user): StreamedResponse
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        abort_unless(
            $user->role === 'user' &&
            $user->verification_status === 'pending',
            404
        );

        abort_unless(
            $user->ktp_path &&
            Storage::disk('local')->exists($user->ktp_path),
            404
        );

        return Storage::disk('local')->download(
            $user->ktp_path,
            'dokumen-identitas-' . $user->id
        );
    }

    public function approve(
        Request $request,
        User $user
    ): RedirectResponse {
        abort_unless(auth()->user()?->role === 'admin', 403);

        abort_unless(
            $user->role === 'user' &&
            $user->verification_status === 'pending',
            404
        );

        $validated = $request->validate([
            'verified_gender' => [
                'required',
                'in:female,male',
            ],
        ], [
            'verified_gender.required' =>
                'Pilih jenis kelamin berdasarkan pemeriksaan KTP.',
            'verified_gender.in' =>
                'Pilihan jenis kelamin tidak valid.',
        ]);

        $this->deleteKtpOrFail($user);

        $user->forceFill([
            'verification_status' => 'approved',
            'verified_gender' => $validated['verified_gender'],
            'verified_at' => now(),
            'ktp_path' => null,
            'verification_note' => null,
        ])->save();

        return back()->with(
            'status',
            'Identitas ' . $user->name .
            ' berhasil diverifikasi. File KTP telah dihapus.'
        );
    }

    public function reject(
        Request $request,
        User $user
    ): RedirectResponse {
        abort_unless(auth()->user()?->role === 'admin', 403);

        abort_unless(
            $user->role === 'user' &&
            $user->verification_status === 'pending',
            404
        );

        $validated = $request->validate([
            'verification_note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'verification_note.max' =>
                'Catatan penolakan maksimal 1000 karakter.',
        ]);

        $this->deleteKtpOrFail($user);

        $user->forceFill([
            'verification_status' => 'rejected',
            'verified_gender' => null,
            'verified_at' => now(),
            'ktp_path' => null,
            'verification_note' =>
                $validated['verification_note'] ?? null,
        ])->save();

        return back()->with(
            'status',
            'Verifikasi identitas ' . $user->name .
            ' ditolak. File KTP telah dihapus.'
        );
    }

    private function deleteKtpOrFail(User $user): void
    {
        if (! $user->ktp_path) {
            return;
        }

        $path = $user->ktp_path;

        if (
            Storage::disk('local')->exists($path) &&
            ! Storage::disk('local')->delete($path)
        ) {
            abort(
                500,
                'File KTP tidak dapat dihapus. ' .
                'Keputusan verifikasi belum disimpan.'
            );
        }
    }
}
