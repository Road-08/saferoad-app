@extends('layouts.app')

@section('title', 'Profil Saya · SafeRoad')

@section('content')
<div class="profile-wrapper">
    <!-- Header Profil -->
    <header class="profile-header-card">
        <div class="user-main-info">
            <div class="profile-avatar-big">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <span class="user-role-badge">
                    {{ ucfirst(auth()->user()->role ?? 'Masyarakat') }}
                </span>
                <h1 class="user-fullname">{{ auth()->user()->name ?? 'Pengguna SafeRoad' }}</h1>
                <p class="user-email-text">{{ auth()->user()->email ?? 'Akun Terdaftar' }}</p>
            </div>
        </div>

        <div class="profile-status-box">
            <div class="status-indicator">
                <span class="dot-active"></span>
                <span>Status Akun: <strong>Aktif</strong></span>
            </div>
            <span class="verified-text">Terotentikasi di Sistem SafeRoad</span>
        </div>
    </header>

    <!-- Kartu Pengingat Verifikasi Identitas -->
    <section class="reminder-card">
        <div class="reminder-content">
            <div class="reminder-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
            </div>
            <div>
                <h3>Lengkapi Verifikasi Identitas Anda</h3>
                <p>Verifikasi KTP dan persetujuan gender membantu komunitas menciptakan lingkungan perjalanan yang lebih aman bagi seluruh pengguna.</p>
            </div>
        </div>
        <a href="{{ route('profile.verifikasi') }}" class="btn-verify-now">
            Verifikasi Sekarang
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>
    </section>

    <!-- Menu Pengaturan & Navigasi -->
    <h2 class="section-title">Pengaturan Akun & Privasi</h2>
    <div class="menu-list-grid">
        <a href="{{ route('profile.verifikasi') }}" class="menu-card-item">
            <div class="menu-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="16" rx="3"></rect>
                    <circle cx="9" cy="10" r="2"></circle>
                    <line x1="15" y1="8" x2="17" y2="8"></line>
                    <line x1="15" y1="12" x2="17" y2="12"></line>
                    <line x1="7" y1="16" x2="17" y2="16"></line>
                </svg>
            </div>
            <div class="menu-text-wrap">
                <h3>Verifikasi Identitas & Dokumen</h3>
                <p>Kelola unggahan berkas KTP dan informasi preferensi gender</p>
            </div>
            <span class="menu-arrow-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </span>
        </a>

        <a href="javascript:void(0)" onclick="alert('Pengaturan privasi: Data perjalanan Anda tidak dibagikan secara publik tanpa izin. Mode anonim tersedia saat pelaporan kejadian.')" class="menu-card-item">
            <div class="menu-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div class="menu-text-wrap">
                <h3>Pengaturan Privasi & Keamanan Data</h3>
                <p>Kendali penuh terhadap riwayat rute dan visibilitas identitas akun</p>
            </div>
            <span class="menu-arrow-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </span>
        </a>

        <a href="javascript:void(0)" onclick="alert('Layanan Darurat SafeRoad:\n• Hotline Darurat: 112\n• Polisi: 110\n• Ambulans: 118 / 119')" class="menu-card-item">
            <div class="menu-icon-box emergency">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
            </div>
            <div class="menu-text-wrap">
                <h3>Informasi Kontak Darurat Cepat</h3>
                <p>Akses nomor penting dan pertolongan pertama selama di perjalanan</p>
            </div>
            <span class="menu-arrow-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </span>
        </a>

        <a href="javascript:void(0)" onclick="alert('SafeRoad adalah platform pemetaan keamanan rute perjalanan yang mengintegrasikan data CCTV, penerangan, dan laporan warga terverifikasi.')" class="menu-card-item">
            <div class="menu-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            </div>
            <div class="menu-text-wrap">
                <h3>Tentang SafeRoad Indonesia</h3>
                <p>Pelajari panduan keamanan, ketentuan layanan, dan versi platform</p>
            </div>
            <span class="menu-arrow-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </span>
        </a>
    </div>
