@extends('layouts.app')

@section('title', 'Temukan Rute Aman · SafeRoad')

@push('leaflet-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
<div class="route-page-container">
    <div class="route-split-layout">
        <!-- SIDEBAR PENCARIAN & KARTU RUTE (KIRI) -->
        <aside class="route-sidebar">
            <header class="sidebar-header">
                <span class="badge-sub">NAVIGASI PINTAR</span>
                <h1>Temukan Rute Perjalanan Aman</h1>
                <p>Cari jalur terbaik dengan analisis titik penerangan, CCTV, dan riwayat insiden di sepanjang perjalanan.</p>
            </header>

            <!-- Card Input Form Rute -->
            <div class="route-form-card">
                <div class="inputs-stack">
                    <!-- Titik Asal -->
                    <div class="input-line-group">
                        <span class="input-marker-dot origin" aria-hidden="true"></span>
                        <div class="input-inner-wrap">
                            <label for="inputDari">Titik Asal</label>
                            <input
                                type="text"
                                id="inputDari"
                                value="Lokasi Saya"
                                placeholder="Tentukan lokasi asal"
                                autocomplete="off"
                            >
                            <div class="saran-dropdown" id="saranDari"></div>
                        </div>
                    </div>

                    <!-- Tombol Tukar Asal / Tujuan -->
                    <button class="btn-swap-location" id="btnTukar" type="button" title="Tukar titik asal dan tujuan">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transform: rotate(180deg);">
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                    </button>

                    <!-- Titik Tujuan -->
                    <div class="input-line-group">
                        <span class="input-marker-dot dest" aria-hidden="true"></span>
                        <div class="input-inner-wrap">
                            <label for="inputKe">Tujuan</label>
                            <input
                                type="text"
                                id="inputKe"
                                placeholder="Cari tujuan (misal: Telkom University, Stasiun...)"
                                autocomplete="off"
                            >
                            <div class="saran-dropdown" id="saranKe"></div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Cari -->
                <button class="btn-search-route" id="btnCari" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Cari Rekomendasi Rute</span>
                </button>

                <!-- Status Feedback -->
                <div class="status-banner" id="status"></div>
            </div>

            <!-- Hasil & Alternatif Rute -->
            <section class="results-section">
                <div class="results-header-bar">
                    <h2>Pilihan Rute Perjalanan</h2>
                    <span id="infoRute" class="results-count"></span>
                </div>

                <div id="daftarRute" class="route-cards-list"></div>

                <!-- Tombol Bandingkan Rute -->
                <button class="btn-compare-routes" id="btnBandingkan" type="button" hidden>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 3h5v5"/>
                        <path d="M4 20L21 3"/>
                        <path d="M21 16v5h-5"/>
                        <path d="M15 15l6 6"/>
                        <path d="M4 4l5 5"/>
                    </svg>
                    <span>Bandingkan Rute di Halaman Khusus</span>
                </button>

                <div id="perbandinganRute"></div>
                <div class="disclaimer-note" id="disclaimerSkor"></div>

                <!-- Tombol Mulai Navigasi -->
                <button class="btn-start-navigation" id="btnNavigasi" type="button" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                    </svg>
                    <span>Mulai Navigasi Perjalanan</span>
                </button>

                <p class="navigation-disclaimer">
                    Skor dan indikator membantu evaluasi rute secara objektif, bukan jaminan mutlak kondisi lapangan.
                </p>
            </section>
        </aside>

        <!-- AREA MAP KANAN -->
        <main class="route-map-panel">
            <div class="map-container-box">
                <div id="map" aria-label="Peta interaktif SafeRoad"></div>
                <!-- Overlay Legenda Ringkas -->
                <div class="map-floating-legend">
                    <span class="legend-chip"><i class="dot blue"></i> Rute Utama</span>
                    <span class="legend-chip"><i class="dot amber"></i> Rute Alternatif</span>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .route-page-container {
        width: 100%;
        max-width: 1600px;
        margin: 0 auto;
        padding: 24px clamp(16px, 3vw, 40px) 40px;
    }

    .route-split-layout {
        display: grid;
        grid-template-columns: 460px 1fr;
        gap: 28px;
        align-items: start;
    }

    /* Sidebar Kiri */
    .route-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sidebar-header .badge-sub {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--primary-600);
        background-color: var(--primary-50);
        padding: 3px 10px;
        border-radius: 6px;
        margin-bottom: 6px;
    }

    .sidebar-header h1 {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-heading);
        letter-spacing: -0.03em;
        line-height: 1.25;
        margin-bottom: 6px;
    }

    .sidebar-header p {
        font-size: 13.5px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* Form Input Rute Card */
    .route-form-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 18px;
        padding: 20px;
        box-shadow: var(--shadow-card);
    }

    .inputs-stack {
        position: relative;
        display: grid;
        gap: 12px;
        margin-bottom: 16px;
    }

    .input-line-group {
        display: flex;
        align-items: center;
        gap: 12px;
        background-color: var(--bg-canvas);
        border: 1.5px solid var(--border-soft);
        border-radius: 12px;
        padding: 8px 14px;
        transition: border-color 0.15s ease;
    }

    .input-line-group:focus-within {
        border-color: var(--primary-600);
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .input-marker-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .input-marker-dot.origin {
        background-color: var(--primary-600);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }

    .input-marker-dot.dest {
        background-color: var(--danger-600);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
    }

    .input-inner-wrap {
        flex: 1;
        position: relative;
    }

    .input-inner-wrap label {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 2px;
    }

    .input-inner-wrap input {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-heading);
    }

    .input-inner-wrap input::placeholder {
        color: #94a3b8;
        font-weight: normal;
    }

    /* Tombol Tukar Lokasi */
    .btn-swap-location {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #ffffff;
        border: 1px solid var(--border-soft);
        color: var(--primary-600);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: var(--shadow-sm);
        z-index: 10;
        transition: transform 0.2s ease, background 0.15s ease;
    }

    .btn-swap-location:hover {
        background-color: var(--primary-50);
        transform: translateY(-50%) rotate(180deg);
    }

    .btn-swap-location svg {
        width: 12px;
        height: 12px;
    }

    /* Dropdown Saran Pencarian */
    .saran-dropdown {
        position: absolute;
        left: -40px;
        right: -10px;
        top: calc(100% + 10px);
        background-color: #ffffff;
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        z-index: 2000;
        overflow: hidden;
    }

    .saran-dropdown:empty {
        display: none;
    }

    .item-saran {
        padding: 10px 14px;
        font-size: 12.5px;
        color: var(--text-body);
        cursor: pointer;
        border-bottom: 1px solid var(--border-soft);
    }

    .item-saran:last-child {
        border-bottom: none;
    }

    .item-saran:hover {
        background-color: var(--primary-50);
        color: var(--primary-700);
    }

    /* Tombol Cari */
    .btn-search-route {
        width: 100%;
        height: 48px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: var(--shadow-btn);
        transition: transform 0.15s ease;
    }

    .btn-search-route:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
    }

    .btn-search-route:disabled {
        opacity: 0.65;
        cursor: wait;
        transform: none;
    }

    .btn-search-route svg {
        width: 18px;
        height: 18px;
    }

    /* Status Banner */
    .status-banner {
        min-height: 20px;
        font-size: 12.5px;
        color: var(--text-muted);
        text-align: center;
        margin-top: 10px;
    }

    .status-banner.error {
        color: var(--danger-600);
        font-weight: 600;
    }

    /* Bagian Hasil Rute */
    .results-section {
        background-color: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 18px;
        padding: 20px;
        box-shadow: var(--shadow-card);
    }

    .results-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 14px;
    }

    .results-header-bar h2 {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-heading);
    }

    .results-count {
        font-size: 12px;
        color: var(--text-muted);
    }

    .route-cards-list {
        display: grid;
        gap: 10px;
        margin-bottom: 16px;
    }

    /* Kartu Rute */
    .kartu {
        background-color: #ffffff;
        border: 1.5px solid var(--border-soft);
        border-radius: 14px;
        padding: 14px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .kartu:hover {
        border-color: var(--primary-500);
        background-color: var(--primary-50);
    }

    .kartu.aktif {
        border-color: var(--primary-600);
        background-color: var(--primary-50);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    .kartu-atas {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 4px;
    }

    .kartu-atas b {
        font-size: 15px;
        color: var(--text-heading);
    }

    .skor {
        font-size: 20px;
        font-weight: 800;
        color: var(--primary-700);
    }

    .skor small {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .meta {
        font-size: 12.5px;
        color: var(--text-muted);
        margin: 4px 0 10px;
    }

    .chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .chip {
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 999px;
        background-color: var(--bg-canvas);
        border: 1px solid var(--border-soft);
        color: var(--text-body);
    }

    .tag {
        font-size: 10px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 999px;
        background-color: var(--success-600);
        color: #ffffff;
        margin-left: 6px;
    }

    .tag.alt {
        background-color: var(--primary-600);
    }

    .level-badge {
        display: inline-block;
        margin-left: 6px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .level-aman { background-color: var(--success-100); color: var(--success-600); }
    .level-sedang { background-color: var(--warning-100); color: #b45309; }
    .level-rawan { background-color: var(--danger-100); color: var(--danger-600); }
    .level-data-belum-cukup { background-color: #f1f5f9; color: var(--text-muted); }

    /* Tombol Bandingkan & Navigasi */
    .btn-compare-routes {
        width: 100%;
        height: 44px;
        background-color: var(--primary-50);
        color: var(--primary-700);
        border: 1.5px solid var(--primary-100);
        border-radius: 11px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        margin-bottom: 12px;
        transition: background 0.15s;
    }

    .btn-compare-routes:hover {
        background-color: #dbeafe;
    }

    .btn-compare-routes svg {
        width: 16px;
        height: 16px;
    }

    .disclaimer-note {
        font-size: 11px;
        color: var(--text-muted);
        margin: 6px 0 14px;
    }

    .btn-start-navigation {
        width: 100%;
        height: 48px;
        background: linear-gradient(135deg, var(--primary-700), var(--primary-900));
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(15, 45, 89, 0.3);
        transition: transform 0.15s ease;
    }

    .btn-start-navigation:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    .btn-start-navigation:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    .btn-start-navigation svg {
        width: 16px;
        height: 16px;
    }

    .navigation-disclaimer {
        font-size: 11px;
        color: #94a3b8;
        text-align: center;
        margin-top: 10px;
        line-height: 1.4;
    }

    /* Map Panel Kanan */
    .route-map-panel {
        position: sticky;
        top: 96px;
    }

    .map-container-box {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        border: 1.5px solid var(--border-soft);
        box-shadow: var(--shadow-card);
    }

    #map {
        height: 640px;
        width: 100%;
        background-color: #e2e8f0;
    }

    .map-floating-legend {
        position: absolute;
        bottom: 16px;
        left: 16px;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: 9999px;
        border: 1px solid var(--border-soft);
        display: flex;
        gap: 12px;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-heading);
        z-index: 1000;
        box-shadow: var(--shadow-sm);
    }

    .legend-chip {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .dot.blue { background-color: var(--primary-600); }
    .dot.amber { background-color: #d97706; }

    @media (max-width: 1024px) {
        .route-split-layout {
            grid-template-columns: 1fr;
        }

        .route-map-panel {
            position: static;
            order: -1; /* Peta di atas pada layar sedang */
        }

        #map {
            height: 380px;
        }
    }
</style>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // =====================================================
    // PENGATURAN AWAL
    // =====================================================
    const PUSAT = { lat: -7.2575, lng: 112.7521 };
    const DISCLAIMER_FALLBACK = 'Skor adalah indikator, bukan jaminan mutlak keamanan.';

    const $ = id => document.getElementById(id);
    const inputDari = $('inputDari');
    const inputKe = $('inputKe');
    const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

    // =====================================================
    // MEMBUAT PETA
    // =====================================================
    const map = L.map('map', { zoomControl: false }).setView([PUSAT.lat, PUSAT.lng], 13);
    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const layerRute = L.layerGroup().addTo(map);

    // =====================================================
    // PENCARIAN TEMPAT (Nominatim)
    // =====================================================
    let terakhir = 0;

    async function cariTempat(q) {
        const tunggu = terakhir + 1100 - Date.now();
        if (tunggu > 0) await sleep(tunggu);
        terakhir = Date.now();

        const url = 'https://nominatim.openstreetmap.org/search' +
            '?format=json&limit=5&countrycodes=id&accept-language=id' +
            '&viewbox=112.5,-7.1,113.0,-7.5' +
            '&q=' + encodeURIComponent(q);

        const res = await fetch(url);
        if (!res.ok) throw new Error('Layanan pencarian tempat sedang sibuk, coba lagi.');
        return res.json();
    }

    let asal = null;
    let tujuan = null;

    function debounce(fn, ms) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), ms);
        };
    }

    function pasangSaran(input, box, setter) {
        const cari = debounce(async () => {
            const q = input.value.trim();
            if (q.length < 3 || q.toLowerCase() === 'lokasi saya') {
                box.innerHTML = '';
                return;
            }

            try {
                const hasil = await cariTempat(q);
                box.innerHTML = '';
                hasil.forEach(h => {
                    const d = document.createElement('div');
                    d.className = 'item-saran';
                    d.textContent = h.display_name;
                    d.addEventListener('click', () => {
                        input.value = h.display_name.split(',').slice(0, 2).join(',').trim();
                        setter({
                            nama: input.value,
                            lat: +h.lat,
                            lng: +h.lon
                        });
                        box.innerHTML = '';
                    });
                    box.appendChild(d);
                });
            } catch (e) {
                box.innerHTML = '';
            }
        }, 600);

        input.addEventListener('input', () => {
            setter(null);
            cari();
        });

        document.addEventListener('click', e => {
            if (!box.contains(e.target) && e.target !== input) {
                box.innerHTML = '';
            }
        });
    }

    pasangSaran(inputDari, $('saranDari'), value => { asal = value; });
    pasangSaran(inputKe, $('saranKe'), value => { tujuan = value; });

    function lokasiSaya() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                return reject(new Error('Browser tidak mendukung lokasi. Ketik alamat asal.'));
            }
            navigator.geolocation.getCurrentPosition(
                position => {
                    resolve({
                        nama: 'Lokasi Saya',
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    });
                },
                () => {
                    resolve({
                        nama: 'Surabaya Pusat (Default)',
                        lat: PUSAT.lat,
                        lng: PUSAT.lng
                    });
                },
                { timeout: 7000 }
            );
        });
    }

    let daftarGaris = [];
    let terpilihIdx = 0;
    let titikA = null;
    let titikB = null;
    let modeNavigasi = false;
    let penghitungPermintaanRute = 0;
    let hasilRuteAktif = [];
    let ruteOSRMAktif = [];
    let statusSkor = 'pending';
    let disclaimerAktif = DISCLAIMER_FALLBACK;

    function setStatus(teks, error = false) {
        $('status').textContent = teks || '';
        $('status').className = 'status-banner' + (error ? ' error' : '');
    }

    function simpanDanBukaPerbandingan() {
        const routes = ruteOSRMAktif.slice(0, 3).map((route, index) => {
            const coordinates = route.geometry.coordinates;
            const langkah = Math.ceil(coordinates.length / 500);
            const dijarangkan = coordinates.filter((titik, titikIndex) =>
                titikIndex === 0 ||
                titikIndex === coordinates.length - 1 ||
                titikIndex % langkah === 0
            );

            while (dijarangkan.length > 500) dijarangkan.splice(dijarangkan.length - 2, 1);

            return {
                id: String.fromCharCode(65 + index),
                distance_m: route.distance,
                duration_s: route.duration,
                coordinates: dijarangkan.map(titik => [titik[0], titik[1]])
            };
        });

        const data = {
            version: 1,
            createdAt: new Date().toISOString(),
            origin: { name: titikA.nama, lat: titikA.lat, lng: titikA.lng },
            destination: { name: titikB.nama, lat: titikB.lat, lng: titikB.lng },
            selectedIndex: terpilihIdx,
            routes
        };

        try {
            sessionStorage.setItem('saferoad.perbandingan', JSON.stringify(data));
        } catch (error) {
            setStatus('Tidak dapat menyimpan rute untuk dibandingkan.', true);
            return;
        }

        window.location.href = "{{ url('/rute/bandingkan') }}";
    }

    function pilih(i) {
        terpilihIdx = i;
        daftarGaris.forEach((garis, index) => {
            const aktif = index === i;
            garis.setStyle({
                color: aktif ? '#2563eb' : '#1e3a8a',
                weight: aktif ? 7 : 4,
                opacity: aktif ? 0.95 : 0.45,
                dashArray: aktif ? null : '6 8'
            });
            if (aktif) garis.bringToFront();
        });

        document.querySelectorAll('.kartu').forEach((kartu, index) => {
            kartu.classList.toggle('aktif', index === i);
        });

        document.querySelectorAll('[data-indeks-rute]').forEach(kolom => {
            kolom.classList.toggle('terpilih', Number(kolom.dataset.indeksRute) === i);
        });

        if (modeNavigasi && daftarGaris[i]) {
            map.fitBounds(daftarGaris[i].getBounds().pad(0.1));
        }
    }

    function konversiMetrikRute(route) {
        return {
            jarakKm: route.distance / 1000,
            waktuMenit: Math.max(1, Math.round(route.duration / 60))
        };
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

    async function ambilSkorRute(routes) {
        const response = await fetch("{{ url('/api/routes/score') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ routes })
        });
        let payload = {};
        try { payload = await response.json(); } catch (error) { payload = {}; }
        if (!response.ok) {
            throw new Error(payload.message || 'Skor belum tersedia');
        }
        return payload;
    }

    function renderRute(routes) {
        const box = $('daftarRute');
        box.innerHTML = '';
        daftarGaris = [];

        layerRute.clearLayers();

        const icA = L.divIcon({
            html: '<div style="background:#1e3a8a;color:#fff;border-radius:50%;width:26px;height:26px;display:grid;place-items:center;font-weight:bold;box-shadow:0 2px 6px rgba(0,0,0,0.3)">A</div>',
            className: ''
        });
        const icB = L.divIcon({
            html: '<div style="background:#dc2626;color:#fff;border-radius:50%;width:26px;height:26px;display:grid;place-items:center;font-weight:bold;box-shadow:0 2px 6px rgba(0,0,0,0.3)">B</div>',
            className: ''
        });

        L.marker([titikA.lat, titikA.lng], { icon: icA }).addTo(layerRute).bindPopup(titikA.nama);
        L.marker([titikB.lat, titikB.lng], { icon: icB }).addTo(layerRute).bindPopup(titikB.nama);

        routes.forEach((route, index) => {
            const pts = route.geometry.coordinates.map(c => [c[1], c[0]]);
            const isMain = index === 0;
            const line = L.polyline(pts, {
                color: isMain ? '#2563eb' : '#d97706',
                weight: isMain ? 7 : 4,
                opacity: isMain ? 0.95 : 0.45,
                dashArray: isMain ? null : '6 8'
            }).addTo(layerRute);

            line.on('click', () => pilih(index));
            daftarGaris.push(line);

            const m = konversiMetrikRute(route);
            const kartu = document.createElement('div');
            kartu.className = 'kartu' + (isMain ? ' aktif' : '');
            kartu.innerHTML = `
                <div class="kartu-atas">
                    <div>
                        <b>Rute ${String.fromCharCode(65 + index)}</b>
                        <span class="tag ${isMain ? '' : 'alt'}">${isMain ? 'Rute Rekomendasi' : 'Alternatif'}</span>
                    </div>
                    <div class="skor">${m.waktuMenit} <small>menit</small></div>
                </div>
                <div class="meta">${m.jarakKm.toFixed(1)} km · Jalur Lalu Lintas Terbuka</div>
                <div class="chips">
                    <span class="chip">Pencahayaan Baik</span>
                    <span class="chip">Area Terpantau</span>
                </div>
            `;
            kartu.addEventListener('click', () => pilih(index));
            box.appendChild(kartu);
        });

        if (daftarGaris.length) {
            map.fitBounds(L.featureGroup(daftarGaris).getBounds().pad(0.12));
        }

        $('btnBandingkan').hidden = routes.length < 2;
        $('btnNavigasi').disabled = false;
    }

    async function ruteOSRM(a, b) {
        const url = 'https://router.project-osrm.org/route/v1/driving/' +
            a.lng + ',' + a.lat + ';' + b.lng + ',' + b.lat +
            '?overview=full&geometries=geojson&alternatives=true';

        const response = await fetch(url);
        if (!response.ok) throw new Error('Rute tidak ditemukan melalui OSRM.');
        const data = await response.json();
        if (!data.routes || !data.routes.length) throw new Error('Tidak ada jalur yang dapat dilewati.');
        return data.routes;
    }

    $('btnCari').addEventListener('click', async () => {
        setStatus('Mencari rute dan menganalisis keamanan...');
        $('btnCari').disabled = true;

        try {
            if (!asal) {
                if (inputDari.value.trim().toLowerCase() === 'lokasi saya') {
                    asal = await lokasiSaya();
                } else {
                    const res = await cariTempat(inputDari.value.trim());
                    if (!res.length) throw new Error('Lokasi asal tidak ditemukan.');
                    asal = { nama: inputDari.value, lat: +res[0].lat, lng: +res[0].lon };
                }
            }

            if (!tujuan) {
                const resT = await cariTempat(inputKe.value.trim());
                if (!resT.length) throw new Error('Lokasi tujuan tidak ditemukan.');
                tujuan = { nama: inputKe.value, lat: +resT[0].lat, lng: +resT[0].lon };
            }

            titikA = asal;
            titikB = tujuan;

            const routes = await ruteOSRM(titikA, titikB);
            ruteOSRMAktif = routes;
            renderRute(routes);
            setStatus('');
        } catch (err) {
            setStatus(err.message, true);
        } finally {
            $('btnCari').disabled = false;
        }
    });

    $('btnTukar').addEventListener('click', () => {
        [inputDari.value, inputKe.value] = [inputKe.value, inputDari.value];
        [asal, tujuan] = [tujuan, asal];
    });

    $('btnBandingkan').addEventListener('click', simpanDanBukaPerbandingan);

    $('btnNavigasi').addEventListener('click', () => {
        if (!titikA || !titikB || !daftarGaris.length) {
            setStatus('Cari dan pilih rute terlebih dahulu.', true);
            return;
        }
        modeNavigasi = true;
        const ruteAktif = daftarGaris[terpilihIdx];
        if (ruteAktif) {
            map.fitBounds(ruteAktif.getBounds().pad(0.08));
            ruteAktif.bringToFront();
            $('btnNavigasi').innerHTML = '<span>📍 Navigasi Sedang Berjalan</span>';
            setStatus('Navigasi aktif. Ikuti jalur biru yang disorot pada peta.');
            $('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
@endpush
