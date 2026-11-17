<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWomenZoneAccess
{
    /**
     * Memastikan akses Women Zone hanya untuk pengguna perempuan terverifikasi.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Wajib autentikasi
        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi tidak valid atau belum masuk.',
                ], 401);
            }

            return redirect()->route('login');
        }

        // 2. Petugas keamanan dan admin bukan pengguna Women Zone
        if (! $user->isMasyarakat()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Fitur Women Zone hanya diperuntukkan bagi masyarakat.',
                ], 403);
            }

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('keamanan.dashboard');
        }

        // 3. Pengguna laki-laki ditolak secara ketat
        if ($user->isMale()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Fitur Women Zone khusus diperuntukkan bagi pengguna perempuan.',
                ], 403);
            }

            abort(403, 'Akses ditolak. Fitur Women Zone khusus diperuntukkan bagi pengguna perempuan.');
        }

        // 4. Pengguna perempuan harus berstatus 'verified' di database
        if (! $user->isFemale() || ! $user->isIdentityVerified()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Fitur Women Zone memerlukan akun perempuan dengan status verifikasi aktif (verified).',
                    'identity_status' => $user->identity_status ?? 'unverified',
                    'gender' => $user->gender,
                ], 403);
            }

            // Untuk web request ke fitur penuh Women Zone, arahkan ke halaman info verifikasi
            if ($request->routeIs('womens-zone.features*')) {
                return redirect()->route('womens-zone');
            }
        }

        return $next($request);
    }
}
