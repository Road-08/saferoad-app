@extends('layouts.app')

@section('title', 'Peta Pemantauan Insiden · SafeRoad Administrator')

@push('leaflet-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="map-page-container">
    <!-- Breadcrumb & Header -->
    <div class="map-header-row">
        <a href="{{ route('admin.dashboard') }}" class="btn-back-dash">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    <header class="map-hero-card">
        <div class="mhc-left">
            <span class="mhc-badge">PENGAWASAN GEOSPASIAL RESMI</span>
            <h1>Peta Pemantauan Sebaran Insiden &amp; Fasilitas</h1>
            <p>Pantau titik laporan warga yang masuk secara real-time, lokasi insiden terverifikasi, serta sebaran infrastruktur CCTV dan penerangan jalan di seluruh wilayah sektor pengawasan.</p>
        </div>
        <div class="mhc-right">
            <div class="mhc-stats-strip">
                <div class="mhc-stat">
                    <strong id="statTotalIncidents">4 Titik</strong>
                    <span>Laporan Kejadian</span>
                </div>
                <div class="mhc-divider"></div>
                <div class="mhc-stat">
                    <strong style="color: #dbeafe;" id="statPendingIncidents">2 Pending</strong>
                    <span>Perlu Divalidasi</span>
                </div>
                <div class="mhc-divider"></div>
                <div class="mhc-stat">
                    <strong style="color: #dbeafe;">18 CCTV</strong>
                    <span>Kamera Terkoneksi</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Map Workspace Grid -->
    <div class="map-workspace-grid">
        <!-- Area Utama: Peta Geospasial -->
        <div class="map-primary-box">
            <!-- Filter Layer Toolbar -->
            <div class="map-toolbar">
                <span class="mt-label">Filter Layer Peta:</span>
                <div class="mt-chips-wrap">
                    <label class="map-layer-chip">
                        <input type="checkbox" id="chkPending" checked onchange="updateAdminMapLayers()">
                        <span class="chip-dot dot-pending"></span>
                        <span>Menunggu Validasi</span>
                    </label>
                    <label class="map-layer-chip">
                        <input type="checkbox" id="chkApproved" checked onchange="updateAdminMapLayers()">
                        <span class="chip-dot dot-approved"></span>
                        <span>Laporan Disetujui</span>
                    </label>
                    <label class="map-layer-chip">
                        <input type="checkbox" id="chkCctv" checked onchange="updateAdminMapLayers()">
                        <span class="chip-dot dot-cctv"></span>
                        <span>CCTV Dishub</span>
                    </label>
                    <label class="map-layer-chip">
                        <input type="checkbox" id="chkLampu" checked onchange="updateAdminMapLayers()">
                        <span class="chip-dot dot-lampu"></span>
                        <span>Lampu PJU</span>
                    </label>
                    <label class="map-layer-chip">
                        <input type="checkbox" id="chkPos" checked onchange="updateAdminMapLayers()">
                        <span class="chip-dot dot-pos"></span>
                        <span>Pos Keamanan</span>
                    </label>
                </div>
            </div>

            <!-- Leaflet Map Container -->
            <div class="leaflet-map-wrapper">
                <div id="fullAdminMap" aria-label="Peta Geospasial SafeRoad Administrator"></div>
                <div class="map-floating-legend">
                    <span class="mfl-item"><i style="background:#64748b"></i> Menunggu Validasi</span>
                    <span class="mfl-item"><i style="background:#dc2626"></i> Laporan Disetujui</span>
                    <span class="mfl-item"><i style="background:#2563eb"></i> CCTV</span>
                    <span class="mfl-item"><i style="background:#3b82f6"></i> Lampu PJU</span>
                    <span class="mfl-item"><i style="background:#1e40af"></i> Pos Siaga</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Kanan: Daftar Titik Pantauan Cepat -->
        <div class="map-sidebar-box">
            <div class="msb-header">
                <h3>Titik Insiden Terpantau</h3>
                <span class="msb-sub">Klik titik untuk fokuskan peta</span>
            </div>
            <div class="msb-list" id="mapIncidentList">
                <!-- Disuntikkan via JS -->
            </div>
            <div class="msb-footer">
                <a href="{{ route('admin.validasi') }}" class="btn-goto-val">
                    <span>Buka Konsol Validasi Penuh</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.map-page-container {
    max-width: 1400px;
    margin: 24px auto 60px;
    padding: 0 clamp(16px, 3vw, 36px);
}

.map-header-row { margin-bottom: 16px; }
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

.map-hero-card {
    background-color: #1e3a8a;
    border: 1px solid #1e40af;
    border-radius: 14px;
    padding: 26px clamp(20px, 3.5vw, 36px);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-card);
    flex-wrap: wrap;
}
.mhc-badge {
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
.mhc-left h1 { font-size: clamp(22px, 3vw, 28px); font-weight: 800; margin-bottom: 6px; }
.mhc-left p { font-size: 13.5px; color: #dbeafe; max-width: 650px; margin: 0; }

.mhc-stats-strip {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 14px;
    padding: 12px 18px;
    gap: 16px;
}
.mhc-stat { display: flex; flex-direction: column; align-items: center; text-align: center; }
.mhc-stat strong { font-size: 16px; font-weight: 800; color: #fff; }
.mhc-stat span { font-size: 11px; color: #cbd5e1; }
.mhc-divider { width: 1px; height: 26px; background: rgba(255, 255, 255, 0.25); }

/* Grid Layout */
.map-workspace-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
}
@media (max-width: 992px) {
    .map-workspace-grid { grid-template-columns: 1fr; }
}

.map-primary-box {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
}

.map-toolbar {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.mt-label { font-size: 12.5px; font-weight: 800; color: #64748b; text-transform: uppercase; }
.mt-chips-wrap { display: flex; gap: 8px; flex-wrap: wrap; }
.map-layer-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #f8fafc;
    border: 1px solid var(--border-soft);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-body);
    cursor: pointer;
    user-select: none;
    transition: all 0.15s;
}
.map-layer-chip:hover { background: #f1f5f9; }
.chip-dot { width: 8px; height: 8px; border-radius: 50%; }
.dot-pending  { background: #64748b; }
.dot-approved { background: #dc2626; }
.dot-cctv     { background: #2563eb; }
.dot-lampu    { background: #3b82f6; }
.dot-pos      { background: #1e40af; }

.leaflet-map-wrapper {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid var(--border-soft);
}
#fullAdminMap {
    height: 540px;
    width: 100%;
}
.map-floating-legend {
    position: absolute;
    bottom: 14px;
    left: 14px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(6px);
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    gap: 12px;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-body);
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    flex-wrap: wrap;
}
.mfl-item { display: inline-flex; align-items: center; gap: 5px; }
.mfl-item i { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }

/* Sidebar */
.map-sidebar-box {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 20px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    height: 620px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
}
.msb-header { margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid var(--border-soft); }
.msb-header h3 { font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 2px; }
.msb-sub { font-size: 11.5px; color: #64748b; }

.msb-list {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding-right: 4px;
}
.msb-item {
    background: #f8fafc;
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    padding: 12px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.msb-item:hover {
    border-color: #93c5fd;
    background: #eff6ff;
    transform: translateY(-1px);
}
.msb-item-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.msb-code { font-family: monospace; font-weight: 800; font-size: 12.5px; color: #0f172a; }
.msb-tag { font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; }
.tag-pend { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
.tag-appr { background: #dbeafe; color: #1e40af; }
.msb-cat { font-size: 12px; font-weight: 700; color: #2563eb; display: block; margin-bottom: 2px; }
.msb-loc { font-size: 11.5px; color: #475569; }

.msb-footer { margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--border-soft); }
.btn-goto-val {
    display: block;
    text-align: center;
    padding: 10px 14px;
    background: #f1f5f9;
    color: var(--primary-700);
    border-radius: 10px;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    transition: background 0.15s;
}
.btn-goto-val:hover { background: #e2e8f0; }
</style>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let adminMap = null;
let markersGroup = null;
let incidentMarkersMap = {};

let mapReports = [];

async function loadLiveMapReports() {
    try {
        const resp = await fetch('{{ url("/admin/data/reports") }}?status=all', {
            headers: { 'Accept': 'application/json' }
        });
        if (resp.ok) {
            const res = await resp.json();
            if (res.success && Array.isArray(res.data)) {
                mapReports = res.data.map(r => ({
                    id: r.id,
                    code: r.report_code,
                    category: r.category,
                    address: r.address || 'Titik Koordinat',
                    status: r.status,
                    lat: r.latitude || -7.3152,
                    lng: r.longitude || 112.7280,
                    description: r.description || ''
                }));
                renderAdminMap();
                renderSidebarList();
            }
        }
    } catch (e) {}
}

// Data Fasilitas Keamanan
const cctvStatic = [
    { nama: 'CCTV Dishub Ketintang Utama', lat: -7.3150, lng: 112.7275 },
    { nama: 'CCTV Simpang Tiga Kampus', lat: -7.3130, lng: 112.7265 },
    { nama: 'CCTV Halte Menanggal', lat: -7.3115, lng: 112.7290 }
];
const lampuStatic = [
    { nama: 'PJU LED Terang Jl. Ketintang', lat: -7.3142, lng: 112.7288 },
    { nama: 'PJU Koridor Timur', lat: -7.3158, lng: 112.7292 }
];
const posStatic = [
    { nama: 'Pos Satpam Terpadu 24 Jam', lat: -7.3125, lng: 112.7258 }
];

function createMarkerIcon(icon, bg, border) {
    return L.divIcon({
        className: '',
        html: `<div style="width:34px;height:34px;border-radius:50%;background:${bg};border:2px solid ${border};display:grid;place-items:center;font-size:15px;box-shadow:0 3px 8px rgba(0,0,0,0.3);cursor:pointer;">${icon}</div>`,
        iconSize: [34, 34],
        iconAnchor: [17, 17]
    });
}

function initFullAdminMap() {
    const PUSAT = { lat: -7.3140, lng: 112.7280 };
    adminMap = L.map('fullAdminMap', { zoomControl: false }).setView([PUSAT.lat, PUSAT.lng], 15);
    L.control.zoom({ position: 'topright' }).addTo(adminMap);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(adminMap);

    markersGroup = L.layerGroup().addTo(adminMap);
    renderAdminMap();
    renderSidebarList();
}

function renderAdminMap() {
    if (!adminMap || !markersGroup) return;
    markersGroup.clearLayers();
    incidentMarkersMap = {};

    const showPending = document.getElementById('chkPending').checked;
    const showApproved = document.getElementById('chkApproved').checked;
    const showCctv = document.getElementById('chkCctv').checked;
    const showLampu = document.getElementById('chkLampu').checked;
    const showPos = document.getElementById('chkPos').checked;

    mapReports.forEach(r => {
        if ((r.status === 'pending_validation' || r.status === 'submitted') && !showPending) return;
        if ((r.status === 'handled' || r.status === 'map_updated') && !showApproved) return;
        if (r.status === 'rejected') return;

        const isPending = (r.status === 'pending_validation' || r.status === 'submitted');
        const iconSymbol = isPending ? 'P' : '!';
        const bg = isPending ? '#f8fafc' : '#fee2e2';
        const border = isPending ? '#64748b' : '#dc2626';

        const marker = L.marker([r.lat, r.lng], {
            icon: createMarkerIcon(iconSymbol, bg, border)
        }).addTo(markersGroup);

        marker.bindPopup(`
            <div style="font-family: inherit; font-size: 13px; line-height: 1.4; min-width: 220px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                    <strong style="color:#0f172a; font-family: monospace;">${r.code}</strong>
                    <span style="font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; ${isPending ? 'background:#f8fafc;color:#64748b;border:1px solid #e2e8f0;' : 'background:#dbeafe;color:#1e40af;'}">${isPending ? 'Pending' : 'Disetujui'}</span>
                </div>
                <strong style="display:block; color:#2563eb; font-size:12.5px;">${r.category}</strong>
                <p style="margin:4px 0; color:#64748b; font-size:11.5px;">${r.address}</p>
                <p style="margin:4px 0 8px; color:#0f172a; font-size:11px; background:#f8fafc; padding:6px; border-radius:6px; border:1px solid #e2e8f0;">"${r.description}"</p>
                ${isPending ? `
                    <div style="display:flex; gap:6px; margin-top:6px;">
                        <button type="button" onclick="quickMapAction(${r.id}, 'approve')" style="flex:1; padding:6px 8px; font-size:11px; font-weight:700; background:#2563eb; color:#ffffff; border:none; border-radius:6px; cursor:pointer;">Setujui</button>
                        <button type="button" onclick="quickMapAction(${r.id}, 'reject')" style="flex:1; padding:6px 8px; font-size:11px; font-weight:700; background:#fee2e2; color:#dc2626; border:1px solid #fecaca; border-radius:6px; cursor:pointer;">Tolak</button>
                    </div>
                ` : `
                    <span style="font-size:11px; color:#1e40af; font-weight:700;">Laporan Aktif di Peta Masyarakat</span>
                `}
            </div>
        `);

        incidentMarkersMap[r.id] = marker;
    });

    if (showCctv) {
        cctvStatic.forEach(c => {
            L.marker([c.lat, c.lng], { icon: createMarkerIcon('C', '#eff6ff', '#2563eb') })
                .bindPopup(`<strong>${c.nama}</strong><br><small style="color:#2563eb">CCTV Aktif Terkoneksi</small>`)
                .addTo(markersGroup);
        });
    }

    if (showLampu) {
        lampuStatic.forEach(l => {
            L.marker([l.lat, l.lng], { icon: createMarkerIcon('L', '#eff6ff', '#3b82f6') })
                .bindPopup(`<strong>${l.nama}</strong><br><small style="color:#2563eb">Penerangan Jalan Baik</small>`)
                .addTo(markersGroup);
        });
    }

    if (showPos) {
        posStatic.forEach(p => {
            L.marker([p.lat, p.lng], { icon: createMarkerIcon('P', '#dbeafe', '#1e40af') })
                .bindPopup(`<strong>${p.nama}</strong><br><small style="color:#1e40af">Pos Satpam Kawasan</small>`)
                .addTo(markersGroup);
        });
    }
}

function updateAdminMapLayers() {
    renderAdminMap();
}

function renderSidebarList() {
    const listEl = document.getElementById('mapIncidentList');
    const validReports = mapReports.filter(r => r.status !== 'rejected');

    document.getElementById('statTotalIncidents').textContent = validReports.length + ' Titik';
    document.getElementById('statPendingIncidents').textContent = mapReports.filter(r => r.status === 'pending_validation' || r.status === 'submitted').length + ' Pending';

    listEl.innerHTML = validReports.map(r => `
        <div class="msb-item" onclick="focusOnReport(${r.id})">
            <div class="msb-item-top">
                <span class="msb-code">${r.code}</span>
                <span class="msb-tag ${(r.status === 'pending_validation' || r.status === 'submitted') ? 'tag-pend' : 'tag-appr'}">
                    ${(r.status === 'pending_validation' || r.status === 'submitted') ? 'Pending' : 'Tayang'}
                </span>
            </div>
            <span class="msb-cat">${r.category}</span>
            <span class="msb-loc">${r.address}</span>
        </div>
    `).join('');
}

function focusOnReport(id) {
    const r = mapReports.find(x => x.id === id);
    const marker = incidentMarkersMap[id];
    if (r && adminMap) {
        adminMap.setView([r.lat, r.lng], 16, { animate: true });
        if (marker) {
            marker.openPopup();
        }
    }
}

function quickMapAction(id, action) {
    const r = mapReports.find(x => x.id === id);
    if (!r) return;

    if (action === 'approve') {
        r.status = 'map_updated';
        alert(`Laporan ${r.code} disetujui.\nTitik kini tayang pada peta masyarakat.`);
    } else {
        r.status = 'rejected';
        alert(`Laporan ${r.code} ditolak.`);
    }

    renderAdminMap();
    renderSidebarList();
}

document.addEventListener('DOMContentLoaded', () => {
    initFullAdminMap();
    loadLiveMapReports();
});
</script>
@endpush
@endsection
