@extends('layouts.app')

@section('title', "Women's Safe Zone · SafeRoad")

@push('leaflet-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="wz-page-container">
    <!-- Header Banner -->
    <header class="wz-hero-banner">
        <div class="wz-banner-content">
            <span class="wz-badge-pill">♀ WOMEN'S PROTECTION ZONE</span>
            <h1>Zona Aman &amp; Perlindungan Perjalanan Perempuan</h1>
            <p>
                Visualisasi titik pencahayaan jalan, pengawasan CCTV aktif, pos aman siaga 24 jam,
                dan riwayat kejadian terverifikasi untuk mendukung mobilitas perempuan yang tenang dan terlindungi.
            </p>
            <div class="wz-stats-strip">
                <div class="wz-stat-item">
                    <strong>94.2%</strong>
                    <span>Cakupan Lampu Terverifikasi</span>
                </div>
                <div class="wz-stat-divider"></div>
                <div class="wz-stat-item">
                    <strong>18 Titik</strong>
                    <span>CCTV Aktif Terkoneksi</span>
                </div>
                <div class="wz-stat-divider"></div>
                <div class="wz-stat-item">
                    <strong>6 Pos</strong>
                    <span>Shelter / Pos Aman Siaga</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Banner Status Terverifikasi (Mode Perempuan Aktif) -->
    <div id="wzVerifiedNotice" class="wz-verified-strip" style="display: none;">
        <div class="wz-verified-inner">
            <span class="wz-verified-icon">♀</span>
            <div>
                <strong>Mode Perempuan Terverifikasi Aktif ✨</strong>
                <span>Identitas Anda terverifikasi sebagai perempuan. Peta perlindungan Women's Zone, pos shelter, dan sebaran CCTV aktif telah terbuka penuh.</span>
            </div>
        </div>
        <div class="wz-verified-actions">
            <button type="button" class="btn-demo-toggle-lock" onclick="demoResetVerification()">
                🔄 Uji Mode Terkunci (Demo)
            </button>
        </div>
    </div>

    <!-- TAMPILAN TERKUNCI JIKA BELUM VERIFIKASI PEREMPUAN -->
    <section id="wzLockedGate" class="wz-locked-card" style="display: none;">
        <div class="wz-locked-header-tag">
            <span class="lock-tag-icon">🔒</span>
            <span>HAK AKSES PRIVAT · KHUSUS PENGGUNA PEREMPUAN TERVERIFIKASI</span>
        </div>
        <div class="wz-locked-icon-badge">
            <span class="wz-flower-icon">🌸</span>
            <span class="wz-lock-mini">🔒</span>
        </div>
        <h2>Akses Women's Zone Memerlukan Verifikasi Gender Perempuan</h2>
        <p class="wz-locked-desc">
            Untuk menjaga kenyamanan, keamanan ruang privat, dan memastikan data perlindungan perjalanan digunakan secara tepat sasaran, modul <strong>Women's Safe Zone</strong> dan fitur <strong>Mode Perempuan</strong> hanya dapat diakses setelah akun diverifikasi dengan identitas (KTP) berjenis kelamin <strong>Perempuan</strong>.
        </p>

        <div class="wz-feature-pillars">
            <div class="wz-pillar-card">
                <div class="wz-pillar-icon">💡</div>
                <div>
                    <strong>Penerangan &amp; CCTV Terintegrasi</strong>
                    <p>Visualisasi jalur perjalanan dengan penerangan maksimal dan jangkauan kamera pengawas aktif.</p>
                </div>
            </div>
            <div class="wz-pillar-card">
                <div class="wz-pillar-icon">🛡️</div>
                <div>
                    <strong>Pos Shelter &amp; Titik Bantuan 24 Jam</strong>
                    <p>Lokasi pos satpam kawasan dan shelter siaga terdekat dengan kontak respon cepat SAPA 129.</p>
                </div>
            </div>
            <div class="wz-pillar-card">
                <div class="wz-pillar-icon">🚗</div>
                <div>
                    <strong>Monitoring Komposisi Pengemudi</strong>
                    <p>Fitur pemantauan jumlah driver laki-laki vs perempuan di rute yang Anda lalui.</p>
                </div>
            </div>
        </div>

        <div class="wz-locked-btn-group">
            <a href="{{ url('/verifikasi-identitas') }}" class="btn-wz-verify-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
                <span>Verifikasi Identitas Sekarang</span>
            </a>
            <button type="button" class="btn-wz-verify-demo" onclick="demoActivateFemaleMode()">
                <span>⚡ Demo Langsung: Aktifkan Mode Perempuan</span>
            </button>
            <a href="{{ url('/rute') }}" class="btn-wz-back-general">
                <span>← Kembali ke Peta Rute Umum</span>
            </a>
        </div>
    </section>

    <!-- Main Grid: Peta & Panel Kontrol -->
    <div class="wz-layout-grid">
        <!-- Area Kiri: Peta Interaktif -->
        <div class="wz-map-section">
            <div class="wz-map-card">
                <!-- Toolbar Filter Layer -->
                <div class="wz-layer-toolbar">
                    <span class="wz-filter-label">Filter Layer Peta:</span>
                    <label class="wz-filter-chip">
                        <input type="checkbox" id="layerCctv" checked>
                        <span>📹 CCTV Aktif</span>
                    </label>
                    <label class="wz-filter-chip">
                        <input type="checkbox" id="layerLampu" checked>
                        <span>💡 Penerangan Jalan</span>
                    </label>
                    <label class="wz-filter-chip">
                        <input type="checkbox" id="layerPos" checked>
                        <span>🛡️ Pos Aman / Shelter</span>
                    </label>
                    <label class="wz-filter-chip">
                        <input type="checkbox" id="layerInsiden" checked>
                        <span>⚠️ Riwayat Terverifikasi</span>
                    </label>
                </div>

                <!-- Peta Leaflet -->
                <div class="wz-map-wrapper">
                    <div id="wzMap" aria-label="Peta Women's Zone SafeRoad"></div>
                    <div class="wz-map-legend">
                        <span class="wz-legend-item"><i style="background:#16a34a"></i> Pos Aman / Siaga</span>
                        <span class="wz-legend-item"><i style="background:#2563eb"></i> CCTV Terkoneksi</span>
                        <span class="wz-legend-item"><i style="background:#f59e0b"></i> Lampu Terang</span>
                        <span class="wz-legend-item"><i style="background:#dc2626"></i> Titik Waspada</span>
                    </div>
                </div>
                <div class="wz-map-footnote">
                    <span>Terakhir diperbarui: <strong>Hari ini, 19:40 WIB</strong> (Sumber: Data Fasilitas Dishub & Komunitas SafeRoad)</span>
                </div>
            </div>
        </div>

        <!-- Area Kanan: Panel Info Keamanan & Aksi Cepat -->
        <aside class="wz-sidebar-panel">
            <!-- Card Skor Keamanan Wilayah -->
            <div class="wz-card wz-score-card">
                <div class="wz-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                    <span>Indeks Keamanan Wilayah</span>
                </div>
                <div class="wz-score-box">
                    <div class="wz-score-circle">
                        <strong>88</strong>
                        <small>/100</small>
                    </div>
                    <div>
                        <span class="wz-score-badge">Kondisi Terpantau Baik</span>
                        <p class="wz-score-desc">Area Ketintang & sekitarnya memiliki pencahayaan memadai dan patroli rutin.</p>
                    </div>
                </div>
            </div>

            <!-- Card Rekomendasi Rute Terang -->
            <div class="wz-card">
                <div class="wz-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span>Rute Utama Prioritas Perempuan</span>
                </div>
                <p class="wz-route-note">Jalur rekomendasi yang menghindari gang sempit tanpa penerangan:</p>
                <div class="wz-route-pill">
                    <span class="wrp-dot"></span>
                    <div>
                        <strong>Koridor Jl. Ketintang Utama → Kampus</strong>
                        <small>CCTV 100% aktif · 4 Pos Jaga siaga</small>
                    </div>
                </div>
                <a href="{{ url('/rute') }}" class="btn-open-route">
                    Buka Navigasi Rute Ini →
                </a>
            </div>

            <!-- Card Bantuan Khusus Perempuan -->
            <div class="wz-card wz-help-card">
                <div class="wz-card-title text-pink">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>Layanan Pengaduan Perempuan</span>
                </div>
                <div class="wz-contact-list">
                    <a href="tel:129" class="wz-contact-btn">
                        <span>Layanan SAPA (KemenPPPA):</span>
                        <strong>129</strong>
                    </a>
                    <a href="tel:110" class="wz-contact-btn">
                        <span>Respon Cepat Polisi:</span>
                        <strong>110</strong>
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>

<style>
.wz-page-container {
    max-width: 1400px;
    margin: 24px auto 60px;
    padding: 0 clamp(16px, 3vw, 36px);
}

/* Hero Banner */
.wz-hero-banner {
    background: linear-gradient(135deg, #831843 0%, #be185d 60%, #db2777 100%);
    border-radius: 20px;
    padding: 36px clamp(20px, 4vw, 44px);
    color: #fff;
    margin-bottom: 28px;
    box-shadow: 0 15px 35px -10px rgba(190, 24, 93, 0.35);
    position: relative;
    overflow: hidden;
}
.wz-hero-banner::after {
    content: "♀";
    position: absolute;
    right: 20px;
    bottom: -40px;
    font-size: 200px;
    color: rgba(255,255,255,0.07);
    font-weight: 900;
    pointer-events: none;
}
.wz-badge-pill {
    display: inline-block;
    padding: 4px 14px;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.18);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.1em;
    color: #fdf2f8;
    margin-bottom: 12px;
}
.wz-hero-banner h1 {
    font-size: clamp(24px, 3.2vw, 34px);
    font-weight: 800;
    letter-spacing: -0.02em;
    margin-bottom: 8px;
}
.wz-hero-banner p {
    font-size: 14.5px;
    color: #fce7f3;
    max-width: 680px;
    line-height: 1.6;
    margin-bottom: 24px;
}
.wz-stats-strip {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    border-top: 1px solid rgba(255,255,255,0.2);
    padding-top: 18px;
}
.wz-stat-item strong {
    display: block;
    font-size: 20px;
    font-weight: 800;
    color: #fff;
}
.wz-stat-item span {
    font-size: 12px;
    color: #fbcfe8;
}
.wz-stat-divider {
    width: 1px;
    height: 30px;
    background: rgba(255,255,255,0.25);
}

/* ===== Verified Strip ===== */
.wz-verified-strip {
    background: #fdf2f8;
    border: 1.5px solid #fbcfe8;
    border-radius: 14px;
    padding: 14px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.wz-verified-inner {
    display: flex;
    align-items: center;
    gap: 12px;
}
.wz-verified-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #be185d;
    color: #fff;
    font-size: 20px;
    font-weight: 800;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}
.wz-verified-inner strong {
    display: block;
    font-size: 14px;
    color: #9d174d;
}
.wz-verified-inner span {
    font-size: 12.5px;
    color: #831843;
}
.btn-demo-toggle-lock {
    padding: 7px 14px;
    border-radius: 8px;
    border: 1px solid #f472b6;
    background: #fff;
    color: #be185d;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-demo-toggle-lock:hover {
    background: #fce7f3;
}

/* ===== Locked Gate Card ===== */
.wz-locked-card {
    background: #ffffff;
    border: 2px solid #fbcfe8;
    border-radius: 20px;
    padding: 44px 36px;
    text-align: center;
    box-shadow: 0 15px 40px rgba(190, 24, 93, 0.08);
    max-width: 860px;
    margin: 20px auto 48px;
}
.wz-locked-header-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 999px;
    background: #fdf2f8;
    border: 1px solid #fbcfe8;
    color: #be185d;
    font-size: 11.5px;
    font-weight: 800;
    letter-spacing: 0.05em;
    margin-bottom: 20px;
}
.wz-locked-icon-badge {
    position: relative;
    width: 84px;
    height: 84px;
    border-radius: 26px;
    background: linear-gradient(135deg, #fce7f3, #fbcfe8);
    border: 2px solid #f472b6;
    margin: 0 auto 20px;
    display: grid;
    place-items: center;
    font-size: 40px;
}
.wz-lock-mini {
    position: absolute;
    bottom: -6px;
    right: -6px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #be185d;
    color: #fff;
    font-size: 15px;
    display: grid;
    place-items: center;
    border: 2px solid #fff;
}
.wz-locked-card h2 {
    font-size: clamp(22px, 2.5vw, 28px);
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin-bottom: 12px;
}
.wz-locked-desc {
    font-size: 14.5px;
    color: #475569;
    line-height: 1.65;
    max-width: 660px;
    margin: 0 auto 28px;
}
.wz-feature-pillars {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    text-align: left;
    margin-bottom: 32px;
}
@media (max-width: 768px) {
    .wz-feature-pillars {
        grid-template-columns: 1fr;
    }
}
.wz-pillar-card {
    background: #fdf2f8;
    border: 1px solid #fce7f3;
    border-radius: 14px;
    padding: 16px;
    display: flex;
    gap: 12px;
}
.wz-pillar-icon {
    font-size: 24px;
    line-height: 1;
    flex-shrink: 0;
}
.wz-pillar-card strong {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #9d174d;
    margin-bottom: 4px;
}
.wz-pillar-card p {
    font-size: 11.5px;
    color: #475569;
    line-height: 1.45;
    margin: 0;
}
.wz-locked-btn-group {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}
.btn-wz-verify-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    background: linear-gradient(135deg, #db2777, #be185d);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(190, 24, 93, 0.3);
    transition: all 0.15s ease;
}
.btn-wz-verify-primary svg {
    width: 18px;
    height: 18px;
}
.btn-wz-verify-primary:hover {
    background: linear-gradient(135deg, #be185d, #9d174d);
    transform: translateY(-1px);
}
.btn-wz-verify-demo {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    border: 1.5px solid #f472b6;
    background: #fff;
    color: #be185d;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-wz-verify-demo:hover {
    background: #fdf2f8;
}
.btn-wz-back-general {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 18px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #475569;
    font-size: 13.5px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.15s ease;
}
.btn-wz-back-general:hover {
    background: #e2e8f0;
}

/* Grid Layout */
.wz-layout-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 960px) {
    .wz-layout-grid {
        grid-template-columns: 1fr;
    }
}

/* Map Card */
.wz-map-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 18px;
    padding: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.wz-layer-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-soft);
}
.wz-filter-label {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--text-heading);
}
.wz-filter-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    background: #f8fafc;
    border: 1.5px solid var(--border-soft);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
}
.wz-filter-chip:has(input:checked) {
    background: #fdf2f8;
    border-color: #fbcfe8;
    color: #9d174d;
}
.wz-filter-chip input {
    accent-color: #be185d;
}
.wz-map-wrapper {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid var(--border-soft);
}
#wzMap {
    height: 520px;
    width: 100%;
}
.wz-map-legend {
    position: absolute;
    bottom: 14px;
    left: 14px;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(4px);
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    gap: 12px;
    font-size: 11px;
    font-weight: 600;
    color: var(--text-heading);
    z-index: 1000;
    flex-wrap: wrap;
}
.wz-legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
}
.wz-legend-item i {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}
.wz-map-footnote {
    font-size: 11.5px;
    color: var(--text-muted);
    margin-top: 12px;
    text-align: right;
}

