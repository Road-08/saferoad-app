<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminVerificationController extends Controller
{
    /**
     * Show the admin verification page.
     */
    public function index()
    {
        // Load view that contains a DataTable of users with pending verification
        return view('admin.verifikasi');
    }

    /**
     * Return JSON data for pending users (used by DataTables).
     */
    public function data(): JsonResponse
    {
        $users = User::where('identity_status', 'pending')
            ->select('id', 'name', 'email', 'nik', 'identity_status')
            ->get();

        // Attach action buttons (Approve / Reject) for each user
        $users->transform(function ($user) {
            $approveUrl = route('admin.verifikasi.approve', $user);
            $rejectUrl  = route('admin.verifikasi.reject', $user);
            $buttons = "<form method='POST' action='$approveUrl' style='display:inline;'>" .
                csrf_field() .
                "<button type='submit' class='btn btn-success btn-sm'>Approve</button></form>" .
                " <form method='POST' action='$rejectUrl' style='display:inline;'>" .
                csrf_field() .
                "<button type='submit' class='btn btn-danger btn-sm'>Reject</button></form>";
            $user->action = $buttons;
            return $user;
        });

        return response()->json(['data' => $users]);
    }

    /**
     * Approve a user's KTP verification.
     */
    public function approve(User $user): JsonResponse
    {
        $user->update([
            'identity_status'      => 'verified',
            'identity_verified_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'User verified.']);
    }

    /**
     * Reject a user's KTP verification.
     */
    public function reject(User $user): JsonResponse
    {
        $user->update([
            'identity_status' => 'rejected',
        ]);
        return response()->json(['success' => true, 'message' => 'User rejected.']);
    }
}
