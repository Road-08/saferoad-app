@extends('layouts.app')

@section('title', 'Riwayat Laporan Keamanan · SafeRoad')

@section('content')
<div class="riwayat-container">
    <div class="riwayat-header">
        <div class="rh-title-area">
            <span class="rh-badge">PELAPORAN MASYARAKAT</span>
            <h1>Riwayat &amp; Status Laporan Anda</h1>
            <p>Pantau proses validasi admin, pemeriksaan petugas lapangan di TKP, hingga status finalisasi penanganan laporan keamanan Anda.</p>
        </div>
        <a href="{{ url('/laporkan') }}" class="btn-create-report" id="btnRiwayatAction">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span id="btnRiwayatActionText">Buat Laporan Baru</span>
        </a>
    </div>

    <!-- Status Flow Guide (To-Be Workflow) -->
    <div class="status-guide-card">
        <div class="sg-item active">
            <span class="sg-dot">1</span>
            <div><strong>Menunggu Validasi</strong><small>Ditelaah oleh admin</small></div>
        </div>
        <div class="sg-arrow">→</div>
        <div class="sg-item">
            <span class="sg-dot">2</span>
            <div><strong>Diteruskan ke Keamanan</strong><small>Pemeriksaan personil TKP</small></div>
        </div>
        <div class="sg-arrow">→</div>
        <div class="sg-item">
            <span class="sg-dot">3</span>
            <div><strong>Ditangani</strong><small>Titik aktif di peta navigasi</small></div>
        </div>
    </div>

    <!-- Daftar Laporan -->
    <div class="reports-list-section">
        <div class="rl-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 14px; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <h2 style="font-size:18px; font-weight:800; color:var(--text-heading); margin:0;">Daftar Laporan yang Telah Diajukan</h2>
                <span id="reportCountBadge" class="count-badge">Memuat…</span>
            </div>
            <small id="riwayatAutoUpdateText" style="font-size:12px; color:#64748b; font-weight:600;">Diperbarui otomatis · terakhir --:--:--</small>
        </div>

        <div id="reportsList" class="reports-grid">
            <!-- Disuntikkan via JavaScript -->
        </div>

        <div id="emptyReportsState" class="empty-state-box" style="display:none;">
            <div class="es-icon">📋</div>
            <h3>Belum Ada Laporan yang Diajukan</h3>
            <p>Jika Anda melihat kejadian mencurigakan, pelecehan, atau jalan rusak, laporkan segera untuk melindungi pengguna jalan lainnya.</p>
            <a href="{{ url('/laporkan') }}" class="btn-primary-action">Mulai Buat Laporan</a>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="riwayatToast" style="position:fixed; bottom:24px; right:24px; z-index:99999; padding:12px 20px; border-radius:12px; color:#fff; font-size:13.5px; font-weight:700; box-shadow:0 10px 25px rgba(0,0,0,0.2); display:none;"></div>

<style>
.riwayat-container {
    max-width: 1140px;
    margin: 32px auto 60px;
    padding: 0 20px;
}
.riwayat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    margin-bottom: 28px;
    flex-wrap: wrap;
}
.rh-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    color: var(--primary-600);
    background: #eff6ff;
    padding: 3px 10px;
    border-radius: 9999px;
    margin-bottom: 8px;
}
.rh-title-area h1 {
    font-size: clamp(24px, 3vw, 32px);
    font-weight: 800;
    color: var(--text-heading);
    margin-bottom: 6px;
    letter-spacing: -0.02em;
}
.rh-title-area p {
    font-size: 14.5px;
    color: var(--text-muted);
    max-width: 620px;
    margin: 0;
}
.btn-create-report {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    background: var(--primary-600);
    color: #fff;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
    transition: all 0.2s;
}
.btn-create-report:hover {
    background: var(--primary-700);
    transform: translateY(-2px);
}

.status-guide-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 16px;
    padding: 18px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    flex-wrap: wrap;
    gap: 12px;
}
.sg-item {
    display: flex;
    align-items: center;
    gap: 12px;
}
.sg-dot {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--primary-50);
    color: var(--primary-600);
    border: 1.5px solid var(--primary-200);
    font-size: 12px;
    font-weight: 800;
    display: grid;
    place-items: center;
}
.sg-item.active .sg-dot {
    background: var(--primary-600);
    color: #fff;
    border-color: var(--primary-600);
}
.sg-item strong {
    display: block;
    font-size: 13px;
    color: var(--text-heading);
}
.sg-item small {
    font-size: 11px;
    color: var(--text-muted);
}
.sg-arrow {
    color: #cbd5e1;
    font-weight: 700;
}

