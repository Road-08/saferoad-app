@extends('layouts.app')

@section('title', 'Dashboard Petugas Keamanan · SafeRoad')

@section('content')
<div class="keamanan-container">
    <!-- Top Officer Banner -->
    <header class="officer-hero-banner">
        <div class="ohb-left">
            <span class="ohb-badge">PORTAL KOMANDO PETUGAS KEAMANAN</span>
            <h1>Pusat Operasional &amp; Verifikasi Lapangan</h1>
            <p>Pantau laporan kejadian masyarakat, verifikasi kondisi langsung di TKP, dan perbarui status keamanan rute.</p>
        </div>
        <div class="ohb-right">
            <div class="officer-status-badge">
                <span class="duty-dot"></span>
                <span>Petugas Siaga: <strong>Sektor Surabaya Selatan</strong></span>
            </div>
        </div>
    </header>

    <!-- Officer Stat Cards -->
    <div class="officer-stats-grid">
        <div class="stat-box box-amber">
            <div class="sb-icon">🚨</div>
            <div>
                <h3>Butuh Cek Lapangan</h3>
                <strong class="sb-num" id="statPendingCount">4</strong>
                <span class="sb-sub">Menunggu patroli petugas</span>
            </div>
        </div>
        <div class="stat-box box-blue">
            <div class="sb-icon">🛡️</div>
            <div>
                <h3>Titik Fasilitas Siaga</h3>
                <strong class="sb-num">18</strong>
                <span class="sb-sub">CCTV &amp; Pos Jaga Aktif</span>
            </div>
        </div>
        <div class="stat-box box-green">
            <div class="sb-icon">✅</div>
            <div>
                <h3>Laporan Diverifikasi</h3>
                <strong class="sb-num" id="statDoneCount">12</strong>
                <span class="sb-sub">Selesai diperiksa minggu ini</span>
            </div>
        </div>
        <div class="stat-box box-red">
            <div class="sb-icon">🚩</div>
            <div>
                <h3>Laporan Di-flag</h3>
                <strong class="sb-num">2</strong>
                <span class="sb-sub">Indikasi laporan tidak valid</span>
            </div>
        </div>
    </div>

    <!-- Patrol Map & Quick Tasks -->
    <div class="officer-main-grid">
        <!-- Tabel Laporan Lapangan -->
        <div class="officer-table-card">
            <div class="otc-header">
                <div>
                    <h2>Daftar Tugas Verifikasi Lapangan (TKP)</h2>
                    <p class="otc-sub">Daftar laporan warga yang perlu diverifikasi kebenarannya oleh personil di lapangan.</p>
                </div>
                <div class="otc-filters">
                    <button type="button" class="otc-filter-btn active" data-filter="all">Semua</button>
                    <button type="button" class="otc-filter-btn" data-filter="officer_check">Butuh Dicek</button>
                    <button type="button" class="otc-filter-btn" data-filter="map_updated">Terverifikasi</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="officer-table">
                    <thead>
                        <tr>
                            <th>Kode &amp; Waktu</th>
                            <th>Kategori Kejadian</th>
                            <th>Lokasi / Alamat TKP</th>
                            <th>Status Penanganan</th>
                            <th style="text-align: right;">Tindakan Petugas</th>
                        </tr>
                    </thead>
                    <tbody id="officerTableBody">
                        <!-- Disuntikkan via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail & Verifikasi Laporan -->
<div id="modalDetailLaporan" class="officer-modal-backdrop" style="display:none;">
    <div class="officer-modal-box">
        <div class="omb-header">
            <h3 id="modalReportCode">Detail Laporan TKP</h3>
            <button type="button" class="omb-close" onclick="closeModalDetail()">✕</button>
        </div>
        <div class="omb-body">
            <div class="omb-info-row">
                <span class="omb-label">Kategori:</span>
                <strong id="modalCategory">-</strong>
            </div>
            <div class="omb-info-row">
                <span class="omb-label">Alamat / Patokan:</span>
                <span id="modalAddress">-</span>
            </div>
            <div class="omb-info-row">
                <span class="omb-label">Deskripsi Warga:</span>
                <p id="modalDesc" class="omb-desc-box">-</p>
            </div>
            <div class="omb-info-row">
                <span class="omb-label">Titik Koordinat:</span>
                <code id="modalCoords">-</code>
            </div>

            <div class="omb-action-form">
                <label for="officerNotes"><strong>Catatan Pemeriksaan Lapangan:</strong></label>
                <textarea id="officerNotes" class="omb-textarea" rows="3" placeholder="Contoh: Petugas telah memeriksa lokasi. Lampu jalan telah diperbaiki dan situasi kembali kondusif."></textarea>
            </div>
        </div>
        <div class="omb-footer">
            <button type="button" class="btn-flag-suspicious" id="btnFlagReport">
                🚩 Tandai Mencurigakan / Palsu
            </button>
            <button type="button" class="btn-verify-success" id="btnConfirmVerify">
                ✓ Selesai Diverifikasi (TKP Aman)
            </button>
        </div>
    </div>
