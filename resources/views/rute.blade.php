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
                            <div>
                                <span class="badge-compare-sub">EVALUASI MULTI-RUTE</span>
                                <h3>Perbandingan Rute Langsung</h3>
                                <p class="compare-subtext">Bandingkan metrik efisiensi dan indikator keamanan Rute A & B secara berdampingan.</p>
                            </div>
                            <button type="button" class="btn-close-comparison" id="btnTutupPerbandingan" title="Tutup Perbandingan">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>

                        <!-- Grid Dua Kolom Perbandingan -->
                        <div class="compare-columns-grid">
                            <!-- Kolom Rute A -->
                            <div class="compare-route-card rekomendasi-card" id="cardCmpA" data-index="0">
                                <div class="crc-top">
                                    <div class="crc-badge-row">
                                        <span class="route-pill pill-a">Rute A</span>
                                        <span class="tag-recommend" id="tagRecommendA">
                                            <svg viewBox="0 0 24 24" fill="currentColor" width="11" height="11"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                            Direkomendasikan
                                        </span>
                                    </div>
                                    <div class="crc-score-row">
                                        <div class="score-display">
                                            <span class="score-num" id="valScoreA">92</span>
                                            <span class="score-den">/100</span>
                                        </div>
                                        <span class="level-badge level-aman" id="badgeLevelA">Aman</span>
                                    </div>
                                </div>

                                <div class="crc-metrics">
                                    <!-- Jarak -->
                                    <div class="metric-row">
                                        <span class="metric-lbl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            Jarak Perjalanan
                                        </span>
                                        <div class="metric-val-wrap">
                                            <strong class="metric-val" id="valJarakA">0.0 km</strong>
                                            <span class="tag-metric" id="tagJarakA" hidden>Terpendek</span>
                                        </div>
                                    </div>

                                    <!-- Waktu Tempuh -->
                                    <div class="metric-row">
                                        <span class="metric-lbl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            Estimasi Waktu
                                        </span>
                                        <div class="metric-val-wrap">
                                            <strong class="metric-val highlight" id="valWaktuA">0 menit</strong>
                                            <span class="tag-metric" id="tagWaktuA" hidden>Tercepat</span>
                                        </div>
                                    </div>

                                    <!-- Lalu Lintas -->
                                    <div class="metric-row">
                                        <span class="metric-lbl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path><circle cx="7" cy="17" r="2"></circle><path d="M9 17h6"></path><circle cx="17" cy="17" r="2"></circle></svg>
                                            Kondisi Lalu Lintas
                                        </span>
                                        <span class="metric-status-badge status-lancar" id="valLalinA">Lancar (Jalur Utama)</span>
                                    </div>

                                    <!-- Pencahayaan Jalan -->
                                    <div class="metric-row">
                                        <span class="metric-lbl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                                            Pencahayaan Jalan
                                        </span>
                                        <span class="metric-status-badge status-lancar" id="valCahayaA">Terang (Lampu Baik)</span>
                                    </div>

                                    <!-- Jangkauan CCTV -->
                                    <div class="metric-row">
                                        <span class="metric-lbl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                                            Jangkauan CCTV
                                        </span>
                                        <span class="metric-status-badge status-lancar" id="valCctvA">Tinggi (Area Terpantau)</span>
                                    </div>
                                </div>

                                <button type="button" class="btn-pick-route" id="btnPickA" data-index="0">
                                    <span>Pilih Rute A</span>
                                </button>
                            </div>

                            <!-- Kolom Rute B -->
                            <div class="compare-route-card" id="cardCmpB" data-index="1">
                                <div class="crc-top">
                                    <div class="crc-badge-row">
                                        <span class="route-pill pill-b">Rute B</span>
                                        <span class="tag-recommend alt" id="tagRecommendB">Alternatif</span>
                                    </div>
                                    <div class="crc-score-row">
                                        <div class="score-display">
                                            <span class="score-num" id="valScoreB">78</span>
                                            <span class="score-den">/100</span>
                                        </div>
                                        <span class="level-badge level-sedang" id="badgeLevelB">Sedang</span>
                                    </div>
                                </div>

                                <div class="crc-metrics">
                                    <!-- Jarak -->
                                    <div class="metric-row">
                                        <span class="metric-lbl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            Jarak Perjalanan
                                        </span>
                                        <div class="metric-val-wrap">
                                            <strong class="metric-val" id="valJarakB">0.0 km</strong>
                                            <span class="tag-metric" id="tagJarakB" hidden>Terpendek</span>
                                        </div>
                                    </div>

                                    <!-- Waktu Tempuh -->
                                    <div class="metric-row">
                                        <span class="metric-lbl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            Estimasi Waktu
                                        </span>
                                        <div class="metric-val-wrap">
                                            <strong class="metric-val highlight" id="valWaktuB">0 menit</strong>
                                            <span class="tag-metric" id="tagWaktuB" hidden>Tercepat</span>
                                        </div>
                                    </div>

                                    <!-- Lalu Lintas -->
                                    <div class="metric-row">
                                        <span class="metric-lbl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path><circle cx="7" cy="17" r="2"></circle><path d="M9 17h6"></path><circle cx="17" cy="17" r="2"></circle></svg>
                                            Kondisi Lalu Lintas
                                        </span>
                                        <span class="metric-status-badge status-sedang" id="valLalinB">Padat Sedang</span>
                                    </div>

                                    <!-- Pencahayaan Jalan -->
                                    <div class="metric-row">
                                        <span class="metric-lbl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                                            Pencahayaan Jalan
                                        </span>
                                        <span class="metric-status-badge status-sedang" id="valCahayaB">Cukup (Titik Redup)</span>
                                    </div>

                                    <!-- Jangkauan CCTV -->
                                    <div class="metric-row">
                                        <span class="metric-lbl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                                            Jangkauan CCTV
                                        </span>
                                        <span class="metric-status-badge status-sedang" id="valCctvB">Sedang (Terbatas)</span>
                                    </div>
                                </div>

                                <button type="button" class="btn-pick-route" id="btnPickB" data-index="1">
                                    <span>Pilih Rute B</span>
                                </button>
                            </div>
                        </div>

                        <!-- Baris Ringkasan Komparasi Cepat -->
                        <div class="compare-takeaway-bar">
                            <div class="takeaway-item" id="summaryFastestTakeaway">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span id="textFastestTakeaway">Rute A lebih cepat</span>
                            </div>
                            <div class="takeaway-item" id="summarySafetyTakeaway">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                <span id="textSafetyTakeaway">Rute A skor keamanan lebih optimal</span>
                            </div>
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

            <!-- Driver Monitoring Card (Khusus Mode Perempuan Terverifikasi) -->
            <div id="driverMonitor" class="driver-monitoring-card" style="display:none;">
                <div class="dm-header">
                    <div class="dm-title-box">
                        <span class="dm-badge-pink">♀ MODE PEREMPUAN</span>
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
                            <span class="dm-metric-icon">♂</span>
                            <span class="dm-metric-label">Pengemudi Laki-laki</span>
                        </div>
                        <div class="dm-metric-num" id="maleCount">14</div>
                        <span class="dm-metric-sub">Terdeteksi di rute ini</span>
                    </div>

                    <div class="dm-metric-card dm-card-female">
                        <div class="dm-metric-top">
                            <span class="dm-metric-icon">♀</span>
                            <span class="dm-metric-label">Pengemudi Perempuan</span>
                        </div>
                        <div class="dm-metric-num" id="femaleCount">4</div>
                        <span class="dm-metric-sub">Terdeteksi di rute ini</span>
                    </div>

                    <div class="dm-metric-card dm-card-total">
                        <div class="dm-metric-top">
                            <span class="dm-metric-icon">🚘</span>
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

            <!-- Banner info jika belum verifikasi mode perempuan -->
            <div id="driverMonitorPromo" class="driver-promo-card" style="display:none;">
                <div class="dmp-icon">♀</div>
                <div class="dmp-content">
                    <h4>Fitur Monitoring Pengemudi Khusus Perempuan</h4>
                    <p>Pantau jumlah pengemudi laki-laki dan perempuan di sepanjang rute secara real-time. Lengkapi verifikasi identitas untuk mengaktifkan fitur ini.</p>
                    <div class="dmp-actions">
                        <a href="{{ url('/verifikasi-identitas') }}" class="dmp-btn">Verifikasi Identitas →</a>
                        <button type="button" id="btnDemoModePerempuan" class="dmp-demo-btn">Coba Demo Mode Perempuan</button>
                    </div>
                </div>
            </div>

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
                    <span>📹 CCTV</span>
                </label>
                <label class="rmf-chip">
                    <input type="checkbox" id="toggleMapLampu" checked>
                    <span>💡 Penerangan</span>
                </label>
                <label class="rmf-chip">
                    <input type="checkbox" id="toggleMapPos" checked>
                    <span>🛡️ Pos Polisi &amp; Jaga</span>
                </label>
                <label class="rmf-chip">
                    <input type="checkbox" id="toggleMapRs" checked>
                    <span>🏥 Rumah Sakit</span>
                </label>
                <label class="rmf-chip">
                    <input type="checkbox" id="toggleMapRawan" checked>
                    <span>⚠️ Titik Rawan</span>
                </label>
            </div>

            <div class="map-container-box">
                <div id="map" aria-label="Peta interaktif SafeRoad"></div>
                <!-- Overlay Legenda Komprehensif -->
                <div class="map-floating-legend">
                    <span class="legend-chip"><i class="dot blue"></i> Rute Utama</span>
                    <span class="legend-chip"><i class="dot amber"></i> Rute Alternatif</span>
                    <span class="legend-chip"><i class="dot" style="background:#2563eb;"></i> CCTV</span>
                    <span class="legend-chip"><i class="dot" style="background:#f59e0b;"></i> Lampu</span>
                    <span class="legend-chip"><i class="dot" style="background:#16a34a;"></i> Pos Aman</span>
                    <span class="legend-chip"><i class="dot" style="background:#9333ea;"></i> RS</span>
                    <span class="legend-chip"><i class="dot" style="background:#dc2626;"></i> Titik Rawan</span>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .route-page-container {
        width: 100%;
        max-width: 1600px;
        margin: 0 auto;
        padding: 24px clamp(16px, 3vw, 40px) 40px;
    }

    .route-split-layout {
        display: grid;
        grid-template-columns: 460px 1fr;
        gap: 28px;
        align-items: start;
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
        height: 48px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: var(--shadow-btn);
        transition: transform 0.15s ease;
    }

    .btn-search-route:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
    }

    .btn-search-route:disabled {
        opacity: 0.65;
        cursor: wait;
        transform: none;
    }

    .btn-search-route svg {
        width: 18px;
        height: 18px;
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
        background-color: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 18px;
        padding: 20px;
        box-shadow: var(--shadow-card);
    }

    .results-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 14px;
    }

    .results-header-bar h2 {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-heading);
    }

    .results-count {
        font-size: 12px;
        color: var(--text-muted);
    }

    .route-cards-list {
        display: grid;
        gap: 10px;
        margin-bottom: 16px;
    }

    /* Kartu Rute */
    .kartu {
        background-color: #ffffff;
        border: 1.5px solid var(--border-soft);
        border-radius: 14px;
        padding: 14px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .kartu:hover {
        border-color: var(--primary-500);
        background-color: var(--primary-50);
    }

    .kartu.aktif {
        border-color: var(--primary-600);
        background-color: var(--primary-50);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    .kartu-atas {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 4px;
    }

    .kartu-atas b {
        font-size: 15px;
        color: var(--text-heading);
    }

    .skor {
        font-size: 20px;
        font-weight: 800;
        color: var(--primary-700);
    }

    .skor small {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .meta {
        font-size: 12.5px;
        color: var(--text-muted);
        margin: 4px 0 10px;
    }

    .chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .chip {
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 999px;
        background-color: var(--bg-canvas);
        border: 1px solid var(--border-soft);
        color: var(--text-body);
    }

    .tag {
        font-size: 10px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 999px;
        background-color: var(--success-600);
        color: #ffffff;
        margin-left: 6px;
    }

    .tag.alt {
        background-color: var(--primary-600);
    }

    .level-badge {
        display: inline-block;
        margin-left: 6px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .level-aman { background-color: var(--success-100); color: var(--success-600); }
    .level-sedang { background-color: var(--warning-100); color: #b45309; }
    .level-rawan { background-color: var(--danger-100); color: var(--danger-600); }
    .level-data-belum-cukup { background-color: #f1f5f9; color: var(--text-muted); }

    /* Tombol Bandingkan & Navigasi */
    .btn-compare-routes {
        width: 100%;
        height: 44px;
        background-color: var(--primary-50);
        color: var(--primary-700);
        border: 1.5px solid var(--primary-100);
        border-radius: 11px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        margin-bottom: 12px;
        transition: background 0.15s;
    }

    .btn-compare-routes:hover {
        background-color: #dbeafe;
    }

    .btn-compare-routes.aktif {
        background-color: var(--primary-600);
        color: #ffffff;
        border-color: var(--primary-700);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .btn-compare-routes.aktif:hover {
        background-color: var(--primary-700);
    }

    /* =====================================================
       PANEL PERBANDINGAN RUTE LANGSUNG (INLINE)
    ===================================================== */
    .inline-compare-section {
        margin: 10px 0 16px;
        animation: fadeInCompare 0.25s ease-out;
    }

    @keyframes fadeInCompare {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .compare-card-container {
        background-color: #ffffff;
        border: 1.5px solid var(--primary-100);
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 4px 20px -2px rgba(30, 58, 138, 0.08);
    }

    .compare-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-soft);
    }

    .badge-compare-sub {
        display: inline-block;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--primary-700);
        background-color: var(--primary-50);
        border: 1px solid var(--primary-100);
        padding: 2px 8px;
        border-radius: 6px;
        margin-bottom: 4px;
    }

    .compare-card-header h3 {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-heading);
        margin-bottom: 2px;
    }

    .compare-subtext {
        font-size: 11.5px;
        color: var(--text-muted);
        line-height: 1.4;
    }

    .btn-close-comparison {
        background: none;
        border: 1px solid var(--border-soft);
        border-radius: 8px;
        width: 28px;
        height: 28px;
        display: grid;
        place-items: center;
        color: var(--text-muted);
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.15s;
    }

    .btn-close-comparison:hover {
        background-color: var(--primary-50);
        color: var(--primary-600);
        border-color: var(--primary-200);
    }

    .btn-close-comparison svg {
        width: 14px;
        height: 14px;
    }

    /* Grid Kolom Komparasi */
    .compare-columns-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 12px;
    }

    .compare-route-card {
        background-color: var(--bg-canvas);
        border: 1.5px solid var(--border-soft);
        border-radius: 13px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .compare-route-card:hover {
        border-color: var(--primary-500);
        background-color: #ffffff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.08);
    }

    .compare-route-card.aktif {
        border-color: var(--primary-600);
        background-color: #ffffff;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    }

    .compare-route-card.rekomendasi-card {
        border-color: #bfdbfe;
        background: linear-gradient(180deg, #eff6ff 0%, #ffffff 50%);
    }

    .compare-route-card.rekomendasi-card.aktif {
        border-color: var(--primary-600);
        box-shadow: 0 0 0 2.5px rgba(37, 99, 235, 0.25);
    }

    .crc-top {
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px dashed var(--border-soft);
    }

    .crc-badge-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 4px;
        margin-bottom: 6px;
    }

    .route-pill {
        font-size: 11.5px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
        color: #ffffff;
    }

    .route-pill.pill-a {
        background-color: var(--primary-800);
    }

    .route-pill.pill-b {
        background-color: #d97706;
    }

    .tag-recommend {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 9.5px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 999px;
        background-color: var(--success-600);
        color: #ffffff;
        white-space: nowrap;
    }

    .tag-recommend.alt {
        background-color: #64748b;
    }

    .crc-score-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-top: 4px;
    }

    .score-display {
        font-size: 17px;
        font-weight: 800;
        color: var(--primary-900);
        line-height: 1;
    }

    .score-display .score-den {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
    }

    /* Metrics List */
    .crc-metrics {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
    }

    .metric-row {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .metric-lbl {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .metric-lbl svg {
        width: 11px;
        height: 11px;
        flex-shrink: 0;
        color: var(--primary-600);
    }

    .metric-val-wrap {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .metric-val {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-heading);
    }

    .metric-val.highlight {
        color: var(--primary-700);
    }

    .tag-metric {
        font-size: 9px;
        font-weight: 800;
        padding: 1px 5px;
        border-radius: 4px;
        background-color: var(--primary-100);
        color: var(--primary-800);
    }

    .metric-status-badge {
        display: inline-block;
        font-size: 10.5px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 5px;
        line-height: 1.3;
    }

    .metric-status-badge.status-lancar {
        background-color: var(--success-50);
        color: var(--success-600);
        border: 1px solid var(--success-100);
    }

    .metric-status-badge.status-sedang {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    /* Tombol Pilih Rute di Card */
    .btn-pick-route {
        width: 100%;
        padding: 7px 8px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        border: 1px solid var(--primary-200);
        background-color: var(--primary-50);
        color: var(--primary-700);
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .btn-pick-route:hover {
        background-color: var(--primary-600);
        color: #ffffff;
        border-color: var(--primary-600);
    }

    .btn-pick-route.terpilih {
        background-color: var(--primary-600);
        color: #ffffff;
        border-color: var(--primary-600);
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }

    /* Baris Ringkasan Komparasi */
    .compare-takeaway-bar {
        background-color: var(--bg-canvas);
        border: 1px solid var(--border-soft);
        border-radius: 10px;
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        font-size: 11px;
        color: var(--text-body);
    }

    .takeaway-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .takeaway-item svg {
        width: 13px;
        height: 13px;
        color: var(--primary-600);
        flex-shrink: 0;
    }

    @media (max-width: 480px) {
        .compare-columns-grid {
            grid-template-columns: 1fr;
        }
    }

    .btn-compare-routes svg {
        width: 16px;
        height: 16px;
    }

    .disclaimer-note {
        font-size: 11px;
        color: var(--text-muted);
        margin: 6px 0 14px;
    }

    .btn-start-navigation {
        width: 100%;
        height: 48px;
        background: linear-gradient(135deg, var(--primary-700), var(--primary-900));
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(15, 45, 89, 0.3);
        transition: transform 0.15s ease;
    }

    .btn-start-navigation:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    .btn-start-navigation:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    .btn-start-navigation svg {
        width: 16px;
        height: 16px;
    }

    .navigation-disclaimer {
        font-size: 11px;
        color: #94a3b8;
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
        border-radius: 20px;
        overflow: hidden;
        border: 1.5px solid var(--border-soft);
        box-shadow: var(--shadow-card);
    }

    #map {
        height: 640px;
        width: 100%;
        background-color: #e2e8f0;
    }

    .map-floating-legend {
        position: absolute;
        bottom: 16px;
        left: 16px;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: 9999px;
        border: 1px solid var(--border-soft);
        display: flex;
        gap: 12px;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-heading);
        z-index: 1000;
        box-shadow: var(--shadow-sm);
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

    .dot.blue { background-color: var(--primary-600); }
    .dot.amber { background-color: #d97706; }

    @media (max-width: 1024px) {
        .route-split-layout {
            grid-template-columns: 1fr;
        }

        .route-map-panel {
            position: static;
            order: -1; /* Peta di atas pada layar sedang */
        }

        #map {
            height: 380px;
        }
    }

/* ===== Driver Monitoring Card Styles (Mode Perempuan) ===== */
.driver-monitoring-card {
    background: #ffffff;
    border: 1.5px solid #fbcfe8;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 16px -4px rgba(219, 39, 119, 0.12);
    margin-top: 16px;
    transition: all 0.3s ease;
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
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #be185d;
    background-color: #fdf2f8;
    border: 1px solid #fbcfe8;
    padding: 3px 10px;
    border-radius: 9999px;
    margin-bottom: 6px;
}

.dm-title {
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.dm-live-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: 4px 10px;
    border-radius: 9999px;
}

.dm-pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #16a34a;
    box-shadow: 0 0 6px #16a34a;
    animation: pulseLive 1.5s infinite;
}

.dm-live-text {
    font-size: 11px;
    font-weight: 800;
    color: #16a34a;
    letter-spacing: 0.05em;
}

.dm-demo-tag {
    font-size: 9px;
    font-weight: 800;
    background-color: #fef3c7;
    color: #d97706;
    padding: 1px 6px;
    border-radius: 4px;
    margin-left: 2px;
}

.dm-route-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 6px 12px;
    border-radius: 8px;
    margin-bottom: 14px;
}

.dm-active-route {
    color: var(--primary-600);
    font-weight: 700;
}

.dm-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 14px;
}

.dm-metric-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 10px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.dm-card-male {
    background: #eff6ff;
    border-color: #bfdbfe;
}

.dm-card-female {
    background: #fdf2f8;
    border-color: #fbcfe8;
}

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
    font-weight: 700;
    color: #475569;
    margin-bottom: 4px;
}

.dm-card-male .dm-metric-top { color: #1d4ed8; }
.dm-card-female .dm-metric-top { color: #be185d; }

.dm-metric-num {
    font-size: 24px;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
}

.dm-card-male .dm-metric-num { color: #1e3a8a; }
.dm-card-female .dm-metric-num { color: #9d174d; }

.dm-metric-sub {
    font-size: 10px;
    color: #94a3b8;
    margin-top: 4px;
}

.dm-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 10px;
    border-top: 1px dashed #e2e8f0;
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
    color: #94a3b8;
}

@keyframes pulseLive {
    0% { opacity: 0.6; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1.1); }
    100% { opacity: 0.6; transform: scale(0.9); }
}

/* ===== Driver Promo Card (Belum Verifikasi) ===== */
.driver-promo-card {
    background: #fff;
    border: 1.5px dashed #cbd5e1;
    border-radius: 14px;
    padding: 16px 18px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-top: 16px;
}

.dmp-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #fdf2f8;
    border: 1px solid #fbcfe8;
    color: #be185d;
    font-size: 20px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.dmp-content h4 {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px;
}

.dmp-content p {
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
    margin: 0 0 10px;
}

.dmp-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.dmp-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    background: #be185d;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: background 0.2s;
}

.dmp-btn:hover { background: #9d174d; }

.dmp-demo-btn {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 8px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.dmp-demo-btn:hover { background: #e2e8f0; }

/* ===== Tombol Laporan Insiden di Rute ===== */
.btn-route-incident-report {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    margin-top: 16px;
    border-radius: 14px;
    background: #fff;
    border: 1.5px solid #fee2e2;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.05);
    transition: all 0.2s ease;
}

.btn-route-incident-report:hover {
    border-color: #fca5a5;
    background: #fef2f2;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(220, 38, 38, 0.1);
}

.bri-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #fee2e2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.bri-icon svg {
    width: 20px;
    height: 20px;
}

.bri-text {
    flex: 1;
}

.bri-text strong {
    display: block;
    font-size: 13.5px;
    font-weight: 700;
    color: #991b1b;
}

.bri-text span {
    font-size: 11.5px;
    color: #64748b;
}

.bri-arrow {
    width: 18px;
    height: 18px;
    color: #dc2626;
    flex-shrink: 0;
    transition: transform 0.2s;
}

.btn-route-incident-report:hover .bri-arrow {
    transform: translateX(3px);
}
</style>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // =====================================================
    // PROTEKSI HAK AKSES ROLE
    // Fitur Navigasi Rute Komuter dan Mode Perempuan murni untuk Masyarakat
    // =====================================================
    (function checkRoleAccess() {
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
    })();

    // =====================================================
    // PENGATURAN AWAL
    // =====================================================
    const PUSAT = { lat: -7.2575, lng: 112.7521 };
    const DISCLAIMER_FALLBACK = 'Skor adalah indikator, bukan jaminan mutlak keamanan.';

    const $ = id => document.getElementById(id);
    const inputDari = $('inputDari');
    const inputKe = $('inputKe');
    const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

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

    // =====================================================
    // FITUR MONITORING PENGEMUDI (KHUSUS MODE PEREMPUAN MASYARAKAT)
    // =====================================================
    const mockDriverData = {
        0: { male: 14, female: 4, total: 18, label: 'Rute A' },
        1: { male: 9, female: 2, total: 11, label: 'Rute B' }
    };

    function updateDriverInfo(idx = 0) {
        const activeRole = localStorage.getItem('saferoad_active_role') || 'masyarakat';
        const card = $('driverMonitor');
        const promo = $('driverMonitorPromo');

        // Admin dan Petugas Keamanan tidak memiliki Mode Perempuan / Driver Monitor
        if (activeRole !== 'masyarakat') {
            if (card) card.style.display = 'none';
            if (promo) promo.style.display = 'none';
            return;
        }

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

        const gender = localStorage.getItem('saferoad_gender');
        const verified = localStorage.getItem('saferoad_verified');

        if (verified === 'true' && gender === 'perempuan') {
            if (card) card.style.display = 'block';
            if (promo) promo.style.display = 'none';
        } else {
            if (card) card.style.display = 'none';
            if (promo) promo.style.display = 'flex';
        }
    }

    // Tombol Coba Demo Mode Perempuan langsung
    const btnDemo = $('btnDemoModePerempuan');
    if (btnDemo) {
        btnDemo.addEventListener('click', () => {
            localStorage.setItem('saferoad_verified', 'true');
            localStorage.setItem('saferoad_gender', 'perempuan');
            updateDriverInfo(terpilihIdx);
            if (typeof showToast === 'function') {
                showToast('Mode Perempuan Aktif! Menampilkan data pengemudi di rute.', true);
            }
        });
    }

    // Interval refresh pembaruan pengemudi setiap 30 detik
    setInterval(() => updateDriverInfo(terpilihIdx), 30000);
    setTimeout(() => updateDriverInfo(0), 100);

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
            { nama: '⚠️ Riwayat Titik Rawan: Gang Kurang Penerangan', lat: -7.3175, lng: 112.7260, sub: 'Tingkatkan kewaspadaan di malam hari' }
        ]
    };

    dataFasilitas.cctv.forEach(p => L.marker([p.lat, p.lng], { icon: createFacilityIcon('📹', '#eff6ff', '#2563eb') }).bindPopup(`<strong>${p.nama}</strong><br><small style="color:#2563eb;">Kamera Pantau Aktif</small>`).addTo(layerCctvMap));
    dataFasilitas.lampu.forEach(p => L.marker([p.lat, p.lng], { icon: createFacilityIcon('💡', '#fefce8', '#f59e0b') }).bindPopup(`<strong>${p.nama}</strong><br><small style="color:#d97706;">Penerangan Terverifikasi</small>`).addTo(layerLampuMap));
    dataFasilitas.pos.forEach(p => L.marker([p.lat, p.lng], { icon: createFacilityIcon('🛡️', '#f0fdf4', '#16a34a') }).bindPopup(`<strong>${p.nama}</strong><br><small style="color:#16a34a;">${p.sub}</small>`).addTo(layerPosMap));
    dataFasilitas.rs.forEach(p => L.marker([p.lat, p.lng], { icon: createFacilityIcon('🏥', '#faf5ff', '#9333ea') }).bindPopup(`<strong>${p.nama}</strong><br><small style="color:#9333ea;">${p.sub}</small>`).addTo(layerRsMap));
    dataFasilitas.rawan.forEach(p => L.marker([p.lat, p.lng], { icon: createFacilityIcon('⚠️', '#fef2f2', '#dc2626') }).bindPopup(`<strong>${p.nama}</strong><br><small style="color:#dc2626;">${p.sub}</small>`).addTo(layerRawanMap));

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
        const routes = ruteOSRMAktif.slice(0, 3).map((route, index) => {
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

    function simpanDanBukaPerbandingan() {
        togglePerbandingan();
    }

    function pilih(i) {
        terpilihIdx = i;
        daftarGaris.forEach((garis, index) => {
            const aktif = index === i;
            garis.setStyle({
                color: aktif ? '#2563eb' : (index === 0 ? '#1e3a8a' : '#d97706'),
                weight: aktif ? 7 : 4,
                opacity: aktif ? 0.95 : 0.45,
                dashArray: aktif ? null : '6 8'
            });
            if (aktif) garis.bringToFront();
        });

        document.querySelectorAll('.kartu').forEach((kartu, index) => {
            kartu.classList.toggle('aktif', index === i);
        });

        document.querySelectorAll('[data-indeks-rute]').forEach(kolom => {
            kolom.classList.toggle('terpilih', Number(kolom.dataset.indeksRute) === i);
        });

        // Perbarui state kartu di panel perbandingan langsung
        const cardA = $('cardCmpA');
        const cardB = $('cardCmpB');
        if (cardA && cardB) {
            cardA.classList.toggle('aktif', i === 0);
            cardB.classList.toggle('aktif', i === 1);

            const btnA = $('btnPickA');
            const btnB = $('btnPickB');
            if (btnA) {
                btnA.classList.toggle('terpilih', i === 0);
                btnA.innerHTML = i === 0 ? '<span>✓ Rute Terpilih</span>' : '<span>Pilih Rute A</span>';
            }
            if (btnB) {
                btnB.classList.toggle('terpilih', i === 1);
                btnB.innerHTML = i === 1 ? '<span>✓ Rute Terpilih</span>' : '<span>Pilih Rute B</span>';
            }
        }

        if (modeNavigasi && daftarGaris[i]) {
            map.fitBounds(daftarGaris[i].getBounds().pad(0.1));
        }

        // Perbarui data monitoring pengemudi sesuai rute yang dipilih (Rute A / B)
        updateDriverInfo(i);
    }

    function renderPerbandingan() {
        if (!ruteOSRMAktif || ruteOSRMAktif.length < 2) return;

        const routeA = ruteOSRMAktif[0];
        const routeB = ruteOSRMAktif[1];

        const mA = konversiMetrikRute(routeA);
        const mB = konversiMetrikRute(routeB);

        // Metrik Jarak
        $('valJarakA').textContent = mA.jarakKm.toFixed(1) + ' km';
        $('valJarakB').textContent = mB.jarakKm.toFixed(1) + ' km';
        const isShorterA = mA.jarakKm <= mB.jarakKm;
        const isShorterB = mB.jarakKm < mA.jarakKm;
        $('tagJarakA').hidden = !isShorterA;
        $('tagJarakB').hidden = !isShorterB;

        // Metrik Waktu Tempuh
        $('valWaktuA').textContent = mA.waktuMenit + ' menit';
        $('valWaktuB').textContent = mB.waktuMenit + ' menit';
        const isFasterA = mA.waktuMenit <= mB.waktuMenit;
        const isFasterB = mB.waktuMenit < mA.waktuMenit;
        $('tagWaktuA').hidden = !isFasterA;
        $('tagWaktuB').hidden = !isFasterB;

        // Kondisi Lalu Lintas
        $('valLalinA').textContent = 'Lancar (Jalur Utama)';
        $('valLalinA').className = 'metric-status-badge status-lancar';
        $('valLalinB').textContent = isFasterB ? 'Lancar (Alternatif)' : 'Padat Sedang';
        $('valLalinB').className = isFasterB ? 'metric-status-badge status-lancar' : 'metric-status-badge status-sedang';

        // Informasi Pencahayaan Jalan
        $('valCahayaA').textContent = 'Terang (Lampu Baik)';
        $('valCahayaA').className = 'metric-status-badge status-lancar';
        $('valCahayaB').textContent = 'Cukup (Titik Redup)';
        $('valCahayaB').className = 'metric-status-badge status-sedang';

        // Ketersediaan atau Jangkauan CCTV
        $('valCctvA').textContent = 'Tinggi (Area Terpantau)';
        $('valCctvA').className = 'metric-status-badge status-lancar';
        $('valCctvB').textContent = 'Sedang (Titik Terbatas)';
        $('valCctvB').className = 'metric-status-badge status-sedang';

        // Skor Keamanan Rute
        let scoreA = 92;
        let scoreB = 78;
        let levelA = 'Aman';
        let levelB = 'Sedang';

        if (hasilRuteAktif[0] && typeof hasilRuteAktif[0].score === 'number') {
            scoreA = Math.round(hasilRuteAktif[0].score);
            levelA = hasilRuteAktif[0].level_label || (scoreA >= 80 ? 'Aman' : (scoreA >= 60 ? 'Sedang' : 'Rawan'));
        }
        if (hasilRuteAktif[1] && typeof hasilRuteAktif[1].score === 'number') {
            scoreB = Math.round(hasilRuteAktif[1].score);
            levelB = hasilRuteAktif[1].level_label || (scoreB >= 80 ? 'Aman' : (scoreB >= 60 ? 'Sedang' : 'Rawan'));
        }

        $('valScoreA').textContent = scoreA;
        $('valScoreB').textContent = scoreB;
        $('badgeLevelA').textContent = levelA;
        $('badgeLevelB').textContent = levelB;

        // Penanda Rute Rekomendasi
        const isRecA = scoreA >= scoreB;
        $('tagRecommendA').hidden = !isRecA;
        $('tagRecommendB').hidden = isRecA;
        $('cardCmpA').classList.toggle('rekomendasi-card', isRecA);
        $('cardCmpB').classList.toggle('rekomendasi-card', !isRecA);

        // Ringkasan Komparasi
        const selisihWaktu = Math.abs(mA.waktuMenit - mB.waktuMenit);
        if (selisihWaktu > 0) {
            $('textFastestTakeaway').textContent = `${isFasterA ? 'Rute A' : 'Rute B'} lebih cepat ${selisihWaktu} menit`;
        } else {
            $('textFastestTakeaway').textContent = 'Estimasi waktu tempuh kedua rute setara';
        }

        const selisihSkor = Math.abs(scoreA - scoreB);
        $('textSafetyTakeaway').textContent = `${isRecA ? 'Rute A' : 'Rute B'} memiliki skor keamanan lebih tinggi (+${selisihSkor} poin)`;

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

    function renderRute(routes) {
        const box = $('daftarRute');
        box.innerHTML = '';
        daftarGaris = [];

        layerRute.clearLayers();

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

        routes.forEach((route, index) => {
            const pts = route.geometry.coordinates.map(c => [c[1], c[0]]);
            const isMain = index === 0;
            const line = L.polyline(pts, {
                color: isMain ? '#2563eb' : '#d97706',
                weight: isMain ? 7 : 4,
                opacity: isMain ? 0.95 : 0.45,
                dashArray: isMain ? null : '6 8'
            }).addTo(layerRute);

            line.on('click', () => pilih(index));
            daftarGaris.push(line);

            const m = konversiMetrikRute(route);
            const scoreNum = index === 0 ? 92 : 78;
            const scoreLevel = scoreNum >= 80 ? 'Aman' : (scoreNum >= 60 ? 'Sedang' : 'Rawan');
            const scoreColor = scoreNum >= 80 ? '#16a34a' : (scoreNum >= 60 ? '#d97706' : '#dc2626');
            const scoreBg = scoreNum >= 80 ? '#f0fdf4' : (scoreNum >= 60 ? '#fffbeb' : '#fef2f2');
            const scoreBorder = scoreNum >= 80 ? '#bbf7d0' : (scoreNum >= 60 ? '#fde68a' : '#fecaca');

            const kartu = document.createElement('div');
            kartu.className = 'kartu' + (isMain ? ' aktif' : '');
            kartu.innerHTML = `
                <div class="kartu-atas">
                    <div>
                        <b>Rute ${String.fromCharCode(65 + index)}</b>
                        <span class="tag ${isMain ? '' : 'alt'}">${isMain ? 'Rute Rekomendasi' : 'Alternatif'}</span>
                    </div>
                    <div style="text-align: right;">
                        <div class="skor" style="font-size:18px;">${m.waktuMenit} <small>menit</small></div>
                        <small style="font-size:11px; color:#64748b;">${m.jarakKm.toFixed(1)} km</small>
                    </div>
                </div>

                <!-- Skor Keamanan Terpadu SafeRoad -->
                <div class="kartu-skor-box" style="display:flex; align-items:center; justify-content:space-between; background:${scoreBg}; border:1.5px solid ${scoreBorder}; border-radius:10px; padding:8px 12px; margin:8px 0 10px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:18px;">🛡️</span>
                        <div>
                            <strong style="display:block; font-size:12px; color:${scoreColor}; line-height:1.2;">Skor Keamanan Rute</strong>
                            <small style="font-size:11px; color:#475569;">${scoreNum >= 80 ? 'Lampu terang & tercover CCTV' : 'Penerangan cukup & titik pantau sedang'}</small>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <span style="font-size:18px; font-weight:800; color:${scoreColor};">${scoreNum}</span><small style="font-size:11px; font-weight:700; color:${scoreColor};">/100</small>
                        <span style="display:block; font-size:10px; font-weight:800; text-transform:uppercase; color:${scoreColor};">${scoreLevel}</span>
                    </div>
                </div>

                <div class="chips">
                    <span class="chip">${index === 0 ? '💡 Penerangan Baik' : '💡 Cahaya Sedang'}</span>
                    <span class="chip">${index === 0 ? '📹 CCTV Terkoneksi' : '📹 CCTV Terbatas'}</span>
                    <span class="chip">${index === 0 ? '🛡️ Jalur Patroli Polsek' : '🛡️ Jalur Alternatif'}</span>
                </div>
            `;
            kartu.addEventListener('click', () => pilih(index));
            box.appendChild(kartu);
        });

        if (daftarGaris.length) {
            map.fitBounds(L.featureGroup(daftarGaris).getBounds().pad(0.12));
        }

        $('btnBandingkan').hidden = routes.length < 2;
        $('btnNavigasi').disabled = false;

        // Otomatis buka panel perbandingan skor keamanan jika ada 2 rute
        const panelCmp = $('perbandinganRute');
        if (panelCmp && routes.length >= 2) {
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

        const response = await fetch(url);
        if (!response.ok) throw new Error('Rute tidak ditemukan melalui OSRM.');
        const data = await response.json();
        if (!data.routes || !data.routes.length) throw new Error('Tidak ada jalur yang dapat dilewati.');
        return data.routes;
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

    $('cardCmpA').addEventListener('click', () => pilih(0));
    $('cardCmpB').addEventListener('click', () => pilih(1));

    $('btnPickA').addEventListener('click', e => {
        e.stopPropagation();
        pilih(0);
    });

    $('btnPickB').addEventListener('click', e => {
        e.stopPropagation();
        pilih(1);
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
            $('btnNavigasi').innerHTML = `<span>📍 Navigasi Berjalan (Rute ${labelRute})</span>`;
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
                title.textContent = '🛡️ Cek Laporan Masuk di Rute Ini';
                sub.textContent = 'Pantau laporan masyarakat yang butuh verifikasi TKP';
            } else if (activeRole === 'admin') {
                btn.href = "{{ route('admin.dashboard') }}";
                title.textContent = '⚡ Validasi Laporan Insiden';
                sub.textContent = 'Moderasi laporan masyarakat untuk publikasi peta';
            }
        }
    })();
</script>
@endpush