/* Sidebar Cards */
.wz-sidebar-panel {
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.wz-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.wz-card-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 800;
    color: var(--text-heading);
    margin-bottom: 14px;
}
.wz-card-title svg {
    width: 18px;
    height: 18px;
    color: var(--primary-600);
}
.text-pink svg { color: #be185d; }

.wz-score-box {
    display: flex;
    align-items: center;
    gap: 16px;
}
.wz-score-circle {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: #fdf2f8;
    border: 2px solid #fbcfe8;
    color: #be185d;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.wz-score-circle strong { font-size: 22px; line-height: 1; }
.wz-score-circle small { font-size: 10px; color: #9d174d; }
.wz-score-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 6px;
    background: #dcfce7;
    color: #15803d;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 4px;
}
.wz-score-desc {
    font-size: 12.5px;
    color: var(--text-body);
    line-height: 1.4;
    margin: 0;
}

.wz-route-note {
    font-size: 12.5px;
    color: var(--text-muted);
    margin-bottom: 10px;
}
.wz-route-pill {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: #f8fafc;
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    margin-bottom: 12px;
}
.wrp-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #16a34a;
    flex-shrink: 0;
}
.wz-route-pill strong {
    display: block;
    font-size: 13px;
    color: var(--text-heading);
}
.wz-route-pill small {
    font-size: 11px;
    color: var(--text-muted);
}
.btn-open-route {
    display: block;
    text-align: center;
    padding: 10px;
    background: #eff6ff;
    color: var(--primary-700);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: background 0.2s;
}
.btn-open-route:hover {
    background: #dbeafe;
}

.wz-contact-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.wz-contact-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    border-radius: 10px;
    background: #fdf2f8;
    border: 1px solid #fbcfe8;
    color: #831843;
    font-size: 12.5px;
    text-decoration: none;
    transition: background 0.2s;
}
.wz-contact-btn:hover { background: #fce7f3; }
.wz-contact-btn strong { font-size: 14px; }
</style>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Fungsi aktivasi cepat untuk keperluan demonstrasi
function demoActivateFemaleMode() {
    localStorage.setItem('saferoad_verified', 'true');
    localStorage.setItem('saferoad_gender', 'perempuan');
    alert('Mode Perempuan berhasil diaktifkan! Fitur Women\'s Zone dan Driver Monitoring kini terbuka.');
    window.location.reload();
}
const activateFemaleModeDemo = demoActivateFemaleMode;

function demoResetVerification() {
    localStorage.removeItem('saferoad_verified');
    localStorage.removeItem('saferoad_gender');
    alert('Status verifikasi direset. Halaman Women\'s Zone kembali terkunci.');
    window.location.reload();
}

document.addEventListener('DOMContentLoaded', () => {
    // Pengecekan Hak Akses Role: Women's Zone murni untuk Masyarakat
    const activeRole = localStorage.getItem('saferoad_active_role') || 'masyarakat';
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has('stay')) {
        if (activeRole === 'admin') {
            window.location.replace("{{ route('admin.dashboard') }}");
            return;
        } else if (activeRole === 'keamanan') {
            window.location.replace("{{ route('keamanan.dashboard') }}");
            return;
        }
    }

    // Pengecekan Verifikasi Gender Perempuan
    const isVerified = localStorage.getItem('saferoad_verified') === 'true';
    const gender = localStorage.getItem('saferoad_gender');
    const isFemale = (isVerified && gender === 'perempuan');

    const lockedGate = document.getElementById('wzLockedGate');
    const verifiedNotice = document.getElementById('wzVerifiedNotice');
    const mapGrid = document.querySelector('.wz-layout-grid');

    if (!isFemale) {
        if (lockedGate) lockedGate.style.display = 'block';
        if (verifiedNotice) verifiedNotice.style.display = 'none';
        if (mapGrid) mapGrid.style.display = 'none';
        return; // Jangan muat peta jika belum terverifikasi
    } else {
        if (lockedGate) lockedGate.style.display = 'none';
        if (verifiedNotice) verifiedNotice.style.display = 'flex';
        if (mapGrid) mapGrid.style.display = 'grid';
    }

    const PUSAT = { lat: -7.3140, lng: 112.7280 };
    const map = L.map('wzMap', { zoomControl: false }).setView([PUSAT.lat, PUSAT.lng], 15);
    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Data Titik Fasilitas Women's Zone
    const cctvPoints = [
        { nama: 'CCTV Simpang Utama Ketintang', lat: -7.3152, lng: 112.7280 },
        { nama: 'CCTV Pertigaan Kampus', lat: -7.3138, lng: 112.7268 },
        { nama: 'CCTV Halte Ramah Perempuan', lat: -7.3120, lng: 112.7295 }
    ];

    const lampuPoints = [
        { nama: 'Lampu LED Penerangan Tinggi - Jl. Ketintang', lat: -7.3145, lng: 112.7275 },
        { nama: 'Lampu Jalan Koridor Timur', lat: -7.3132, lng: 112.7262 },
        { nama: 'Lampu Jalan Depan Fakultas', lat: -7.3155, lng: 112.7290 }
    ];

    const posPoints = [
        { nama: '🛡️ Pos Satpam Terpadu Siaga 24 Jam', lat: -7.3126, lng: 112.7258, kontak: '(031) 829-1234' },
        { nama: '🛡️ Shelter Aman & Pos Jaga Ketintang Kulon', lat: -7.3147, lng: 112.7291, kontak: 'Petugas Siaga' }
    ];

    const insidenPoints = [
        { nama: '⚠️ Riwayat: Gang Kurang Penerangan (Perlu Waspada)', lat: -7.3160, lng: 112.7265, ket: 'Telah diajukan perbaikan lampu' }
    ];

    // Layers
    const layerGroupCctv = L.layerGroup().addTo(map);
    const layerGroupLampu = L.layerGroup().addTo(map);
    const layerGroupPos = L.layerGroup().addTo(map);
    const layerGroupInsiden = L.layerGroup().addTo(map);

    function createMarkerIcon(icon, bg, border) {
        return L.divIcon({
            className: '',
            html: `<div style="width:30px;height:30px;border-radius:50%;background:${bg};border:2px solid ${border};display:grid;place-items:center;font-size:14px;box-shadow:0 2px 6px rgba(0,0,0,0.25);">${icon}</div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });
    }

    // Render Markers
    cctvPoints.forEach(p => {
        L.marker([p.lat, p.lng], { icon: createMarkerIcon('📹', '#eff6ff', '#2563eb') })
            .bindPopup(`<strong>${p.nama}</strong><br><small style="color:#2563eb">CCTV Terkoneksi Sistem</small>`)
            .addTo(layerGroupCctv);
    });

    lampuPoints.forEach(p => {
        L.marker([p.lat, p.lng], { icon: createMarkerIcon('💡', '#fefce8', '#f59e0b') })
            .bindPopup(`<strong>${p.nama}</strong><br><small style="color:#ca8a04">Penerangan Terang Terverifikasi</small>`)
            .addTo(layerGroupLampu);
    });

    posPoints.forEach(p => {
        L.marker([p.lat, p.lng], { icon: createMarkerIcon('🛡️', '#f0fdf4', '#16a34a') })
            .bindPopup(`<strong>${p.nama}</strong><br><small style="color:#16a34a">Kontak: ${p.kontak}</small>`)
            .addTo(layerGroupPos);
    });

    insidenPoints.forEach(p => {
        L.marker([p.lat, p.lng], { icon: createMarkerIcon('⚠️', '#fef2f2', '#dc2626') })
            .bindPopup(`<strong>${p.nama}</strong><br><small style="color:#dc2626">${p.ket}</small>`)
            .addTo(layerGroupInsiden);
    });

    // Toggle Filter Handler
    document.getElementById('layerCctv').addEventListener('change', e => {
        e.target.checked ? map.addLayer(layerGroupCctv) : map.removeLayer(layerGroupCctv);
    });
    document.getElementById('layerLampu').addEventListener('change', e => {
        e.target.checked ? map.addLayer(layerGroupLampu) : map.removeLayer(layerGroupLampu);
    });
    document.getElementById('layerPos').addEventListener('change', e => {
        e.target.checked ? map.addLayer(layerGroupPos) : map.removeLayer(layerGroupPos);
    });
    document.getElementById('layerInsiden').addEventListener('change', e => {
        e.target.checked ? map.addLayer(layerGroupInsiden) : map.removeLayer(layerGroupInsiden);
    });
});
</script>
@endpush
@endsection
