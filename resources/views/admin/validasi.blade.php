@extends('layouts.app')

@section('title', 'Validasi & Moderasi Laporan · SafeRoad Administrator')

@push('leaflet-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

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
            <h1>Validasi &amp; Moderasi Laporan Masuk</h1>
            <p>Tinjau dan verifikasi setiap laporan kejadian yang diajukan oleh masyarakat. Laporan yang disetujui akan diteruskan ke petugas keamanan untuk pemeriksaan lapangan.</p>
        </div>
        <div class="vtc-right">
            <div class="status-summary-pills">
                <span class="ssp-item ssp-pending">
                    <strong><span id="statPendingNum">0</span> Laporan</strong> Menunggu Validasi
                </span>
                <span class="ssp-item ssp-ready">
                    <strong><span id="statReadyNum">0</span> Laporan</strong> Siap Difinalisasi
                </span>
            </div>
        </div>
    </header>

    <!-- Main Moderation Panel -->
    <div class="val-main-card">
        <!-- Controls & Filters Toolbar -->
        <div class="val-toolbar">
            <div class="val-tabs-wrap">
                <button type="button" class="val-tab active" id="tabPending" onclick="switchTab('pending_validation')">
                    Menunggu Validasi (<span id="countPending">0</span>)
                </button>
                <button type="button" class="val-tab" id="tabForwarded" onclick="switchTab('forwarded_to_security')">
                    Diteruskan ke Keamanan (<span id="countForwarded">0</span>)
                </button>
                <button type="button" class="val-tab" id="tabRevision" onclick="switchTab('needs_revision')">
                    Perlu Perbaikan (<span id="countRevision">0</span>)
                </button>
                <button type="button" class="val-tab" id="tabHandled" onclick="switchTab('handled')">
                    Ditangani (<span id="countHandled">0</span>)
                </button>
                <button type="button" class="val-tab" id="tabRejected" onclick="switchTab('rejected')">
                    Ditolak (<span id="countRejected">0</span>)
                </button>
            </div>

            <div class="val-filter-right">
                <select id="valCategoryFilter" class="val-select" onchange="filterCategory()">
                    <option value="all">Semua Kategori</option>
                    <option value="Pelecehan / Gangguan Keamanan">Pelecehan / Gangguan</option>
                    <option value="Pencurian">Tindak Kriminal / Begal</option>
                    <option value="Kondisi Jalan">Kondisi Jalan / Lampu</option>
                    <option value="Fasilitas Keamanan">Fasilitas Keamanan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>

                <div class="val-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="valSearchInput" placeholder="Cari kode / jalan / pelapor..." oninput="filterSearch()">
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
            <small id="autoUpdateText" style="font-size: 12px; color: #64748b; font-weight: 600;">Diperbarui otomatis · terakhir --:--:--</small>
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
                        <th>Dilaporkan</th>
                        <th>Pelapor</th>
                        <th>Status Validasi</th>
                        <th style="text-align: right; min-width: 200px;">Tindakan Administrator</th>
                    </tr>
                </thead>
                <tbody id="valTableBody">
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #64748b;">
                            Memuat data laporan...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail & Validasi Laporan -->
<div id="modalDetailAdmin" class="val-modal-backdrop" style="display:none;">
    <div class="val-modal-box" style="max-width: 620px; max-height: 90vh; overflow-y: auto;">
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
                    <span class="vmb-label">Identitas Pelapor:</span>
                    <span id="modalReporter">-</span>
                </div>
            </div>

            <div class="vmb-grid-2">
                <div class="vmb-field">
                    <span class="vmb-label">Waktu Kejadian:</span>
                    <span id="modalIncidentAt">-</span>
                </div>
                <div class="vmb-field">
                    <span class="vmb-label">Dilaporkan Pada:</span>
                    <span id="modalReportedAt">-</span>
                </div>
            </div>

            <div class="vmb-field">
                <span class="vmb-label">Lokasi / Alamat Kejadian:</span>
                <span id="modalAddress" style="font-weight: 600; color: #1e293b;">-</span>
                <div id="adminModalMap" style="height: 160px; width: 100%; border-radius: 12px; margin-top: 6px; border: 1.5px solid var(--border-soft); display: none;"></div>
            </div>

            <div class="vmb-field">
                <span class="vmb-label">Deskripsi Kejadian dari Warga:</span>
                <p id="modalDesc" class="vmb-desc-box">-</p>
            </div>

            <!-- Hasil Pemeriksaan Petugas (Jika Ada) -->
            <div id="modalSecuritySection" style="display: none; background: #eff6ff; border: 1px solid #dbeafe; border-radius: 12px; padding: 14px;">
                <span class="vmb-label" style="color: #1e40af;">Hasil Pemeriksaan Petugas Keamanan</span>
                <div style="font-size: 13px; color: #0f172a; margin-top: 6px; line-height: 1.5;">
                    <div><strong>Petugas Pemeriksa:</strong> <span id="modalOfficerName">-</span></div>
                    <div><strong>Waktu Konfirmasi:</strong> <span id="modalOfficerTime">-</span></div>
                    <div><strong>Perlu Tindakan Lapangan:</strong> <span id="modalNeedsAction">-</span></div>
                    <div style="margin-top: 4px;"><strong>Catatan Pemeriksaan:</strong> <span id="modalInspectionNote">-</span></div>
                    <div id="modalActionNoteWrap" style="margin-top: 4px; display: none;"><strong>Catatan Tindakan:</strong> <span id="modalActionNote">-</span></div>
                </div>
            </div>

            <!-- Catatan Perbaikan / Penolakan Admin (Jika Ada) -->
            <div id="modalAdminNoteSection" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                <span class="vmb-label" style="color: #0f172a;">Catatan Admin</span>
                <p id="modalAdminNote" style="font-size: 13px; color: #64748b; margin-top: 4px;">-</p>
            </div>

            <!-- Timeline Status -->
            <div class="vmb-field">
                <span class="vmb-label">Riwayat &amp; Timeline Status:</span>
                <div id="modalTimeline" style="display: flex; flex-direction: column; gap: 8px; margin-top: 6px;"></div>
            </div>

            <!-- Form Aksi Perbaikan / Penolakan (Hidden by default) -->
            <div id="boxRevisionInput" style="display:none; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px;">
                <label for="inputAdminNote" style="font-size:12px; font-weight:700; color:#0f172a;">Catatan Perbaikan untuk Pelapor (Wajib):</label>
                <textarea id="inputAdminNote" rows="2" style="width:100%; margin-top:6px; padding:8px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px;" placeholder="Tuliskan bagian laporan yang perlu diperbaiki..."></textarea>
                <div style="display:flex; justify-content:flex-end; gap:6px; margin-top:8px;">
                    <button type="button" onclick="cancelActionBox()" style="padding:6px 12px; border-radius:6px; border:1px solid #e2e8f0; background:#fff; font-size:12px; font-weight:700; cursor:pointer;">Batal</button>
                    <button type="button" onclick="submitRevision()" style="padding:6px 12px; border-radius:6px; border:none; background:#2563eb; color:#fff; font-size:12px; font-weight:700; cursor:pointer;">Kirim Permintaan Perbaikan</button>
                </div>
            </div>

            <div id="boxRejectInput" style="display:none; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:12px;">
                <label for="inputRejectReason" style="font-size:12px; font-weight:700; color:#991b1b;">Alasan Penolakan Laporan (Wajib):</label>
                <textarea id="inputRejectReason" rows="2" style="width:100%; margin-top:6px; padding:8px; border:1.5px solid #fecaca; border-radius:8px; font-size:13px;" placeholder="Tuliskan alasan penolakan laporan..."></textarea>
                <div style="display:flex; justify-content:flex-end; gap:6px; margin-top:8px;">
                    <button type="button" onclick="cancelActionBox()" style="padding:6px 12px; border-radius:6px; border:1px solid #cbd5e1; background:#fff; font-size:12px; font-weight:700; cursor:pointer;">Batal</button>
                    <button type="button" onclick="submitReject()" style="padding:6px 12px; border-radius:6px; border:none; background:#dc2626; color:#fff; font-size:12px; font-weight:700; cursor:pointer;">Tolak Laporan</button>
                </div>
            </div>
        </div>
        <div class="vmb-footer" id="modalActionButtons">
            <!-- Disuntikkan sesuai status -->
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="adminToast" style="position:fixed; bottom:24px; right:24px; z-index:99999; padding:12px 20px; border-radius:12px; color:#fff; font-size:13.5px; font-weight:700; box-shadow:0 10px 25px rgba(0,0,0,0.2); display:none;"></div>

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
.ssp-pending { background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.25); color: #ffffff; }
.ssp-ready { background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.25); color: #dbeafe; }
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
    margin-bottom: 16px;
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

.highlight-new-row {
    background-color: #eff6ff !important;
    transition: background-color 1.5s ease;
}

.badge-new-tag {
    display: inline-block;
    background: #2563eb;
    color: #fff;
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 5px;
    border-radius: 4px;
    margin-left: 5px;
}

.btn-val-approve {
    padding: 6px 12px;
    border-radius: 7px;
    background: #2563eb;
    color: #ffffff;
    border: 1px solid #2563eb;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s;
}
.btn-val-approve:hover { background: #1e40af; }

.btn-val-revision {
    padding: 6px 12px;
    border-radius: 7px;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #dbeafe;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    margin-left: 4px;
    transition: background 0.15s;
}
.btn-val-revision:hover { background: #dbeafe; }

.btn-val-reject {
    padding: 6px 12px;
    border-radius: 7px;
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    margin-left: 4px;
    transition: background 0.15s;
}
.btn-val-reject:hover { background: #fecaca; }

.btn-val-finalize {
    padding: 6px 14px;
    border-radius: 7px;
    background: #1e40af;
    color: #ffffff;
    border: none;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s;
}
.btn-val-finalize:hover { background: #1e3a8a; }

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
    width: min(100%, 620px);
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
    flex-wrap: wrap;
}
</style>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let currentTab = 'pending_validation';
    let loadedReports = [];
    let selectedReportId = null;
    let modalMap = null;
    let modalMarker = null;
    let lastServerTime = null;
    let pollingInterval = null;
    let timeAgoInterval = null;

    // Standar helper timeAgo
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

    function showToast(msg, type = 'success') {
        const toast = document.getElementById('adminToast');
        if (!toast) return;
        toast.textContent = msg;
        toast.style.background = type === 'error' ? '#dc2626' : '#2563eb';
        toast.style.display = 'block';
        clearTimeout(toast._t);
        toast._t = setTimeout(() => { toast.style.display = 'none'; }, 4000);
    }

    // Status pill generator (Monochromatic Blue)
    function renderStatusPill(status, isConfirmed) {
        switch (status) {
            case 'pending_validation':
                return '<span style="padding:3px 9px;border-radius:6px;background:#f8fafc;border:1px solid #e2e8f0;color:#64748b;font-size:11px;font-weight:700;">Menunggu Validasi</span>';
            case 'forwarded_to_security':
                if (isConfirmed) {
                    return '<span style="padding:3px 9px;border-radius:6px;background:#dbeafe;color:#1e40af;font-size:11px;font-weight:700;">Diperiksa Petugas (Siap Finalisasi)</span>';
                }
                return '<span style="padding:3px 9px;border-radius:6px;background:#eff6ff;border:1px solid #dbeafe;color:#2563eb;font-size:11px;font-weight:700;">Diteruskan ke Keamanan</span>';
            case 'needs_revision':
                return '<span style="padding:3px 9px;border-radius:6px;background:#f8fafc;border:1px solid #cbd5e1;color:#0f172a;font-size:11px;font-weight:700;">Perlu Perbaikan</span>';
            case 'handled':
                return '<span style="padding:3px 9px;border-radius:6px;background:#dbeafe;color:#1e40af;font-size:11px;font-weight:700;">Ditangani (Tayang)</span>';
            case 'rejected':
                return '<span style="padding:3px 9px;border-radius:6px;background:#fee2e2;color:#dc2626;font-size:11px;font-weight:700;">Ditolak</span>';
            default:
                return `<span style="padding:3px 9px;border-radius:6px;background:#f8fafc;color:#64748b;font-size:11px;font-weight:700;">${status}</span>`;
        }
    }

    // Render Tabel
    function renderTable() {
        const tbody = document.getElementById('valTableBody');
        const catVal = document.getElementById('valCategoryFilter').value;
        const searchVal = document.getElementById('valSearchInput').value.toLowerCase().trim();

        const filtered = loadedReports.filter(r => {
            const matchStatus = (r.status === currentTab);
            const matchCat = (catVal === 'all' || r.category === catVal);
            const matchSearch = (!searchVal ||
                (r.report_code && r.report_code.toLowerCase().includes(searchVal)) ||
                (r.address && r.address.toLowerCase().includes(searchVal)) ||
                (r.reporter_name && r.reporter_name.toLowerCase().includes(searchVal)) ||
                (r.description && r.description.toLowerCase().includes(searchVal))
            );
            return matchStatus && matchCat && matchSearch;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px 20px; color: #64748b;">
                        <strong style="display: block; font-size: 15px; color: #0f172a;">Tidak ada laporan pada kategori ini</strong>
                        <p style="font-size: 12.5px; margin-top: 4px;">Belum ada laporan dengan status yang dipilih atau sesuaikan pencarian.</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filtered.map(r => {
            const isConfirmed = !!r.security_confirmed_at;
            const newBadge = r._isNew ? '<span class="badge-new-tag">BARU</span>' : '';
            const rowClass = r._isNew ? 'highlight-new-row' : '';

            return `
                <tr class="${rowClass}" id="reportRow-${r.id}">
                    <td>
                        <strong style="color: #0f172a; font-family: monospace; font-size: 13.5px;">${r.report_code}</strong>
                        ${newBadge}
                    </td>
                    <td><span style="font-weight: 700; color: #2563eb;">${r.category}</span></td>
                    <td><span>${r.address || '-'}</span></td>
                    <td><span style="font-size: 12px; color: #475569;" title="${formatFullDateTime(r.incident_at)}">${timeAgo(r.incident_at)}</span></td>
                    <td><span style="font-size: 12px; color: #64748b;" title="${formatFullDateTime(r.reported_at)}">${timeAgo(r.reported_at)}</span></td>
                    <td>
                        <span style="font-size: 12px; color: #334155;">
                            ${r.is_anonymous ? '<em>(Anonim)</em>' : (r.reporter_name || 'Anonim')}
                        </span>
                    </td>
                    <td>${renderStatusPill(r.status, isConfirmed)}</td>
                    <td style="text-align: right; white-space: nowrap;">
                        <button type="button" class="btn-val-detail" onclick="window.adminApp.openModal(${r.id})">Detail</button>
                        ${r.status === 'pending_validation' ? `
                            <button type="button" class="btn-val-approve" onclick="window.adminApp.quickApprove(${r.id})">Setujui</button>
                            <button type="button" class="btn-val-revision" onclick="window.adminApp.promptRevision(${r.id})">Perbaikan</button>
                            <button type="button" class="btn-val-reject" onclick="window.adminApp.promptReject(${r.id})">Tolak</button>
                        ` : ''}
                        ${r.status === 'forwarded_to_security' && isConfirmed ? `
                            <button type="button" class="btn-val-finalize" onclick="window.adminApp.quickFinalize(${r.id})">Finalisasi</button>
                        ` : ''}
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Muat jumlah data (counts)
    async function fetchCounts() {
        try {
            const resp = await fetch('{{ url("/admin/data/reports/counts") }}', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            if (!resp.ok) return;
            const res = await resp.json();
            if (res.success && res.data) {
                const c = res.data;
                document.getElementById('countPending').textContent = c.pending_validation || 0;
                document.getElementById('countForwarded').textContent = c.forwarded_to_security || 0;
                document.getElementById('countRevision').textContent = c.needs_revision || 0;
                document.getElementById('countHandled').textContent = c.handled || 0;
                document.getElementById('countRejected').textContent = c.rejected || 0;

                document.getElementById('statPendingNum').textContent = c.pending_validation || 0;
                document.getElementById('statReadyNum').textContent = c.ready_to_finalize || 0;

                // Update Navbar Badges
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
        } catch (e) {
            // Silently retry on next interval
        }
    }

    // Muat data laporan utama
    async function fetchReports(tabStatus) {
        try {
            const url = `{{ url("/admin/data/reports") }}?status=${tabStatus}`;
            const resp = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            if (!resp.ok) return;
            const res = await resp.json();
            if (res.success && Array.isArray(res.data)) {
                loadedReports = res.data;
                if (res.server_time) lastServerTime = res.server_time;
                renderTable();
                updateTimestampText();
            }
        } catch (e) {
            // Silently retry
        }
    }

    // Polling Realtime setiap 10 detik
    async function pollRealtime() {
        if (!lastServerTime) return;
        try {
            const url = `{{ url("/admin/data/reports") }}?since=${encodeURIComponent(lastServerTime)}`;
            const resp = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            if (!resp.ok) return;
            const res = await resp.json();

            if (res.success && res.server_time) {
                lastServerTime = res.server_time;
            }

            if (res.success && Array.isArray(res.data) && res.data.length > 0) {
                let hasNew = false;
                res.data.forEach(item => {
                    item._isNew = true;
                    const idx = loadedReports.findIndex(r => r.id === item.id);
                    if (idx >= 0) {
                        loadedReports[idx] = item;
                    } else {
                        loadedReports.unshift(item);
                        hasNew = true;
                    }

                    // Hapus highlight setelah 5 detik
                    setTimeout(() => {
                        item._isNew = false;
                        const row = document.getElementById(`reportRow-${item.id}`);
                        if (row) {
                            row.classList.remove('highlight-new-row');
                            const tag = row.querySelector('.badge-new-tag');
                            if (tag) tag.remove();
                        }
                    }, 5000);
                });

                if (hasNew) {
                    showToast('1 laporan baru masuk', 'info');
                }
                renderTable();
            }

            // Selalu perbarui counts saat polling
            await fetchCounts();
            updateTimestampText();
        } catch (e) {
            // Silently retry next time
        }
    }

    function updateTimestampText() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const el = document.getElementById('autoUpdateText');
        if (el) el.textContent = `Diperbarui otomatis · terakhir ${timeStr}`;
    }

    // Tab switching
    window.switchTab = function (status) {
        currentTab = status;
        document.querySelectorAll('.val-tab').forEach(t => t.classList.remove('active'));
        if (status === 'pending_validation') document.getElementById('tabPending').classList.add('active');
        else if (status === 'forwarded_to_security') document.getElementById('tabForwarded').classList.add('active');
        else if (status === 'needs_revision') document.getElementById('tabRevision').classList.add('active');
        else if (status === 'handled') document.getElementById('tabHandled').classList.add('active');
        else if (status === 'rejected') document.getElementById('tabRejected').classList.add('active');

        fetchReports(currentTab);
    };

    window.filterCategory = function () { renderTable(); };
    window.filterSearch = function () { renderTable(); };

    // Detail Modal Handlers
    window.adminApp = {
        openModal: async function (id) {
            selectedReportId = id;
            try {
                const resp = await fetch(`{{ url("/admin/data/reports") }}/${id}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                if (!resp.ok) throw new Error('Gagal memuat detail laporan.');
                const res = await resp.json();
                if (!res.success || !res.data) throw new Error('Data tidak ditemukan.');
                const r = res.data;

                document.getElementById('modalReportCode').textContent = `Laporan: ${r.report_code}`;
                document.getElementById('modalCategory').textContent = r.category;
                document.getElementById('modalReporter').textContent = r.is_anonymous ? 'Anonim (Disembunyikan)' : (r.reporter_name || 'Anonim');
                document.getElementById('modalIncidentAt').textContent = formatFullDateTime(r.incident_at);
                document.getElementById('modalReportedAt').textContent = formatFullDateTime(r.reported_at);
                document.getElementById('modalAddress').textContent = r.address || '-';
                document.getElementById('modalDesc').textContent = r.description || '-';

                // Status Badge
                const badge = document.getElementById('modalBadgeStatus');
                badge.innerHTML = renderStatusPill(r.status, !!r.security_confirmed_at);

                // Leaflet Map
                const mapEl = document.getElementById('adminModalMap');
                if (r.latitude && r.longitude) {
                    mapEl.style.display = 'block';
                    if (!modalMap) {
                        modalMap = L.map('adminModalMap', { zoomControl: false });
                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(modalMap);
                    }
                    modalMap.setView([r.latitude, r.longitude], 15);
                    if (modalMarker) modalMap.removeLayer(modalMarker);
                    modalMarker = L.marker([r.latitude, r.longitude]).addTo(modalMap);
                    setTimeout(() => modalMap.invalidateSize(), 200);
                } else {
                    mapEl.style.display = 'none';
                }

                // Security Section
                const secSection = document.getElementById('modalSecuritySection');
                if (r.security_confirmed_at) {
                    secSection.style.display = 'block';
                    document.getElementById('modalOfficerName').textContent = r.inspected_by_name || 'Petugas Keamanan';
                    document.getElementById('modalOfficerTime').textContent = formatFullDateTime(r.security_confirmed_at);
                    document.getElementById('modalNeedsAction').textContent = r.needs_action ? 'Ya (Memerlukan Tindakan Lapangan)' : 'Tidak (Kondusif)';
                    document.getElementById('modalInspectionNote').textContent = r.inspection_note || '-';
                    const actWrap = document.getElementById('modalActionNoteWrap');
                    if (r.needs_action && r.action_note) {
                        actWrap.style.display = 'block';
                        document.getElementById('modalActionNote').textContent = r.action_note;
                    } else {
                        actWrap.style.display = 'none';
                    }
                } else if (r.status === 'forwarded_to_security') {
                    secSection.style.display = 'block';
                    secSection.style.background = '#eff6ff';
                    secSection.style.borderColor = '#bfdbfe';
                    secSection.innerHTML = '<span class="vmb-label" style="color: #1e40af;">Menunggu Pemeriksaan Petugas Keamanan Lapangan</span>';
                } else {
                    secSection.style.display = 'none';
                }

                // Admin Note Section
                const adminNoteSection = document.getElementById('modalAdminNoteSection');
                if (r.admin_note) {
                    adminNoteSection.style.display = 'block';
                    document.getElementById('modalAdminNote').textContent = r.admin_note;
                } else {
                    adminNoteSection.style.display = 'none';
                }

                // Timeline
                const tl = document.getElementById('modalTimeline');
                if (Array.isArray(r.histories) && r.histories.length > 0) {
                    tl.innerHTML = r.histories.map(h => `
                        <div style="display:flex; align-items:flex-start; gap:8px; font-size:12.5px; border-left:2px solid #cbd5e1; padding-left:10px; margin-left:4px;">
                            <div style="flex:1;">
                                <strong style="color:#0f172a;">${h.status_label || h.status}</strong>
                                <span style="color:#64748b; font-size:11.5px; margin-left:6px;">${timeAgo(h.created_at)}</span>
                                <div style="color:#334155; margin-top:2px;">${h.note || '-'}</div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    tl.innerHTML = '<span style="font-size:12px; color:#64748b;">Belum ada riwayat aktivitas.</span>';
                }

                // Action Buttons
                cancelActionBox();
                const btnBox = document.getElementById('modalActionButtons');
                if (r.status === 'pending_validation') {
                    btnBox.innerHTML = `
                        <button type="button" class="btn-val-reject" onclick="window.adminApp.promptReject(${r.id})" style="padding:9px 16px;">Tolak</button>
                        <button type="button" class="btn-val-revision" onclick="window.adminApp.promptRevision(${r.id})" style="padding:9px 16px;">Minta Perbaikan</button>
                        <button type="button" class="btn-val-approve" onclick="window.adminApp.quickApprove(${r.id})" style="padding:9px 18px;">Setujui &amp; Teruskan ke Keamanan</button>
                    `;
                } else if (r.status === 'forwarded_to_security' && r.security_confirmed_at) {
                    btnBox.innerHTML = `
                        <button type="button" onclick="closeAdminModal()" style="padding:9px 16px; border-radius:8px; border:1px solid #cbd5e1; background:#fff; font-size:13px; font-weight:700; cursor:pointer;">Tutup</button>
                        <button type="button" class="btn-val-finalize" onclick="window.adminApp.quickFinalize(${r.id})" style="padding:9px 20px; font-size:13px;">Finalisasi (Tayangkan ke Peta)</button>
                    `;
                } else {
                    btnBox.innerHTML = `
                        <button type="button" onclick="closeAdminModal()" style="padding:9px 18px; border-radius:8px; border:1px solid #cbd5e1; background:#fff; font-size:13px; font-weight:700; cursor:pointer;">Tutup</button>
                    `;
                }

                document.getElementById('modalDetailAdmin').style.display = 'flex';
            } catch (err) {
                showToast(err.message, 'error');
            }
        },

        quickApprove: async function (id) {
            try {
                const resp = await fetch(`{{ url("/admin/data/reports") }}/${id}/approve`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                const res = await resp.json();
                if (resp.ok && res.success) {
                    showToast('Laporan disetujui dan diteruskan ke petugas keamanan.', 'success');
                    closeAdminModal();
                    await fetchReports(currentTab);
                    await fetchCounts();
                } else {
                    throw new Error(res.message || 'Gagal menyetujui laporan.');
                }
            } catch (e) {
                showToast(e.message, 'error');
            }
        },

        promptRevision: function (id) {
            selectedReportId = id;
            document.getElementById('modalDetailAdmin').style.display = 'flex';
            document.getElementById('boxRevisionInput').style.display = 'block';
            document.getElementById('boxRejectInput').style.display = 'none';
            document.getElementById('inputAdminNote').focus();
        },

        promptReject: function (id) {
            selectedReportId = id;
            document.getElementById('modalDetailAdmin').style.display = 'flex';
            document.getElementById('boxRejectInput').style.display = 'block';
            document.getElementById('boxRevisionInput').style.display = 'none';
            document.getElementById('inputRejectReason').focus();
        },

        quickFinalize: async function (id) {
            try {
                const resp = await fetch(`{{ url("/admin/data/reports") }}/${id}/finalize`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                const res = await resp.json();
                if (resp.ok && res.success) {
                    showToast('Laporan berhasil difinalisasi dan ditandai Ditangani.', 'success');
                    closeAdminModal();
                    await fetchReports(currentTab);
                    await fetchCounts();
                } else {
                    throw new Error(res.message || 'Gagal finalisasi laporan.');
                }
            } catch (e) {
                showToast(e.message, 'error');
            }
        }
    };

    window.closeAdminModal = function () {
        document.getElementById('modalDetailAdmin').style.display = 'none';
        cancelActionBox();
    };

    window.cancelActionBox = function () {
        const revBox = document.getElementById('boxRevisionInput');
        const rejBox = document.getElementById('boxRejectInput');
        if (revBox) revBox.style.display = 'none';
        if (rejBox) rejBox.style.display = 'none';
        const inNote = document.getElementById('inputAdminNote');
        const inRej = document.getElementById('inputRejectReason');
        if (inNote) inNote.value = '';
        if (inRej) inRej.value = '';
    };

    window.submitRevision = async function () {
        const note = document.getElementById('inputAdminNote').value.trim();
        if (!note) {
            alert('Catatan perbaikan wajib diisi.');
            return;
        }

        try {
            const resp = await fetch(`{{ url("/admin/data/reports") }}/${selectedReportId}/revision`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({ admin_note: note })
            });
            const res = await resp.json();
            if (resp.ok && res.success) {
                showToast('Permintaan perbaikan dikirim ke pelapor.', 'success');
                closeAdminModal();
                await fetchReports(currentTab);
                await fetchCounts();
            } else {
                throw new Error(res.message || 'Gagal memproses perbaikan.');
            }
        } catch (e) {
            showToast(e.message, 'error');
        }
    };

    window.submitReject = async function () {
        const reason = document.getElementById('inputRejectReason').value.trim();
        if (!reason) {
            alert('Alasan penolakan wajib diisi.');
            return;
        }

        try {
            const resp = await fetch(`{{ url("/admin/data/reports") }}/${selectedReportId}/reject`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({ rejection_reason: reason })
            });
            const res = await resp.json();
            if (resp.ok && res.success) {
                showToast('Laporan berhasil ditolak.', 'success');
                closeAdminModal();
                await fetchReports(currentTab);
                await fetchCounts();
            } else {
                throw new Error(res.message || 'Gagal menolak laporan.');
            }
        } catch (e) {
            showToast(e.message, 'error');
        }
    };

    // Inisialisasi & Polling Lifecycle
    function startPolling() {
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(pollRealtime, 10000);
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    // Visibility change handler
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopPolling();
        } else {
            pollRealtime();
            startPolling();
        }
    });

    document.addEventListener('DOMContentLoaded', async () => {
        const urlParams = new URLSearchParams(window.location.search);
        const presetStatus = urlParams.get('status');
        const presetCode = urlParams.get('code');

        if (presetCode) {
            document.getElementById('valSearchInput').value = presetCode;
        }

        if (presetStatus && ['pending_validation', 'forwarded_to_security', 'needs_revision', 'handled', 'rejected'].includes(presetStatus)) {
            currentTab = presetStatus;
        }

        switchTab(currentTab);
        await fetchCounts();
        startPolling();

        // Update relative time setiap 30 detik
        timeAgoInterval = setInterval(() => {
            renderTable();
        }, 30000);
    });
})();
</script>
@endpush
@endsection
