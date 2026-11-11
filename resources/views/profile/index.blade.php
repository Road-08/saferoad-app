
@extends('layouts.app')

@section('title', 'Profil Saya - SafeRoad')

@section('content')
<div class="profile-page">

    <header class="profile-header">
        <div>
            <p class="profile-greeting">Selamat datang di SafeRoad</p>
            <h1>Profil Saya</h1>
        </div>
        <div class="profile-avatar">K</div>
    </header>

    <section class="profile-card">
        <div class="profile-user">
            <div class="user-avatar">K</div>
            <div>
                <h2>Pengguna SafeRoad</h2>
                <p>Anggota SafeRoad</p>
            </div>
        </div>
        <div class="profile-divider"></div>
        <div class="profile-info">
            <span>Status akun</span>
            <span class="status-badge">Aktif</span>
        </div>
    </section>

    <section class="profile-section">
        <h2>Pengaturan Akun</h2>

        <a href="{{ route('profile.verifikasi') }}" class="profile-menu">
            <div class="menu-icon">♧</div>
            <div class="menu-text">
                <h3>Verifikasi Identitas</h3>
                <p>Lengkapi data identitas dan informasi gender</p>
            </div>
            <span class="menu-arrow">›</span>
        </a>

        <a href="#" class="profile-menu">
            <div class="menu-icon">⚙</div>
            <div class="menu-text">
                <h3>Pengaturan Privasi</h3>
                <p>Kelola informasi dan privasi akun</p>
            </div>
            <span class="menu-arrow">›</span>
        </a>

        <a href="#" class="profile-menu">
            <div class="menu-icon">ⓘ</div>
            <div class="menu-text">
                <h3>Tentang SafeRoad</h3>
                <p>Informasi mengenai aplikasi SafeRoad</p>
            </div>
            <span class="menu-arrow">›</span>
        </a>
    </section>

    <section class="verification-reminder">
        <div>
            <h3>Lengkapi identitasmu</h3>
            <p>Lakukan verifikasi agar pengalaman menggunakan SafeRoad lebih optimal.</p>
        </div>
        <a href="{{ route('profile.verifikasi') }}" class="reminder-button">
            Verifikasi Sekarang
        </a>
    </section>

</div>
@endsection
