<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NikService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isKeamanan()) {
                return redirect()->route('keamanan.dashboard');
            }
            if ($user->canAccessWomenZone()) {
                return redirect()->route('rute');
            }
            return redirect()->route('rute');
        }

        return view('auth.form', ['mode' => 'login']);
    }

    public function showRegister(): View
    {
        return view('auth.form', ['mode' => 'register']);
    }

    public function login(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = $this->loginThrottleKey($credentials['email'], $request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (! Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $request->boolean('remember'),
        )) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi tidak cocok.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        return $this->redirectAfterAuth($user, $request);
    }

    public function register(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);

        // Rate limit registration per IP (10 per hour)
        $throttleKey = 'register|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan pendaftaran. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nik' => ['required', 'digits:16'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus 16 digit angka.',
        ]);

        // Detect gender from NIK
        $nikResult = NikService::detect($validated['nik']);
        if (! $nikResult['valid']) {
            throw ValidationException::withMessages([
                'nik' => $nikResult['message'],
            ]);
        }

        // Generate nik_hash and ensure uniqueness
        $nikHash = hash_hmac('sha256', $validated['nik'], config('app.key'));
        if (User::where('nik_hash', $nikHash)->exists()) {
            throw ValidationException::withMessages([
                'nik' => 'NIK sudah terdaftar di akun lain.',
            ]);
        }

        RateLimiter::hit($throttleKey, 3600);

        // Create user with detected gender and verified identity
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'masyarakat',
            'gender' => $nikResult['gender'],
            'nik_hash' => $nikHash,
            'identity_status' => 'verified',
            'identity_verified_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->canAccessWomenZone()) {
            return redirect()->route('rute');
        }

        return redirect()->route('rute');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectAfterAuth(User $user, Request $request): RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->isKeamanan()) {
            return redirect()->intended(route('keamanan.dashboard'));
        }

        // Jangan izinkan intended URL ke womens-zone untuk non-perempuan terverifikasi
        $intended = $request->session()->get('url.intended');
        if ($intended && str_contains($intended, 'womens-zone') && ! $user->canAccessWomenZone()) {
            $request->session()->forget('url.intended');
        }

        if ($user->canAccessWomenZone()) {
            return redirect()->intended(route('rute'));
        }

        return redirect()->intended(route('rute'));
    }

    private function normalizeEmail(Request $request): void
    {
        $email = $request->input('email');

        if (is_string($email)) {
            $request->merge(['email' => Str::lower(trim($email))]);
        }
    }

    private function loginThrottleKey(string $email, Request $request): string
    {
        return Str::transliterate(Str::lower($email).'|'.$request->ip());
    }
}
