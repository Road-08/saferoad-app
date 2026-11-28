@extends('layouts.app')

@section('title', 'Statistik & Rekap Keamanan · SafeRoad Administrator')

@section('content')
<div class="stat-page-container">
    <!-- Breadcrumb & Header -->
    <div class="stat-header-row">
        <a href="{{ route('admin.dashboard') }}" class="btn-back-dash">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    <header class="stat-hero-card">
        <div class="shc-left">
            <span class="shc-badge">LAPORAN ANALITIK RESMI</span>
            <h1>Statistik &amp; Rekapitulasi Keamanan Wilayah</h1>
            <p>Analisis tren kejadian, distribusi jenis gangguan keselamatan, serta evaluasi data rekapitulasi keselamatan perjalanan di seluruh kawasan terpantau SafeRoad.</p>
        </div>
        <div class="shc-right">
            <div class="shc-date-pill">
                <span>Periode: <strong>Bulan Ini (November {{ date('Y') }})</strong></span>
            </div>
            <button type="button" class="btn-print-stat" onclick="window.print()">
                <span>Cetak / Ekspor Laporan</span>
            </button>
        </div>
    </header>

    <!-- KPI Metric Summary Strip -->
    <div class="stat-kpi-grid">
        <div class="sk-card">
            <div class="sk-icon" style="background:#eff6ff; color:#2563eb;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <div>
                <h3>Total Laporan Terhimpun</h3>
                <strong class="sk-num">28</strong>
                <span class="sk-sub">+12% dibanding bulan lalu</span>
            </div>
        </div>
        <div class="sk-card">
            <div class="sk-icon" style="background:#eff6ff; color:#2563eb;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <h3>Rasio Validasi Positif</h3>
                <strong class="sk-num" style="color: #1e40af;">85.7%</strong>
                <span class="sk-sub">24 laporan terverifikasi</span>
            </div>
        </div>
        <div class="sk-card">
            <div class="sk-icon" style="background:#eff6ff; color:#2563eb;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <h3>Rata-rata Waktu Verifikasi</h3>
                <strong class="sk-num" style="color: #0f172a;">18 Menit</strong>
                <span class="sk-sub">Respon cepat petugas</span>
            </div>
        </div>
        <div class="sk-card">
            <div class="sk-icon" style="background:#eff6ff; color:#1e40af;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div>
                <h3>Indeks Keamanan Kawasan</h3>
                <strong class="sk-num" style="color: #1e40af;">91.4 / 100</strong>
                <span class="sk-sub">Status: Kondusif &amp; Terjaga</span>
            </div>
        </div>
    </div>

    <!-- Charts 2-Column Section -->
    <div class="stat-charts-grid">
        <!-- Chart 1: Distribusi Kategori Insiden -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h3>Distribusi Kategori Laporan Masuk</h3>
                    <small style="color: #64748b;">Persentase aduan berdasarkan tipe insiden</small>
                </div>
                <span class="chart-tag">Akumulatif</span>
            </div>
            <div class="cat-bar-list">
                <div class="cat-bar-item">
                    <div class="cbi-label">
                        <span>Pelecehan / Gangguan Keamanan</span>
                        <strong>35% (10 kejadian)</strong>
                    </div>
                    <div class="cbi-track"><div class="cbi-fill fill-red" style="width: 35%;"></div></div>
                </div>
                <div class="cat-bar-item">
                    <div class="cbi-label">
                        <span>Kondisi Jalan &amp; Lampu Mati</span>
                        <strong>28% (8 kejadian)</strong>
                    </div>
                    <div class="cbi-track"><div class="cbi-fill fill-amber" style="width: 28%;"></div></div>
                </div>
                <div class="cat-bar-item">
                    <div class="cbi-label">
                        <span>Tindak Kriminal / Begal</span>
                        <strong>22% (6 kejadian)</strong>
                    </div>
                    <div class="cbi-track"><div class="cbi-fill fill-orange" style="width: 22%;"></div></div>
                </div>
                <div class="cat-bar-item">
                    <div class="cbi-label">
                        <span>Kerusakan Fasilitas Keamanan</span>
                        <strong>15% (4 kejadian)</strong>
                    </div>
                    <div class="cbi-track"><div class="cbi-fill fill-blue" style="width: 15%;"></div></div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Tren Laporan Harian (Mingguan) -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h3>Frekuensi Laporan Masuk Harian</h3>
                    <small style="color: #64748b;">Aktivitas laporan selama 7 hari terakhir</small>
                </div>
                <span class="chart-tag">Mingguan</span>
            </div>
            <div class="timeline-bars-chart">
                <div class="tbc-col">
                    <div class="tbc-bar" style="height: 45%;"><span>3</span></div>
                    <small>Sen</small>
                </div>
                <div class="tbc-col">
                    <div class="tbc-bar" style="height: 60%;"><span>5</span></div>
                    <small>Sel</small>
                </div>
                <div class="tbc-col">
                    <div class="tbc-bar" style="height: 30%;"><span>2</span></div>
                    <small>Rab</small>
                </div>
                <div class="tbc-col">
                    <div class="tbc-bar" style="height: 80%;"><span>7</span></div>
                    <small>Kam</small>
                </div>
                <div class="tbc-col">
                    <div class="tbc-bar" style="height: 65%;"><span>5</span></div>
                    <small>Jum</small>
                </div>
                <div class="tbc-col">
                    <div class="tbc-bar active" style="height: 95%;"><span>9</span></div>
                    <small>Sab</small>
                </div>
                <div class="tbc-col">
                    <div class="tbc-bar" style="height: 50%;"><span>4</span></div>
                    <small>Min</small>
                </div>
            </div>
            <div class="peak-time-banner">
                <div>
                    <strong>Puncak Laporan: Sabtu Malam (20:00 - 23:30 WIB)</strong>
                    <p>Dominan laporan terkait jalan minim penerangan dan kepadatan pengendara akhir pekan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rekapitulasi per Sektor Wilayah -->
    <div class="sector-table-card">
        <div class="stc-header">
            <div>
                <h2>Rekapitulasi Data Keamanan per Sektor Wilayah</h2>
                <p>Pemetaan data laporan, jumlah fasilitas aktif, dan status kerawanan di tiap zona patroli Surabaya.</p>
            </div>
            <a href="{{ route('admin.peta') }}" class="btn-check-map-link">
                Buka di Peta Geospasial
            </a>
        </div>
        <div class="table-responsive">
            <table class="sector-table">
                <thead>
                    <tr>
                        <th>Sektor / Kecamatan</th>
                        <th>Jumlah Laporan</th>
                        <th>CCTV Terpasang</th>
                        <th>Lampu Terverifikasi</th>
                        <th>Tingkat Kerawanan</th>
                        <th>Status Rekomendasi Patroli</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Sektor Ketintang / Gayungan</strong></td>
                        <td>9 Laporan</td>
                        <td>8 Titik</td>
                        <td>94% Terang</td>
                        <td><span class="badge-status-low">Rendah</span></td>
                        <td><span class="status-patrol ok">Patroli Rutin Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Sektor Wonokromo</strong></td>
                        <td>11 Laporan</td>
                        <td>6 Titik</td>
                        <td>82% Cukup</td>
                        <td><span class="badge-status-med">Sedang</span></td>
                        <td><span class="status-patrol warn">Intensifkan Patroli Malam</span></td>
                    </tr>
                    <tr>
                        <td><strong>Sektor Jambangan</strong></td>
                        <td>5 Laporan</td>
                        <td>3 Titik</td>
                        <td>90% Terang</td>
                        <td><span class="badge-status-low">Rendah</span></td>
                        <td><span class="status-patrol ok">Kondusif</span></td>
                    </tr>
                    <tr>
                        <td><strong>Sektor Wonocolo / Siwalankerto</strong></td>
                        <td>3 Laporan</td>
                        <td>5 Titik</td>
                        <td>88% Cukup</td>
                        <td><span class="badge-status-low">Rendah</span></td>
                        <td><span class="status-patrol ok">Kondusif</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.stat-page-container {
    max-width: 1400px;
    margin: 24px auto 60px;
    padding: 0 clamp(16px, 3vw, 36px);
}

