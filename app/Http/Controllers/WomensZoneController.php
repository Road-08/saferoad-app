<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class WomensZoneController extends Controller
{
    /**
     * Menampilkan halaman Women Zone berdasarkan status verifikasi pengguna dari database.
     */
    public function index(Request $request): View|Response
    {
        $user = $request->user();

        // 1. Pengguna wajib login
        if (! $user) {
            return redirect()->route('login');
        }

        // 2. Petugas keamanan dan admin bukan pengguna Women Zone
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isKeamanan()) {
            return redirect()->route('keamanan.dashboard');
        }

        // 3. Pengguna laki-laki ditolak secara ketat
        if ($user->isMale()) {
            abort(403, 'Akses ditolak. Fitur Women Zone khusus diperuntukkan bagi pengguna perempuan.');
        }

        // 4. Evaluasi status verifikasi gender dari database
        $gender = $user->gender;
        $identityStatus = $user->identity_status ?? 'unverified';

        if ($gender === 'perempuan' && $identityStatus === 'verified') {
            $gateState = 'verified';
        } elseif ($identityStatus === 'pending') {
            $gateState = 'pending';
        } elseif ($identityStatus === 'rejected') {
            $gateState = 'rejected';
        } else {
            $gateState = 'not_submitted';
        }

        return view('womens-zone', [
            'user' => $user,
            'gateState' => $gateState,
            'identityStatus' => $identityStatus,
            'isVerifiedFemale' => $gateState === 'verified',
            'rejectionNote' => $user->identity_rejection_note,
        ]);
    }
}