.count-badge {
    font-size: 12px;
    font-weight: 700;
    background: #eff6ff;
    color: var(--primary-700);
    padding: 3px 10px;
    border-radius: 9999px;
}
.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}
.report-card-item {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s, border-color 0.2s;
}
.report-card-item:hover {
    transform: translateY(-2px);
    border-color: var(--primary-500);
}
.rci-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}
.rci-code {
    font-size: 12px;
    font-weight: 800;
    color: var(--primary-700);
    background: #eff6ff;
    padding: 3px 8px;
    border-radius: 6px;
}
.rci-status-pill {
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 9999px;
}
.status-pending_validation { background: #fef3c7; color: #b45309; }
.status-forwarded_to_security { background: #e0e7ff; color: #4338ca; }
.status-needs_revision { background: #fff7ed; color: #c2410c; }
.status-handled { background: #dcfce7; color: #15803d; }
.status-rejected { background: #fee2e2; color: #b91c1c; }

.rci-category {
    font-size: 15px;
    font-weight: 800;
    color: var(--text-heading);
    margin-bottom: 6px;
}
.rci-desc {
    font-size: 13px;
    color: var(--text-body);
    line-height: 1.5;
    margin-bottom: 14px;
}
.rci-meta {
    font-size: 12px;
    color: var(--text-muted);
    display: flex;
    flex-direction: column;
    gap: 6px;
    border-top: 1px dashed var(--border-soft);
    padding-top: 12px;
}
.rci-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
}
.rci-meta-row svg { width: 14px; height: 14px; flex-shrink: 0; }

.empty-state-box {
    background: #fff;
    border: 2px dashed var(--border-soft);
    border-radius: 16px;
    padding: 48px 24px;
    text-align: center;
    max-width: 520px;
    margin: 40px auto;
}
.es-icon { font-size: 40px; margin-bottom: 12px; }
.empty-state-box h3 { font-size: 17px; font-weight: 800; color: var(--text-heading); margin-bottom: 8px; }
.empty-state-box p { font-size: 13.5px; color: var(--text-muted); line-height: 1.5; margin-bottom: 20px; }
.btn-primary-action {
    display: inline-flex;
    align-items: center;
    padding: 10px 22px;
    border-radius: 10px;
    background: var(--primary-600);
    color: #fff;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
}
</style>

@push('scripts')
<script>
(function () {
    const listEl = document.getElementById('reportsList');
    const emptyEl = document.getElementById('emptyReportsState');
    const badgeEl = document.getElementById('reportCountBadge');
    const autoUpdateEl = document.getElementById('riwayatAutoUpdateText');

    let userReports = [];
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
        const toast = document.getElementById('riwayatToast');
        if (!toast) return;
        toast.textContent = msg;
        toast.style.background = type === 'success' ? '#16a34a' : type === 'error' ? '#dc2626' : '#2563eb';
        toast.style.display = 'block';
        clearTimeout(toast._t);
        toast._t = setTimeout(() => { toast.style.display = 'none'; }, 4000);
    }

    const statusMap = {
        'pending_validation':    { label: 'Menunggu Validasi', class: 'status-pending_validation' },
        'forwarded_to_security': { label: 'Diteruskan ke Keamanan', class: 'status-forwarded_to_security' },
        'needs_revision':        { label: 'Perlu Perbaikan', class: 'status-needs_revision' },
        'handled':               { label: 'Ditangani', class: 'status-handled' },
        'rejected':              { label: 'Ditolak', class: 'status-rejected' },
    };

    function renderReports() {
        if (userReports.length === 0) {
            listEl.innerHTML = '';
            emptyEl.style.display = 'block';
            badgeEl.textContent = '0 Laporan';
            return;
        }

        emptyEl.style.display = 'none';
        badgeEl.textContent = `${userReports.length} Laporan`;

        listEl.innerHTML = userReports.map(r => {
            const st = statusMap[r.status] || { label: r.status_label || r.status, class: 'status-pending_validation' };
            const timeIncident = timeAgo(r.incident_at);
            const timeReported = timeAgo(r.reported_at || r.created_at);

            const timelineHtml = Array.isArray(r.histories) && r.histories.length > 0 ? `
                <div style="margin-top:10px; padding-top:10px; border-top:1px dashed var(--border-soft); font-size:11.5px;">
                    <strong style="color:#0f172a; display:block; margin-bottom:4px;">Timeline Status:</strong>
                    ${r.histories.map(h => `
                        <div style="display:flex; justify-content:space-between; color:#475569; margin-bottom:2px;">
                            <span>● ${h.status_label || h.status}</span>
                            <span title="${formatFullDateTime(h.created_at)}">${timeAgo(h.created_at)}</span>
                        </div>
                    `).join('')}
                </div>
            ` : '';

            const adminNoteHtml = r.admin_note ? `
                <div style="margin-top:8px; padding:8px 10px; background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; font-size:12px; color:#9a3412;">
                    <strong>Catatan Admin:</strong> ${r.admin_note}
                </div>
            ` : '';

            return `
                <div class="report-card-item">
                    <div>
                        <div class="rci-top">
                            <span class="rci-code">${r.report_code}</span>
                            <span class="rci-status-pill ${st.class}">● ${st.label}</span>
                        </div>
                        <div class="rci-category">${r.category}</div>
                        <p class="rci-desc">${r.description || '-'}</p>
                        ${adminNoteHtml}
                    </div>
                    <div>
                        <div class="rci-meta">
                            <div class="rci-meta-row">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>${r.address || 'Titik koordinat GPS'}</span>
                            </div>
                            <div class="rci-meta-row">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span title="Waktu Kejadian: ${formatFullDateTime(r.incident_at)}">Kejadian: ${timeIncident}</span>
                            </div>
                            <div class="rci-meta-row">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                <span title="Dilaporkan: ${formatFullDateTime(r.reported_at || r.created_at)}">Dilaporkan: ${timeReported}</span>
                            </div>
                        </div>
                        ${timelineHtml}
                    </div>
                </div>
            `;
        }).join('');
    }

    function updateTimestampText() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        if (autoUpdateEl) autoUpdateEl.textContent = `Diperbarui otomatis · terakhir ${timeStr}`;
    }

    async function fetchUserReports() {
        try {
            const resp = await fetch('{{ url("/laporan/data") }}', {
                headers: { 'Accept': 'application/json' }
            });
            if (!resp.ok) return;
            const res = await resp.json();
            if (res.success && Array.isArray(res.data)) {
                userReports = res.data;
                if (res.server_time) lastServerTime = res.server_time;
                renderReports();
                updateTimestampText();
            }
        } catch (e) {
            // Silently retry
        }
    }

    // Polling setiap 15 detik
    async function pollRealtime() {
        if (!lastServerTime) return;
        try {
            const url = `{{ url("/laporan/data") }}?since=${encodeURIComponent(lastServerTime)}`;
            const resp = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            if (!resp.ok) return;
            const res = await resp.json();

            if (res.success && res.server_time) {
                lastServerTime = res.server_time;
            }

            if (res.success && Array.isArray(res.data) && res.data.length > 0) {
                res.data.forEach(updatedItem => {
                    const existing = userReports.find(r => r.id === updatedItem.id || r.report_code === updatedItem.report_code);
                    if (existing) {
                        if (existing.status !== updatedItem.status) {
                            showToast(`Laporan ${updatedItem.report_code} kini ${updatedItem.status_label || updatedItem.status}`, 'info');
                        }
                        Object.assign(existing, updatedItem);
                    } else {
                        userReports.unshift(updatedItem);
                        showToast(`Laporan ${updatedItem.report_code} terdaftar: ${updatedItem.status_label}`, 'info');
                    }
                });
                renderReports();
            }

            updateTimestampText();
        } catch (e) {
            // Silently retry
        }
    }

    function startPolling() {
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(pollRealtime, 15000);
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
        await fetchUserReports();
        startPolling();

        timeAgoInterval = setInterval(() => {
            renderReports();
        }, 30000);
    });
})();
</script>
@endpush
@endsection
