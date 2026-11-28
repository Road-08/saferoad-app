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
                <a href="{{ route('admin.validasi') }}" class="aql-btn aql-btn-highlight">Validasi Laporan</a>
                <a href="{{ route('admin.peta') }}" class="aql-btn">Peta Pemantauan</a>
                <a href="{{ route('admin.statistik') }}" class="aql-btn">Statistik</a>
            </div>
        </div>
    </header>

    <!-- KPI Metric Cards Overview -->
    <div class="admin-kpi-grid">
        <a href="{{ route('admin.validasi') }}" class="kpi-card card-total" title="Lihat semua laporan">
            <div class="kpi-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <div class="kpi-body">
                <h3>Total Laporan Warga</h3>
                <strong class="kpi-num" id="dashKpiTotal">4</strong>
                <span class="kpi-sub">Kelola di Halaman Validasi</span>
            </div>
        </a>
        <a href="{{ route('admin.validasi') }}?status=pending_validation" class="kpi-card card-pending" title="Lihat laporan butuh validasi">
            <div class="kpi-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="kpi-body">
                <h3>Menunggu Validasi</h3>
                <strong class="kpi-num" id="dashKpiPending" style="color: #0f172a;">0</strong>
                <span class="kpi-sub">Perlu persetujuan admin</span>
            </div>
        </a>
        <a href="{{ route('admin.validasi') }}?status=handled" class="kpi-card card-approved" title="Lihat laporan ditangani">
            <div class="kpi-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e40af" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="kpi-body">
                <h3>Laporan Ditangani</h3>
                <strong class="kpi-num" id="dashKpiApproved" style="color: #1e40af;">0</strong>
                <span class="kpi-sub">Aktif pada peta masyarakat</span>
            </div>
        </a>
        <a href="{{ route('admin.validasi') }}?status=rejected" class="kpi-card card-rejected" title="Lihat laporan ditolak">
            <div class="kpi-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div class="kpi-body">
                <h3>Laporan Ditolak</h3>
                <strong class="kpi-num" id="dashKpiRejected" style="color: #64748b;">0</strong>
                <span class="kpi-sub">Laporan tidak valid / duplikat</span>
            </div>
        </a>
    </div>

    <!-- Modul Utama SafeRoad Administrator (Navigasi Halaman Terpisah) -->
    <h2 class="section-title">Modul Operasional Administrator</h2>
    <div class="admin-modules-grid">

        <!-- Modul 1: Validasi & Moderasi Laporan -->
        <div class="module-card">
            <div class="mc-icon-badge icon-blue" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div class="mc-content">
                <div class="mc-tag tag-blue" id="dashModulePendingTag">0 BUTUH VALIDASI SEGERA</div>
                <h3>Validasi &amp; Moderasi Laporan Warga</h3>
                <p>Tinjau setiap aduan keamanan masyarakat, verifikasi foto dan koordinat TKP, lalu setujui untuk dipublikasikan ke peta atau tolak laporan yang tidak valid.</p>
                <div class="mc-features-list">
                    <span>• Tinjau bukti foto &amp; deskripsi laporan</span>
                    <span>• Setujui publikasi titik insiden ke peta warga</span>
                    <span>• Tolak laporan tidak valid</span>
                </div>
            </div>
            <div class="mc-footer">
                <a href="{{ route('admin.validasi') }}" class="btn-module-cta cta-blue">
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
            <div class="mc-icon-badge icon-blue" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
            </div>
            <div class="mc-content">
                <div class="mc-tag tag-blue">MONITORING GEOSPASIAL KOTA</div>
                <h3>Peta Pemantauan Sebaran Insiden</h3>
                <p>Pantau sebaran titik laporan masuk dan fasilitas pengawasan kota (CCTV Dishub, Lampu PJU, dan Pos Polisi/Shelter) secara geospasial real-time.</p>
                <div class="mc-features-list">
                    <span>• Peta pemantauan penuh (Surabaya &amp; Sekitarnya)</span>
                    <span>• Layer toggle: Laporan Pending, Disetujui, CCTV, Lampu</span>
                    <span>• Klik penanda peta untuk validasi langsung</span>
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
            <div class="mc-icon-badge icon-blue" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <div class="mc-content">
                <div class="mc-tag tag-blue">REKAP &amp; ANALISIS DATA</div>
                <h3>Statistik &amp; Rekap Keamanan Wilayah</h3>
                <p>Analisis tren kejadian mingguan, distribusi kategori laporan (pelecehan, kondisi jalan, kriminal), serta evaluasi efektivitas penanganan keamanan.</p>
                <div class="mc-features-list">
                    <span>• Grafik distribusi persentase kategori insiden</span>
                    <span>• Tren frekuensi laporan harian (7 hari terakhir)</span>
                    <span>• Rekapitulasi rasio laporan valid vs tidak valid</span>
                </div>
            </div>
            <div class="mc-footer">
                <a href="{{ route('admin.statistik') }}" class="btn-module-cta cta-blue">
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
            <h3>Aduan Terkini Memerlukan Tindakan Admin</h3>
            <a href="{{ route('admin.validasi') }}" style="font-size: 13px; font-weight: 700; color: #2563eb;">Lihat Semua di Halaman Validasi</a>
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
    background-color: #1e3a8a;
    border: 1px solid #1e40af;
    border-radius: 14px;
    padding: 28px clamp(20px, 3.5vw, 36px);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-card);
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
.admin-quick-links { display: flex; gap: 8px; flex-wrap: wrap; }
.aql-btn {
    height: 38px;
    padding: 0 14px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.24);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
    transition: background-color 0.15s ease;
}
.aql-btn:hover {
    background: rgba(255, 255, 255, 0.24);
}
.aql-btn-highlight {
    background: #ffffff;
    color: #1e40af;
    border-color: #ffffff;
    font-weight: 700;
}
.aql-btn-highlight:hover {
    background: #eff6ff;
    color: #1e3a8a;
}

