<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class WomensZoneController extends Controller
{
    /**
     * Menampilkan halaman Women Zone hanya untuk pengguna yang memiliki akses Women Zone.
     */
    public function index(Request $request): View|Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isKeamanan()) {
            return redirect()->route('keamanan.dashboard');
        }

        if (! $user->canAccessWomenZone()) {
            return redirect()->route('rute')->with('error', 'Zona Perempuan khusus pengguna perempuan terverifikasi.');
        }

        return view('womens-zone', [
            'user' => $user,
            'canAccessWomenZone' => true,
            'isVerifiedFemale' => true,
        ]);
    }
}
