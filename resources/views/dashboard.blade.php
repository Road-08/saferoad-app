@extends('layouts.app')

@section('title', 'Dashboard Akun · SafeRoad')

@section('content')
<div class="dashboard-page-container">

    {{-- ===== Banner Mode Perempuan (muncul setelah verifikasi) ===== --}}
    <div id="femaleModeBar" class="female-mode-bar" style="display:none;">
        <div class="female-mode-inner">
            <span class="female-mode-icon">♀</span>
            <div>
                <strong>Mode Perempuan Terverifikasi Aktif ✨</strong>
                <span>Akses Women's Safe Zone dan fitur monitoring driver telah terbuka penuh untuk akun Anda.</span>
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ url('/womens-zone') }}" class="female-mode-cta" style="background:#be185d;">🌸 Women's Zone →</a>
            <a href="{{ url('/rute') }}" class="female-mode-cta">Buka Rute →</a>
        </div>
    </div>

    {{-- ===== Banner Selamat Datang ===== --}}
    <section class="welcome-banner">
        <span class="eyebrow-tag">PORTAL MASYARAKAT SAFEROAD</span>
        <h1>Selamat Datang, {{ auth()->user()->name }} 👋</h1>
        <p>
            Sesi akun SafeRoad Anda aktif. Jelajahi peta keamanan perjalanan,
            cari alternatif rute terlindungi, dan pantau titik fasilitas di sekitar wilayah Anda.
        </p>

        <div class="session-status-strip">
            <span class="status-pill-live">
                <span class="live-dot"></span>
                Sesi Akun Aktif
            </span>
            <span>Terhubung sebagai <strong>{{ auth()->user()->email }}</strong></span>
        </div>
    </section>

    {{-- ===== Kartu Navigasi Fitur Cepat ===== --}}
    <h2 class="section-heading">Layanan &amp; Fitur Keamanan Rute</h2>
    <div class="feature-grid">

        {{-- Card 1: Cari Rute Aman --}}
        <div class="action-card">
            <div class="action-card-body">
                <div class="action-card-header">
                    <div class="card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div>
                        <h3>Cari Rute Aman</h3>
                        <p>Eksplorasi jalur perjalanan dengan visualisasi peta interaktif, indikator keamanan, dan perbandingan rute langsung.</p>
                    </div>
                </div>
            </div>
            <a href="{{ url('/rute') }}" class="card-footer-link">
                Buka Navigasi Rute
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>

        {{-- Card 2: Verifikasi Identitas --}}
        <div class="action-card">
            <div class="action-card-body">
                <div class="action-card-header">
                    <div class="card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10"/>
                        </svg>
                    </div>
                    <div>
                        <h3>Verifikasi Identitas</h3>
                        <p>Lengkapi dokumen identitas dan data gender untuk pengalaman perjalanan yang optimal.</p>
                    </div>
                </div>
            </div>
            <a href="{{ url('/verifikasi-identitas') }}" class="card-footer-link">
                Lengkapi Verifikasi
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>

        {{-- Card 3: Laporan Insiden --}}
        <div class="action-card">
            <div class="action-card-body">
                <div class="action-card-header">
                    <div class="card-icon card-icon--danger" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </div>
                    <div>
                        <h3>Laporan Insiden</h3>
                        <p>Laporkan kejadian jalan secara cepat dengan posisi GPS Anda langsung dari lokasi kejadian.</p>
                    </div>
                </div>
            </div>
            <a href="{{ url('/laporkan') }}" class="card-footer-link card-footer-link--danger">
                Buat Laporan
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>

    </div>{{-- /feature-grid --}}

    {{-- ===== Detail Informasi Akun ===== --}}
    <section class="account-details-card" aria-label="Informasi Akun Anda">
        <h2 class="account-details-title">Informasi Kredensial Akun</h2>
        <div class="account-details-grid">
            <div class="detail-item">
                <div class="detail-label">Nama Pengguna</div>
                <div class="detail-value">{{ auth()->user()->name }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Alamat Email</div>
                <div class="detail-value">{{ auth()->user()->email }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Peran Akun</div>
                <div class="detail-value detail-value--primary">
                    {{ ucfirst(auth()->user()->role ?? 'Masyarakat') }}
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status Proteksi</div>
                <div class="detail-value detail-value--success">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Terotentikasi Aman
                </div>
            </div>
        </div>
    </section>

</div>{{-- /dashboard-page-container --}}

<style>
/* ===== Female Mode Bar ===== */
.female-mode-bar {
    background: linear-gradient(135deg, #be185d, #db2777);
    border-radius: 16px;
    padding: 16px 22px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    color: #fff;
    box-shadow: 0 8px 24px -6px rgba(190, 24, 93, 0.35);
    animation: slideDown .4s ease;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.female-mode-inner {
    display: flex;
    align-items: center;
    gap: 14px;
}
.female-mode-icon {
    font-size: 28px;
    line-height: 1;
    background: rgba(255,255,255,.2);
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.female-mode-inner strong {
    display: block;
    font-size: 14px;
    font-weight: 800;
    margin-bottom: 2px;
}
.female-mode-inner span {
    font-size: 12.5px;
    color: rgba(255,255,255,.85);
    line-height: 1.4;
}
.female-mode-cta {
    padding: 9px 18px;
    border-radius: 10px;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.35);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: background .2s;
    flex-shrink: 0;
}
.female-mode-cta:hover { background: rgba(255,255,255,.32); }

/* ===== Layout ===== */
.dashboard-page-container {
    width: min(100% - 40px, 1140px);
    margin: 40px auto 60px;
}

/* ===== Welcome Banner ===== */
.welcome-banner {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    border-radius: 20px;
    padding: 36px clamp(24px, 4vw, 44px);
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px -10px rgba(37, 99, 235, 0.3);
    margin-bottom: 32px;
}

.welcome-banner::after {
    content: "";
    position: absolute;
    right: -20px;
    bottom: -30px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
    pointer-events: none;
}

.eyebrow-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 12px;
    border-radius: 9999px;
    background: rgba(255,255,255,0.15);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #dbeafe;
    margin-bottom: 14px;
}

.welcome-banner h1 {
    font-size: clamp(26px, 3.2vw, 36px);
    font-weight: 800;
    letter-spacing: -0.03em;
    margin-bottom: 8px;
}

.welcome-banner > p {
    font-size: 15px;
    color: #dbeafe;
    max-width: 600px;
    line-height: 1.6;
}

.session-status-strip {
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 13px;
    flex-wrap: wrap;
}

.status-pill-live {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 12px;
    background: rgba(22, 163, 74, 0.25);
    border: 1px solid rgba(74, 222, 128, 0.4);
    border-radius: 9999px;
    color: #bbf7d0;
    font-weight: 700;
    font-size: 12px;
}

.live-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: #4ade80;
    box-shadow: 0 0 6px #4ade80;
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

/* ===== Feature Grid ===== */
.section-heading {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-heading, #1e293b);
    margin-bottom: 16px;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

/* ===== Action Card ===== */
.action-card {
    background-color: var(--bg-card, #ffffff);
    border: 1px solid var(--border-soft, #e2e8f0);
    border-radius: 16px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.action-card:hover {
    transform: translateY(-3px);
    border-color: var(--primary-500, #3b82f6);
    box-shadow: 0 16px 30px -10px rgba(37, 99, 235, 0.15);
}

.action-card-body {
    flex: 1;
}

.action-card-header {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.card-icon {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-icon svg {
    width: 22px;
    height: 22px;
}

.card-icon--danger {
    background: rgba(220, 38, 38, 0.1);
    color: #dc2626;
}

.action-card-header h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-heading, #1e293b);
    margin-bottom: 6px;
}

.action-card-header p {
    font-size: 13.5px;
    color: var(--text-muted, #64748b);
    line-height: 1.55;
    margin: 0;
}

.card-footer-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #2563eb;
    text-decoration: none;
    padding: 8px 0;
    border-top: 1px solid var(--border-soft, #e2e8f0);
    transition: gap 0.2s ease, color 0.2s ease;
}

.card-footer-link:hover {
    gap: 12px;
    color: #1d4ed8;
}

.card-footer-link svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.card-footer-link--danger {
    color: #dc2626;
}

.card-footer-link--danger:hover {
    color: #b91c1c;
}

/* ===== Account Details Card ===== */
.account-details-card {
    background-color: var(--bg-card, #ffffff);
    border: 1px solid var(--border-soft, #e2e8f0);
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.account-details-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--text-heading, #1e293b);
    margin-bottom: 20px;
}

.account-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted, #94a3b8);
}

.detail-value {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-heading, #1e293b);
}

.detail-value--primary {
    color: var(--primary-600, #2563eb);
}

.detail-value--success {
    color: var(--success-600, #16a34a);
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ===== Responsive ===== */
@media (max-width: 600px) {
    .dashboard-page-container {
        margin-top: 24px;
    }

    .feature-grid {
        grid-template-columns: 1fr;
    }

    .account-details-grid {
        grid-template-columns: 1fr 1fr;
    }

    .session-status-strip {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
}
</style>

@push('scripts')
<script>
    (function () {
        // Redirection role jika user login melalui portal Keamanan atau Admin
        const role = localStorage.getItem('saferoad_active_role');
        const urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has('mode_warga') && !urlParams.has('stay')) {
            if (role === 'keamanan') {
                window.location.replace("{{ route('keamanan.dashboard') }}");
                return;
            } else if (role === 'admin') {
                window.location.replace("{{ route('admin.dashboard') }}");
                return;
            }
        }

        // Cek apakah user telah verifikasi sebagai perempuan
        const gender = localStorage.getItem('saferoad_gender');
        const verified = localStorage.getItem('saferoad_verified');
        const femaleBar = document.getElementById('femaleModeBar');
        if (femaleBar && verified === 'true' && gender === 'perempuan') {
            femaleBar.style.display = 'flex';
        }
    })();
</script>
@endpush
@endsection