.stat-header-row { margin-bottom: 16px; }
.btn-back-dash {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--primary-700);
    padding: 6px 12px;
    border-radius: 8px;
    transition: background 0.15s;
    text-decoration: none;
}
.btn-back-dash:hover { background: #eff6ff; }
.btn-back-dash svg { width: 16px; height: 16px; }

.stat-hero-card {
    background: #1e3a8a;
    border-radius: 16px;
    padding: 28px clamp(20px, 4vw, 36px);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-card);
    flex-wrap: wrap;
}
.shc-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.18);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    color: #e0e7ff;
    margin-bottom: 8px;
}
.shc-left h1 { font-size: clamp(22px, 3vw, 28px); font-weight: 800; margin-bottom: 6px; }
.shc-left p { font-size: 13.5px; color: #cbd5e1; max-width: 650px; margin: 0; }

.shc-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
.shc-date-pill {
    padding: 6px 14px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    font-size: 12px;
    color: #e2e8f0;
}
.btn-print-stat {
    height: 38px;
    padding: 0 16px;
    background: #ffffff;
    color: #1e40af;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: background-color 0.15s;
}
.btn-print-stat:hover { background: #eff6ff; }

/* KPI Grid */
.stat-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.sk-card {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: var(--shadow-sm);
}
.sk-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.sk-card h3 { font-size: 12px; font-weight: 600; color: var(--text-muted); margin: 0 0 2px; }
.sk-num { font-size: 24px; font-weight: 800; color: var(--text-heading); display: block; line-height: 1.1; }
.sk-sub { font-size: 11px; color: var(--text-muted); margin-top: 2px; display: block; }

/* Charts Grid */
.stat-charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 24px;
}
@media (max-width: 860px) {
    .stat-charts-grid { grid-template-columns: 1fr; }
}
.chart-card {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 14px;
    padding: 22px;
    box-shadow: var(--shadow-sm);
}
.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}
.chart-header h3 { font-size: 16px; font-weight: 800; color: var(--text-heading); margin: 0 0 2px; }
.chart-tag {
    font-size: 11px;
    font-weight: 700;
    color: var(--primary-700);
    background: #eff6ff;
    padding: 3px 9px;
    border-radius: 6px;
}