/* KPI Cards */
.admin-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.kpi-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    color: inherit;
    transition: border-color 0.15s ease;
}
.kpi-card:hover {
    border-color: #2563eb;
}
.kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.card-total .kpi-icon    { background: #eff6ff; }
.card-pending .kpi-icon  { background: #eff6ff; }
.card-approved .kpi-icon { background: #dbeafe; }
.card-rejected .kpi-icon { background: #f8fafc; }

.kpi-body { flex: 1; }
.kpi-card h3 { font-size: 12px; font-weight: 600; color: var(--text-muted); margin: 0 0 2px; }
.kpi-num { font-size: 24px; font-weight: 800; color: var(--text-heading); display: block; line-height: 1.1; }
.kpi-sub { font-size: 11px; color: var(--primary-600); font-weight: 600; margin-top: 2px; display: block; }

/* Modules Grid */
.section-title {
    font-size: 17px;
    font-weight: 800;
    color: var(--text-heading);
    margin-bottom: 14px;
}
.admin-modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}
.module-card {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 14px;
    padding: 22px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: var(--shadow-sm);
    transition: border-color 0.15s ease;
}
.module-card:hover {
    border-color: #2563eb;
}
.mc-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
}
.icon-blue { background: #eff6ff; }

.mc-tag {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.04em;
    margin-bottom: 8px;
}
.tag-blue { background: #eff6ff; color: #1e40af; }

.mc-content h3 {
    font-size: 16.5px;
    font-weight: 700;
    color: var(--text-heading);
    margin-bottom: 6px;
}
.mc-content p {
    font-size: 13.5px;
    color: var(--text-muted);
    line-height: 1.55;
    margin-bottom: 14px;
}
.mc-features-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 12.5px;
    color: var(--text-muted);
    margin-bottom: 18px;
}
.btn-module-cta {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    height: 42px;
    padding: 0 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    color: #ffffff;
    background-color: #2563eb;
    white-space: nowrap;
    transition: background-color 0.15s ease;
}
.btn-module-cta:hover { background-color: #1e40af; }
.btn-module-cta svg { width: 16px; height: 16px; flex-shrink: 0; }

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
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #dbeafe;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
}
.recent-action-btn:hover {
    background: #dbeafe;
}
</style>

@push('scripts')
<script>
(function () {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let pollingInterval = null;

    function formatFullDateTime(d) {
        if (!d) return '-';
        const dateObj = typeof d === 'string' ? new Date(d) : d;
        if (isNaN(dateObj.getTime())) return '-';
        return new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }).format(dateObj) + ' WIB';
    }

    function timeAgo(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '-';
        const now = new Date();
        const diffSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

        if (diffSeconds < 60) return 'baru saja';
        const diffMinutes = Math.floor(diffSeconds / 60);
        if (diffMinutes < 60) return `${diffMinutes} menit lalu`;
        const diffHours = Math.floor(diffMinutes / 60);
        if (diffHours < 24) return `${diffHours} jam lalu`;

        const formatterDateOnly = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta', year: 'numeric', month: '2-digit', day: '2-digit' });
        const formatterTimeOnly = new Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hour12: false });

        const dateStr = formatterDateOnly.format(date);
        const yesterday = new Date(now.getTime() - 86400000);
        const yesterdayStr = formatterDateOnly.format(yesterday);
        const timePart = formatterTimeOnly.format(date).replace('.', ':');

        if (dateStr === yesterdayStr) {
            return `kemarin ${timePart}`;
        }

        return formatFullDateTime(date);
    }

    async function loadDashboardData() {
        try {
            // 1. Fetch counts
            const countResp = await fetch('{{ url("/admin/data/reports/counts") }}', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            if (countResp.ok) {
                const countRes = await countResp.json();
                if (countRes.success && countRes.data) {
                    const c = countRes.data;
                    const total = (c.pending_validation || 0) + (c.forwarded_to_security || 0) + (c.needs_revision || 0) + (c.handled || 0) + (c.rejected || 0);
                    document.getElementById('dashKpiTotal').textContent = total;
                    document.getElementById('dashKpiPending').textContent = c.pending_validation || 0;
                    document.getElementById('dashKpiApproved').textContent = c.handled || 0;
                    document.getElementById('dashKpiRejected').textContent = c.rejected || 0;

                    const tagEl = document.getElementById('dashModulePendingTag');
                    if (tagEl) {
                        tagEl.textContent = `${c.pending_validation || 0} BUTUH VALIDASI SEGERA`;
                    }

                    // Navbar badges
                    const navPending = document.getElementById('navBadgePendingValidation');
                    if (navPending) {
                        if (c.pending_validation > 0) {
                            navPending.textContent = c.pending_validation;
                            navPending.style.display = 'inline-block';
                        } else {
                            navPending.style.display = 'none';
                        }
                    }

                    const navReady = document.getElementById('navBadgeReadyToFinalize');
                    if (navReady) {
                        if (c.ready_to_finalize > 0) {
                            navReady.textContent = c.ready_to_finalize;
                            navReady.style.display = 'inline-block';
                        } else {
                            navReady.style.display = 'none';
                        }
                    }
                }
            }

            // 2. Fetch pending reports for recent box
            const reportsResp = await fetch('{{ url("/admin/data/reports") }}?status=pending_validation&per_page=5', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            if (reportsResp.ok) {
                const reportsRes = await reportsResp.json();
                const listEl = document.getElementById('dashRecentList');
                if (reportsRes.success && Array.isArray(reportsRes.data) && reportsRes.data.length > 0) {
                    listEl.innerHTML = reportsRes.data.slice(0, 4).map(r => `
                        <div class="recent-item">
                            <div class="recent-left">
                                <div>
                                    <div class="recent-title">${r.report_code} · ${r.category}</div>
                                    <div class="recent-sub">${r.address || '-'} · <span title="${formatFullDateTime(r.incident_at)}">${timeAgo(r.incident_at)}</span></div>
                                </div>
                            </div>
                            <a href="{{ route('admin.validasi') }}?code=${r.report_code}" class="recent-action-btn">
                                Validasi Sekarang
                            </a>
                        </div>
                    `).join('');
                } else {
                    listEl.innerHTML = `
                        <div style="text-align:center; padding: 18px; color: #64748b; font-weight: 600;">
                            Semua laporan telah selesai dimoderasi. Tidak ada aduan yang tertunda.
                        </div>
                    `;
                }
            }
        } catch (e) {
            // Silently retry next interval
        }
    }

    function startPolling() {
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(loadDashboardData, 10000);
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopPolling();
        } else {
            loadDashboardData();
            startPolling();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadDashboardData();
        startPolling();
    });
})();
</script>
@endpush
@endsection
