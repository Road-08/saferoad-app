@extends('layouts.app')

@section('title', 'Bandingkan Rute · SafeRoad')

@push('leaflet-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
<div class="compare-page-container">
    <!-- Breadcrumb & Kembali -->
    <div class="compare-nav-bar">
        <a href="{{ url('/rute') }}" class="btn-back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Pencarian Rute</span>
        </a>
    </div>

    <!-- State Ketika Belum Ada Data Rute Dipilih -->
    <section id="tanpa-data" class="empty-state-card" hidden>
        <div class="empty-icon-circle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <h2>Belum Ada Rute untuk Dibandingkan</h2>
        <p>Silakan tentukan titik asal dan tujuan terlebih dahulu di halaman pencarian rute untuk membandingkan alternatif rute perjalanan.</p>
        <a href="{{ url('/rute') }}" class="btn-primary-action">
            <span>Cari Rute Sekarang</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>
    </section>

    <!-- State Utama Ketika Data Rute Tersedia -->
    <section id="konten-rute" hidden>
        <header class="compare-header">
            <div>
                <span class="compare-tag">ANALISIS MULTI-RUTE</span>
                <h1>Perbandingan Alternatif Rute</h1>
                <p>Bandingkan metrik efisiensi jarak, estimasi durasi, dan indikator keamanan sebelum memulai perjalanan.</p>
            </div>
        </header>

        <p id="pesan-info" class="status-alert-banner" hidden></p>
        <p id="pesan-satu" class="status-alert-banner info" hidden>
            Hanya ada satu rute yang ditemukan, sehingga tidak ada rute alternatif lain untuk diperbandingkan.
        </p>

        <!-- Grid Komparasi: Peta & Tabel -->
        <div class="compare-main-grid">
            <!-- Kolom Kiri: Peta Interaktif -->
            <div class="map-comparison-card">
                <div class="card-header-bar">
                    <h3>Visualisasi Peta Rute</h3>
                    <div class="map-legend-pills">
                        <span class="pill-legenda rute-1">Rute 1</span>
                        <span class="pill-legenda rute-2">Rute 2</span>
                        <span class="pill-legenda rute-3">Rute 3</span>
                    </div>
                </div>
                <div class="map-viewport-wrapper">
                    <div id="map" aria-label="Peta perbandingan rute"></div>
                </div>
                <div class="map-card-note">
                    Garis tebal solid menandai rute yang saat ini Anda pilih untuk inspeksi detail.
                </div>
            </div>

            <!-- Kolom Kanan: Rincian Keamanan & Faktor -->
            <div class="details-comparison-card">
                <section id="bagian-rincian">
                    <h3 id="judul-rincian">Rincian Keamanan Rute Terpilih</h3>
                    <div id="kartu-rute" class="route-selector-pills"></div>
                    <div id="daftar-faktor" class="factors-container"></div>
                </section>
            </div>
        </div>

        <!-- Tabel Perbandingan Komprehensif -->
        <section id="bagian-perbandingan" class="table-section-card" hidden>
            <div class="table-card-header">
                <div>
                    <h2>Tabel Evaluasi & Rekomendasi Rute</h2>
                    <p class="bantuan-note">Rute tercepat belum tentu rute teraman. Pertimbangkan pencahayaan dan titik pantau CCTV.</p>
                </div>
                <button type="button" class="btn-refresh" id="coba-lagi" hidden>
                    Muat Ulang Skor
                </button>
            </div>

            <div class="table-scroll-container" tabindex="0" aria-label="Tabel perbandingan rute">
                <table class="table-compare" id="tabel-perbandingan"></table>
            </div>

            <p class="disclaimer-text" id="disclaimer"></p>
        </section>
    </section>
</div>

<style>
    .compare-page-container {
        width: min(100% - 32px, 1140px);
        margin: 32px auto 60px;
    }

    .compare-nav-bar {
        margin-bottom: 20px;
    }

    .btn-back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--primary-600);
        transition: transform 0.15s ease;
    }

    .btn-back-link:hover {
        transform: translateX(-3px);
    }

    .btn-back-link svg {
        width: 16px;
        height: 16px;
    }

    /* Empty State */
    .empty-state-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 20px;
        padding: 60px 24px;
        text-align: center;
        box-shadow: var(--shadow-card);
        max-width: 580px;
        margin: 40px auto;
    }

    .empty-icon-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background-color: var(--primary-50);
        color: var(--primary-600);
        display: grid;
        place-items: center;
        margin: 0 auto 20px;
    }

    .empty-icon-circle svg {
        width: 32px;
        height: 32px;
    }

    .empty-state-card h2 {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-heading);
        margin-bottom: 8px;
    }

    .empty-state-card p {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .btn-primary-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
        color: #ffffff;
        border-radius: 11px;
        font-size: 14px;
        font-weight: 700;
        box-shadow: var(--shadow-btn);
        transition: transform 0.15s ease;
    }

    .btn-primary-action:hover {
        transform: translateY(-2px);
    }

    .btn-primary-action svg {
        width: 16px;
        height: 16px;
    }

    /* Header */
    .compare-header {
        margin-bottom: 24px;
    }

    .compare-tag {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--primary-600);
        background-color: var(--primary-50);
        padding: 4px 10px;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    .compare-header h1 {
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 800;
        color: var(--text-heading);
        letter-spacing: -0.03em;
        margin-bottom: 6px;
    }

    .compare-header p {
        font-size: 14px;
        color: var(--text-muted);
    }

    .status-alert-banner {
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13.5px;
        margin-bottom: 20px;
        background-color: var(--danger-50);
        border: 1px solid var(--danger-100);
        color: var(--danger-600);
    }

    .status-alert-banner.info {
        background-color: var(--primary-50);
        border-color: var(--primary-100);
        color: var(--primary-700);
    }

    /* Grid Utama Komparasi */
    .compare-main-grid {
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 24px;
        margin-bottom: 28px;
    }

    .map-comparison-card,
    .details-comparison-card,
    .table-section-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 18px;
        padding: 24px;
        box-shadow: var(--shadow-card);
    }

    .card-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .card-header-bar h3,
    .details-comparison-card h3 {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-heading);
    }

    .map-legend-pills {
        display: flex;
        gap: 6px;
    }

    .pill-legenda {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        color: #ffffff;
    }

    .pill-legenda.rute-1 { background-color: #1e3a8a; }
    .pill-legenda.rute-2 { background-color: #d97706; }
    .pill-legenda.rute-3 { background-color: #7c3aed; }

    .map-viewport-wrapper {
        border-radius: 14px;
        overflow: hidden;
        border: 1.5px solid var(--border-soft);
    }

    #map {
        height: 380px;
        width: 100%;
        background-color: #e2e8f0;
    }

    .map-card-note {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 10px;
    }

    /* Route Selector Pills */
    .route-selector-pills {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin: 14px 0 20px;
    }

    .kartu-rute {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid var(--border-soft);
        border-radius: 12px;
        background-color: #ffffff;
        color: var(--text-heading);
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .kartu-rute:hover {
        border-color: var(--primary-500);
        background-color: var(--primary-50);
    }

    .kartu-rute.terpilih {
        border-color: var(--primary-600);
        background-color: var(--primary-50);
        color: var(--primary-800);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    /* Factors Container */
    .factors-container {
        display: grid;
        gap: 12px;
    }

    .faktor {
        padding: 12px 14px;
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        background-color: var(--bg-canvas);
    }

    .faktor.tidak-tersedia {
        opacity: 0.6;
    }

    .faktor-atas {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-heading);
    }

    .bar-latar {
        height: 7px;
        margin-top: 8px;
        border-radius: 999px;
        background-color: var(--border-soft);
        overflow: hidden;
    }

    .bar-nilai {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
    }

    .faktor-note {
        margin-top: 6px;
        font-size: 11.5px;
        color: var(--text-muted);
    }

    /* Tabel Perbandingan */
    .table-section-card {
        margin-top: 24px;
    }

    .table-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .table-card-header h2 {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-heading);
        margin-bottom: 4px;
    }

    .bantuan-note {
        font-size: 13px;
        color: var(--text-muted);
    }

    .btn-refresh {
        padding: 8px 14px;
        background-color: var(--primary-50);
        color: var(--primary-700);
        border: 1px solid var(--primary-100);
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
    }

    .table-scroll-container {
        overflow-x: auto;
        border: 1px solid var(--border-soft);
        border-radius: 14px;
    }

    .table-compare {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .table-compare th,
    .table-compare td {
        padding: 12px 16px;
        text-align: center;
        border-bottom: 1px solid var(--border-soft);
    }

    .table-compare th:first-child,
    .table-compare td:first-child {
        text-align: left;
        font-weight: 700;
        color: var(--text-heading);
        background-color: #f8fafc;
        width: 180px;
    }

    .table-compare th {
        background-color: var(--primary-50);
    }

    .pilih-kolom {
        padding: 6px 12px;
        border: none;
        border-radius: 8px;
        background-color: transparent;
        font-size: 13px;
        font-weight: 800;
        color: var(--primary-800);
        cursor: pointer;
        transition: background-color 0.15s;
    }

    .table-compare th.terpilih .pilih-kolom {
        background-color: var(--primary-600);
        color: #ffffff;
    }

    .table-compare [data-route-id].terpilih {
        background-color: #eff6ff;
    }

    /* Badges & Tags */
    .tag {
        display: inline-block;
        margin-left: 6px;
        padding: 2px 7px;
        border-radius: 9999px;
        background-color: var(--success-600);
        color: #ffffff;
        font-size: 10px;
        font-weight: 800;
    }

    .tag.alt {
        background-color: var(--primary-600);
    }

    .level-badge {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 800;
    }

    .level-aman { background-color: var(--success-100); color: var(--success-600); }
    .level-sedang { background-color: var(--warning-100); color: #b45309; }
    .level-rawan { background-color: var(--danger-100); color: var(--danger-600); }
    .level-data-belum-cukup { background-color: #f1f5f9; color: var(--text-muted); }

    .disclaimer-text {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 14px;
        text-align: right;
        font-style: italic;
    }

    @media (max-width: 900px) {
        .compare-main-grid {
            grid-template-columns: 1fr;
        }

        #map {
            height: 280px;
        }
    }
</style>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const URL_SKOR = "{{ url('/api/routes/score') }}";
    const KUNCI_DATA = 'saferoad.perbandingan';
    const KUNCI_PILIHAN = 'saferoad.rutePilihan';
    const DISCLAIMER_FALLBACK = 'Skor adalah indikator, bukan jaminan mutlak keamanan.';
    const WARNA_RUTE = ['#1e3a8a', '#d97706', '#7c3aed'];
    const $ = id => document.getElementById(id);
    let dataRute = null;
    let hasilSkor = new Map();
    let statusSkor = 'pending';
    let disclaimerAktif = DISCLAIMER_FALLBACK;
    let indeksTerpilih = 0;
    let nomorPermintaan = 0;
    let map = null;
    let garisRute = [];

    function ambilDataPerbandingan() {
        try {
            const parsed = JSON.parse(sessionStorage.getItem(KUNCI_DATA));
            if (!parsed || parsed.version !== 1 || !Array.isArray(parsed.routes) || parsed.routes.length < 1 || parsed.routes.length > 3) return null;
            const koordinatValid = titik => Array.isArray(titik) && titik.length >= 2 && Number.isFinite(titik[0]) && titik[0] >= -180 && titik[0] <= 180 && Number.isFinite(titik[1]) && titik[1] >= -90 && titik[1] <= 90;
            const titikLokasiValid = lokasi => lokasi && Number.isFinite(lokasi.lat) && lokasi.lat >= -90 && lokasi.lat <= 90 && Number.isFinite(lokasi.lng) && lokasi.lng >= -180 && lokasi.lng <= 180;
            if (!titikLokasiValid(parsed.origin) || !titikLokasiValid(parsed.destination)) return null;
            const routes = parsed.routes.map(route => {
                if (!route || typeof route.id !== 'string' || typeof route.distance_m !== 'number' || !Number.isFinite(route.distance_m) || route.distance_m <= 0 || typeof route.duration_s !== 'number' || !Number.isFinite(route.duration_s) || route.duration_s <= 0 || !Array.isArray(route.coordinates) || route.coordinates.length < 2 || !route.coordinates.every(koordinatValid)) return null;
                return { id: route.id, distance_m: route.distance_m, duration_s: route.duration_s, coordinates: route.coordinates };
            });
            if (routes.some(route => route === null) || !Number.isInteger(parsed.selectedIndex) || parsed.selectedIndex < 0 || parsed.selectedIndex >= routes.length) return null;
            return { origin: parsed.origin, destination: parsed.destination, selectedIndex: parsed.selectedIndex, routes };
        } catch (error) {
            return null;
        }
    }

    function konversiMetrikRute(route) {
        return { jarakKm: route.distance_m / 1000, waktuMenit: Math.max(1, Math.ceil(route.duration_s / 60)) };
    }

    async function ambilSkorRute(routes) {
        const payload = routes.map(route => {
            const points = route.coordinates;
            const step = Math.ceil(points.length / 500);
            const sampled = points.filter((point, index) => index === 0 || index === points.length - 1 || index % step === 0);
            while (sampled.length > 500) sampled.splice(sampled.length - 2, 1);
            return { id: route.id, coordinates: sampled.map(point => [point[0], point[1]]) };
        });
        let response;
        try {
            response = await fetch(URL_SKOR, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ routes: payload })
            });
        } catch (error) {
            throw new Error('Koneksi layanan skor gagal.');
        }
        let result = {};
        try { result = await response.json(); } catch (error) { result = {}; }
        if (!response.ok) {
            if (response.status === 422) {
                const errors = result.errors && Object.values(result.errors).flat();
                throw new Error((errors && errors[0]) || result.message || 'Data rute tidak dapat dihitung.');
            }
            if (response.status === 429) throw new Error('Permintaan skor terlalu sering.');
            if (response.status === 404) throw new Error('Layanan kalkulasi skor belum terhubung.');
            if (response.status >= 500) throw new Error('Layanan skor sedang bermasalah.');
            throw new Error(result.message || 'Skor belum tersedia.');
        }
        return result;
    }

    function buatElemen(tag, className, text) {
        const element = document.createElement(tag);
        if (className) element.className = className;
        if (text !== undefined && text !== null) element.textContent = String(text);
        return element;
    }

    function formatLevel(level) {
        const levels = {
            aman: { label: 'Aman', className: 'level-aman' },
            sedang: { label: 'Sedang', className: 'level-sedang' },
            rawan: { label: 'Rawan', className: 'level-rawan' },
            data_belum_cukup: { label: 'Data belum cukup', className: 'level-data-belum-cukup' }
        };
        return levels[level] || null;
    }

    function indeksRuteTerbaik(compare) {
        let best = 0;
        dataRute.routes.forEach((route, index) => { if (compare(route, dataRute.routes[best]) < 0) best = index; });
        return best;
    }

    function hitungPenanda() {
        const fastest = indeksRuteTerbaik((a, b) => a.duration_s - b.duration_s);
        const shortest = indeksRuteTerbaik((a, b) => a.distance_m - b.distance_m);
        const scored = dataRute.routes.map((route, index) => ({ index, score: hasilSkor.get(route.id)?.score }))
            .filter(item => typeof item.score === 'number' && Number.isFinite(item.score));
        scored.sort((a, b) => b.score - a.score || dataRute.routes[a.index].duration_s - dataRute.routes[b.index].duration_s);
        return { fastest, shortest, safest: scored.length ? scored[0].index : null };
    }

    function initPeta() {
        map = L.map('map', { zoomControl: false });
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        const originName = typeof dataRute.origin.name === 'string' ? dataRute.origin.name : 'Asal';
        const destinationName = typeof dataRute.destination.name === 'string' ? dataRute.destination.name : 'Tujuan';
        L.circleMarker([dataRute.origin.lat, dataRute.origin.lng], { radius: 8, color: '#fff', weight: 3, fillColor: '#1e3a8a', fillOpacity: 1 }).addTo(map).bindPopup(originName);
        L.marker([dataRute.destination.lat, dataRute.destination.lng]).addTo(map).bindPopup(destinationName);
        dataRute.routes.forEach((route, index) => {
            const line = L.polyline(route.coordinates.map(point => [point[1], point[0]]), { color: WARNA_RUTE[index], weight: 5, opacity: .45, dashArray: '8 7' }).addTo(map);
            garisRute.push(line);
        });
        map.fitBounds(L.featureGroup(garisRute).getBounds().pad(.15));
    }

    function renderTabel() {
        const table = $('tabel-perbandingan');
        table.replaceChildren();
        const head = document.createElement('thead');
        const headRow = document.createElement('tr');
        headRow.appendChild(buatElemen('th', '', 'Indikator Evaluasi'));
        dataRute.routes.forEach((route, index) => {
            const th = document.createElement('th');
            th.dataset.routeId = route.id;
            const button = buatElemen('button', 'pilih-kolom', 'Rute ' + route.id);
            button.type = 'button';
            button.setAttribute('aria-pressed', String(index === indeksTerpilih));
            button.addEventListener('click', () => pilihRute(index));
            th.appendChild(button);
            headRow.appendChild(th);
        });
        head.appendChild(headRow);
        table.appendChild(head);
        const body = document.createElement('tbody');
        const markers = hitungPenanda();
        const rows = [
            ['Jarak Tempuh (km)', (route, index) => nilaiMetrik(route, 'jarak', index, markers)],
            ['Estimasi Waktu', (route, index) => nilaiMetrik(route, 'waktu', index, markers)],
            ['Tingkat Keamanan', (route, index) => nilaiLevel(route, index, markers)],
            ['Skor Keamanan (0-100)', (route, index) => nilaiSkor(route)]
        ];
        rows.forEach(([label, render]) => {
            const tr = document.createElement('tr');
            tr.appendChild(buatElemen('td', '', label));
            dataRute.routes.forEach((route, index) => {
                const td = document.createElement('td');
                td.dataset.routeId = route.id;
                render(route, index).forEach(node => td.appendChild(node));
                tr.appendChild(td);
            });
            body.appendChild(tr);
        });
        table.appendChild(body);
        tandaiPilihan();
    }

    function nilaiMetrik(route, type, index, markers) {
        const metric = konversiMetrikRute(route);
        const value = type === 'jarak' ? metric.jarakKm.toFixed(1) + ' km' : String(metric.waktuMenit) + ' mnt';
        const nodes = [document.createTextNode(value)];
        if (type === 'jarak' && markers.shortest === index) nodes.push(buatElemen('span', 'tag alt', 'Terpendek'));
        if (type === 'waktu' && markers.fastest === index) nodes.push(buatElemen('span', 'tag alt', 'Tercepat'));
        return nodes;
    }

    function nilaiLevel(route, index, markers) {
        const nodes = [];
        const score = hasilSkor.get(route.id);
        const level = score && formatLevel(score.level);
        nodes.push(level ? buatElemen('span', 'level-badge ' + level.className, level.label) : document.createTextNode(statusSkor === 'pending' ? 'Menghitung...' : 'Belum tersedia'));
        if (markers.safest === index) nodes.push(buatElemen('span', 'tag', 'Teraman'), buatElemen('span', 'tag alt', 'Disarankan'));
        return nodes;
    }

    function nilaiSkor(route) {
        if (statusSkor === 'pending') return [document.createTextNode('Menghitung...')];
        if (statusSkor === 'unavailable') return [document.createTextNode('Skor belum tersedia')];
        const score = hasilSkor.get(route.id);
        if (!score || score.score === null || score.level === 'data_belum_cukup') return [document.createTextNode('-')];
        return [document.createTextNode(typeof score.score === 'number' ? String(score.score) : '-')];
    }

    function renderKartu() {
        const container = $('kartu-rute');
        container.replaceChildren();
        if (dataRute.routes.length < 2) return;
        dataRute.routes.forEach((route, index) => {
            const button = buatElemen('button', 'kartu-rute' + (index === indeksTerpilih ? ' terpilih' : ''), 'Rute ' + route.id + ' · ' + konversiMetrikRute(route).jarakKm.toFixed(1) + ' km · ' + konversiMetrikRute(route).waktuMenit + ' menit');
            button.type = 'button';
            button.setAttribute('aria-pressed', String(index === indeksTerpilih));
            button.addEventListener('click', () => pilihRute(index));
            container.appendChild(button);
        });
    }

    function renderFaktor() {
        const container = $('daftar-faktor');
        container.replaceChildren();
        const result = hasilSkor.get(dataRute.routes[indeksTerpilih].id);
        const factors = result && Array.isArray(result.factors) ? result.factors : [];
        if (!factors.length) {
            container.appendChild(buatElemen('p', 'bantuan-note', statusSkor === 'pending' ? 'Rincian faktor keamanan sedang dianalisis...' : 'Rincian faktor penerangan dan CCTV belum tersedia pada rute ini.'));
            return;
        }
        factors.forEach(factor => {
            const available = factor.available === true;
            const wrapper = buatElemen('div', 'faktor' + (available ? '' : ' tidak-tersedia'));
            const top = buatElemen('div', 'faktor-atas');
            top.append(buatElemen('strong', '', factor.label || factor.key || 'Faktor Keamanan'), buatElemen('span', '', typeof factor.score === 'number' ? String(Math.round(factor.score)) : '-'));
            wrapper.appendChild(top);
            const track = buatElemen('div', 'bar-latar');
            const bar = buatElemen('div', 'bar-nilai');
            bar.style.width = available && typeof factor.score === 'number' ? Math.max(0, Math.min(100, factor.score)) + '%' : '0%';
            track.appendChild(bar);
            wrapper.appendChild(track);
            if (factor.note) wrapper.appendChild(buatElemen('div', 'faktor-note', factor.note));
            container.appendChild(wrapper);
        });
    }

    function tandaiPilihan() {
        document.querySelectorAll('#tabel-perbandingan [data-route-id]').forEach(element => {
            const selected = element.dataset.routeId === dataRute.routes[indeksTerpilih].id;
            element.classList.toggle('terpilih', selected);
            if (element.tagName === 'TH') element.querySelector('button').setAttribute('aria-pressed', String(selected));
        });
        garisRute.forEach((line, index) => {
            const selected = index === indeksTerpilih;
            line.setStyle({ weight: selected ? 8 : 4, opacity: selected ? 1 : .42, dashArray: selected ? null : '8 7' });
            if (selected) line.bringToFront();
        });
        renderKartu();
    }

    function pilihRute(index) {
        indeksTerpilih = index;
        try { sessionStorage.setItem(KUNCI_PILIHAN, JSON.stringify({ id: dataRute.routes[index].id, savedAt: new Date().toISOString() })); } catch (error) {}
        tandaiPilihan();
        renderFaktor();
    }

    function renderSemua() {
        renderTabel();
        renderFaktor();
        $('disclaimer').textContent = disclaimerAktif;
    }

    async function muatSkor() {
        const request = ++nomorPermintaan;
        statusSkor = 'pending';
        hasilSkor = new Map();
        $('pesan-info').hidden = true;
        $('coba-lagi').hidden = true;
        if (dataRute.routes.length > 1) renderSemua();
        else renderFaktor();
        try {
            const payload = await ambilSkorRute(dataRute.routes);
            if (request !== nomorPermintaan) return;
            const results = Array.isArray(payload.data) ? payload.data : [];
            results.forEach(result => { if (result && typeof result.id === 'string') hasilSkor.set(result.id, result); });
            disclaimerAktif = typeof payload.disclaimer === 'string' ? payload.disclaimer : DISCLAIMER_FALLBACK;
            statusSkor = 'available';
        } catch (error) {
            if (request !== nomorPermintaan) return;
            statusSkor = 'unavailable';
            disclaimerAktif = DISCLAIMER_FALLBACK;
            $('pesan-info').textContent = error.message || 'Layanan kalkulasi skor rute belum terhubung.';
            $('pesan-info').className = 'status-alert-banner';
            $('pesan-info').hidden = false;
            $('coba-lagi').hidden = false;
        }
        renderSemua();
    }

    function mulai() {
        dataRute = ambilDataPerbandingan();
        if (!dataRute) {
            $('tanpa-data').hidden = false;
            return;
        }
        $('konten-rute').hidden = false;
        indeksTerpilih = dataRute.selectedIndex;
        if (dataRute.routes.length === 1) {
            $('pesan-satu').hidden = false;
            $('bagian-perbandingan').hidden = true;
        } else {
            $('bagian-perbandingan').hidden = false;
        }
        initPeta();
        try { sessionStorage.setItem(KUNCI_PILIHAN, JSON.stringify({ id: dataRute.routes[indeksTerpilih].id, savedAt: new Date().toISOString() })); } catch (error) {}
        if (dataRute.routes.length > 1) renderSemua();
        else renderFaktor();
        $('coba-lagi').addEventListener('click', muatSkor);
        muatSkor();
    }

    mulai();
</script>
@endpush
