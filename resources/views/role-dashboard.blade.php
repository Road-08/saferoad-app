@extends('auth.layout')

@section('title', $heading)

@section('form-content')
<div class="dashboard-card">
    <span class="mini-label">SAFEROAD ACCOUNT</span>

    <h2>{{ $heading }}</h2>

    <p>{{ $description }}</p>

    {{-- Menu Administrator --}}
    @if (auth()->user()->role === 'admin')
        <div class="dashboard-menu">
            <h3>Menu Administrator</h3>

            <p>
                Kelola verifikasi identitas pengguna yang mendaftar
                pada sistem SafeRoad.
            </p>

            <a
                class="primary"
                href="{{ route('admin.verifications.index') }}"
            >
                Verifikasi Identitas
            </a>
        </div>
    @endif

    {{-- Menu Petugas Keamanan --}}
    @if (auth()->user()->role === 'security')
        <div class="dashboard-menu">
            <h3>Menu Petugas Keamanan</h3>

            <p>
                Akses menu untuk memeriksa dan menangani laporan
                kejadian setelah fitur laporan diintegrasikan.
            </p>

            <button class="primary" type="button" disabled>
                Laporan Kejadian — Belum Terintegrasi
            </button>
        </div>
    @endif

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button class="primary" type="submit">
            Keluar dari SafeRoad
        </button>
    </form>
</div>
@endsection