</div>

<style>
    .profile-wrapper {
        width: min(100% - 32px, 860px);
        margin: 36px auto;
        padding-bottom: 40px;
    }

    /* Header Profil */
    .profile-header-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 20px;
        padding: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: var(--shadow-card);
        margin-bottom: 24px;
    }

    .user-main-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .profile-avatar-big {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--primary-600), var(--primary-800));
        color: #ffffff;
        font-size: 28px;
        font-weight: 800;
        display: grid;
        place-items: center;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
    }

    .user-role-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--primary-600);
        background-color: var(--primary-50);
        padding: 3px 10px;
        border-radius: 6px;
        margin-bottom: 6px;
    }

    .user-fullname {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-heading);
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .user-email-text {
        font-size: 14px;
        color: var(--text-muted);
    }

    .profile-status-box {
        text-align: right;
    }

    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: var(--success-50);
        border: 1px solid var(--success-100);
        padding: 5px 12px;
        border-radius: 9999px;
        font-size: 12.5px;
        color: var(--success-600);
        margin-bottom: 6px;
    }

    .dot-active {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: var(--success-600);
        box-shadow: 0 0 6px rgba(22, 163, 74, 0.6);
    }

    .verified-text {
        display: block;
        font-size: 11.5px;
        color: var(--text-muted);
    }

    /* Kartu Pengingat */
    .reminder-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1.5px solid var(--primary-100);
        border-radius: 18px;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 32px;
    }

    .reminder-content {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .reminder-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background-color: var(--primary-600);
        color: #ffffff;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .reminder-icon-box svg {
        width: 22px;
        height: 22px;
    }

    .reminder-card h3 {
        font-size: 15px;
        font-weight: 800;
        color: var(--primary-900);
        margin-bottom: 4px;
    }

    .reminder-card p {
        font-size: 13px;
        color: #334155;
        line-height: 1.5;
        max-width: 520px;
    }

    .btn-verify-now {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 20px;
        background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        border-radius: 10px;
        white-space: nowrap;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        transition: transform 0.15s ease;
    }

    .btn-verify-now:hover {
        transform: translateY(-2px);
    }

    .btn-verify-now svg {
        width: 15px;
        height: 15px;
    }

    /* Grid Menu Pengaturan */
    .section-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--text-heading);
        margin-bottom: 16px;
    }

    .menu-list-grid {
        display: grid;
        gap: 12px;
    }

    .menu-card-item {
        background-color: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--shadow-sm);
        transition: all 0.15s ease;
    }

    .menu-card-item:hover {
        border-color: var(--primary-500);
        box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.12);
        transform: translateY(-1px);
    }

    .menu-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background-color: var(--primary-50);
        color: var(--primary-600);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .menu-icon-box.emergency {
        background-color: var(--danger-50);
        color: var(--danger-600);
    }

    .menu-icon-box svg {
        width: 20px;
        height: 20px;
    }

    .menu-text-wrap {
        flex: 1;
    }

    .menu-text-wrap h3 {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--text-heading);
        margin-bottom: 2px;
    }

    .menu-text-wrap p {
        font-size: 12.5px;
        color: var(--text-muted);
    }

    .menu-arrow-icon {
        width: 18px;
        height: 18px;
        color: #94a3b8;
        display: grid;
        place-items: center;
        transition: transform 0.15s ease, color 0.15s ease;
    }

    .menu-card-item:hover .menu-arrow-icon {
        color: var(--primary-600);
        transform: translateX(3px);
    }

    @media (max-width: 768px) {
        .profile-header-card {
            flex-direction: column;
            align-items: flex-start;
            padding: 22px;
        }

        .profile-status-box {
            text-align: left;
            width: 100%;
            border-top: 1px solid var(--border-soft);
            padding-top: 14px;
        }

        .reminder-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-verify-now {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection
