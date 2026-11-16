@extends('layouts.app')

@section('title', 'Validasi & Moderasi Laporan · SafeRoad Administrator')

@section('content')
<div class="val-page-container">
    <!-- Top Breadcrumb & Header -->
    <div class="val-header-row">
        <a href="{{ route('admin.dashboard') }}" class="btn-back-dash">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    <header class="val-title-card">
        <div class="vtc-left">
            <span class="vtc-badge">MODERASI RESMI TIK</span>
            <h1>⚖️ Validasi &amp; Moderasi Laporan Masuk</h1>
            <p>Tinjau dan verifikasi setiap laporan kejadian yang diajukan oleh masyarakat. Laporan yang disetujui akan dipublikasikan secara langsung pada peta navigasi warga.</p>
        </div>
        <div class="vtc-right">
            <div class="status-summary-pills">
                <span class="ssp-item ssp-pending">
                    <strong><span id="statPendingNum">2</span> Laporan</strong> Menunggu Tinjauan
                </span>
                <span class="ssp-item ssp-approved">
                    <strong><span id="statApprovedNum">1</span> Laporan</strong> Disetujui
                </span>
            </div>
        </div>
    </header>

    <!-- Main Moderation Panel -->
    <div class="val-main-card">
        <!-- Controls & Filters Toolbar -->
        <div class="val-toolbar">
            <div class="val-tabs-wrap">
                <button type="button" class="val-tab active" id="tabValAll" onclick="setValFilter('all')">
                    Semua (<span id="countAll">4</span>)
                </button>
                <button type="button" class="val-tab" id="tabValPending" onclick="setValFilter('submitted')">
                    ⏳ Menunggu Validasi (<span id="countPending">2</span>)
                </button>
                <button type="button" class="val-tab" id="tabValApproved" onclick="setValFilter('map_updated')">
                    ✅ Disetujui (<span id="countApproved">1</span>)
                </button>
                <button type="button" class="val-tab" id="tabValRejected" onclick="setValFilter('rejected')">
                    ❌ Ditolak (<span id="countRejected">1</span>)
                </button>
            </div>

            <div class="val-filter-right">
                <select id="valCategoryFilter" class="val-select" onchange="applyValFilters()">
                    <option value="">Semua Kategori</option>
                    <option value="Pelecehan">🔴 Pelecehan / Gangguan</option>
                    <option value="Pencurian">🟠 Tindak Kriminal / Begal</option>
                    <option value="Kondisi Jalan">🟡 Kondisi Jalan / Lampu</option>
                    <option value="Fasilitas Keamanan">🔵 Fasilitas Keamanan</option>
                    <option value="Lainnya">🟣 Lainnya</option>
                </select>

                <div class="val-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="valSearchInput" placeholder="Cari kode / jalan / pelapor..." oninput="applyValFilters()">
                </div>
            </div>
        </div>

        <!-- Tabel Laporan -->
        <div class="table-responsive">
            <table class="val-table">
                <thead>
                    <tr>
                        <th>Kode Laporan</th>
                        <th>Kategori Insiden</th>
                        <th>Alamat / Lokasi TKP</th>
                        <th>Waktu Kejadian</th>
                        <th>Pelapor</th>
                        <th>Status Validasi</th>
                        <th style="text-align: right; min-width: 230px;">Tindakan Administrator</th>
                    </tr>
                </thead>
                <tbody id="valTableBody">
                    <!-- Disuntikkan via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail & Validasi Laporan -->
<div id="modalDetailAdmin" class="val-modal-backdrop" style="display:none;">
    <div class="val-modal-box">
        <div class="vmb-header">
            <div>
                <span class="vmb-badge" id="modalBadgeStatus">STATUS</span>
                <h3 id="modalReportCode" style="margin-top: 4px; font-size: 18px; font-weight: 800; color: #0f172a;">Detail Laporan Insiden</h3>
            </div>
            <button type="button" class="vmb-close" onclick="closeAdminModal()">✕</button>
        </div>
        <div class="vmb-body">
            <div class="vmb-grid-2">
                <div class="vmb-field">
                    <span class="vmb-label">Kategori Insiden:</span>
                    <strong id="modalCategory" style="color: #2563eb;">-</strong>
                </div>
                <div class="vmb-field">
                    <span class="vmb-label">Waktu Dilaporkan:</span>
                    <span id="modalTime">-</span>
                </div>
            </div>
            <div class="vmb-field">
                <span class="vmb-label">Lokasi / Alamat Kejadian:</span>
                <span id="modalAddress" style="font-weight: 600; color: #1e293b;">-</span>
            </div>
            <div class="vmb-field">
                <span class="vmb-label">Identitas Pelapor:</span>
                <span id="modalReporter">-</span>
            </div>
            <div class="vmb-field">
                <span class="vmb-label">Deskripsi Kejadian dari Warga:</span>
                <p id="modalDesc" class="vmb-desc-box">-</p>
            </div>
            <div class="vmb-field">
                <span class="vmb-label">Titik Koordinat di Peta:</span>
                <code id="modalCoords" style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-size: 12px; color: #0f172a;">-</code>
            </div>
        </div>
        <div class="vmb-footer" id="modalActionButtons">
            <!-- Disuntikkan sesuai status -->
        </div>
    </div>
</div>

<style>
.val-page-container {
    max-width: 1400px;
    margin: 24px auto 60px;
    padding: 0 clamp(16px, 3vw, 36px);
}

