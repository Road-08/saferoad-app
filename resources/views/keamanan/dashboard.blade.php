@extends('layouts.app')

@section('title', 'Dashboard Petugas Keamanan · SafeRoad')

@push('leaflet-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="keamanan-container">
    <!-- Top Officer Banner -->
    <header class="officer-hero-banner">
        <div class="ohb-left">
            <span class="ohb-badge">PORTAL KOMANDO PETUGAS KEAMANAN</span>
            <h1>Pusat Operasional &amp; Pemeriksaan Lapangan</h1>
            <p>Pantau laporan kejadian yang telah disetujui admin, lakukan verifikasi di TKP, dan kirimkan hasil konfirmasi pemeriksaan kembali ke admin.</p>
        </div>
        <div class="ohb-right">
            <div class="officer-status-badge">
                <span class="duty-dot"></span>
                <span>Petugas Siaga: <strong>{{ auth()->user()->name ?? 'Personil Keamanan' }}</strong></span>
            </div>
        </div>
    </header>

    <!-- Officer Stat Cards -->
    <div class="officer-stats-grid">
        <div class="stat-box box-blue">
            <div class="sb-icon" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <h3>Belum Diperiksa (TKP)</h3>
                <strong class="sb-num" id="statPendingCount">0</strong>
                <span class="sb-sub">Menunggu patroli petugas</span>
            </div>
        </div>
        <div class="stat-box box-blue">
            <div class="sb-icon" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1e40af" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <h3>Sudah Dikonfirmasi</h3>
                <strong class="sb-num" id="statDoneCount">0</strong>
                <span class="sb-sub">Menunggu finalisasi admin</span>
            </div>
        </div>
        <div class="stat-box box-blue">
            <div class="sb-icon" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div>
                <h3>Total Diteruskan Admin</h3>
                <strong class="sb-num" id="statTotalCount">0</strong>
                <span class="sb-sub">Laporan masuk ke keamanan</span>
            </div>
        </div>
    </div>

    <!-- Patrol Map & Quick Tasks -->
    <div class="officer-main-grid">
        <!-- Tabel Laporan Lapangan -->
        <div class="officer-table-card" id="tabelAduan">
            <div class="otc-header">
                <div>
                    <h2>Daftar Tugas Pemeriksaan Lapangan (TKP)</h2>
                    <p class="otc-sub">Hanya menampilkan laporan masyarakat yang telah disetujui &amp; diteruskan oleh administrator.</p>
                </div>
                <div class="otc-filters">
                    <button type="button" class="otc-filter-btn active" id="tabUnconfirmedBtn" onclick="setKeamananTab('unconfirmed')">
                        Belum Diperiksa (<span id="keamananCountUnconfirmed">0</span>)
                    </button>
                    <button type="button" class="otc-filter-btn" id="tabConfirmedBtn" onclick="setKeamananTab('confirmed')">
                        Sudah Dikonfirmasi (<span id="keamananCountConfirmed">0</span>)
                    </button>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
                <small id="keamananAutoUpdateText" style="font-size: 12px; color: #64748b; font-weight: 600;">Diperbarui otomatis · terakhir --:--:--</small>
            </div>

            <div class="table-responsive">
                <table class="officer-table">
                    <thead>
                        <tr>
                            <th>Kode &amp; Waktu Kejadian</th>
                            <th>Kategori Kejadian</th>
                            <th>Lokasi / Alamat TKP</th>
                            <th>Pelapor</th>
                            <th>Status Pemeriksaan</th>
                            <th style="text-align: right;">Tindakan Petugas</th>
                        </tr>
                    </thead>
                    <tbody id="officerTableBody">
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                                Memuat daftar laporan...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail & Periksa Laporan -->
<div id="modalDetailLaporan" class="officer-modal-backdrop" style="display:none;">
    <div class="officer-modal-box" style="max-width: 620px; max-height: 90vh; overflow-y: auto;">
        <div class="omb-header">
            <div>
                <span id="modalOfficerBadge" style="font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 999px; background: #e0e7ff; color: #4338ca;">PEMERIKSAAN TKP</span>
                <h3 id="modalReportCode" style="margin-top: 4px; font-size: 18px; font-weight: 800; color: #0f172a;">Detail Laporan TKP</h3>
            </div>
            <button type="button" class="omb-close" onclick="closeModalDetail()">✕</button>
        </div>
        <div class="omb-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="omb-info-row">
                    <span class="omb-label">Kategori Insiden:</span>
                    <strong id="modalCategory" style="color: #2563eb;">-</strong>
                </div>
                <div class="omb-info-row">
                    <span class="omb-label">Identitas Pelapor:</span>
                    <span id="modalReporter">-</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="omb-info-row">
                    <span class="omb-label">Waktu Kejadian:</span>
                    <span id="modalIncidentAt">-</span>
                </div>
                <div class="omb-info-row">
                    <span class="omb-label">Diteruskan Admin:</span>
                    <span id="modalForwardedAt">-</span>
                </div>
            </div>

            <div class="omb-info-row">
                <span class="omb-label">Alamat / Patokan:</span>
                <span id="modalAddress" style="font-weight: 600; color: #1e293b;">-</span>
                <div id="keamananModalMap" style="height: 160px; width: 100%; border-radius: 12px; margin-top: 6px; border: 1.5px solid var(--border-soft); display: none;"></div>
            </div>

            <div class="omb-info-row">
                <span class="omb-label">Deskripsi Kejadian dari Warga:</span>
                <p id="modalDesc" class="omb-desc-box">-</p>
            </div>

            <!-- Timeline Status -->
            <div class="omb-info-row">
                <span class="omb-label">Riwayat &amp; Timeline Status:</span>
                <div id="modalTimelineKeamanan" style="display: flex; flex-direction: column; gap: 8px; margin-top: 6px;"></div>
            </div>

            <!-- Form Pemeriksaan (Jika Belum Dikonfirmasi) -->
            <div id="inspectionFormArea" class="omb-action-form" style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 14px; padding: 16px;">
                <h4 style="font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Form Hasil Pemeriksaan Petugas Lapangan</h4>

                <div style="margin-bottom: 12px;">
                    <label for="officerNotes" style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                        Catatan Hasil Pemeriksaan Lapangan <span style="color: #dc2626;">*</span>:
                    </label>
                    <textarea id="officerNotes" class="omb-textarea" rows="3" placeholder="Contoh: Petugas telah mendatangi TKP. Situasi saat ini terkendali dan lampu jalan sedang diperbaiki oleh dinas terkait..."></textarea>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
                        Apakah Memerlukan Tindakan Lanjutan? <span style="color: #dc2626;">*</span>
                    </label>
                    <div style="display: flex; gap: 20px; align-items: center; font-size: 13px;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <input type="radio" name="radioNeedsAction" value="0" checked onchange="toggleActionNoteField()">
                            <span>Tidak (Situasi Aman / Kondusif)</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <input type="radio" name="radioNeedsAction" value="1" onchange="toggleActionNoteField()">
                            <span>Ya (Perlu Tindakan Khusus)</span>
                        </label>
                    </div>
                </div>

                <div id="wrapActionNote" style="display: none; margin-bottom: 8px;">
                    <label for="actionNoteInput" style="font-size: 12px; font-weight: 700; color: #b45309; display: block; margin-bottom: 4px;">
                        Catatan Tindakan Lapangan yang Dilakukan / Diperlukan <span style="color: #dc2626;">*</span>:
                    </label>
                    <textarea id="actionNoteInput" class="omb-textarea" rows="2" placeholder="Tuliskan tindakan yang telah diambil atau dibutuhkan (contoh: koordinasi dengan polsek / penambahan patroli rutin)..."></textarea>
                </div>
            </div>

            <!-- View Read-only Jika Sudah Dikonfirmasi -->
            <div id="inspectionReadOnlyArea" style="display: none; background: #eff6ff; border: 1px solid #dbeafe; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <strong style="color: #1e40af; font-size: 13.5px;">Hasil Pemeriksaan Telah Terkirim</strong>
                    <span style="font-size: 11px; font-weight: 700; background: #dbeafe; color: #1e40af; padding: 3px 8px; border-radius: 6px;">Menunggu finalisasi admin</span>
                </div>
                <div style="font-size: 13px; color: #0f172a; line-height: 1.5;">
                    <div><strong>Waktu Konfirmasi:</strong> <span id="roConfirmedAt">-</span></div>
                    <div><strong>Perlu Tindakan:</strong> <span id="roNeedsAction">-</span></div>
                    <div style="margin-top: 4px;"><strong>Catatan Pemeriksaan:</strong> <span id="roInspectionNote">-</span></div>
                    <div id="roActionNoteWrap" style="margin-top: 4px; display: none;"><strong>Catatan Tindakan:</strong> <span id="roActionNote">-</span></div>
                </div>
            </div>
        </div>
        <div class="omb-footer" id="modalFooterActions">
            <button type="button" class="btn-verify-cancel" onclick="closeModalDetail()" style="padding: 9px 16px; border-radius: 10px; border: 1px solid #cbd5e1; background: #fff; font-weight: 700; font-size: 12.5px; cursor: pointer;">
                Tutup
            </button>
            <button type="button" class="btn-verify-success" id="btnSubmitInspection" onclick="submitOfficerInspection()" style="padding: 9px 18px; border-radius: 10px; background: #2563eb; color: #fff; border: none; font-weight: 700; font-size: 13px; cursor: pointer;">
                Kirim Konfirmasi ke Admin
            </button>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="keamananToast" style="position:fixed; bottom:24px; right:24px; z-index:99999; padding:12px 20px; border-radius:12px; color:#fff; font-size:13.5px; font-weight:700; box-shadow:0 10px 25px rgba(0,0,0,0.2); display:none;"></div>

<style>
.keamanan-container {
    max-width: 1400px;
    margin: 28px auto 60px;
    padding: 0 clamp(16px, 3vw, 36px);
}
.officer-hero-banner {
    background-color: #1e3a8a;
    border: 1px solid #1e40af;
    border-radius: 14px;
    padding: 28px clamp(20px, 3.5vw, 36px);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    margin-bottom: 28px;
    box-shadow: var(--shadow-card);
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
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 8px 16px;
    border-radius: 9999px;
    font-size: 12.5px;
    color: #ffffff;
}
.duty-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: #dbeafe;
}

