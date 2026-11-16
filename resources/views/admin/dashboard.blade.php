@extends('layouts.app')

@section('title', 'Dashboard Administrator · SafeRoad')

@section('content')
<div class="admin-container">
    <!-- Admin Hero Header -->
    <header class="admin-header-card">
        <div class="ahc-left">
            <span class="ahc-badge">SISTEM KENDALI ADMINISTRATOR</span>
            <h1>Dashboard Ringkasan Operasional SafeRoad</h1>
            <p>Pusat komando dan kendali sistem SafeRoad. Pilih menu operasional untuk memvalidasi laporan warga, memantau peta insiden kota, atau meninjau statistik keselamatan.</p>
        </div>
        <div class="ahc-right">
            <div class="admin-quick-links">
                <a href="{{ route('admin.validasi') }}" class="aql-btn aql-btn-highlight">⚖️ Buka Validasi Laporan</a>
                <a href="{{ route('admin.peta') }}" class="aql-btn">🗺️ Buka Peta Pemantauan</a>
                <a href="{{ route('admin.statistik') }}" class="aql-btn">📊 Buka Statistik</a>
            </div>
        </div>
    </header>

    <!-- KPI Metric Cards Overview -->
    <div class="admin-kpi-grid">
        <a href="{{ route('admin.validasi') }}" class="kpi-card card-total" title="Lihat semua laporan">
            <div class="kpi-icon">📊</div>
            <div class="kpi-body">
                <h3>Total Laporan Warga</h3>
                <strong class="kpi-num" id="dashKpiTotal">4</strong>
                <span class="kpi-sub">Kelola di Halaman Validasi →</span>
            </div>
        </a>
        <a href="{{ route('admin.validasi') }}?status=submitted" class="kpi-card card-pending" title="Lihat laporan butuh validasi">
            <div class="kpi-icon">⏳</div>
            <div class="kpi-body">
                <h3>Menunggu Validasi</h3>
                <strong class="kpi-num" id="dashKpiPending" style="color: #b45309;">2</strong>
                <span class="kpi-sub">Perlu persetujuan admin →</span>
            </div>
        </a>
        <a href="{{ route('admin.validasi') }}?status=map_updated" class="kpi-card card-approved" title="Lihat laporan disetujui">
            <div class="kpi-icon">✅</div>
            <div class="kpi-body">
                <h3>Laporan Disetujui</h3>
                <strong class="kpi-num" id="dashKpiApproved" style="color: #15803d;">1</strong>
                <span class="kpi-sub">Aktif pada peta masyarakat →</span>
            </div>
        </a>
        <a href="{{ route('admin.validasi') }}?status=rejected" class="kpi-card card-rejected" title="Lihat laporan ditolak">
            <div class="kpi-icon">❌</div>
            <div class="kpi-body">
                <h3>Laporan Ditolak</h3>
                <strong class="kpi-num" id="dashKpiRejected" style="color: #b91c1c;">1</strong>
                <span class="kpi-sub">Laporan palsu / duplikat →</span>
            </div>
        </a>
    </div>

    <!-- Modul Utama SafeRoad Administrator (Navigasi Halaman Terpisah) -->
    <h2 class="section-title">Modul Operasional Administrator</h2>
    <div class="admin-modules-grid">

        <!-- Modul 1: Validasi & Moderasi Laporan -->
        <div class="module-card">
            <div class="mc-icon-badge icon-red">⚖️</div>
            <div class="mc-content">
                <div class="mc-tag tag-amber">2 BUTUH VALIDASI SEGERA</div>
                <h3>Validasi &amp; Moderasi Laporan Warga</h3>
                <p>Tinjau setiap aduan keamanan masyarakat, verifikasi foto dan koordinat TKP, lalu setujui untuk dipublikasikan ke peta atau tolak laporan yang tidak valid.</p>
                <div class="mc-features-list">
                    <span>✓ Tinjau bukti foto &amp; deskripsi laporan</span>
                    <span>✓ Setujui publikasi titik insiden ke peta warga</span>
                    <span>✓ Tolak laporan palsu / indikasi hoax</span>
                </div>
            </div>
            <div class="mc-footer">
                <a href="{{ route('admin.validasi') }}" class="btn-module-cta cta-red">
                    <span>Buka Halaman Validasi Laporan</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Modul 2: Peta Pemantauan Geospasial -->
        <div class="module-card">
            <div class="mc-icon-badge icon-blue">🗺️</div>
            <div class="mc-content">
                <div class="mc-tag tag-blue">MONITORING GEOSPASIAL KOTA</div>
                <h3>Peta Pemantauan Sebaran Insiden</h3>
                <p>Pantau sebaran titik laporan masuk dan fasilitas pengawasan kota (CCTV Dishub, Lampu PJU, dan Pos Polisi/Shelter) secara geospasial real-time.</p>
                <div class="mc-features-list">
                    <span>✓ Peta pemantauan penuh (Surabaya &amp; Sekitarnya)</span>
                    <span>✓ Layer toggle: Laporan Pending, Disetujui, CCTV, Lampu</span>
                    <span>✓ Klik pin peta untuk validasi langsung</span>
                </div>
            </div>
            <div class="mc-footer">
                <a href="{{ route('admin.peta') }}" class="btn-module-cta cta-blue">
                    <span>Buka Peta Pemantauan Insiden</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Modul 3: Statistik & Rekap Data -->
        <div class="module-card">
            <div class="mc-icon-badge icon-purple">📊</div>
            <div class="mc-content">
                <div class="mc-tag tag-purple">REKAP &amp; ANALISIS DATA</div>
                <h3>Statistik &amp; Rekap Keamanan Wilayah</h3>
                <p>Analisis tren kejadian mingguan, distribusi kategori laporan (pelecehan, kondisi jalan, kriminal), serta evaluasi efektivitas penanganan keamanan.</p>
                <div class="mc-features-list">
                    <span>✓ Grafik distribusi persentase kategori insiden</span>
                    <span>✓ Tren frekuensi laporan harian (7 hari terakhir)</span>
                    <span>✓ Rekapitulasi rasio laporan valid vs palsu</span>
                </div>
            </div>
            <div class="mc-footer">
                <a href="{{ route('admin.statistik') }}" class="btn-module-cta cta-purple">
                    <span>Buka Halaman Statistik &amp; Rekap</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>

    </div>

    <!-- Ringkasan Aktivitas Terkini -->
    <div class="recent-box-card">
        <div class="rbc-header">
            <h3>📋 Aduan Terkini Memerlukan Tindakan Admin</h3>
            <a href="{{ route('admin.validasi') }}" style="font-size: 13px; font-weight: 700; color: #2563eb;">Lihat Semua di Halaman Validasi →</a>
        </div>
        <div class="recent-list" id="dashRecentList">
            <!-- Disuntikkan via JS -->
        </div>
    </div>