.val-header-row { margin-bottom: 16px; }
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
}
.btn-back-dash:hover { background: #eff6ff; }
.btn-back-dash svg { width: 16px; height: 16px; }

.val-title-card {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
    border-radius: 20px;
    padding: 28px clamp(20px, 4vw, 36px);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
    box-shadow: 0 12px 30px -8px rgba(49, 46, 129, 0.35);
    flex-wrap: wrap;
}
.vtc-badge {
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
.vtc-left h1 { font-size: clamp(22px, 3vw, 28px); font-weight: 800; margin-bottom: 6px; }
.vtc-left p { font-size: 13.5px; color: #e0e7ff; max-width: 650px; margin: 0; }

.status-summary-pills { display: flex; gap: 10px; flex-wrap: wrap; }
.ssp-item {
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 12.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.ssp-pending { background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #fef3c7; }
.ssp-approved { background: rgba(34, 197, 94, 0.2); border: 1px solid rgba(34, 197, 94, 0.4); color: #dcfce7; }
.ssp-item strong { font-size: 14px; font-weight: 800; }

.val-main-card {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 20px;
    padding: 24px clamp(16px, 2.5vw, 28px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
}

.val-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.val-tabs-wrap {
    display: inline-flex;
    background: #f1f5f9;
    padding: 4px;
    border-radius: 10px;
    gap: 4px;
    flex-wrap: wrap;
}
.val-tab {
    padding: 7px 14px;
    border-radius: 7px;
    border: none;
    background: transparent;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.15s;
}
.val-tab:hover { color: var(--text-heading); }
.val-tab.active {
    background: #ffffff;
    color: var(--primary-700);
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

.val-filter-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.val-select {
    padding: 8px 12px;
    border-radius: 8px;
    border: 1.5px solid var(--border-soft);
    font-size: 13px;
    background: #fff;
    color: var(--text-heading);
    font-weight: 600;
}
.val-search-box {
    position: relative;
    display: flex;
    align-items: center;
}
.val-search-box svg {
    position: absolute;
    left: 10px;
    width: 15px;
    height: 15px;
    color: #94a3b8;
}
.val-search-box input {
    padding: 8px 12px 8px 32px;
    border-radius: 8px;
    border: 1.5px solid var(--border-soft);
    font-size: 13px;
    width: 240px;
}
.val-search-box input:focus {
    outline: none;
    border-color: var(--primary-600);
}

.table-responsive { overflow-x: auto; }
.val-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
.val-table th {
    padding: 13px 14px;
    background: #f8fafc;
    color: var(--text-muted);
    font-weight: 800;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1.5px solid var(--border-soft);
}
.val-table td {
    padding: 14px;
    border-bottom: 1px solid var(--border-soft);
    color: var(--text-body);
    vertical-align: middle;
}
.val-table tr:hover td { background-color: #fafbfc; }

.btn-val-approve {
    padding: 6px 12px;
    border-radius: 7px;
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s;
}
.btn-val-approve:hover { background: #bbf7d0; }
.btn-val-reject {
    padding: 6px 12px;
    border-radius: 7px;
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    margin-left: 4px;
    transition: background 0.15s;
}
.btn-val-reject:hover { background: #fecaca; }
.btn-val-detail {
    padding: 6px 10px;
    border-radius: 7px;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    margin-right: 4px;
    transition: background 0.15s;
}
.btn-val-detail:hover { background: #e2e8f0; }

/* Modal Detail */
.val-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.val-modal-box {
    background: #ffffff;
    width: min(100%, 540px);
    border-radius: 20px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
    overflow: hidden;
    animation: modalPop 0.2s ease-out;
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.vmb-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px 24px;
    background: #f8fafc;
    border-bottom: 1px solid var(--border-soft);
}
.vmb-badge {
    font-size: 10px;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 999px;
    text-transform: uppercase;
}
.vmb-close {
    border: none;
    background: #e2e8f0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    font-weight: 700;
    font-size: 13px;
    display: grid;
    place-items: center;
}
.vmb-body { padding: 22px 24px; display: flex; flex-direction: column; gap: 14px; }
.vmb-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.vmb-field { display: flex; flex-direction: column; gap: 3px; font-size: 13px; }
.vmb-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; }
.vmb-desc-box {
    background: #f8fafc;
    border: 1px solid var(--border-soft);
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    line-height: 1.5;
    color: #334155;
    margin: 2px 0 0;
}
.vmb-footer {
    padding: 16px 24px;
    background: #f8fafc;
    border-top: 1px solid var(--border-soft);
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
</style>

@push('scripts')
<script>
let adminReports = [
    {
        id: 1,
        code: 'SR-2611-B892',
        category: 'Pelecehan / Gangguan Keamanan',
        address: 'Jl. Ketintang Barat No. 12',
        time: '19:15 WIB',
        status: 'submitted',
        lat: -7.3152,
        lng: 112.7280,
        description: 'Penerangan redup dan ada oknum mencurigakan berkumpul di dekat lorong perumahan.',
        reporter: 'Warga (Anonim)'
    },
    {
        id: 2,
        code: 'SR-2611-C419',
        category: 'Pencurian',
        address: 'Simpang Lampu Merah Ketintang Timur',
        time: '18:40 WIB',
        status: 'submitted',
        lat: -7.3138,
        lng: 112.7268,
        description: 'Percobaan penarikan tas pengendara motor saat menunggu lampu merah.',
        reporter: 'Dewi S.'
    },
    {
        id: 3,
        code: 'SR-2611-A104',
        category: 'Kondisi Jalan',
        address: 'Depan Gerbang Barat Kampus',
        time: '16:20 WIB',
        status: 'map_updated',
        lat: -7.3120,
        lng: 112.7295,
        description: 'Lampu PJU padam sepanjang 100 meter, jalan berlubang cukup dalam.',
        reporter: 'Ahmad F.'
    },
    {
        id: 4,
        code: 'SR-2611-X009',
        category: 'Kerusakan Fasilitas Keamanan',
        address: 'Jl. Ahmad Yani KM 8',
        time: '14:10 WIB',
        status: 'rejected',
        lat: -7.3160,
        lng: 112.7265,
        description: 'Tiang CCTV miring setelah terserempet truk.',
        reporter: 'Warga Setempat'
    }
];

// Sinkronkan laporan lokal warga dari localStorage
(function syncLocalReports() {
    try {
        const localList = JSON.parse(localStorage.getItem('saferoad_my_reports') || '[]');
        if (Array.isArray(localList) && localList.length > 0) {
            localList.forEach((item, index) => {
                if (!adminReports.some(r => r.code === item.report_code)) {
                    adminReports.unshift({
                        id: 100 + index,
                        code: item.report_code || ('SR-' + Math.floor(1000 + Math.random() * 9000)),
                        category: item.category || 'Pelecehan / Gangguan Keamanan',
                        address: item.address || 'Lokasi Titik Peta',
                        time: item.incident_time ? new Date(item.incident_time).toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' }) + ' WIB' : 'Baru saja',
                        status: item.status || 'submitted',
                        lat: item.latitude || (-7.3140 + (Math.random() - 0.5) * 0.006),
                        lng: item.longitude || (112.7280 + (Math.random() - 0.5) * 0.006),
                        description: item.description || 'Laporan insiden yang baru dikirimkan oleh masyarakat.',
                        reporter: item.is_anonymous ? 'Warga (Anonim)' : 'Warga Pengguna'
                    });
                }
            });
        }
    } catch (e) {}
})();

let currentFilterStatus = 'all';

function updateValCounts() {
    const total = adminReports.length;
    const submitted = adminReports.filter(r => r.status === 'submitted').length;
    const approved = adminReports.filter(r => r.status === 'map_updated').length;
    const rejected = adminReports.filter(r => r.status === 'rejected').length;

    document.getElementById('countAll').textContent = total;
    document.getElementById('countPending').textContent = submitted;
    document.getElementById('countApproved').textContent = approved;
    document.getElementById('countRejected').textContent = rejected;

    document.getElementById('statPendingNum').textContent = submitted;
    document.getElementById('statApprovedNum').textContent = approved;
}

function renderValTable() {
    const tbody = document.getElementById('valTableBody');
    const searchVal = document.getElementById('valSearchInput').value.toLowerCase().trim();
    const catVal = document.getElementById('valCategoryFilter').value.toLowerCase().trim();

    const filtered = adminReports.filter(r => {
        const matchStatus = (currentFilterStatus === 'all' || r.status === currentFilterStatus);
        const matchCat = (!catVal || r.category.toLowerCase().includes(catVal));
        const matchQuery = (!searchVal ||
            r.code.toLowerCase().includes(searchVal) ||
            r.category.toLowerCase().includes(searchVal) ||
            r.address.toLowerCase().includes(searchVal) ||
            r.reporter.toLowerCase().includes(searchVal)
        );
        return matchStatus && matchCat && matchQuery;
    });

    const statusPills = {
        'submitted':   '<span style="padding:4px 9px;border-radius:999px;background:#fef3c7;color:#b45309;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;"><span style="width:6px;height:6px;border-radius:50%;background:#d97706;"></span> Menunggu Validasi</span>',
        'map_updated': '<span style="padding:4px 9px;border-radius:999px;background:#dcfce7;color:#15803d;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;"><span style="width:6px;height:6px;border-radius:50%;background:#16a34a;"></span> Disetujui (Tayang)</span>',
        'rejected':    '<span style="padding:4px 9px;border-radius:999px;background:#fee2e2;color:#b91c1c;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;"><span style="width:6px;height:6px;border-radius:50%;background:#dc2626;"></span> Ditolak</span>'
    };

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px 20px; color: #64748b;">
                    <div style="font-size: 32px; margin-bottom: 8px;">🔍</div>
                    <strong style="display: block; font-size: 15px; color: #0f172a;">Tidak ada laporan yang cocok</strong>
                    <p style="font-size: 12.5px; margin-top: 4px;">Sesuaikan kata kunci pencarian atau ganti filter status di atas.</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = filtered.map(r => `
        <tr>
            <td><strong style="color: #0f172a; font-family: monospace; font-size: 13.5px;">${r.code}</strong></td>
            <td><span style="font-weight: 700; color: #2563eb;">${r.category}</span></td>
            <td><span>📍 ${r.address}</span></td>
            <td><span style="font-size: 12px; color: #64748b;">${r.time}</span></td>
            <td><span style="font-size: 12px; color: #475569;">${r.reporter}</span></td>
            <td>${statusPills[r.status] || r.status}</td>
            <td style="text-align: right;">
                <button type="button" class="btn-val-detail" onclick="openAdminModal(${r.id})">👁️ Detail</button>
                ${r.status === 'submitted' ? `
                    <button type="button" class="btn-val-approve" onclick="approveReport(${r.id})">✓ Setujui</button>
                    <button type="button" class="btn-val-reject" onclick="rejectReport(${r.id})">✕ Tolak</button>
                ` : `
                    <span style="font-size: 12px; color: #94a3b8; font-weight: 600;">Selesai Dimoderasi</span>
                `}
            </td>
        </tr>
    `).join('');
}

function setValFilter(status) {
    currentFilterStatus = status;
    document.querySelectorAll('.val-tab').forEach(t => t.classList.remove('active'));

    if (status === 'all') document.getElementById('tabValAll').classList.add('active');
    else if (status === 'submitted') document.getElementById('tabValPending').classList.add('active');
    else if (status === 'map_updated') document.getElementById('tabValApproved').classList.add('active');
    else if (status === 'rejected') document.getElementById('tabValRejected').classList.add('active');

    renderValTable();
}

function applyValFilters() {
    renderValTable();
}

function approveReport(id) {
    const r = adminReports.find(x => x.id === id);
    if (r) {
        r.status = 'map_updated';
        alert(`✅ Laporan ${r.code} (${r.category}) berhasil DISETUJUI!\nTitik insiden kini tayang pada peta masyarakat.`);
        updateValCounts();
        renderValTable();
        closeAdminModal();
    }
}

function rejectReport(id) {
    const r = adminReports.find(x => x.id === id);
    if (r) {
        r.status = 'rejected';
        alert(`❌ Laporan ${r.code} berhasil DITOLAK.\nLaporan ini tidak akan dipublikasikan ke peta masyarakat.`);
        updateValCounts();
        renderValTable();
        closeAdminModal();
    }
}

function openAdminModal(id) {
    const r = adminReports.find(x => x.id === id);
    if (!r) return;

    document.getElementById('modalReportCode').textContent = `Laporan: ${r.code}`;
    document.getElementById('modalCategory').textContent = r.category;
    document.getElementById('modalTime').textContent = r.time;
    document.getElementById('modalAddress').textContent = r.address;
    document.getElementById('modalReporter').textContent = r.reporter;
    document.getElementById('modalDesc').textContent = r.description;
    document.getElementById('modalCoords').textContent = `${r.lat.toFixed(5)}, ${r.lng.toFixed(5)}`;

    const badge = document.getElementById('modalBadgeStatus');
    if (r.status === 'submitted') {
        badge.textContent = 'Menunggu Validasi';
        badge.style.background = '#fef3c7';
        badge.style.color = '#b45309';
    } else if (r.status === 'map_updated') {
        badge.textContent = 'Disetujui';
        badge.style.background = '#dcfce7';
        badge.style.color = '#15803d';
    } else {
        badge.textContent = 'Ditolak';
        badge.style.background = '#fee2e2';
        badge.style.color = '#b91c1c';
    }

    const actionBox = document.getElementById('modalActionButtons');
    if (r.status === 'submitted') {
        actionBox.innerHTML = `
            <button type="button" class="btn-val-reject" onclick="rejectReport(${r.id})" style="padding:9px 18px; font-size:13px;">✕ Tolak Laporan</button>
            <button type="button" class="btn-val-approve" onclick="approveReport(${r.id})" style="padding:9px 18px; font-size:13px;">✓ Setujui &amp; Publikasikan ke Peta</button>
        `;
    } else {
        actionBox.innerHTML = `
            <button type="button" onclick="closeAdminModal()" style="padding:9px 18px; border-radius:8px; border:1px solid #cbd5e1; background:#fff; font-size:13px; font-weight:700; cursor:pointer;">Tutup</button>
        `;
    }

    document.getElementById('modalDetailAdmin').style.display = 'flex';
}

function closeAdminModal() {
    document.getElementById('modalDetailAdmin').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    // Tangani parameter query seperti ?status=submitted atau ?code=SR-...
    const urlParams = new URLSearchParams(window.location.search);
    const presetStatus = urlParams.get('status');
    const presetCode = urlParams.get('code');

    if (presetCode) {
        document.getElementById('valSearchInput').value = presetCode;
    }
    if (presetStatus && ['submitted', 'map_updated', 'rejected'].includes(presetStatus)) {
        setValFilter(presetStatus);
    } else {
        setValFilter('all');
    }

    updateValCounts();
});
</script>
@endpush
@endsection