/* Stat Grid */
.officer-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.stat-box {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: var(--shadow-sm);
}
.sb-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.box-amber .sb-icon,
.box-blue .sb-icon,
.box-green .sb-icon { background: #eff6ff; }

.stat-box h3 { font-size: 12px; font-weight: 600; color: var(--text-muted); margin: 0 0 2px; }
.sb-num { font-size: 24px; font-weight: 800; color: var(--text-heading); display: block; line-height: 1.1; }
.sb-sub { font-size: 11px; color: var(--text-muted); }

/* Table Section */
.officer-table-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 14px;
    padding: 22px;
    box-shadow: var(--shadow-sm);
}
.otc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.otc-header h2 { font-size: 18px; font-weight: 800; color: var(--text-heading); margin: 0; }
.otc-sub { font-size: 13px; color: var(--text-muted); margin: 2px 0 0; }
.otc-filters { display: flex; gap: 8px; flex-wrap: wrap; }
.otc-filter-btn {
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid var(--border-soft);
    background: #fff;
    color: var(--text-body);
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}
.otc-filter-btn:hover { background: #f8fafc; }
.otc-filter-btn.active {
    background: #0f2d59;
    color: #fff;
    border-color: #0f2d59;
}

.table-responsive { overflow-x: auto; }
.officer-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
.officer-table th {
    padding: 13px 14px;
    background: #f8fafc;
    color: var(--text-muted);
    font-weight: 800;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1.5px solid var(--border-soft);
}
.officer-table td {
    padding: 14px;
    border-bottom: 1px solid var(--border-soft);
    color: var(--text-body);
    vertical-align: middle;
}
.officer-table tr:hover td { background-color: #fafbfc; }

.ot-code { font-family: monospace; font-size: 13px; font-weight: 800; color: #0f172a; display: block; }
.ot-time { font-size: 11.5px; color: #64748b; }

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

.btn-action-inspect {
    padding: 6px 14px;
    border-radius: 8px;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #dbeafe;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: background-color 0.15s ease;
}
.btn-action-inspect:hover {
    background: #dbeafe;
}

/* Modal */
.officer-modal-backdrop {
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
.officer-modal-box {
    background: #ffffff;
    width: min(100%, 620px);
    border-radius: 20px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
    overflow: hidden;
    animation: modalPop 0.2s ease-out;
}
.omb-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px 24px;
    background: #f8fafc;
    border-bottom: 1px solid var(--border-soft);
}
.omb-close {
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
.omb-body { padding: 22px 24px; display: flex; flex-direction: column; gap: 14px; }
.omb-info-row { display: flex; flex-direction: column; gap: 3px; font-size: 13px; }
.omb-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; }
.omb-desc-box {
    background: #f8fafc;
    border: 1px solid var(--border-soft);
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    line-height: 1.5;
    color: #334155;
    margin: 2px 0 0;
}
.omb-textarea {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1.5px solid #cbd5e1;
    font-size: 13px;
    font-family: inherit;
    resize: vertical;
}
.omb-textarea:focus {
    outline: none;
    border-color: #2563eb;
}
.omb-footer {
    padding: 16px 24px;
    background: #f8fafc;
    border-top: 1px solid var(--border-soft);
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
</style>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let currentKeamananTab = 'unconfirmed'; // 'unconfirmed' or 'confirmed'
    let loadedReports = [];
    let selectedReport = null;
    let modalMap = null;
    let modalMarker = null;
    let lastServerTime = null;
    let pollingInterval = null;
    let timeAgoInterval = null;

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
        const toast = document.getElementById('keamananToast');
        if (!toast) return;
        toast.textContent = msg;
        toast.style.background = type === 'error' ? '#dc2626' : '#2563eb';
        toast.style.display = 'block';
        clearTimeout(toast._t);
        toast._t = setTimeout(() => { toast.style.display = 'none'; }, 4000);
    }

    function renderTable() {
        const tbody = document.getElementById('officerTableBody');

        const filtered = loadedReports.filter(r => {
            const isConf = !!r.security_confirmed_at;
            return currentKeamananTab === 'confirmed' ? isConf : !isConf;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px 20px; color: #64748b;">
                        <strong style="display: block; font-size: 15px; color: #0f172a;">Belum ada laporan yang diteruskan admin.</strong>
                        <p style="font-size: 12.5px; margin-top: 4px;">Laporan masyarakat yang telah divalidasi admin akan tampil di sini untuk diperiksa.</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filtered.map(r => {
            const isConfirmed = !!r.security_confirmed_at;
            const newBadge = r._isNew ? '<span class="badge-new-tag">BARU</span>' : '';
            const rowClass = r._isNew ? 'highlight-new-row' : '';

            const statusBadge = isConfirmed
                ? '<span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px;background:#dbeafe;color:#1e40af;">Sudah Dikonfirmasi</span>'
                : '<span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px;background:#f8fafc;border:1px solid #e2e8f0;color:#64748b;">Belum Diperiksa TKP</span>';

            return `
                <tr class="${rowClass}" id="keamananRow-${r.id}">
                    <td>
                        <span class="ot-code">${r.report_code} ${newBadge}</span>
                        <span class="ot-time" title="${formatFullDateTime(r.incident_at)}">Kejadian: ${timeAgo(r.incident_at)}</span>
                    </td>
                    <td><strong>${r.category}</strong></td>
                    <td>${r.address || '-'}</td>
                    <td>
                        <span style="font-size: 12px; color: #334155;">
                            ${r.is_anonymous ? '<em>(Anonim)</em>' : (r.reporter_name || 'Anonim')}
                        </span>
                    </td>
                    <td>${statusBadge}</td>
                    <td style="text-align: right; white-space: nowrap;">
                        <button type="button" class="btn-action-inspect" onclick="window.keamananApp.openModal(${r.id})">
                            ${isConfirmed ? 'Lihat Hasil TKP' : 'Periksa & Tindak'}
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function fetchCounts() {
        try {
            const resp = await fetch('{{ url("/keamanan/data/reports/counts") }}', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            if (!resp.ok) return;
            const res = await resp.json();
            if (res.success && res.data) {
                const c = res.data;
                document.getElementById('statPendingCount').textContent = c.unconfirmed || 0;
                document.getElementById('statDoneCount').textContent = c.confirmed || 0;
                document.getElementById('statTotalCount').textContent = c.total || 0;

                document.getElementById('keamananCountUnconfirmed').textContent = c.unconfirmed || 0;
                document.getElementById('keamananCountConfirmed').textContent = c.confirmed || 0;

                const navBadge = document.getElementById('navBadgeKeamananUnconfirmed');
                if (navBadge) {
                    if (c.unconfirmed > 0) {
                        navBadge.textContent = c.unconfirmed;
                        navBadge.style.display = 'inline-block';
                    } else {
                        navBadge.style.display = 'none';
                    }
                }
            }
        } catch (e) {
            // Silently retry
        }
    }

    async function fetchReports() {
        try {
            const resp = await fetch(`{{ url("/keamanan/data/reports") }}?filter=${currentKeamananTab}`, {
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

    async function pollRealtime() {
        if (!lastServerTime) return;
        try {
            const url = `{{ url("/keamanan/data/reports") }}?since=${encodeURIComponent(lastServerTime)}`;
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

                    setTimeout(() => {
                        item._isNew = false;
                        const row = document.getElementById(`keamananRow-${item.id}`);
                        if (row) {
                            row.classList.remove('highlight-new-row');
                            const tag = row.querySelector('.badge-new-tag');
                            if (tag) tag.remove();
                        }
                    }, 5000);
                });

                if (hasNew) {
                    showToast('1 laporan baru diteruskan admin', 'info');
                }
                renderTable();
            }

            await fetchCounts();
            updateTimestampText();
        } catch (e) {
            // Silently retry
        }
    }

    function updateTimestampText() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const el = document.getElementById('keamananAutoUpdateText');
        if (el) el.textContent = `Diperbarui otomatis · terakhir ${timeStr}`;
    }

    window.setKeamananTab = function (tab) {
        currentKeamananTab = tab;
        document.querySelectorAll('.otc-filter-btn').forEach(b => b.classList.remove('active'));
        if (tab === 'unconfirmed') document.getElementById('tabUnconfirmedBtn').classList.add('active');
        else document.getElementById('tabConfirmedBtn').classList.add('active');

        fetchReports();
    };

    window.toggleActionNoteField = function () {
        const val = document.querySelector('input[name="radioNeedsAction"]:checked')?.value;
        const wrap = document.getElementById('wrapActionNote');
        if (wrap) {
            wrap.style.display = val === '1' ? 'block' : 'none';
        }
    };

    window.keamananApp = {
        openModal: async function (id) {
            try {
                const resp = await fetch(`{{ url("/keamanan/data/reports") }}/${id}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                if (!resp.ok) throw new Error('Gagal memuat detail laporan.');
                const res = await resp.json();
                if (!res.success || !res.data) throw new Error('Data tidak ditemukan.');
                selectedReport = res.data;

                document.getElementById('modalReportCode').textContent = `Pemeriksaan TKP: ${selectedReport.report_code}`;
                document.getElementById('modalCategory').textContent = selectedReport.category;
                document.getElementById('modalReporter').textContent = selectedReport.is_anonymous ? 'Anonim (Disembunyikan)' : (selectedReport.reporter_name || 'Anonim');
                document.getElementById('modalIncidentAt').textContent = formatFullDateTime(selectedReport.incident_at);
                document.getElementById('modalForwardedAt').textContent = formatFullDateTime(selectedReport.forwarded_at);
                document.getElementById('modalAddress').textContent = selectedReport.address || '-';
                document.getElementById('modalDesc').textContent = selectedReport.description || '-';

                // Leaflet Map
                const mapEl = document.getElementById('keamananModalMap');
                if (selectedReport.latitude && selectedReport.longitude) {
                    mapEl.style.display = 'block';
                    if (!modalMap) {
                        modalMap = L.map('keamananModalMap', { zoomControl: false });
                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(modalMap);
                    }
                    modalMap.setView([selectedReport.latitude, selectedReport.longitude], 15);
                    if (modalMarker) modalMap.removeLayer(modalMarker);
                    modalMarker = L.marker([selectedReport.latitude, selectedReport.longitude]).addTo(modalMap);
                    setTimeout(() => modalMap.invalidateSize(), 200);
                } else {
                    mapEl.style.display = 'none';
                }

                // Timeline
                const tl = document.getElementById('modalTimelineKeamanan');
                if (Array.isArray(selectedReport.histories) && selectedReport.histories.length > 0) {
                    tl.innerHTML = selectedReport.histories.map(h => `
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

                const isConfirmed = !!selectedReport.security_confirmed_at;
                const formArea = document.getElementById('inspectionFormArea');
                const roArea = document.getElementById('inspectionReadOnlyArea');
                const btnSubmit = document.getElementById('btnSubmitInspection');

                if (isConfirmed) {
                    formArea.style.display = 'none';
                    roArea.style.display = 'block';
                    document.getElementById('roConfirmedAt').textContent = formatFullDateTime(selectedReport.security_confirmed_at);
                    document.getElementById('roNeedsAction').textContent = selectedReport.needs_action ? 'Ya (Perlu Tindakan Khusus)' : 'Tidak (Kondusif)';
                    document.getElementById('roInspectionNote').textContent = selectedReport.inspection_note || '-';
                    const actWrap = document.getElementById('roActionNoteWrap');
                    if (selectedReport.needs_action && selectedReport.action_note) {
                        actWrap.style.display = 'block';
                        document.getElementById('roActionNote').textContent = selectedReport.action_note;
                    } else {
                        actWrap.style.display = 'none';
                    }
                    if (btnSubmit) btnSubmit.style.display = 'none';
                } else {
                    formArea.style.display = 'block';
                    roArea.style.display = 'none';
                    document.getElementById('officerNotes').value = '';
                    document.getElementById('actionNoteInput').value = '';
                    const rads = document.getElementsByName('radioNeedsAction');
                    if (rads[0]) rads[0].checked = true;
                    toggleActionNoteField();
                    if (btnSubmit) btnSubmit.style.display = 'inline-block';
                }

                document.getElementById('modalDetailLaporan').style.display = 'flex';
            } catch (err) {
                showToast(err.message, 'error');
            }
        }
    };

    window.closeModalDetail = function () {
        document.getElementById('modalDetailLaporan').style.display = 'none';
    };

    window.submitOfficerInspection = async function () {
        if (!selectedReport) return;

        const inspectionNote = document.getElementById('officerNotes').value.trim();
        if (!inspectionNote) {
            alert('Catatan hasil pemeriksaan lapangan wajib diisi.');
            document.getElementById('officerNotes').focus();
            return;
        }

        const needsActionVal = document.querySelector('input[name="radioNeedsAction"]:checked')?.value;
        const needsAction = needsActionVal === '1';

        const actionNote = document.getElementById('actionNoteInput').value.trim();
        if (needsAction && !actionNote) {
            alert('Catatan tindakan wajib diisi jika memerlukan tindakan lapangan.');
            document.getElementById('actionNoteInput').focus();
            return;
        }

        const btn = document.getElementById('btnSubmitInspection');
        btn.disabled = true;
        btn.textContent = 'Mengirim Konfirmasi...';

        try {
            const resp = await fetch(`{{ url("/keamanan/data/reports") }}/${selectedReport.id}/confirm`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    inspection_note: inspectionNote,
                    needs_action: needsAction,
                    action_note: needsAction ? actionNote : null
                })
            });

            const res = await resp.json();
            if (resp.ok && res.success) {
                showToast('Hasil pemeriksaan terkirim ke admin. Status laporan menunggu finalisasi admin.', 'success');
                closeModalDetail();
                await fetchReports();
                await fetchCounts();
            } else {
                throw new Error(res.message || 'Gagal mengirim konfirmasi pemeriksaan.');
            }
        } catch (e) {
            showToast(e.message, 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Kirim Konfirmasi ke Admin';
        }
    };

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

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopPolling();
        } else {
            pollRealtime();
            startPolling();
        }
    });

    document.addEventListener('DOMContentLoaded', async () => {
        await fetchReports();
        await fetchCounts();
        startPolling();

        timeAgoInterval = setInterval(() => {
            renderTable();
        }, 30000);
    });
})();
</script>
@endpush
@endsection
