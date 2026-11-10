@extends('auth.layout')

@section('title', 'Dashboard')

@section('form-content')
<div class="dashboard-card">
    <span class="mini-label">SAFEROAD ACCOUNT</span>

    <h2>Halo, {{ $user->name }}.</h2>

    <p>
        Selamat datang di SafeRoad. Akun Anda berhasil masuk.
    </p>

    @if ($user->verification_status !== 'approved')
        <div class="notice notice-warning">
            <strong>
                Verifikasi identitas:
                {{ $user->verification_status === 'rejected'
                    ? 'Ditolak'
                    : 'Menunggu' }}.
            </strong>

            <br>

            Fitur yang memerlukan identitas terverifikasi
            belum dapat digunakan. Status verifikasi Anda
            belum disetujui administrator.
        </div>
    @endif

    <div class="dashboard-actions">
        <span class="status-chip">
            {{ $user->verification_status === 'approved'
                ? 'Identitas terverifikasi'
                : 'Menunggu verifikasi' }}
        </span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button class="primary" type="submit">
                Keluar dari SafeRoad
            </button>
        </form>
    </div>
</div>
@endsection
