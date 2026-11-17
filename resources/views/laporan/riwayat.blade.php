@extends('layouts.app')

@section('title', 'Riwayat Laporan Keamanan · SafeRoad')

@section('content')
<div class="riwayat-container">
    <div class="riwayat-header">
        <div class="rh-title-area">
            <span class="rh-badge">PELAPORAN MASYARAKAT</span>
            <h1>Riwayat & Status Laporan Anda</h1>
            <p>Pantau proses validasi, pemeriksaan petugas lapangan, hingga pembaruan titik peringatan pada peta SafeRoad.</p>
        </div>
        <a href="{{ url('/laporkan') }}" class="btn-create-report" id="btnRiwayatAction">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span id="btnRiwayatActionText">Buat Laporan Baru</span>
        </a>
    </div>

    <!-- Status Flow Guide -->
    <div class="status-guide-card">
        <div class="sg-item active">
            <span class="sg-dot">1</span>
            <div><strong>Terkirim</strong><small>Menunggu antrean review</small></div>
        </div>
        <div class="sg-arrow">→</div>
        <div class="sg-item">
            <span class="sg-dot">2</span>
            <div><strong>Review Admin</strong><small>Verifikasi awal keaslian</small></div>
        </div>
        <div class="sg-arrow">→</div>
        <div class="sg-item">
            <span class="sg-dot">3</span>
            <div><strong>Cek Petugas</strong><small>Patroli verifikasi TKP</small></div>
        </div>
        <div class="sg-arrow">→</div>
        <div class="sg-item">
            <span class="sg-dot">4</span>
            <div><strong>Peta Diperbarui</strong><small>Tampil sebagai titik rawan</small></div>
        </div>
    </div>

    <!-- Daftar Laporan -->
    <div class="reports-list-section">
        <div class="rl-header">
            <h2>Daftar Laporan yang Telah Diajukan</h2>
            <span id="reportCountBadge" class="count-badge">Memuat…</span>
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
.btn-create-report svg { width: 18px; height: 18px; }

/* Status Guide */
.status-guide-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 16px;
    padding: 18px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
    overflow-x: auto;
    gap: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.sg-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    white-space: nowrap;
}
.sg-dot {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #64748b;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}
.sg-item.active .sg-dot {
    background: var(--primary-600);
    color: #fff;
}
.sg-item strong { display: block; color: var(--text-heading); font-size: 13px; }
.sg-item small { color: var(--text-muted); font-size: 11px; }
.sg-arrow { color: #cbd5e1; font-weight: 700; }

/* Reports List */
.reports-list-section {
    margin-top: 10px;
}
.rl-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}
.rl-header h2 {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-heading);
    margin: 0;
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
.status-submitted    { background: #fef3c7; color: #b45309; }
.status-admin_review { background: #e0e7ff; color: #4338ca; }
.status-officer_check{ background: #dbeafe; color: #1d4ed8; }
.status-map_updated  { background: #dcfce7; color: #15803d; }
.status-rejected     { background: #fee2e2; color: #b91c1c; }

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
document.addEventListener('DOMContentLoaded', async () => {
    const listEl = document.getElementById('reportsList');
    const emptyEl = document.getElementById('emptyReportsState');
    const badgeEl = document.getElementById('reportCountBadge');

    let allReports = [];

    // 1. Coba ambil dari endpoint backend GET /laporan/terakhir
    try {
        const resp = await fetch('{{ url("/laporan/terakhir") }}', {
            headers: { 'Accept': 'application/json' }
        });
        if (resp.ok) {
            const result = await resp.json();
            if (result.success && result.data) {
                allReports.push(result.data);
            }
        }
    } catch (e) {}

    // 2. Ambil riwayat dari LocalStorage
    try {
        const localList = JSON.parse(localStorage.getItem('saferoad_my_reports') || '[]');
        localList.forEach(item => {
            if (!allReports.some(r => r.report_code === item.report_code)) {
                allReports.push(item);
            }
        });
    } catch (e) {}

    // 3. Fallback dummy jika user baru pertama kali membuka
    if (allReports.length === 0) {
        allReports = [
            {
                report_code: 'SR-2611-B892',
                category: 'Pelecehan / Gangguan Keamanan',
                description: 'Pria tak dikenal membuntuti pejalan kaki saat melintas di gang dekat halte.',
                address: 'Jl. Ketintang Barat No. 12',
                status: 'officer_check',
                created_at: new Date(Date.now() - 3600000 * 4).toISOString()
            },
            {
                report_code: 'SR-2611-A104',
                category: 'Kondisi Jalan',
                description: 'Lampu jalan utama padam total sepanjang 200 meter, area sangat gelap.',
                address: 'Simpang Jetis Kulon',
                status: 'map_updated',
                created_at: new Date(Date.now() - 86400000).toISOString()
            }
        ];
    }

    badgeEl.textContent = `${allReports.length} Laporan`;

    const statusMap = {
        'submitted':     { label: 'Terkirim', class: 'status-submitted' },
        'admin_review':  { label: 'Review Admin', class: 'status-admin_review' },
        'officer_check': { label: 'Pengecekan Petugas', class: 'status-officer_check' },
        'map_updated':   { label: 'Peta Diperbarui', class: 'status-map_updated' },
        'rejected':      { label: 'Ditolak', class: 'status-rejected' }
    };

    listEl.innerHTML = allReports.map(r => {
        const st = statusMap[r.status] || { label: r.status, class: 'status-submitted' };
        const timeFormatted = new Date(r.created_at).toLocaleString('id-ID', {
            dateStyle: 'medium', timeStyle: 'short'
        });

        return `
            <div class="report-card-item">
                <div>
                    <div class="rci-top">
                        <span class="rci-code">${r.report_code}</span>
                        <span class="rci-status-pill ${st.class}">● ${st.label}</span>
                    </div>
                    <div class="rci-category">${r.category}</div>
                    <p class="rci-desc">${r.description}</p>
                </div>
                <div class="rci-meta">
                    <div class="rci-meta-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>${r.address || 'Titik koordinat GPS'}</span>
                    </div>
                    <div class="rci-meta-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>${timeFormatted} WIB</span>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    // Disesuaikan dengan role yang dipilih saat login
    const activeRole = localStorage.getItem('saferoad_active_role') || 'masyarakat';
    const btnAction = document.getElementById('btnRiwayatAction');
    const btnText = document.getElementById('btnRiwayatActionText');
    if (btnAction && btnText) {
        if (activeRole === 'keamanan') {
            btnAction.href = "{{ route('keamanan.dashboard') }}";
            btnText.textContent = "🛡️ Cek Laporan Masuk";
        } else if (activeRole === 'admin') {
            btnAction.href = "{{ route('admin.dashboard') }}";
            btnText.textContent = "⚡ Validasi Laporan Admin";
        }
    }
});
</script>
@endpush
@endsection
