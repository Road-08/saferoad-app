@extends('layouts.app')

@section('title', 'Dashboard Akun · SafeRoad')

@section('content')
<div class="dashboard-page-container">

    @if(auth()->user()->canAccessWomenZone())
    {{-- ===== Banner Mode Perempuan ===== --}}
    <div id="femaleModeBar" class="female-mode-bar">
        <div class="female-mode-inner">
            <span class="female-mode-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
            </span>
            <div>
                <strong>Mode Perempuan Terverifikasi Aktif</strong>
                <span>Fitur navigasi aman khusus perempuan dan monitoring pengemudi aktif otomatis untuk perjalanan Anda.</span>
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('rute') }}" class="female-mode-cta">Buka Rute Perempuan</a>
        </div>
    </div>
    @endif

    {{-- ===== Banner Selamat Datang ===== --}}
    <section class="welcome-banner">
        <span class="eyebrow-tag">PORTAL MASYARAKAT SAFEROAD</span>
        <h1>Selamat Datang, {{ auth()->user()->name }}</h1>
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



        {{-- Card 3: Laporan Insiden --}}
        <div class="action-card">
            <div class="action-card-body">
                <div class="action-card-header">
                    <div class="card-icon" aria-hidden="true">
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
            <a href="{{ url('/laporkan') }}" class="card-footer-link">
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
/* ===== Female Mode Bar (Clean Minimalist Blue/Slate Surface) ===== */
.female-mode-bar {
    background-color: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    color: #0f172a;
}
.female-mode-inner {
    display: flex;
    align-items: center;
    gap: 14px;
}
.female-mode-icon {
    background-color: #dbeafe;
    color: #1e40af;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.female-mode-inner strong {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
}
.female-mode-inner span {
    font-size: 13px;
    color: #64748b;
    line-height: 1.4;
}
.female-mode-cta {
    padding: 8px 16px;
    border-radius: 8px;
    background-color: #2563eb;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: background-color .15s ease;
    flex-shrink: 0;
}
.female-mode-cta:hover { background-color: #1e40af; }

/* ===== Layout ===== */
.dashboard-page-container {
    width: min(100% - 40px, 1140px);
    margin: 32px auto 60px;
}

/* ===== Welcome Banner (Clean Flat Professional Blue) ===== */
.welcome-banner {
    background-color: #1e40af;
    border: 1px solid #1e40af;
    border-radius: 14px;
    padding: 32px clamp(24px, 3.5vw, 40px);
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    margin-bottom: 28px;
}

.eyebrow-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 10px;
    border-radius: 6px;
    background: rgba(255,255,255,0.12);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #dbeafe;
    margin-bottom: 12px;
}

.welcome-banner h1 {
    font-size: clamp(26px, 3.2vw, 34px);
    font-weight: 800;
    letter-spacing: -0.025em;
    margin-bottom: 8px;
}

.welcome-banner > p {
    font-size: 14.5px;
    color: #dbeafe;
    max-width: 600px;
    line-height: 1.6;
}

.session-status-strip {
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid rgba(255,255,255,0.16);
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
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.24);
    border-radius: 9999px;
    color: #ffffff;
    font-weight: 600;
    font-size: 12px;
}

.live-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: #dbeafe;
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
    border-radius: 14px;
    padding: 22px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 18px;
    box-shadow: var(--shadow-sm);
    transition: border-color 0.15s ease;
}

.action-card:hover {
    border-color: var(--primary-600, #2563eb);
}

.action-card-body {
    flex: 1;
}

.action-card-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.card-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-icon svg {
    width: 20px;
    height: 20px;
}

.card-icon--danger {
    background: #eff6ff;
    color: #2563eb;
}

.action-card-header h3 {
    font-size: 15.5px;
    font-weight: 700;
    color: var(--text-heading, #0f172a);
    margin-bottom: 4px;
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
    padding-top: 12px;
    border-top: 1px solid var(--border-soft, #e2e8f0);
    white-space: nowrap;
    transition: color 0.15s ease;
}

.card-footer-link:hover {
    color: #1e40af;
}

.card-footer-link svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

/* ===== Account Details Card ===== */
.account-details-card {
    background-color: var(--bg-card, #ffffff);
    border: 1px solid var(--border-soft, #e2e8f0);
    border-radius: 14px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
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
    color: #2563eb;
}

.detail-value--success {
    color: #2563eb;
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

        // Pembersihan status demo lama jika ada
        localStorage.removeItem('saferoad_verified');
        localStorage.removeItem('saferoad_gender');
    })();
</script>
@endpush
@endsection