</div>

<style>
.admin-container {
    max-width: 1400px;
    margin: 28px auto 60px;
    padding: 0 clamp(16px, 3vw, 36px);
}

.admin-header-card {
    background: linear-gradient(135deg, #1e1b4b 0%, #1e3a8a 60%, #2563eb 100%);
    border-radius: 20px;
    padding: 32px clamp(20px, 4vw, 40px);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
    box-shadow: 0 15px 35px -10px rgba(30, 58, 138, 0.35);
    flex-wrap: wrap;
}
.ahc-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.18);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    color: #e0e7ff;
    margin-bottom: 10px;
}
.ahc-left h1 {
    font-size: clamp(22px, 3vw, 30px);
    font-weight: 800;
    letter-spacing: -0.02em;
    margin-bottom: 6px;
}
.ahc-left p {
    font-size: 14px;
    color: #dbeafe;
    max-width: 680px;
    margin: 0;
}
.admin-quick-links { display: flex; gap: 10px; flex-wrap: wrap; }
.aql-btn {
    padding: 10px 16px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}
.aql-btn:hover {
    background: rgba(255, 255, 255, 0.28);
    transform: translateY(-2px);
}
.aql-btn-highlight {
    background: #ffffff;
    color: #1e3a8a;
    border-color: #ffffff;
}
.aql-btn-highlight:hover {
    background: #f1f5f9;
    color: #0f2d59;
}

/* KPI Cards */
.admin-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}
.kpi-card {
    background: #fff;
    border: 1.5px solid var(--border-soft);
    border-radius: 16px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}