</div>

<style>
.keamanan-container {
    max-width: 1400px;
    margin: 28px auto 60px;
    padding: 0 clamp(16px, 3vw, 36px);
}
.officer-hero-banner {
    background: linear-gradient(135deg, #0f2d59 0%, #1e3a8a 100%);
    border-radius: 20px;
    padding: 32px clamp(20px, 4vw, 40px);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    margin-bottom: 28px;
    box-shadow: 0 15px 35px -10px rgba(15, 45, 89, 0.35);
    flex-wrap: wrap;
}
.ohb-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.18);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    color: #bfdbfe;
    margin-bottom: 10px;
}
.ohb-left h1 {
    font-size: clamp(22px, 3vw, 30px);
    font-weight: 800;
    letter-spacing: -0.02em;
    margin-bottom: 6px;
}
.ohb-left p {
    font-size: 14px;
    color: #dbeafe;
    max-width: 620px;
    margin: 0;
}
.officer-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(16, 185, 129, 0.2);
    border: 1px solid rgba(16, 185, 129, 0.4);
    padding: 8px 16px;
    border-radius: 9999px;
    font-size: 12.5px;
    color: #a7f3d0;
}
.duty-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #34d399;
    box-shadow: 0 0 6px #34d399;
}

/* Stat Grid */
.officer-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}
.stat-box {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.sb-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}
.box-amber .sb-icon { background: #fef3c7; }
.box-blue .sb-icon  { background: #eff6ff; }
.box-green .sb-icon { background: #dcfce7; }
.box-red .sb-icon   { background: #fee2e2; }

.stat-box h3 { font-size: 12.5px; font-weight: 700; color: var(--text-muted); margin: 0 0 2px; }
.sb-num { font-size: 26px; font-weight: 800; color: var(--text-heading); display: block; line-height: 1.1; }
.sb-sub { font-size: 11px; color: var(--text-muted); }

/* Table Section */
.officer-table-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}
.otc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.otc-header h2 { font-size: 18px; font-weight: 800; color: var(--text-heading); margin: 0; }
.otc-sub { font-size: 13px; color: var(--text-muted); margin: 2px 0 0; }
.otc-filters { display: flex; gap: 8px; }
.otc-filter-btn {
    padding: 6px 14px;
    border-radius: 8px;
    border: 1px solid var(--border-soft);
    background: #f8fafc;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
}
.otc-filter-btn.active {
    background: var(--primary-600);
    border-color: var(--primary-600);
    color: #fff;
}

.table-responsive { overflow-x: auto; }
.officer-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
.officer-table th {
    padding: 12px 16px;
    background: #f8fafc;
    color: var(--text-muted);
    font-weight: 800;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1.5px solid var(--border-soft);
}
.officer-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-soft);
    color: var(--text-body);
}
.ot-code { font-weight: 800; color: var(--primary-700); }
.ot-time { font-size: 11px; color: var(--text-muted); display: block; }
.btn-action-inspect {
    padding: 6px 12px;
    border-radius: 8px;
    background: #eff6ff;
    color: var(--primary-700);
    border: 1px solid #bfdbfe;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
}
.btn-action-inspect:hover {
    background: var(--primary-600);
    color: #fff;
}

/* Modal */
.officer-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.officer-modal-box {
    background: #fff;
    border-radius: 20px;
    max-width: 580px;
    width: 100%;
    padding: 28px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.omb-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}
.omb-header h3 { font-size: 17px; font-weight: 800; margin: 0; color: var(--text-heading); }
.omb-close { background: none; border: none; font-size: 18px; cursor: pointer; color: var(--text-muted); }
.omb-info-row { margin-bottom: 10px; font-size: 13.5px; }
.omb-label { font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 2px; }
.omb-desc-box { background: #f8fafc; border: 1px solid var(--border-soft); border-radius: 8px; padding: 10px; margin: 4px 0 0; }
.omb-textarea {
    width: 100%;
    border: 1.5px solid var(--border-soft);
    border-radius: 10px;
    padding: 10px;
    font-size: 13px;
    margin-top: 6px;
    box-sizing: border-box;
}
.omb-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid var(--border-soft);
}
.btn-flag-suspicious {
    padding: 9px 14px;
    border-radius: 10px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
}
.btn-verify-success {
    padding: 9px 16px;
    border-radius: 10px;
    background: #16a34a;
    border: none;
    color: #fff;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
}
</style>

@push('scripts')
<script>
let activeReports = [
    {
        id: 1,
        report_code: 'SR-2611-B892',
        category: 'Pelecehan / Gangguan Keamanan',
        address: 'Jl. Ketintang Barat No. 12',
        description: 'Pria tak dikenal membuntuti pejalan kaki saat melintas di gang dekat halte.',
        coords: '-7.3150, 112.7270',
        status: 'officer_check',
        created_at: '19:15 WIB'
    },
    {
        id: 2,
        report_code: 'SR-2611-C419',
        category: 'Pencurian / Begal',
        address: 'Simpang Lampu Merah Ketintang Timur',
        description: 'Upaya penjambretan tas motor saat malam hari.',
        coords: '-7.3138, 112.7295',
        status: 'officer_check',
        created_at: '18:40 WIB'
    },
    {
        id: 3,
        report_code: 'SR-2611-A104',
        category: 'Kondisi Jalan',
        address: 'Depan Gerbang Barat Kampus',
        description: 'Lampu jalan padam total sepanjang 200m.',
        coords: '-7.3120, 112.7260',
        status: 'map_updated',
        created_at: '16:20 WIB'
    }
];

let selectedReport = null;

function renderTable(filter = 'all') {
    const tbody = document.getElementById('officerTableBody');
    const filtered = filter === 'all' ? activeReports : activeReports.filter(r => r.status === filter);

    tbody.innerHTML = filtered.map(r => `
        <tr>
            <td>
                <span class="ot-code">${r.report_code}</span>
                <span class="ot-time">${r.created_at}</span>
            </td>
            <td><strong>${r.category}</strong></td>
            <td>${r.address}</td>
            <td>
                <span style="font-size:11.5px;font-weight:700;padding:3px 10px;border-radius:999px;background:${r.status === 'officer_check' ? '#fef3c7' : '#dcfce7'};color:${r.status === 'officer_check' ? '#b45309' : '#15803d'};">
                    ● ${r.status === 'officer_check' ? 'Butuh Cek TKP' : 'Terverifikasi Aman'}
                </span>
            </td>
            <td style="text-align: right;">
                <button type="button" class="btn-action-inspect" onclick="openModalDetail(${r.id})">
                    Periksa & Tindak →
                </button>
            </td>
        </tr>
    `).join('');
}

function openModalDetail(id) {
    selectedReport = activeReports.find(r => r.id === id);
    if (!selectedReport) return;

    document.getElementById('modalReportCode').textContent = `Pemeriksaan TKP: ${selectedReport.report_code}`;
    document.getElementById('modalCategory').textContent = selectedReport.category;
    document.getElementById('modalAddress').textContent = selectedReport.address;
    document.getElementById('modalDesc').textContent = selectedReport.description;
    document.getElementById('modalCoords').textContent = selectedReport.coords;
    document.getElementById('modalDetailLaporan').style.display = 'flex';
}

function closeModalDetail() {
    document.getElementById('modalDetailLaporan').style.display = 'none';
}

// Tindakan Verifikasi
document.getElementById('btnConfirmVerify').addEventListener('click', () => {
    if (selectedReport) {
        selectedReport.status = 'map_updated';
        alert(`✅ Laporan ${selectedReport.report_code} berhasil diverifikasi! Titik keamanan diperbarui pada peta.`);
        closeModalDetail();
        renderTable();
    }
});

// Tindakan Tandai Mencurigakan (Hit Backend Endpoint jika ID sesuai)
document.getElementById('btnFlagReport').addEventListener('click', async () => {
    if (!selectedReport) return;

    try {
        const resp = await fetch(`/laporan/${selectedReport.id}/tandai-mencurigakan`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const res = await resp.json();
        alert(res.message || 'Laporan berhasil ditandai sebagai mencurigakan.');
    } catch (e) {
        alert(`🚩 Laporan ${selectedReport.report_code} ditandai mencurigakan (Palsu) oleh petugas keamanan.`);
    }

    selectedReport.status = 'rejected';
    closeModalDetail();
    renderTable();
});

// Filter Tab Click
document.querySelectorAll('.otc-filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.otc-filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        renderTable(this.dataset.filter);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    renderTable();
});
</script>
@endpush
@endsection
