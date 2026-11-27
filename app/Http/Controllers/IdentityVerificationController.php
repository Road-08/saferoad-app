<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IdentityVerificationController extends Controller
{
    public function show()
    {
        return view('profile.verifikasi-identitas');
    }

    public function submit(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'ktp' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // maks 5MB
            'gender' => ['required', Rule::in(['perempuan', 'laki-laki', 'lainnya', 'tidak_menyebutkan'])],
            'gender_consent' => ['required', 'accepted'],
        ], [
            'ktp.required' => 'Dokumen KTP wajib diunggah.',
            'ktp.mimes' => 'Format berkas KTP harus berupa JPG, PNG, atau PDF.',
            'ktp.max' => 'Ukuran berkas KTP tidak boleh melebihi 5 MB.',
            'gender.required' => 'Pilihan preferensi gender wajib dipilih.',
            'gender_consent.accepted' => 'Anda harus menyetujui pemrosesan data identitas untuk verifikasi.',
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Hapus berkas KTP lama jika ada (cek disk local dan public)
        if ($user->ktp_path) {
            if (Storage::disk('local')->exists($user->ktp_path)) {
                Storage::disk('local')->delete($user->ktp_path);
            }
            if (Storage::disk('public')->exists($user->ktp_path)) {
                Storage::disk('public')->delete($user->ktp_path);
            }
        }

        // Simpan dokumen KTP di storage privat (tidak terekspos langsung ke web public)
        $path = $request->file('ktp')->store('identitas_ktp', 'local');

        $user->update([
            'ktp_path' => $path,
            'gender' => $validated['gender'],
            'gender_consent' => true,
            'identity_status' => 'pending',
            'identity_rejection_note' => null,
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen verifikasi identitas berhasil dikirim dan sedang dalam proses peninjauan sistem.',
                'data' => [
                    'identity_status' => $user->identity_status,
                    'gender' => $user->gender,
                    'gender_consent' => $user->gender_consent,
                ],
            ]);
        }

        return redirect()->route('profile.index')
            ->with('success', 'Dokumen verifikasi identitas Anda telah berhasil dikirim untuk proses peninjauan.');
    }
}
