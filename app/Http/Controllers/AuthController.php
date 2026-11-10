<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
            'gender' => [
                'nullable',
                'in:female,male,prefer_not_to_say',
            ],
            'gender_statistics_consent' => [
                'nullable',
                'accepted',
            ],
            'ktp' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'gender.in' => 'Pilihan jenis kelamin tidak valid.',
            'gender_statistics_consent.accepted' =>
                'Persetujuan statistik tidak valid.',
            'ktp.required' => 'Dokumen KTP wajib diunggah.',
            'ktp.mimes' => 'Format KTP harus JPG, PNG, atau PDF.',
            'ktp.max' => 'Ukuran file KTP maksimal 5 MB.',
        ]);

        // Simpan KTP pada penyimpanan privat.
        $ktpPath = $request->file('ktp')->store('ktp', 'local');

        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'gender' => $validated['gender'] ?? null,
                'gender_statistics_consent' =>
                    $request->boolean('gender_statistics_consent'),

                // Role masyarakat ditetapkan oleh server.
                'role' => 'user',

                'verification_status' => 'pending',
                'ktp_path' => $ktpPath,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($ktpPath);

            throw $exception;
        }

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Pendaftaran berhasil. Silakan masuk untuk melanjutkan. ' .
                'Identitas Anda sedang menunggu verifikasi administrator.'
            );
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
            ],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (! Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            return back()
                ->withErrors([
                    'email' => 'Email atau kata sandi yang Anda masukkan salah.',
                ])
                ->onlyInput('email');
        }

        // Mencegah session fixation setelah login.
        $request->session()->regenerate();

        $user = $request->user();

        // Pengalihan berdasarkan role pengguna.
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'security' => redirect()->route('security.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'Anda telah keluar dari SafeRoad.');
    }
}
