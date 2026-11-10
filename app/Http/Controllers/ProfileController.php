<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil pengguna yang sedang login.
     */
    public function show(): View
    {
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    /**
     * Memperbarui informasi profil pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => [
                'nullable',
                'string',
                Rule::in(['male', 'female', 'other']),
            ],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'gender.in' => 'Pilihan gender tidak valid.',
        ]);

        // Hanya memperbarui atribut profil yang diizinkan.
        $user->name = $validated['name'];
        $user->gender = $validated['gender'] ?? null;
        $user->save();

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Memperbarui persetujuan penggunaan data gender.
     */
    public function updateConsent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gender_statistics_consent' => [
                'required',
                'boolean',
            ],
        ], [
            'gender_statistics_consent.required' =>
                'Status persetujuan wajib dikirim.',
            'gender_statistics_consent.boolean' =>
                'Status persetujuan tidak valid.',
        ]);

        $user = Auth::user();

        $user->gender_statistics_consent =
            (bool) $validated['gender_statistics_consent'];

        $user->save();

        return redirect()
            ->route('profile.show')
            ->with(
                'success',
                $user->gender_statistics_consent
                    ? 'Persetujuan penggunaan data gender berhasil diberikan.'
                    : 'Persetujuan penggunaan data gender berhasil ditarik.'
            );
    }
}
