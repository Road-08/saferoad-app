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

        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi tidak valid atau belum masuk.',
                ], 401);
            }

            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Fitur Women Zone hanya diperuntukkan bagi masyarakat.',
                ], 403);
            }
            return redirect()->route('admin.dashboard');
        }

        if ($user->isKeamanan()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Fitur Women Zone hanya diperuntukkan bagi masyarakat.',
                ], 403);
            }
            return redirect()->route('keamanan.dashboard');
        }

        if (! $user->canAccessWomenZone()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zona Perempuan khusus pengguna perempuan terverifikasi.',
                    'identity_status' => $user->identity_status ?? 'unverified',
                    'gender' => $user->gender,
                ], 403);
            }

            return redirect()->route('rute')->with('error', 'Zona Perempuan khusus pengguna perempuan terverifikasi.');
        }

        return $next($request);
    }
}