.cat-bar-list { display: flex; flex-direction: column; gap: 14px; }
.cbi-label { display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; margin-bottom: 5px; color: var(--text-body); }
.cbi-track { height: 10px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.cbi-fill { height: 100%; border-radius: 99px; }
.fill-red    { background: #1e40af; }
.fill-amber  { background: #2563eb; }
.fill-orange { background: #3b82f6; }
.fill-blue   { background: #93c5fd; }

/* Timeline Bar Chart */
.timeline-bars-chart {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    height: 160px;
    padding-top: 20px;
}
.tbc-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    height: 100%;
    justify-content: flex-end;
    flex: 1;
}
.tbc-bar {
    width: 30px;
    background: #e2e8f0;
    border-radius: 6px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 4px;
    font-size: 11px;
    font-weight: 800;
    color: #475569;
    transition: all 0.2s ease;
}
.tbc-bar.active {
    background: var(--primary-600);
    color: #fff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}
.tbc-col small { font-size: 11px; color: var(--text-muted); font-weight: 700; }

.peak-time-banner {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #f8fafc;
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    padding: 12px 14px;
    margin-top: 18px;
    font-size: 12.5px;
}
.ptb-icon { font-size: 16px; margin-top: 1px; }
.peak-time-banner strong { color: #0f172a; display: block; margin-bottom: 2px; }
.peak-time-banner p { margin: 0; color: #64748b; font-size: 12px; }

/* Sector Table */
.sector-table-card {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}
.stc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}
.stc-header h2 { font-size: 18px; font-weight: 800; color: var(--text-heading); margin: 0 0 2px; }
.stc-header p { font-size: 13px; color: var(--text-muted); margin: 0; }
.btn-check-map-link {
    font-size: 13px;
    font-weight: 700;
    color: var(--primary-700);
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 8px;
    background: #eff6ff;
}
.btn-check-map-link:hover { background: #dbeafe; }

.table-responsive { overflow-x: auto; }
.sector-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
.sector-table th {
    padding: 12px 14px;
    background: #f8fafc;
    color: var(--text-muted);
    font-weight: 800;
    font-size: 11px;
    text-transform: uppercase;
    border-bottom: 1.5px solid var(--border-soft);
}
.sector-table td {
    padding: 14px;
    border-bottom: 1px solid var(--border-soft);
    color: var(--text-body);
}
.badge-status-low { padding: 3px 8px; border-radius: 6px; background: #eff6ff; color: #1e40af; font-size: 11px; font-weight: 700; }
.badge-status-med { padding: 3px 8px; border-radius: 6px; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; font-size: 11px; font-weight: 700; }
.status-patrol { font-weight: 700; font-size: 12px; }
.status-patrol.ok { color: #2563eb; }
.status-patrol.warn { color: #0f172a; }
</style>
@endsection