.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border-color: #93c5fd;
}
.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.card-total .kpi-icon    { background: #eff6ff; }
.card-pending .kpi-icon  { background: #fef3c7; }
.card-approved .kpi-icon { background: #dcfce7; }
.card-rejected .kpi-icon { background: #fee2e2; }

.kpi-body { flex: 1; }
.kpi-card h3 { font-size: 12.5px; font-weight: 700; color: var(--text-muted); margin: 0 0 2px; }
.kpi-num { font-size: 26px; font-weight: 800; color: var(--text-heading); display: block; line-height: 1.1; }
.kpi-sub { font-size: 11px; color: var(--primary-600); font-weight: 600; margin-top: 2px; display: block; }

/* Modules Grid */
.section-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-heading);
    margin-bottom: 16px;
}
.admin-modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}
.module-card {
    background: #ffffff;
    border: 1.5px solid var(--border-soft);
    border-radius: 20px;
    padding: 26px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
}
.module-card:hover {
    border-color: #93c5fd;
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(30, 58, 138, 0.08);
}
.mc-icon-badge {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin-bottom: 16px;
}
.icon-red    { background: #fee2e2; }
.icon-blue   { background: #dbeafe; }
.icon-purple { background: #ede9fe; }

.mc-tag {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
}
.tag-amber  { background: #fef3c7; color: #b45309; }
.tag-blue   { background: #eff6ff; color: #1d4ed8; }
.tag-purple { background: #f5f3ff; color: #6d28d9; }

.mc-content h3 {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-heading);
    margin-bottom: 8px;
}
.mc-content p {
    font-size: 13.5px;
    color: var(--text-body);
    line-height: 1.5;
    margin-bottom: 14px;
}
.mc-features-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 12.5px;
    color: var(--text-muted);
    margin-bottom: 20px;
}
.btn-module-cta {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 12px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    color: #ffffff;
    transition: all 0.2s ease;
}
.cta-red    { background: linear-gradient(135deg, #dc2626, #b91c1c); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25); }
.cta-blue   { background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
.cta-purple { background: linear-gradient(135deg, #7c3aed, #6d28d9); box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25); }
.btn-module-cta:hover { transform: translateY(-2px); filter: brightness(1.08); }
.btn-module-cta svg { width: 18px; height: 18px; }

/* Recent List */
.recent-box-card {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 18px;
    padding: 22px 26px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.rbc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}
.rbc-header h3 { font-size: 15px; font-weight: 800; color: var(--text-heading); margin: 0; }
.recent-list { display: flex; flex-direction: column; gap: 10px; }
.recent-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #f8fafc;
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    gap: 14px;
    font-size: 13px;
}
.recent-left { display: flex; align-items: center; gap: 12px; }
.recent-icon { font-size: 20px; }
.recent-title { font-weight: 700; color: #0f172a; }
.recent-sub { font-size: 12px; color: #64748b; }
.recent-action-btn {
    padding: 6px 12px;
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let reports = [
        { code: 'SR-2611-B892', cat: 'Pelecehan / Gangguan Keamanan', loc: 'Jl. Ketintang Barat No. 12', status: 'submitted' },
        { code: 'SR-2611-C419', cat: 'Pencurian', loc: 'Simpang Lampu Merah Ketintang Timur', status: 'submitted' },
        { code: 'SR-2611-A104', cat: 'Kondisi Jalan', loc: 'Depan Gerbang Barat Kampus', status: 'map_updated' },
        { code: 'SR-2611-X009', cat: 'Kerusakan Fasilitas Keamanan', loc: 'Jl. Ahmad Yani KM 8', status: 'rejected' }
    ];

    try {
        const localList = JSON.parse(localStorage.getItem('saferoad_my_reports') || '[]');
        if (Array.isArray(localList) && localList.length > 0) {
            localList.forEach(item => {
                if (!reports.some(r => r.code === item.report_code)) {
                    reports.unshift({
                        code: item.report_code || 'SR-DEMO-NEW',
                        cat: item.category || 'Pelecehan',
                        loc: item.address || 'Lokasi Titik Peta',
                        status: item.status || 'submitted'
                    });
                }
            });
        }
    } catch (e) {}

    const total = reports.length;
    const submitted = reports.filter(r => r.status === 'submitted').length;
    const approved = reports.filter(r => r.status === 'map_updated').length;
    const rejected = reports.filter(r => r.status === 'rejected').length;

    document.getElementById('dashKpiTotal').textContent = total;
    document.getElementById('dashKpiPending').textContent = submitted;
    document.getElementById('dashKpiApproved').textContent = approved;
    document.getElementById('dashKpiRejected').textContent = rejected;

    const listEl = document.getElementById('dashRecentList');
    const pendingList = reports.filter(r => r.status === 'submitted').slice(0, 3);

    if (pendingList.length === 0) {
        listEl.innerHTML = `
            <div style="text-align:center; padding: 18px; color: #16a34a; font-weight: 700;">
                ✓ Semua laporan telah selesai dimoderasi. Tidak ada aduan yang tertunda.
            </div>
        `;
    } else {
        listEl.innerHTML = pendingList.map(r => `
            <div class="recent-item">
                <div class="recent-left">
                    <span class="recent-icon">⏳</span>
                    <div>
                        <div class="recent-title">${r.code} · ${r.cat}</div>
                        <div class="recent-sub">📍 ${r.loc}</div>
                    </div>
                </div>
                <a href="{{ route('admin.validasi') }}?code=${r.code}" class="recent-action-btn">
                    ⚖️ Validasi Sekarang
                </a>
            </div>
        `).join('');
    }
});
</script>
@endpush
@endsection
