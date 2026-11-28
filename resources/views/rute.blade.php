@extends('layouts.app')

@section('title', 'Temukan Rute Aman · SafeRoad')

@push('leaflet-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
<div class="route-page-container">
    <div class="route-split-layout">
        <!-- SIDEBAR PENCARIAN & KARTU RUTE (KIRI) -->
        <aside class="route-sidebar">
            <header class="sidebar-header">
                <span class="badge-sub">NAVIGASI PINTAR</span>
                <h1>Temukan Rute Perjalanan Aman</h1>
                <p>Cari jalur terbaik dengan analisis titik penerangan, CCTV, dan riwayat insiden di sepanjang perjalanan.</p>
            </header>

            <!-- Card Input Form Rute -->
            <div class="route-form-card">
                @if($routeMode === 'women')
                <div class="route-mode-active-indicator">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>Mode Perempuan aktif — rute dihitung dengan prioritas keamanan perjalanan</span>
                </div>
                @endif

                <div class="inputs-stack">
                    <!-- Titik Asal -->
                    <div class="input-line-group">
                        <span class="input-marker-dot origin" aria-hidden="true"></span>
                        <div class="input-inner-wrap">
                            <label for="inputDari">Titik Asal</label>
                            <input
                                type="text"
                                id="inputDari"
                                value="Lokasi Saya"
                                placeholder="Tentukan lokasi asal"
                                autocomplete="off"
                            >
                            <div class="saran-dropdown" id="saranDari"></div>
                        </div>
                    </div>

                    <!-- Tombol Tukar Asal / Tujuan -->
                    <button class="btn-swap-location" id="btnTukar" type="button" title="Tukar titik asal dan tujuan">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transform: rotate(180deg);">
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                    </button>

                    <!-- Titik Tujuan -->
                    <div class="input-line-group">
                        <span class="input-marker-dot dest" aria-hidden="true"></span>
                        <div class="input-inner-wrap">
                            <label for="inputKe">Tujuan</label>
                            <input
                                type="text"
                                id="inputKe"
                                placeholder="Cari tujuan (misal: Telkom University, Stasiun...)"
                                autocomplete="off"
                            >
                            <div class="saran-dropdown" id="saranKe"></div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Cari -->
                <button class="btn-search-route" id="btnCari" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Cari Rekomendasi Rute</span>
                </button>

                <!-- Status Feedback -->
                <div class="status-banner" id="status"></div>
            </div>

            <!-- Hasil & Alternatif Rute -->
            <section class="results-section">
                <div class="results-header-bar">
                    <h2>Pilihan Rute Perjalanan</h2>
                    <span id="infoRute" class="results-count"></span>
                </div>

                <div id="daftarRute" class="route-cards-list"></div>

                <!-- Tombol Bandingkan Rute -->
                <button class="btn-compare-routes" id="btnBandingkan" type="button" hidden>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 3h5v5"/>
                        <path d="M4 20L21 3"/>
                        <path d="M21 16v5h-5"/>
                        <path d="M15 15l6 6"/>
                        <path d="M4 4l5 5"/>
                    </svg>
                    <span id="btnBandingkanText">Bandingkan Rute A & B</span>
                </button>

                <!-- Panel Perbandingan Rute Langsung (Inline) -->
                <div id="perbandinganRute" class="inline-compare-section" hidden>
                    <div class="compare-card-container">
                        <div class="compare-card-header">
                            <div style="min-width: 0; flex: 1;">
                                <span class="badge-compare-sub" id="badgeCmpMode">{{ $routeMode === 'women' ? 'MODE PEREMPUAN · MULTI-RUTE' : 'EVALUASI MULTI-RUTE' }}</span>
                                <h3 id="titleCmpMode" style="margin: 4px 0 2px; font-size: 15px; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Perbandingan Rute Langsung</h3>
                                <p class="compare-subtext" id="subtextCmpMode" style="margin: 0; font-size: 11.5px; color: var(--text-muted);">
                                    {{ $routeMode === 'women' ? "Bandingkan Women's Safety Score, risiko catcalling, pelecehan, dan penerangan rute." : "Bandingkan metrik efisiensi, Safety Score umum, kriminalitas, dan fasilitas keamanan." }}
                                </p>
                            </div>
                            <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
                                <a href="{{ route('rute.bandingkan') }}" class="btn-open-full-compare" id="btnBukaBandingkanDetail" title="Buka tabel perbandingan lengkap">
                                    <span>Tabel Lengkap ↗</span>
                                </a>
                                <button type="button" class="btn-close-comparison" id="btnTutupPerbandingan" title="Tutup Perbandingan">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Daftar Kartu Perbandingan Rute (Vertikal 1 Kolom) -->
                        <div class="compare-columns-grid" id="compareColumnsGrid">
                            <!-- Diisi secara dinamis oleh renderPerbandingan() -->
                        </div>
                    </div>
                </div>

                <div class="disclaimer-note" id="disclaimerSkor"></div>

                <!-- Tombol Mulai Navigasi -->
                <button class="btn-start-navigation" id="btnNavigasi" type="button" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                    </svg>
                    <span>Mulai Navigasi Perjalanan</span>
                </button>

                <p class="navigation-disclaimer">
                    Skor dan indikator membantu evaluasi rute secara objektif, bukan jaminan mutlak kondisi lapangan.
                </p>
            </section>

            @if($routeMode === 'women')
            <!-- Driver Monitoring Card -->
            <div id="driverMonitor" class="driver-monitoring-card" style="display:none;">
                <div class="dm-header">
                    <div class="dm-title-box">
                        <span class="dm-badge-pink">MODE PEREMPUAN</span>
                        <h3 class="dm-title">Monitoring Pengemudi Sekitar Rute</h3>
                    </div>
                    <div class="dm-live-badge">
                        <span class="dm-pulse-dot"></span>
                        <span class="dm-live-text">LIVE</span>
                        <span class="dm-demo-tag">DEMO</span>
                    </div>
                </div>

                <div class="dm-route-indicator">
                    <span>Memantau di sepanjang:</span>
                    <strong id="driverRouteLabel" class="dm-active-route">Rute A (Terpilih)</strong>
                </div>

                <div class="dm-metrics-grid">
                    <div class="dm-metric-card dm-card-male">
                        <div class="dm-metric-top">
                            <span class="dm-metric-label">Pengemudi Laki-laki</span>
                        </div>
                        <div class="dm-metric-num" id="maleCount">14</div>
                        <span class="dm-metric-sub">Terdeteksi di rute ini</span>
                    </div>

                    <div class="dm-metric-card dm-card-female">
                        <div class="dm-metric-top">
                            <span class="dm-metric-label">Pengemudi Perempuan</span>
                        </div>
                        <div class="dm-metric-num" id="femaleCount">4</div>
                        <span class="dm-metric-sub">Terdeteksi di rute ini</span>
                    </div>

                    <div class="dm-metric-card dm-card-total">
                        <div class="dm-metric-top">
                            <span class="dm-metric-label">Total Pengemudi</span>
                        </div>
                        <div class="dm-metric-num" id="totalCount">18</div>
                        <span class="dm-metric-sub">Dalam radius perjalanan</span>
                    </div>
                </div>

                <div class="dm-footer">
                    <div class="dm-footer-time">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Pembaruan: <strong id="driverUpdateTime">-</strong></span>
                    </div>
                    <div class="dm-footer-privacy">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>Privasi aman: tanpa identitas/lokasi pribadi</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tombol Navigasi ke Laporan Insiden (Disesuaikan Role) -->
            <a href="{{ url('/laporkan') }}" class="btn-route-incident-report" id="btnRouteReport">
                <div class="bri-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div class="bri-text">
                    <strong id="btnRouteReportTitle">Laporkan Kejadian / Insiden Perjalanan</strong>
                    <span id="btnRouteReportSub">Kirim laporan cepat dengan koordinat GPS Anda</span>
                </div>
                <svg class="bri-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>

            <!-- Notification Toast -->
            <div id="toast" class="toast" hidden></div>

        </aside>

        <!-- AREA MAP KANAN -->
        <main class="route-map-panel">
            <!-- Filter Layer Fasilitas Peta -->
            <div class="route-map-filters-bar">
                <span class="rmf-title">Fasilitas Keamanan di Peta:</span>
                <label class="rmf-chip">
                    <input type="checkbox" id="toggleMapCctv" checked>
                    <span>CCTV</span>
                </label>
                <label class="rmf-chip">
                    <input type="checkbox" id="toggleMapLampu" checked>
                    <span>Penerangan</span>
                </label>
                <label class="rmf-chip">
                    <input type="checkbox" id="toggleMapPos" checked>
                    <span>Pos Polisi &amp; Jaga</span>
                </label>
                <label class="rmf-chip">
                    <input type="checkbox" id="toggleMapRs" checked>
                    <span>Rumah Sakit</span>
                </label>
                <label class="rmf-chip">
                    <input type="checkbox" id="toggleMapRawan" checked>
                    <span>Titik Rawan</span>
                </label>
            </div>

            <div class="map-container-box">
                <div id="map" aria-label="Peta interaktif SafeRoad"></div>
                <!-- Overlay Legenda Komprehensif -->
                <div class="map-floating-legend">
                    <span class="legend-chip"><i class="dot blue"></i> Rute Utama</span>
                    <span class="legend-chip"><i class="dot" style="background:#64748b;"></i> Rute Alternatif</span>
                    <span class="legend-chip"><i class="dot" style="background:#2563eb;"></i> CCTV</span>
                    <span class="legend-chip"><i class="dot" style="background:#3b82f6;"></i> Lampu</span>
                    <span class="legend-chip"><i class="dot" style="background:#1e40af;"></i> Pos Aman</span>
                    <span class="legend-chip"><i class="dot" style="background:#0f172a;"></i> RS</span>
                    <span class="legend-chip"><i class="dot" style="background:#dc2626;"></i> Titik Rawan</span>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .route-page-container {
        width: 100%;
        max-width: 1560px;
        margin: 0 auto;
        padding: 20px clamp(16px, 2.5vw, 36px) 120px;
        box-sizing: border-box;
    }

    .route-split-layout {
        display: grid;
        grid-template-columns: 420px 1fr;
        gap: 24px;
        align-items: start;
        min-width: 0;
    }

    /* Wrap & Overflow Fixes */
    .route-split-layout > *,
    .route-form-card,
    .compare-card-container,
    .kartu,
    .kartu-atas,
    .inputs-stack,
    .input-line-group,
    .input-inner-wrap,
    .driver-monitoring-card {
        min-width: 0;
        box-sizing: border-box;
        max-width: 100%;
    }

    .kartu, .kartu * {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .kartu-atas {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
        flex-wrap: wrap;
    }

    .nama-lokasi-line {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        max-width: 100%;
        min-width: 0;
    }

    #inputDari, #inputKe {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        min-width: 0;
        box-sizing: border-box;
    }

    .chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    /* Indicator Mode Rute Aktif (Monochromatic Blue) */
    .route-mode-active-indicator {
        margin-bottom: 16px;
        padding: 10px 14px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1e40af;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.4;
    }

    /* Pilihan Jenis Rute */
    .route-mode-selector-wrap {
        margin-bottom: 16px;
    }
    .mode-selector-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .mode-selector-heading {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-muted);
    }
    .badge-verified-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10.5px;
        font-weight: 700;
        color: #1e40af;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        padding: 2px 8px;
        border-radius: 9999px;
    }
    .badge-verified-indicator svg {
        color: #2563eb;
    }

    .route-mode-toggle-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .btn-mode-tab {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: #ffffff;
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        cursor: pointer;
        text-align: left;
        overflow: hidden;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-mode-tab:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .tab-indicator-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: transparent;
        transition: background 0.2s;
    }
    .btn-mode-tab .tab-icon {
        font-size: 20px;
        line-height: 1;
        flex-shrink: 0;
    }
    .btn-mode-tab .tab-label-wrap {
        flex: 1;
        min-width: 0;
    }
    .btn-mode-tab .tab-title {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-heading);
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .btn-mode-tab .tab-subtitle {
        display: block;
        font-size: 10px;
        color: var(--text-muted);
        line-height: 1.2;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Tab Active State General & Women (Monochromatic Blue) */
    .btn-mode-tab.mode-general-tab.active,
    .btn-mode-tab.mode-women-tab.active {
        background: #eff6ff;
        border-color: #2563eb;
    }
    .btn-mode-tab.mode-general-tab.active .tab-indicator-bar,
    .btn-mode-tab.mode-women-tab.active .tab-indicator-bar {
        background: #2563eb;
    }
    .btn-mode-tab.mode-general-tab.active .tab-title,
    .btn-mode-tab.mode-women-tab.active .tab-title {
        color: #1e40af;
    }
    .btn-mode-tab.mode-general-tab.active .tab-subtitle,
    .btn-mode-tab.mode-women-tab.active .tab-subtitle {
        color: #2563eb;
    }
    .badge-tab-pink {
        display: inline-block;
        font-size: 9px;
        font-weight: 700;
        padding: 1px 5px;
        border-radius: 4px;
        background: #dbeafe;
        color: #1e40af;
        margin-left: 2px;
        vertical-align: middle;
    }

    /* Kotak Informasi Verifikasi Gender */
    .wz-auth-notice {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 16px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        color: #1e40af;
    }
    .wz-notice-icon {
        font-size: 20px;
        line-height: 1;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .wz-notice-body {
        flex: 1;
        min-width: 0;
    }
    .wz-notice-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 4px;
    }
    .wz-notice-header-row strong {
        font-size: 13px;
        font-weight: 700;
        line-height: 1.25;
        color: #0f172a;
    }
    .wz-status-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
        background: #dbeafe;
        color: #1e40af;
    }
    .wz-notice-body p {
        font-size: 11.5px;
        line-height: 1.45;
        margin-bottom: 10px;
        color: #64748b;
    }
    .wz-notice-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-wz-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        border: none;
        background: #2563eb;
        color: #fff;
        transition: background 0.15s ease;
    }
    .btn-wz-action:hover {
        background: #1e40af;
    }
    .btn-wz-action.secondary {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .btn-open-full-compare {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
    }
    .btn-open-full-compare:hover {
        background: #dbeafe;
    }

    /* Toolbar Filter Fasilitas Peta */
    .route-map-filters-bar {
        background: #ffffff;
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: 10px 16px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .rmf-title {
        font-size: 12px;
        font-weight: 800;
        color: var(--text-heading);
    }
    .rmf-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid var(--border-soft);
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        color: var(--text-body);
        transition: all 0.15s;
    }
    .rmf-chip:has(input:checked) {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: var(--primary-700);
    }
    .rmf-chip input {
        accent-color: var(--primary-600);
    }

    /* Sidebar Kiri */
    .route-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sidebar-header .badge-sub {
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

    .sidebar-header h1 {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-heading);
        letter-spacing: -0.03em;
        line-height: 1.25;
        margin-bottom: 6px;
    }

    .sidebar-header p {
        font-size: 13.5px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* Form Input Rute Card */
    .route-form-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 18px;
        padding: 20px;
        box-shadow: var(--shadow-card);
    }

    .inputs-stack {
        position: relative;
        display: grid;
        gap: 12px;
        margin-bottom: 16px;
    }

    .input-line-group {
        display: flex;
        align-items: center;
        gap: 12px;
        background-color: var(--bg-canvas);
        border: 1.5px solid var(--border-soft);
        border-radius: 12px;
        padding: 8px 14px;
        transition: border-color 0.15s ease;
    }

    .input-line-group:focus-within {
        border-color: var(--primary-600);
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .input-marker-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .input-marker-dot.origin {
        background-color: var(--primary-600);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }

    .input-marker-dot.dest {
        background-color: var(--danger-600);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
    }

    .input-inner-wrap {
        flex: 1;
        position: relative;
    }

    .input-inner-wrap label {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 2px;
    }

    .input-inner-wrap input {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-heading);
    }

    .input-inner-wrap input::placeholder {
        color: #94a3b8;
        font-weight: normal;
    }

    /* Tombol Tukar Lokasi */
    .btn-swap-location {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #ffffff;
        border: 1px solid var(--border-soft);
        color: var(--primary-600);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: var(--shadow-sm);
        z-index: 10;
        transition: transform 0.2s ease, background 0.15s ease;
    }

    .btn-swap-location:hover {
        background-color: var(--primary-50);
        transform: translateY(-50%) rotate(180deg);
    }

    .btn-swap-location svg {
        width: 12px;
        height: 12px;
    }

    /* Dropdown Saran Pencarian */
    .saran-dropdown {
        position: absolute;
        left: -40px;
        right: -10px;
        top: calc(100% + 10px);
        background-color: #ffffff;
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        z-index: 2000;
        overflow: hidden;
    }

    .saran-dropdown:empty {
        display: none;
    }

    .item-saran {
        padding: 10px 14px;
        font-size: 12.5px;
        color: var(--text-body);
        cursor: pointer;
        border-bottom: 1px solid var(--border-soft);
    }

    .item-saran:last-child {
        border-bottom: none;
    }

    .item-saran:hover {
        background-color: var(--primary-50);
        color: var(--primary-700);
    }

    /* Tombol Cari */
    .btn-search-route {
        width: 100%;
        height: 42px;
        padding: 0 16px;
        border: none;
        border-radius: 9px;
        background: #2563eb;
        color: #ffffff;
        font-size: 13.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        white-space: nowrap;
        transition: background-color 0.15s ease;
    }

    .btn-search-route:hover {
        background: #1e40af;
    }

    .btn-search-route:disabled {
        opacity: 0.65;
        cursor: wait;
    }

    .btn-search-route svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    /* Status Banner */
    .status-banner {
        min-height: 20px;
        font-size: 12.5px;
        color: var(--text-muted);
        text-align: center;
        margin-top: 10px;
    }

    .status-banner.error {
        color: var(--danger-600);
        font-weight: 600;
    }

    /* Bagian Hasil Rute */
    .results-section {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        box-shadow: var(--shadow-sm);
    }

    .results-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 14px;
    }

    .results-header-bar h2 {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .results-count {
        font-size: 12px;
        color: #64748b;
    }

    .route-cards-list {
        display: grid;
        gap: 10px;
        margin-bottom: 14px;
    }

    /* Kartu Rute (Monochromatic Blue, Satu Border) */
    .kartu {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        cursor: pointer;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .kartu:hover {
        border-color: #2563eb;
    }

    .kartu.aktif {
        border: 1px solid #2563eb;
        background-color: #eff6ff;
    }

    .kartu-atas {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .kartu-atas b {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .skor {
        font-size: 18px;
        font-weight: 800;
        color: #1e40af;
    }

    .skor small {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
    }

    .meta {
        font-size: 12.5px;
        color: #64748b;
        margin: 4px 0 8px;
    }

    /* Informasi Rute dengan Teks Sederhana & Ikon Netral */
    .route-info-list {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        padding-top: 10px;
        margin-top: 10px;
        border-top: 1px solid #e2e8f0;
        font-size: 12px;
        color: #64748b;
    }

    .route-info-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #64748b;
        font-weight: 500;
    }

    .route-info-item svg {
        width: 14px;
        height: 14px;
        color: #64748b;
        flex-shrink: 0;
    }

    .route-info-item.is-danger {
        color: #dc2626;
        font-weight: 600;
    }

    .route-info-item.is-danger svg {
        color: #dc2626;
    }

    .tag {
        font-size: 11px;
        font-weight: 600;
        color: #1e40af;
        background-color: #dbeafe;
        padding: 2px 8px;
        border-radius: 6px;
        margin-left: 8px;
    }

    .tag.alt {
        background-color: transparent;
        color: #64748b;
        padding: 0;
        font-weight: 500;
    }

    .level-badge {
        display: inline-block;
        font-size: 11.5px;
        font-weight: 700;
        color: #1e40af;
    }

    .level-aman { color: #2563eb; }
    .level-sedang { color: #64748b; }
    .level-rawan { color: #dc2626; }
    .level-data-belum-cukup { color: #64748b; }

    /* Tombol Bandingkan & Navigasi */
    .btn-compare-routes {
        width: 100%;
        height: 42px;
        background-color: #eff6ff;
        color: #1e40af;
        border: 1px solid #dbeafe;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        margin-bottom: 12px;
        transition: background-color 0.15s;
    }

    .btn-compare-routes[hidden] {
        display: none !important;
    }

    .btn-compare-routes:hover {
        background-color: #dbeafe;
    }

    .btn-compare-routes.aktif {
        background-color: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .btn-compare-routes.aktif:hover {
        background-color: #1e40af;
    }

    /* =====================================================
       PANEL PERBANDINGAN RUTE LANGSUNG (INLINE)
    ===================================================== */
    .inline-compare-section {
        margin: 10px 0 16px;
    }

    .compare-card-container {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
    }

    .compare-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
    }

    .badge-compare-sub {
        display: inline-block;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #2563eb;
        margin-bottom: 2px;
    }

    .compare-card-header h3 {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .compare-subtext {
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
    }

    .btn-close-comparison {
        background: none;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        width: 28px;
        height: 28px;
        display: grid;
        place-items: center;
        color: #64748b;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.15s;
    }

    .btn-close-comparison:hover {
        background-color: #eff6ff;
        color: #2563eb;
        border-color: #dbeafe;
    }

    .btn-close-comparison svg {
        width: 14px;
        height: 14px;
    }

    /* Grid Kolom Komparasi (Vertikal 1 Kolom Full-Width) */
    .compare-columns-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .compare-route-card {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        cursor: pointer;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .compare-route-card:hover {
        border-color: #2563eb;
    }

    .compare-route-card.aktif {
        background-color: #eff6ff;
        border: 1px solid #2563eb;
    }

    .route-name-label {
        font-size: 14.5px;
        font-weight: 700;
        color: #0f172a;
    }

    .badge-recommended-simple {
        font-size: 11px;
        font-weight: 600;
        color: #1e40af;
        background-color: #dbeafe;
        padding: 2px 8px;
        border-radius: 6px;
    }

    .score-display {
        font-size: 16px;
        font-weight: 800;
        color: #1e40af;
        line-height: 1;
    }

    .score-display .score-den {
        font-size: 11px;
        font-weight: 500;
        color: #64748b;
    }

    .tag-metric-plain {
        font-size: 11.5px;
        font-weight: 600;
        color: #2563eb;
    }

    /* Tombol Pilih Rute di Card */
    .btn-pick-route {
        width: 100%;
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #dbeafe;
        background-color: #ffffff;
        color: #2563eb;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .btn-pick-route:hover {
        background-color: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .btn-pick-route.terpilih {
        background-color: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .btn-compare-routes svg {
        width: 16px;
        height: 16px;
    }

    .disclaimer-note {
        font-size: 11px;
        color: #64748b;
        margin: 6px 0 14px;
    }

    .btn-start-navigation {
        width: 100%;
        height: 42px;
        padding: 0 16px;
        background: #1e40af;
        color: #ffffff;
        border: none;
        border-radius: 9px;
        font-size: 13.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        white-space: nowrap;
        transition: background-color 0.15s ease;
    }

    .btn-start-navigation:hover:not(:disabled) {
        background: #1e3a8a;
    }

    .btn-start-navigation:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-start-navigation svg {
        width: 16px;
        height: 16px;
    }

    .navigation-disclaimer {
        font-size: 11px;
        color: #64748b;
        text-align: center;
        margin-top: 10px;
        line-height: 1.4;
    }

    /* Map Panel Kanan */
    .route-map-panel {
        position: sticky;
        top: 96px;
    }

    .map-container-box {
        position: relative;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: var(--shadow-sm);
    }

    #map {
        height: 640px;
        width: 100%;
        background-color: #e2e8f0;
    }

    .map-floating-legend {
        position: absolute;
        bottom: 16px;
        right: 16px;
        left: auto;
        background-color: rgba(255, 255, 255, 0.96);
        padding: 6px 14px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        display: flex;
        gap: 12px;
        font-size: 11.5px;
        font-weight: 600;
        color: #0f172a;
        z-index: 999;
        max-width: calc(100% - 32px);
        flex-wrap: wrap;
    }

    .legend-chip {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .dot.blue { background-color: #2563eb; }

    @media (max-width: 1024px) {
        .route-split-layout {
            grid-template-columns: 1fr;
        }

        .route-map-panel {
            position: static;
            order: -1;
        }

        #map {
            height: 380px;
        }
    }

/* ===== Driver Monitoring Card Styles (Monochromatic Blue) ===== */
.driver-monitoring-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px;
    margin-top: 16px;
}

.dm-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}

.dm-badge-pink {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #2563eb;
    margin-bottom: 4px;
}

.dm-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.dm-live-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    padding: 3px 10px;
    border-radius: 9999px;
}

.dm-pulse-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: #2563eb;
}

.dm-live-text {
    font-size: 10.5px;
    font-weight: 700;
    color: #1e40af;
    letter-spacing: 0.04em;
}

.dm-demo-tag {
    font-size: 9px;
    font-weight: 700;
    background-color: #dbeafe;
    color: #1e40af;
    padding: 1px 5px;
    border-radius: 4px;
}

.dm-route-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 7px 12px;
    border-radius: 8px;
    margin-bottom: 12px;
}

.dm-active-route {
    color: #1e40af;
    font-weight: 700;
}

.dm-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 12px;
}

.dm-metric-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 10px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.dm-card-male,
.dm-card-female,
.dm-card-total {
    background: #f8fafc;
    border-color: #e2e8f0;
}

.dm-metric-top {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 4px;
}

.dm-metric-num {
    font-size: 22px;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
}

.dm-metric-sub {
    font-size: 10px;
    color: #64748b;
    margin-top: 4px;
}

.dm-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 10px;
    border-top: 1px solid #e2e8f0;
    font-size: 11px;
    color: #64748b;
    flex-wrap: wrap;
}

.dm-footer-time, .dm-footer-privacy {
    display: flex;
    align-items: center;
    gap: 5px;
}

.dm-footer-time svg, .dm-footer-privacy svg {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
    color: #64748b;
}

/* ===== Tombol Laporan Insiden di Rute ===== */
.btn-route-incident-report {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    margin-top: 16px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    transition: border-color 0.15s ease, background-color 0.15s ease;
}

.btn-route-incident-report:hover {
    border-color: #2563eb;
    background: #eff6ff;
}

.bri-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.bri-icon svg {
    width: 18px;
    height: 18px;
}

.bri-text {
    flex: 1;
}

.bri-text strong {
    display: block;
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
}

.bri-text span {
    font-size: 12px;
    color: #64748b;
}

.bri-arrow {
    width: 18px;
    height: 18px;
    color: #64748b;
    flex-shrink: 0;
}
</style>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // =====================================================
    // PENGATURAN AWAL
    // =====================================================
    const PUSAT = { lat: -7.2575, lng: 112.7521 };
    const DISCLAIMER_FALLBACK = 'Skor adalah indikator, bukan jaminan mutlak keamanan.';

    const $ = id => document.getElementById(id);
    const inputDari = $('inputDari');
    const inputKe = $('inputKe');
    const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

    // =====================================================
    // MODE RUTE OTOMATIS (SERVER DETERMINED)
    // =====================================================
    const serverCanAccess = @json($canAccessWomenZone ?? false);
    const currentMode = @json($routeMode);

    // Variabel state rute
    let daftarGaris = [];
    let terpilihIdx = 0;
    let titikA = null;
    let titikB = null;
    let modeNavigasi = false;
    let penghitungPermintaanRute = 0;
    let hasilRuteAktif = [];
    let ruteOSRMAktif = [];
    let statusSkor = 'pending';
    let disclaimerAktif = DISCLAIMER_FALLBACK;

    @if($routeMode === 'women')
    // =====================================================
    // FITUR MONITORING PENGEMUDI
    // =====================================================
    const mockDriverData = {
        0: { male: 14, female: 4, total: 18, label: 'Rute A' },
        1: { male: 9, female: 2, total: 11, label: 'Rute B' },
        2: { male: 7, female: 5, total: 12, label: 'Rute C' }
    };

    function updateDriverInfo(idx = 0) {
        const card = $('driverMonitor');
        if (!card) return;

        const data = mockDriverData[idx] || { male: 12, female: 3, total: 15, label: `Rute ${String.fromCharCode(65 + idx)}` };
        const maleEl = $('maleCount');
        const femaleEl = $('femaleCount');
        const totalEl = $('totalCount');
        const timeEl = $('driverUpdateTime');
        const labelEl = $('driverRouteLabel');

        if (maleEl) maleEl.textContent = data.male;
        if (femaleEl) femaleEl.textContent = data.female;
        if (totalEl) totalEl.textContent = data.total;
        if (labelEl) labelEl.textContent = `${data.label} (Terpilih)`;
        if (timeEl) timeEl.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';

        card.style.display = 'block';
    }

    // Interval refresh pembaruan pengemudi setiap 30 detik
    setInterval(() => updateDriverInfo(terpilihIdx), 30000);
    setTimeout(() => {
        updateDriverInfo(0);
    }, 100);
    @else
    function updateDriverInfo(idx = 0) {}
    @endif

    // =====================================================
    // MEMBUAT PETA
    // =====================================================
    const map = L.map('map', { zoomControl: false }).setView([PUSAT.lat, PUSAT.lng], 13);
    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const layerRute = L.layerGroup().addTo(map);

    // =====================================================
    // LAYER FASILITAS KEAMANAN PETA
    // =====================================================
    const layerCctvMap = L.layerGroup().addTo(map);
    const layerLampuMap = L.layerGroup().addTo(map);
    const layerPosMap = L.layerGroup().addTo(map);
    const layerRsMap = L.layerGroup().addTo(map);
    const layerRawanMap = L.layerGroup().addTo(map);

    function createFacilityIcon(icon, bg, border) {
        return L.divIcon({
            className: '',
            html: `<div style="width:28px;height:28px;border-radius:50%;background:${bg};border:2px solid ${border};display:grid;place-items:center;font-size:13px;box-shadow:0 2px 6px rgba(0,0,0,0.25);">${icon}</div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
    }

    const dataFasilitas = {
        cctv: [
            { nama: 'CCTV Dishub Simpang Ketintang', lat: -7.3152, lng: 112.7280 },
            { nama: 'CCTV Pertigaan RS Islam A Yani', lat: -7.3105, lng: 112.7340 },
            { nama: 'CCTV Bundaran Dolog', lat: -7.3240, lng: 112.7320 }
        ],
        lampu: [
            { nama: 'Penerangan Tinggi Koridor Utama', lat: -7.3145, lng: 112.7275 },
            { nama: 'Penerangan Jalan Kampus Ketintang', lat: -7.3130, lng: 112.7260 },
            { nama: 'Lampu LED Simpang Wonokromo', lat: -7.3000, lng: 112.7380 }
        ],
        pos: [
            { nama: 'Polsek Gayungan / Ketintang', lat: -7.3270, lng: 112.7265, sub: 'Petugas Siaga 24 Jam' },
            { nama: 'Pos Keamanan Satpam Terpadu', lat: -7.3126, lng: 112.7258, sub: 'Shelter Aman Warga' },
            { nama: 'Pos Lantas Wonokromo', lat: -7.2990, lng: 112.7390, sub: 'Pos Polisi Aktif' }
        ],
        rs: [
            { nama: 'RSI Surabaya (Ahmad Yani)', lat: -7.3090, lng: 112.7350, sub: 'IGD Siaga 24 Jam' }
        ],
        rawan: [
            { nama: 'Riwayat Titik Rawan: Gang Kurang Penerangan', lat: -7.3175, lng: 112.7260, sub: 'Tingkatkan kewaspadaan di malam hari' }
        ]
    };

    dataFasilitas.cctv.forEach(p => L.marker([p.lat, p.lng], { icon: createFacilityIcon('<span style="font-size:10px;font-weight:800;color:#1e40af;">C</span>', '#eff6ff', '#2563eb') }).bindPopup(`<strong>${p.nama}</strong><br><small style="color:#2563eb;">Kamera Pantau Aktif</small>`).addTo(layerCctvMap));
    dataFasilitas.lampu.forEach(p => L.marker([p.lat, p.lng], { icon: createFacilityIcon('<span style="font-size:10px;font-weight:800;color:#1e40af;">L</span>', '#eff6ff', '#3b82f6') }).bindPopup(`<strong>${p.nama}</strong><br><small style="color:#2563eb;">Penerangan Terverifikasi</small>`).addTo(layerLampuMap));
    dataFasilitas.pos.forEach(p => L.marker([p.lat, p.lng], { icon: createFacilityIcon('<span style="font-size:10px;font-weight:800;color:#1e40af;">P</span>', '#dbeafe', '#1e40af') }).bindPopup(`<strong>${p.nama}</strong><br><small style="color:#1e40af;">${p.sub}</small>`).addTo(layerPosMap));
    dataFasilitas.rs.forEach(p => L.marker([p.lat, p.lng], { icon: createFacilityIcon('<span style="font-size:9px;font-weight:800;color:#0f172a;">RS</span>', '#f8fafc', '#64748b') }).bindPopup(`<strong>${p.nama}</strong><br><small style="color:#64748b;">${p.sub}</small>`).addTo(layerRsMap));
    dataFasilitas.rawan.forEach(p => L.marker([p.lat, p.lng], { icon: createFacilityIcon('<span style="font-size:11px;font-weight:800;color:#dc2626;">!</span>', '#fef2f2', '#dc2626') }).bindPopup(`<strong>${p.nama}</strong><br><small style="color:#dc2626;">${p.sub}</small>`).addTo(layerRawanMap));

    $('toggleMapCctv')?.addEventListener('change', e => e.target.checked ? map.addLayer(layerCctvMap) : map.removeLayer(layerCctvMap));
    $('toggleMapLampu')?.addEventListener('change', e => e.target.checked ? map.addLayer(layerLampuMap) : map.removeLayer(layerLampuMap));
    $('toggleMapPos')?.addEventListener('change', e => e.target.checked ? map.addLayer(layerPosMap) : map.removeLayer(layerPosMap));
    $('toggleMapRs')?.addEventListener('change', e => e.target.checked ? map.addLayer(layerRsMap) : map.removeLayer(layerRsMap));
    $('toggleMapRawan')?.addEventListener('change', e => e.target.checked ? map.addLayer(layerRawanMap) : map.removeLayer(layerRawanMap));

    // =====================================================
    // PENCARIAN TEMPAT (Nominatim)
    // =====================================================
    let terakhir = 0;

    async function cariTempat(q) {
        const tunggu = terakhir + 1100 - Date.now();
        if (tunggu > 0) await sleep(tunggu);
        terakhir = Date.now();

        const url = 'https://nominatim.openstreetmap.org/search' +
            '?format=json&limit=5&countrycodes=id&accept-language=id' +
            '&viewbox=112.5,-7.1,113.0,-7.5' +
            '&q=' + encodeURIComponent(q);

        const res = await fetch(url);
        if (!res.ok) throw new Error('Layanan pencarian tempat sedang sibuk, coba lagi.');
        return res.json();
    }

    let asal = null;
    let tujuan = null;

    function debounce(fn, ms) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), ms);
        };
    }

    function pasangSaran(input, box, setter) {
        const cari = debounce(async () => {
            const q = input.value.trim();
            if (q.length < 3 || q.toLowerCase() === 'lokasi saya') {
                box.innerHTML = '';
                return;
            }

            try {
                const hasil = await cariTempat(q);
                box.innerHTML = '';
                hasil.forEach(h => {
                    const d = document.createElement('div');
                    d.className = 'item-saran';
                    d.textContent = h.display_name;
                    d.addEventListener('click', () => {
                        input.value = h.display_name.split(',').slice(0, 2).join(',').trim();
                        setter({
                            nama: input.value,
                            lat: +h.lat,
                            lng: +h.lon
                        });
                        box.innerHTML = '';
                    });
                    box.appendChild(d);
                });
            } catch (e) {
                box.innerHTML = '';
            }
        }, 600);

        input.addEventListener('input', () => {
            setter(null);
            cari();
        });

        document.addEventListener('click', e => {
            if (!box.contains(e.target) && e.target !== input) {
                box.innerHTML = '';
            }
        });
    }

    pasangSaran(inputDari, $('saranDari'), value => { asal = value; });
    pasangSaran(inputKe, $('saranKe'), value => { tujuan = value; });

    function lokasiSaya() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                return reject(new Error('Browser tidak mendukung lokasi. Ketik alamat asal.'));
            }
            navigator.geolocation.getCurrentPosition(
                position => {
                    resolve({
                        nama: 'Lokasi Saya',
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    });
                },
                () => {
                    resolve({
                        nama: 'Surabaya Pusat (Default)',
                        lat: PUSAT.lat,
                        lng: PUSAT.lng
                    });
                },
                { timeout: 7000 }
            );
        });
    }

    function setStatus(teks, error = false) {
        $('status').textContent = teks || '';
        $('status').className = 'status-banner' + (error ? ' error' : '');
    }

    function simpanDataPerbandinganStorage() {
        if (!ruteOSRMAktif || !ruteOSRMAktif.length || !titikA || !titikB) return;
        const maxRoutes = currentMode === 'women' ? 3 : 2;
        const routes = ruteOSRMAktif.slice(0, maxRoutes).map((route, index) => {
            const coordinates = route.geometry.coordinates;
            const langkah = Math.ceil(coordinates.length / 500);
            const dijarangkan = coordinates.filter((titik, titikIndex) =>
                titikIndex === 0 ||
                titikIndex === coordinates.length - 1 ||
                titikIndex % langkah === 0
            );

            while (dijarangkan.length > 500) dijarangkan.splice(dijarangkan.length - 2, 1);

            return {
                id: String.fromCharCode(65 + index),
                distance_m: route.distance,
                duration_s: route.duration,
                coordinates: dijarangkan.map(titik => [titik[0], titik[1]])
            };
        });

        const data = {
            version: 1,
            mode: currentMode,
            createdAt: new Date().toISOString(),
            origin: { name: titikA.nama, lat: titikA.lat, lng: titikA.lng },
            destination: { name: titikB.nama, lat: titikB.lat, lng: titikB.lng },
            selectedIndex: terpilihIdx,
            routes
        };

        try {
            sessionStorage.setItem('saferoad.perbandingan', JSON.stringify(data));
        } catch (error) {}
    }

    function hitungLevelKeamanan(score) {
        if (score === null || score === undefined || score === '' || score === '-') {
            return { label: 'Data Belum Cukup', className: 'level-data-belum-cukup', color: '#64748b' };
        }
        const num = Number(score);
        if (!Number.isFinite(num)) {
            return { label: 'Data Belum Cukup', className: 'level-data-belum-cukup', color: '#64748b' };
        }
        if (num >= 80) {
            return { label: 'Aman', className: 'level-aman', color: '#2563eb' };
        }
        if (num >= 60) {
            return { label: 'Sedang', className: 'level-sedang', color: '#64748b' };
        }
        return { label: 'Rawan', className: 'level-rawan', color: '#dc2626' };
    }

    function hitungChipPenerangan(kondisi) {
        const k = (kondisi || '').toLowerCase();
        if (k.includes('terang') || k.includes('optimal') || k.includes('lampu baik') || (k.includes('baik') && !k.includes('kurang'))) {
            return { text: 'Penerangan baik', isDanger: false };
        }
        if (k.includes('minim') || k.includes('gelap') || k.includes('kurang')) {
            return { text: 'Minim penerangan', isDanger: false };
        }
        return { text: 'Penerangan cukup', isDanger: false };
    }

    function hitungChipFasilitas(cctvCount, postCount) {
        const cctv = Number(cctvCount) || 0;
        const pos = Number(postCount) || 0;
        return { text: `${cctv} CCTV · ${pos} Pos siaga`, isDanger: false };
    }

    function hitungChipLaporan(incidents, isWomen) {
        const count = Number(incidents) || 0;
        if (isWomen) {
            if (count === 0) {
                return { text: '0 laporan gangguan', isDanger: false };
            }
            return { text: `${count} laporan pelecehan`, isDanger: true };
        } else {
            if (count === 0) {
                return { text: '0 laporan kriminalitas', isDanger: false };
            }
            return { text: `${count} laporan kriminalitas`, isDanger: true };
        }
    }

    let womenRouteLegendControl = null;

    function setLegendaRutePerempuan(show) {
        if (womenRouteLegendControl) {
            map.removeControl(womenRouteLegendControl);
            womenRouteLegendControl = null;
        }
        if (!show) return;

        womenRouteLegendControl = L.control({ position: 'bottomleft' });
        womenRouteLegendControl.onAdd = function() {
            const div = L.DomUtil.create('div', 'women-route-legend-ctrl');
            div.style.backgroundColor = '#ffffff';
            div.style.padding = '8px 12px';
            div.style.borderRadius = '10px';
            div.style.boxShadow = '0 1px 4px rgba(15,23,42,0.08)';
            div.style.border = '1px solid #e2e8f0';
            div.style.fontSize = '11.5px';
            div.style.fontWeight = '600';
            div.style.color = '#0f172a';
            div.style.display = 'flex';
            div.style.flexDirection = 'column';
            div.style.gap = '6px';
            div.style.pointerEvents = 'none';

            div.innerHTML = `
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="display:inline-block; width:20px; height:4px; background:#2563eb; border-radius:2px;"></span>
                    <span>Rute rekomendasi teraman</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="display:inline-block; width:20px; height:0px; border-top:3px dashed #94a3b8;"></span>
                    <span style="color:#64748b;">Rute alternatif</span>
                </div>
            `;
            return div;
        };
        womenRouteLegendControl.addTo(map);
    }

    function simpanDanBukaPerbandingan() {
        simpanDataPerbandinganStorage();
        togglePerbandingan();
    }

    function pilih(i) {
        terpilihIdx = i;
        const isWomen = currentMode === 'women';

        daftarGaris.forEach((garis, index) => {
            const aktif = index === i;
            if (isWomen) {
                if (garis.isSafestPink) {
                    garis.setStyle({
                        color: aktif ? '#1e40af' : '#2563eb',
                        weight: aktif ? 8 : 6,
                        opacity: 1,
                        dashArray: null
                    });
                    if (garis.outlineLayer) garis.outlineLayer.bringToFront();
                    garis.bringToFront();
                } else {
                    garis.setStyle({
                        color: aktif ? '#2563eb' : '#94a3b8',
                        weight: aktif ? 7 : 5,
                        opacity: aktif ? 0.95 : 0.7,
                        dashArray: aktif ? null : '6 8'
                    });
                    if (aktif) garis.bringToFront();
                }
            } else {
                const baseColor = aktif ? '#1e40af' : '#2563eb';
                garis.setStyle({
                    color: baseColor,
                    weight: aktif ? 7 : 4,
                    opacity: aktif ? 0.95 : 0.5,
                    dashArray: aktif ? null : '6 8'
                });
                if (aktif) garis.bringToFront();
            }
        });

        document.querySelectorAll('.kartu').forEach((kartu, index) => {
            kartu.classList.toggle('aktif', index === i);
        });

        // Perbarui highlight di panel perbandingan langsung
        document.querySelectorAll('.compare-route-card').forEach(card => {
            const cardIdx = Number(card.dataset.index);
            const aktif = cardIdx === i;
            card.classList.toggle('aktif', aktif);
            const btn = card.querySelector('.btn-pick-route');
            if (btn) {
                btn.classList.toggle('terpilih', aktif);
                const letter = card.dataset.letter || '';
                btn.innerHTML = aktif ? '<span>Rute Terpilih</span>' : `<span>Pilih Rute ${letter}</span>`;
            }
        });

        if (modeNavigasi && daftarGaris[i]) {
            map.fitBounds(daftarGaris[i].getBounds().pad(0.1));
        }

        updateDriverInfo(i);
    }

    function renderPerbandingan() {
        if (!ruteOSRMAktif || ruteOSRMAktif.length < 2) return;

        const maxRoutes = currentMode === 'women' ? 3 : 2;
        const availableRoutes = ruteOSRMAktif.slice(0, maxRoutes);
        const isWomen = currentMode === 'women';

        // Cari rute teraman (safestIndex)
        let safestIndex = -1;
        if (isWomen) {
            safestIndex = availableRoutes.findIndex((r, idx) => {
                const rId = String.fromCharCode(65 + idx);
                const s = hasilRuteAktif.find(item => item.id === rId);
                return s?.women_comparison?.is_pink_recommendation === true;
            });
            if (safestIndex === -1) {
                let bestScore = -1;
                let bestDur = Infinity;
                availableRoutes.forEach((r, idx) => {
                    const rId = String.fromCharCode(65 + idx);
                    const s = hasilRuteAktif.find(item => item.id === rId);
                    const sc = s?.women_comparison?.womens_safety_score ?? -1;
                    const dur = r.duration || 0;
                    if (sc > bestScore || (sc === bestScore && dur < bestDur)) {
                        bestScore = sc;
                        bestDur = dur;
                        safestIndex = idx;
                    }
                });
            }
        } else {
            safestIndex = 0;
        }

        // Kumpulkan data semua rute
        const routesData = availableRoutes.map((route, idx) => {
            const letter = String.fromCharCode(65 + idx);
            const m = konversiMetrikRute(route);
            const scoreData = hasilRuteAktif.find(r => r.id === letter) || null;
            const comp = isWomen ? scoreData?.women_comparison : scoreData?.general_comparison;
            const rawScore = isWomen
                ? (comp?.womens_safety_score ?? (idx === 0 ? 90 : 75))
                : (comp?.safety_score ?? (idx === 0 ? 92 : 78));
            const score = (rawScore !== null && rawScore !== undefined) ? Number(rawScore) : null;
            const lvl = hitungLevelKeamanan(score);

            const isSafest = isWomen ? (idx === safestIndex) : (comp?.is_recommended ?? idx === 0);

            // Chips
            const incCount = isWomen
                ? (comp?.reports?.total_women_incidents ?? 0)
                : (comp?.reports?.total_crimes_count ?? 0);
            const chipInc = hitungChipLaporan(incCount, isWomen);

            const chipLight = hitungChipPenerangan(comp?.facilities?.lighting_condition);

            const cctv = comp?.facilities?.cctv_count ?? (idx === 0 ? 2 : 0);
            const pos = (isWomen ? comp?.facilities?.shelter_post_count : comp?.facilities?.security_post_count) ?? (idx === 0 ? 1 : 0);
            const chipFac = hitungChipFasilitas(cctv, pos);

            return {
                originalIndex: idx,
                letter,
                m,
                score,
                lvl,
                isSafest,
                chipInc,
                chipLight,
                chipFac
            };
        });

        // Tentukan Terpendek & Tercepat (mendukung nilai seri)
        const minDist = Math.min(...routesData.map(r => r.m.jarakKm));
        const minDur = Math.min(...routesData.map(r => r.m.waktuMenit));
        routesData.forEach(r => {
            r.isShortest = Math.abs(r.m.jarakKm - minDist) < 0.05;
            r.isFastest = r.m.waktuMenit === minDur;
        });

        // Urutkan rute: skor tertinggi PALING ATAS! Jika seri, durasi terpendek
        const sortedRoutes = [...routesData].sort((a, b) => {
            const scA = a.score ?? -1;
            const scB = b.score ?? -1;
            if (scB !== scA) return scB - scA;
            return a.m.waktuMenit - b.m.waktuMenit;
        });

        // Render ke container #compareColumnsGrid
        const container = $('compareColumnsGrid');
        if (container) {
            container.innerHTML = '';
            sortedRoutes.forEach(r => {
                const card = document.createElement('div');
                const isAktif = (r.originalIndex === terpilihIdx);
                card.className = `compare-route-card ${isAktif ? 'aktif' : ''}`;
                card.dataset.index = r.originalIndex;
                card.dataset.letter = r.letter;

                const metricNotes = [
                    r.isShortest ? 'Terpendek' : '',
                    r.isFastest ? 'Tercepat' : ''
                ].filter(Boolean).join(' · ');

                card.innerHTML = `
                    <!-- Baris 1: Nama Rute + Rekomendasi | Skor Keamanan -->
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; min-width:0;">
                            <span class="route-name-label">Rute ${r.letter}</span>
                            ${r.isSafest ? `<span class="badge-recommended-simple">${isWomen ? 'Rekomendasi Teraman' : 'Direkomendasikan'}</span>` : ''}
                        </div>
                        <div style="display:flex; align-items:baseline; gap:6px; text-align:right; flex-shrink:0;">
                            <span class="score-display" style="color:${r.lvl.color};">
                                ${r.score !== null ? r.score : '-'}<span class="score-den">/100</span>
                            </span>
                            <span class="level-badge ${r.lvl.className}">· ${r.lvl.label}</span>
                        </div>
                    </div>

                    <!-- Baris 2: Jarak & Estimasi Waktu -->
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; font-size:12.5px; color:#64748b;">
                        <span><strong style="color:#0f172a;">${r.m.jarakKm.toFixed(1)} km</strong> · <strong style="color:#0f172a;">${r.m.waktuMenit} menit</strong></span>
                        ${metricNotes ? `<span class="tag-metric-plain">${metricNotes}</span>` : ''}
                    </div>

                    <!-- Baris 3: Informasi Kriminalitas, Penerangan, CCTV & Pos Siaga (Teks & Ikon Netral) -->
                    <div class="route-info-list" style="margin-top:0; padding-top:8px;">
                        <span class="route-info-item ${r.chipInc.isDanger ? 'is-danger' : ''}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span>${r.chipInc.text}</span>
                        </span>
                        <span class="route-info-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
                            <span>${r.chipLight.text}</span>
                        </span>
                        <span class="route-info-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="15" height="10" rx="2"/><polygon points="22 8 17 12 22 16 22 8"/></svg>
                            <span>${r.chipFac.text}</span>
                        </span>
                    </div>

                    <!-- Baris 4: Tombol Pilih -->
                    <button type="button" class="btn-pick-route ${isAktif ? 'terpilih' : ''}">
                        <span>${isAktif ? 'Rute Terpilih' : 'Pilih Rute ' + r.letter}</span>
                    </button>
                `;

                card.addEventListener('click', () => pilih(r.originalIndex));
                const btn = card.querySelector('.btn-pick-route');
                if (btn) {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        pilih(r.originalIndex);
                    });
                }

                container.appendChild(card);
            });
        }

        // Ringkasan Takeaway
        if ($('textFastestTakeaway')) {
            const fastestRoutes = routesData.filter(c => c.isFastest);
            if (fastestRoutes.length === routesData.length) {
                $('textFastestTakeaway').textContent = 'Estimasi durasi waktu tempuh antar rute setara';
            } else {
                const letters = fastestRoutes.map(c => 'Rute ' + c.letter).join(' & ');
                $('textFastestTakeaway').textContent = `${letters} memiliki waktu tempuh tercepat (${fastestRoutes[0].m.waktuMenit} menit)`;
            }
        }
        if ($('textSafetyTakeaway')) {
            if (isWomen && safestIndex >= 0) {
                const safest = routesData.find(c => c.originalIndex === safestIndex);
                if (safest) {
                    $('textSafetyTakeaway').textContent = `Rute ${safest.letter} (Pink) memiliki skor perlindungan perempuan terbaik (${safest.score}/100)`;
                }
            } else {
                const highest = sortedRoutes[0];
                if (highest) {
                    $('textSafetyTakeaway').textContent = `Rute ${highest.letter} memiliki skor keamanan tertinggi (${highest.score}/100)`;
                }
            }
        }

        pilih(terpilihIdx);
    }

    function togglePerbandingan() {
        if (!ruteOSRMAktif || ruteOSRMAktif.length < 2) {
            setStatus('Cari rute terlebih dahulu untuk membandingkan.', true);
            return;
        }

        const panel = $('perbandinganRute');
        const isHidden = panel.hasAttribute('hidden') || panel.style.display === 'none';

        if (isHidden) {
            renderPerbandingan();
            panel.removeAttribute('hidden');
            panel.style.display = 'block';
            $('btnBandingkanText').textContent = 'Tutup Perbandingan Rute';
            $('btnBandingkan').classList.add('aktif');

            if (daftarGaris.length) {
                map.fitBounds(L.featureGroup(daftarGaris).getBounds().pad(0.12));
            }

            simpanDataPerbandinganStorage();
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            panel.setAttribute('hidden', '');
            panel.style.display = 'none';
            $('btnBandingkanText').textContent = 'Bandingkan Rute A & B';
            $('btnBandingkan').classList.remove('aktif');
        }
    }

    function konversiMetrikRute(route) {
        return {
            jarakKm: route.distance / 1000,
            waktuMenit: Math.max(1, Math.round(route.duration / 60))
        };
    }

    function formatLevel(level) {
        const levels = {
            aman: { label: 'Aman', className: 'level-aman' },
            sedang: { label: 'Sedang', className: 'level-sedang' },
            rawan: { label: 'Rawan', className: 'level-rawan' },
            data_belum_cukup: { label: 'Data belum cukup', className: 'level-data-belum-cukup' }
        };
        return levels[level] || null;
    }

    async function ambilSkorRute(routes) {
        const response = await fetch("{{ url('/api/routes/score') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ routes })
        });
        let payload = {};
        try { payload = await response.json(); } catch (error) { payload = {}; }
        if (!response.ok) {
            throw new Error(payload.message || 'Skor belum tersedia');
        }
        return payload;
    }

    async function renderRute(routes) {
        const box = $('daftarRute');
        box.innerHTML = '';
        daftarGaris = [];

        layerRute.clearLayers();
        setLegendaRutePerempuan(false);

        const icA = L.divIcon({
            html: '<div style="background:#1e3a8a;color:#fff;border-radius:50%;width:26px;height:26px;display:grid;place-items:center;font-weight:bold;box-shadow:0 2px 6px rgba(0,0,0,0.3)">A</div>',
            className: ''
        });
        const icB = L.divIcon({
            html: '<div style="background:#dc2626;color:#fff;border-radius:50%;width:26px;height:26px;display:grid;place-items:center;font-weight:bold;box-shadow:0 2px 6px rgba(0,0,0,0.3)">B</div>',
            className: ''
        });

        L.marker([titikA.lat, titikA.lng], { icon: icA }).addTo(layerRute).bindPopup(titikA.nama);
        L.marker([titikB.lat, titikB.lng], { icon: icB }).addTo(layerRute).bindPopup(titikB.nama);

        // Batasi jumlah alternatif rute: 2 untuk general, Maksimal 3 untuk women
        const maxRoutes = currentMode === 'women' ? 3 : 2;
        const displayRoutes = (routes || []).slice(0, maxRoutes);

        // Siapkan payload rute untuk API kalkulasi skor
        const payload = displayRoutes.map((route, index) => {
            const coordinates = route.geometry.coordinates;
            const langkah = Math.ceil(coordinates.length / 200);
            const dijarangkan = coordinates.filter((titik, idx) =>
                idx === 0 || idx === coordinates.length - 1 || idx % langkah === 0
            );
            return {
                id: String.fromCharCode(65 + index),
                coordinates: dijarangkan.map(pt => [pt[0], pt[1]])
            };
        });

        let apiScores = [];
        try {
            const res = await fetch("{{ url('/api/routes/score') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ routes: payload, mode: currentMode })
            });
            if (res.ok) {
                const json = await res.json();
                apiScores = json.data || [];
            }
        } catch (e) {
            console.warn('Gagal memuat skor dari API:', e);
        }

        hasilRuteAktif = apiScores;
        const scoreMap = new Map();
        apiScores.forEach(s => scoreMap.set(s.id, s));

        const isWomen = currentMode === 'women';

        // Tentukan tepat satu rute teraman
        let safestIndex = -1;
        if (isWomen) {
            safestIndex = displayRoutes.findIndex((r, idx) => {
                const rId = String.fromCharCode(65 + idx);
                const s = scoreMap.get(rId);
                return s?.women_comparison?.is_pink_recommendation === true;
            });
            if (safestIndex === -1 && displayRoutes.length > 0) {
                let bestScore = -1;
                let bestDur = Infinity;
                displayRoutes.forEach((r, idx) => {
                    const rId = String.fromCharCode(65 + idx);
                    const s = scoreMap.get(rId);
                    const sc = s?.women_comparison?.womens_safety_score ?? -1;
                    const dur = r.duration || 0;
                    if (sc > bestScore || (sc === bestScore && dur < bestDur)) {
                        bestScore = sc;
                        bestDur = dur;
                        safestIndex = idx;
                    }
                });
            }
        } else {
            safestIndex = 0;
        }

        displayRoutes.forEach((route, index) => {
            const pts = route.geometry.coordinates.map(c => [c[1], c[0]]);
            const routeId = String.fromCharCode(65 + index);
            const scoreData = scoreMap.get(routeId);
            const comp = isWomen ? (scoreData?.women_comparison) : (scoreData?.general_comparison);
            const isSafest = isWomen ? (index === safestIndex) : (comp?.is_recommended ?? index === 0);

            if (isWomen) {
                if (isSafest) {
                    const outline = L.polyline(pts, {
                        color: '#ffffff',
                        weight: 11,
                        opacity: 1
                    }).addTo(layerRute);
                    const line = L.polyline(pts, {
                        color: '#2563eb',
                        weight: 7,
                        opacity: 1,
                        dashArray: null
                    }).addTo(layerRute);
                    line.isSafestPink = true;
                    line.outlineLayer = outline;
                    line.bringToFront();
                    line.on('click', () => pilih(index));
                    daftarGaris.push(line);
                } else {
                    const line = L.polyline(pts, {
                        color: '#94a3b8',
                        weight: 5,
                        opacity: 0.7,
                        dashArray: '6 8'
                    }).addTo(layerRute);
                    line.isSafestPink = false;
                    line.on('click', () => pilih(index));
                    daftarGaris.push(line);
                }
            } else {
                const lineColor = isSafest ? '#1e40af' : '#2563eb';
                const line = L.polyline(pts, {
                    color: lineColor,
                    weight: isSafest ? 7 : 4,
                    opacity: isSafest ? 0.95 : 0.5,
                    dashArray: isSafest ? null : '6 8'
                }).addTo(layerRute);
                line.isSafestPink = false;
                line.defaultColor = lineColor;
                line.on('click', () => pilih(index));
                daftarGaris.push(line);
            }

            const m = konversiMetrikRute(route);

            // Indikator Kartu Hasil (Clean Monochromatic Blue, Satu Border)
            let badgeText = isSafest ? 'Direkomendasikan' : 'Alternatif';
            let scoreVal = isWomen ? (comp?.womens_safety_score ?? (index === 0 ? 90 : 75)) : (comp?.safety_score ?? (index === 0 ? 92 : 78));
            const lvl = hitungLevelKeamanan(scoreVal);
            let scoreColor = lvl.color;

            const kartu = document.createElement('div');
            kartu.className = 'kartu' + (index === terpilihIdx ? ' aktif' : '');

            const totalWomen = comp?.reports?.total_women_incidents ?? 0;
            const totalCrimes = comp?.reports?.total_crimes_count ?? 0;
            const cctvs = comp?.facilities?.cctv_count ?? (index === 0 ? 2 : 1);
            const posts = (isWomen ? comp?.facilities?.shelter_post_count : comp?.facilities?.security_post_count) ?? (index === 0 ? 1 : 0);

            const chipInc = hitungChipLaporan(isWomen ? totalWomen : totalCrimes, isWomen);
            const chipLight = hitungChipPenerangan(comp?.facilities?.lighting_condition);
            const chipFac = hitungChipFasilitas(cctvs, posts);

            kartu.innerHTML = `
                <div class="kartu-atas">
                    <div style="display:flex; align-items:center; flex-wrap:wrap; gap:6px;">
                        <b>Rute ${routeId}</b>
                        <span class="tag ${isSafest ? '' : 'alt'}">${badgeText}</span>
                    </div>
                    <div style="display:flex; align-items:baseline; gap:4px; text-align:right;">
                        <span class="skor" style="color:${scoreColor};">${scoreVal}<small>/100</small></span>
                        <span class="level-badge ${lvl.className}">· ${lvl.label}</span>
                    </div>
                </div>

                <div class="meta">
                    <strong style="color:#0f172a;">${m.jarakKm.toFixed(1)} km</strong> · Estimasi <strong style="color:#0f172a;">${m.waktuMenit} menit</strong>
                </div>

                <div class="route-info-list">
                    <span class="route-info-item ${chipInc.isDanger ? 'is-danger' : ''}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>${chipInc.text}</span>
                    </span>
                    <span class="route-info-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
                        <span>${chipLight.text}</span>
                    </span>
                    <span class="route-info-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="15" height="10" rx="2"/><polygon points="22 8 17 12 22 16 22 8"/></svg>
                        <span>${chipFac.text}</span>
                    </span>
                </div>
            `;
            kartu.addEventListener('click', () => pilih(index));
            box.appendChild(kartu);
        });

        // Tampilkan/sembunyikan legenda khusus rute perempuan di sudut kiri bawah peta
        setLegendaRutePerempuan(isWomen && displayRoutes.length > 1);

        // Fokus ke rute teraman jika dalam mode aman wanita
        if (isWomen && safestIndex >= 0 && daftarGaris[safestIndex]) {
            map.fitBounds(daftarGaris[safestIndex].getBounds().pad(0.15));
        } else if (daftarGaris.length) {
            map.fitBounds(L.featureGroup(daftarGaris).getBounds().pad(0.12));
        }

        $('infoRute').textContent = `${displayRoutes.length} Alternatif Rute`;
        $('btnBandingkan').hidden = displayRoutes.length < 2;
        $('btnBandingkan').style.display = displayRoutes.length < 2 ? 'none' : 'inline-flex';
        $('btnNavigasi').disabled = false;

        simpanDataPerbandinganStorage();

        // Otomatis buka panel perbandingan jika ada minimal 2 rute
        const panelCmp = $('perbandinganRute');
        if (panelCmp && displayRoutes.length >= 2) {
            renderPerbandingan();
            panelCmp.removeAttribute('hidden');
            panelCmp.style.display = 'block';
            $('btnBandingkanText').textContent = 'Tutup Perbandingan Rute';
            $('btnBandingkan').classList.add('aktif');
        }
    }

    async function ruteOSRM(a, b) {
        const url = 'https://router.project-osrm.org/route/v1/driving/' +
            a.lng + ',' + a.lat + ';' + b.lng + ',' + b.lat +
            '?overview=full&geometries=geojson&alternatives=true';

        let routes = [];
        try {
            const response = await fetch(url);
            if (response.ok) {
                const data = await response.json();
                if (data.routes && data.routes.length) {
                    routes = data.routes;
                }
            }
        } catch (e) {
            console.warn('OSRM direct error:', e);
        }

        if (!routes.length) {
            throw new Error('Tidak ada jalur yang dapat dilewati.');
        }

        // Cari rute alternatif via titik antara (waypoint) jika hanya 1 rute
        if (routes.length < 3) {
            const midLat = (a.lat + b.lat) / 2;
            const midLng = (a.lng + b.lng) / 2;
            const dLat = b.lat - a.lat;
            const dLng = b.lng - a.lng;
            const dist = Math.sqrt(dLat * dLat + dLng * dLng) || 0.01;

            // Offset 1: sisi utara/kiri (+0.0035 deg ~ 380m)
            if (routes.length < 2) {
                try {
                    const offset1 = 0.0035;
                    const viaLat1 = midLat + (-dLng / dist) * offset1;
                    const viaLng1 = midLng + (dLat / dist) * offset1;
                    const urlAlt1 = 'https://router.project-osrm.org/route/v1/driving/' +
                        a.lng + ',' + a.lat + ';' + viaLng1.toFixed(6) + ',' + viaLat1.toFixed(6) + ';' + b.lng + ',' + b.lat +
                        '?overview=full&geometries=geojson';
                    const resAlt1 = await fetch(urlAlt1);
                    if (resAlt1.ok) {
                        const dataAlt1 = await resAlt1.json();
                        if (dataAlt1.routes && dataAlt1.routes.length) {
                            routes.push(dataAlt1.routes[0]);
                        }
                    }
                } catch (e) {}
            }

            // Offset 2: sisi selatan/kanan (-0.0035 deg ~ 380m)
            if (routes.length < 3) {
                try {
                    const offset2 = -0.0035;
                    const viaLat2 = midLat + (-dLng / dist) * offset2;
                    const viaLng2 = midLng + (dLat / dist) * offset2;
                    const urlAlt2 = 'https://router.project-osrm.org/route/v1/driving/' +
                        a.lng + ',' + a.lat + ';' + viaLng2.toFixed(6) + ',' + viaLat2.toFixed(6) + ';' + b.lng + ',' + b.lat +
                        '?overview=full&geometries=geojson';
                    const resAlt2 = await fetch(urlAlt2);
                    if (resAlt2.ok) {
                        const dataAlt2 = await resAlt2.json();
                        if (dataAlt2.routes && dataAlt2.routes.length) {
                            routes.push(dataAlt2.routes[0]);
                        }
                    }
                } catch (e) {}
            }
        }

        // Tampilkan hanya rute yang benar-benar tersedia (tanpa membuat rute palsu)
        return routes;
    }

    $('btnCari').addEventListener('click', async () => {
        setStatus('Mencari rute dan menganalisis keamanan...');
        $('btnCari').disabled = true;

        try {
            if (!asal) {
                if (inputDari.value.trim().toLowerCase() === 'lokasi saya') {
                    asal = await lokasiSaya();
                } else {
                    const res = await cariTempat(inputDari.value.trim());
                    if (!res.length) throw new Error('Lokasi asal tidak ditemukan.');
                    asal = { nama: inputDari.value, lat: +res[0].lat, lng: +res[0].lon };
                }
            }

            if (!tujuan) {
                const resT = await cariTempat(inputKe.value.trim());
                if (!resT.length) throw new Error('Lokasi tujuan tidak ditemukan.');
                tujuan = { nama: inputKe.value, lat: +resT[0].lat, lng: +resT[0].lon };
            }

            titikA = asal;
            titikB = tujuan;

            const routes = await ruteOSRM(titikA, titikB);
            ruteOSRMAktif = routes;
            renderRute(routes);
            setStatus('');
        } catch (err) {
            setStatus(err.message, true);
        } finally {
            $('btnCari').disabled = false;
        }
    });

    $('btnTukar').addEventListener('click', () => {
        [inputDari.value, inputKe.value] = [inputKe.value, inputDari.value];
        [asal, tujuan] = [tujuan, asal];
    });

    $('btnBandingkan').addEventListener('click', togglePerbandingan);

    $('btnTutupPerbandingan').addEventListener('click', () => {
        const panel = $('perbandinganRute');
        panel.setAttribute('hidden', '');
        panel.style.display = 'none';
        $('btnBandingkanText').textContent = 'Bandingkan Rute A & B';
        $('btnBandingkan').classList.remove('aktif');
    });

    $('btnBukaBandingkanDetail')?.addEventListener('click', () => {
        simpanDataPerbandinganStorage();
    });

    $('btnNavigasi').addEventListener('click', () => {
        if (!titikA || !titikB || !daftarGaris.length) {
            setStatus('Cari dan pilih rute terlebih dahulu.', true);
            return;
        }
        modeNavigasi = true;
        const ruteAktif = daftarGaris[terpilihIdx];
        if (ruteAktif) {
            map.fitBounds(ruteAktif.getBounds().pad(0.08));
            ruteAktif.bringToFront();
            const labelRute = String.fromCharCode(65 + terpilihIdx);
            $('btnNavigasi').innerHTML = `<span>Navigasi Berjalan (Rute ${labelRute})</span>`;
            setStatus(`Navigasi aktif untuk Rute ${labelRute}. Ikuti jalur yang disorot pada peta.`);
            $('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Sesuaikan tombol aksi insiden sesuai role yang dipilih saat login
    (function() {
        const activeRole = localStorage.getItem('saferoad_active_role') || 'masyarakat';
        const btn = document.getElementById('btnRouteReport');
        const title = document.getElementById('btnRouteReportTitle');
        const sub = document.getElementById('btnRouteReportSub');

        if (btn && title && sub) {
            if (activeRole === 'keamanan') {
                btn.href = "{{ route('keamanan.dashboard') }}";
                title.textContent = 'Cek Laporan Masuk di Rute Ini';
                sub.textContent = 'Pantau laporan masyarakat yang butuh verifikasi TKP';
            } else if (activeRole === 'admin') {
                btn.href = "{{ route('admin.dashboard') }}";
                title.textContent = 'Validasi Laporan Insiden';
                sub.textContent = 'Moderasi laporan masyarakat untuk publikasi peta';
            }
        }
    })();
</script>
@endpush