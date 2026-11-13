<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bandingkan rute - SafeRoad</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        :root {
            --biru-muda: #E3F2FD;
            --biru-sedang: #90CAF9;
            --biru-utama: #2196F3;
            --biru-tua: #0D47A1;
            --hijau: #16a34a;
            --merah: #dc2626;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #cfe3f5; color: var(--biru-tua); overflow-x: hidden; }
        .app { max-width: 480px; min-height: 100vh; margin: 0 auto; background: var(--biru-muda); padding-bottom: 32px; overflow: hidden; }
        .topbar { display: flex; align-items: center; gap: 12px; padding: 14px 16px; background: var(--biru-tua); color: #fff; }
        .topbar .back { display: grid; place-items: center; width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,.15); color: #fff; text-decoration: none; font-size: 20px; }
        .topbar .judul { flex: 1; }
        .topbar small { display: block; color: var(--biru-sedang); font-size: 10px; letter-spacing: 1px; }
        .topbar strong { font-size: 16px; }
        .konten { padding: 16px; min-width: 0; }
        h1 { margin: 0 0 12px; font-size: 20px; }
        .tautan-kembali { display: inline-block; margin-bottom: 14px; color: var(--biru-tua); font-size: 13px; }
        .pesan { padding: 10px 12px; margin: 0 0 10px; border-radius: 10px; background: #fff; font-size: 13px; }
        .pesan.error { color: #991b1b; border: 1px solid #fecaca; }
        .peta-wrap { border: 2px solid var(--biru-sedang); border-radius: 16px; overflow: hidden; margin-bottom: 14px; }
        #map { width: 100%; height: 270px; }
        h2 { margin: 14px 0 8px; font-size: 15px; }
        .perbandingan-wrap { max-width: 100%; overflow-x: auto; overscroll-behavior-x: contain; margin: 10px 0; border: 1px solid var(--biru-sedang); border-radius: 12px; background: #fff; }
        .perbandingan { width: 100%; min-width: 370px; border-collapse: collapse; font-size: 11px; }
        .perbandingan th, .perbandingan td { min-width: 82px; padding: 8px 6px; border-bottom: 1px solid var(--biru-muda); text-align: center; vertical-align: middle; }
        .perbandingan th:first-child, .perbandingan td:first-child { min-width: 96px; text-align: left; }
        .perbandingan th { background: var(--biru-muda); }
        .pilih-kolom { width: 100%; padding: 6px 3px; border: 0; border-radius: 6px; color: var(--biru-tua); background: transparent; font: inherit; font-weight: bold; cursor: pointer; }
        .perbandingan [data-route-id].terpilih { background: #d8ecff; }
        .perbandingan th.terpilih .pilih-kolom { background: #c2e2ff; }
        button:focus-visible, a:focus-visible { outline: 3px solid #f59e0b; outline-offset: 2px; }
        .tag { display: inline-block; margin: 2px; padding: 3px 6px; border-radius: 999px; background: var(--hijau); color: #fff; font-size: 9px; font-weight: bold; white-space: nowrap; }
        .tag.alt { background: var(--biru-utama); }
        .level-badge { display: inline-block; padding: 3px 7px; border-radius: 999px; font-size: 10px; font-weight: bold; white-space: nowrap; }
        .level-aman { background: var(--hijau); color: #fff; }
        .level-sedang { background: #facc15; color: #713f12; }
        .level-rawan { background: var(--merah); color: #fff; }
        .level-data-belum-cukup { background: #e5e7eb; color: #374151; }
        .disclaimer, .bantuan { margin: 8px 0; color: #5b7aa5; font-size: 11px; line-height: 1.45; }
        .btn { padding: 10px 14px; border: 0; border-radius: 10px; background: var(--biru-tua); color: #fff; font-size: 13px; font-weight: bold; cursor: pointer; }
        .btn:disabled { opacity: .6; cursor: wait; }
        .kartu-rute { width: 100%; margin: 5px 0; padding: 10px; border: 2px solid var(--biru-sedang); border-radius: 12px; background: #fff; color: var(--biru-tua); text-align: left; cursor: pointer; }
        .kartu-rute.terpilih { border-color: var(--biru-utama); box-shadow: 0 2px 10px rgba(33,150,243,.25); }
        .faktor { margin: 8px 0; padding: 10px; border: 1px solid var(--biru-sedang); border-radius: 10px; background: #fff; }
        .faktor.tidak-tersedia { opacity: .55; }
        .faktor-atas { display: flex; justify-content: space-between; gap: 8px; font-size: 12px; }
        .bar-latar { height: 7px; margin-top: 7px; border-radius: 999px; background: var(--biru-muda); overflow: hidden; }
        .bar-nilai { height: 100%; border-radius: inherit; background: var(--biru-utama); }
        .faktor-note { margin-top: 5px; color: #5b7aa5; font-size: 11px; }
        [hidden] { display: none !important; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body>
<div class="app">
    <header class="topbar">
        <a class="back" href="{{ url('/rute') }}" aria-label="Kembali ke pencarian">&#8249;</a>
        <div class="judul"><small>SAFEROAD</small><strong>Bandingkan rute</strong></div>
    </header>
    <main class="konten">
        <h1>Bandingkan rute</h1>
        <a class="tautan-kembali" href="{{ url('/rute') }}">Kembali ke pencarian</a>
        <section id="tanpa-data" hidden>
            <p class="pesan">Belum ada rute untuk dibandingkan.</p>
            <a class="btn" href="{{ url('/rute') }}" style="display:inline-block;text-decoration:none">Cari rute</a>
        </section>
        <section id="konten-rute" hidden>
            <p id="pesan-info" class="pesan" hidden></p>
            <div class="peta-wrap"><div id="map" aria-label="Peta perbandingan rute"></div></div>
            <p id="pesan-satu" class="pesan" hidden>Hanya ada satu rute, tidak ada yang perlu dibandingkan.</p>
            <section id="bagian-perbandingan" hidden>
                <h2>Perbandingan rute</h2>
                <p class="bantuan">Rute tercepat belum tentu teraman.</p>
                <div class="perbandingan-wrap" tabindex="0" aria-label="Tabel perbandingan rute, dapat digulir mendatar">
                    <table class="perbandingan" id="tabel-perbandingan"></table>
                </div>
                <button type="button" class="btn" id="coba-lagi" hidden>Coba lagi</button>
                <p class="disclaimer" id="disclaimer"></p>
            </section>
            <section id="bagian-rincian">
                <h2 id="judul-rincian">Rincian keamanan</h2>
                <div id="kartu-rute"></div>
                <div id="daftar-faktor"></div>
            </section>
        </section>
    </main>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const URL_SKOR = "{{ url('/api/routes/score') }}";
    const KUNCI_DATA = 'saferoad.perbandingan';
    const KUNCI_PILIHAN = 'saferoad.rutePilihan';
    const DISCLAIMER_FALLBACK = 'Skor adalah indikator, bukan jaminan keamanan.';
    const WARNA_RUTE = ['#0D47A1', '#e67e22', '#7b1fa2'];
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
            throw new Error('Koneksi gagal. Skor belum tersedia.');
        }
        let result = {};
        try { result = await response.json(); } catch (error) { result = {}; }
        if (!response.ok) {
            if (response.status === 422) {
                const errors = result.errors && Object.values(result.errors).flat();
                throw new Error((errors && errors[0]) || result.message || 'Data rute tidak dapat dihitung.');
            }
            if (response.status === 429) throw new Error('Permintaan skor terlalu sering. Skor belum tersedia.');
            if (response.status === 404) throw new Error('Layanan skor belum tersedia. Skor belum tersedia.');
            if (response.status >= 500) throw new Error('Layanan skor sedang bermasalah. Skor belum tersedia.');
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
        L.circleMarker([dataRute.origin.lat, dataRute.origin.lng], { radius: 8, color: '#fff', weight: 3, fillColor: '#0D47A1', fillOpacity: 1 }).addTo(map).bindPopup(originName);
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
        headRow.appendChild(buatElemen('th', '', ''));
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
            ['Jarak (km)', (route, index) => nilaiMetrik(route, 'jarak', index, markers)],
            ['Waktu (menit)', (route, index) => nilaiMetrik(route, 'waktu', index, markers)],
            ['Level keamanan', (route, index) => nilaiLevel(route, index, markers)],
            ['Skor', (route, index) => nilaiSkor(route)]
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
        const value = type === 'jarak' ? metric.jarakKm.toFixed(1) : String(metric.waktuMenit);
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
            container.appendChild(buatElemen('p', 'bantuan', statusSkor === 'pending' ? 'Rincian keamanan sedang dihitung.' : 'Rincian faktor belum tersedia.'));
            return;
        }
        factors.forEach(factor => {
            const available = factor.available === true;
            const wrapper = buatElemen('div', 'faktor' + (available ? '' : ' tidak-tersedia'));
            const top = buatElemen('div', 'faktor-atas');
            top.append(buatElemen('strong', '', factor.label || factor.key || 'Faktor'), buatElemen('span', '', typeof factor.score === 'number' ? String(Math.round(factor.score)) : '-'));
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
        try { sessionStorage.setItem(KUNCI_PILIHAN, JSON.stringify({ id: dataRute.routes[index].id, savedAt: new Date().toISOString() })); } catch (error) { /* Penyimpanan pilihan bersifat tambahan. */ }
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
            $('pesan-info').textContent = error.message || 'Skor belum tersedia.';
            $('pesan-info').className = 'pesan error';
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
        try { sessionStorage.setItem(KUNCI_PILIHAN, JSON.stringify({ id: dataRute.routes[indeksTerpilih].id, savedAt: new Date().toISOString() })); } catch (error) { /* Penyimpanan pilihan bersifat tambahan. */ }
        if (dataRute.routes.length > 1) renderSemua();
        else renderFaktor();
        $('coba-lagi').addEventListener('click', muatSkor);
        muatSkor();
    }

    mulai();
</script>
</body>
</html>
