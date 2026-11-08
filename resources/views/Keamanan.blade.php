<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Skor Keamanan Rute - SafeRoad</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        :root {
            --biru-muda: #E3F2FD; --biru-sedang: #90CAF9; --biru-utama: #2196F3; --biru-tua: #0D47A1;
            --aman: #16a34a; --waspada: #f59e0b; --rawan: #ef4444;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #cfe3f5; color: var(--biru-tua); }
        .app { max-width: 480px; margin: 0 auto; min-height: 100vh; background: var(--biru-muda); padding-bottom: 84px; }

        .topbar { display: flex; align-items: center; gap: 12px; padding: 14px 16px; background: var(--biru-tua); color: #fff; }
        .topbar .back { width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,.15); display: grid; place-items: center; font-size: 20px; }
        .topbar .judul { flex: 1; }
        .topbar small { display: block; font-size: 10px; letter-spacing: 1px; color: var(--biru-sedang); }
        .topbar strong { font-size: 16px; }
        .topbar .perisai { width: 32px; height: 32px; border-radius: 10px; background: rgba(255,255,255,.15); display: grid; place-items: center; }

        .konten { padding: 16px; }

        /* Kartu skor */
        .skor-card { background: #fff; border: 1px solid var(--biru-sedang); border-radius: 16px; padding: 16px; margin-bottom: 16px; }
        .skor-atas { display: flex; align-items: center; gap: 16px; }
        .skor-ring { --warna: var(--aman); --persen: 0; width: 92px; height: 92px; border-radius: 50%; flex-shrink: 0;
            background: conic-gradient(var(--warna) calc(var(--persen) * 1%), #e2e8f0 0);
            display: grid; place-items: center; }
        .skor-ring > div { width: 72px; height: 72px; border-radius: 50%; background: #fff; display: grid; place-items: center; text-align: center; }
        .skor-ring b { font-size: 26px; line-height: 1; display: block; }
        .skor-ring span { font-size: 10px; color: #5b7aa5; display: block; margin-top: 2px; }
        .skor-info small { font-size: 10px; letter-spacing: 1px; color: #5b7aa5; }
        .badge { display: inline-block; margin: 4px 0 6px; padding: 4px 12px; border-radius: 999px; color: #fff; font-size: 13px; font-weight: bold; background: var(--aman); }
        .skor-info p { margin: 0; font-size: 12px; color: #475569; line-height: 1.4; }

        .faktor { margin-top: 14px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
        .faktor-baris { margin-bottom: 10px; }
        .faktor-label { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px; }
        .bar { height: 7px; border-radius: 99px; background: #e2e8f0; overflow: hidden; }
        .bar i { display: block; height: 100%; border-radius: 99px; background: var(--biru-utama); }

        .legenda-skor { display: flex; justify-content: space-between; font-size: 10px; color: #5b7aa5; margin-top: 12px; }
        .legenda-skor span { display: flex; align-items: center; gap: 4px; }
        .legenda-skor i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

        .baris-judul { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px; }
        .baris-judul h3 { margin: 0; font-size: 14px; }
        .baris-judul span { font-size: 11px; color: #5b7aa5; }

        .peta-wrap { position: relative; border-radius: 16px; overflow: hidden; border: 2px solid var(--biru-sedang); }
        #map { height: 250px; width: 100%; }
        .btn-lokasi { position: absolute; right: 10px; bottom: 28px; width: 38px; height: 38px; border-radius: 10px; background: #fff; border: 1px solid var(--biru-sedang); display: grid; place-items: center; font-size: 18px; cursor: pointer; z-index: 1000; box-shadow: 0 1px 5px rgba(0,0,0,.25); }
        .pin { width: 28px; height: 28px; border-radius: 50%; background: #fff; border: 2px solid; display: grid; place-items: center; font-size: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.3); }
        .tujuan-tip { background: var(--biru-tua); color: #fff; border: none; font-size: 11px; font-weight: bold; box-shadow: none; }
        .tujuan-tip::before { display: none; }

        .legenda-baris { display: flex; justify-content: space-between; padding: 8px 4px 14px; font-size: 11px; }
        .legenda-baris span { display: flex; align-items: center; gap: 5px; }

        .pos { position: relative; background: #e8f7ee; border: 1px solid #86d0a0; border-radius: 16px; padding: 12px 14px; margin-bottom: 14px; }
        .pos small { color: var(--aman); font-weight: bold; font-size: 10px; letter-spacing: 1px; }
        .pos .perisai-kecil { position: absolute; right: 14px; top: 12px; }
        .pos b { display: block; font-size: 16px; margin: 4px 0 6px; color: #14532d; }
        .pos .bawah { display: flex; justify-content: space-between; font-size: 12px; color: #4b6b57; }
        .pos a { color: var(--aman); font-weight: bold; text-decoration: none; }

        .catatan { font-size: 10px; text-align: center; color: #5b7aa5; margin-top: 4px; opacity: .8; }

        .nav { position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 480px; background: #fff; border-top: 1px solid var(--biru-sedang); display: flex; justify-content: space-around; padding: 8px 0; z-index: 1000; }
        .nav div, .nav a { text-align: center; font-size: 11px; color: #5b7aa5; padding: 4px 10px; border-radius: 12px; }
        .nav a { text-decoration: none; }
        .nav div.aktif, .nav a.aktif { background: var(--biru-muda); color: var(--biru-tua); font-weight: bold; }
        .nav div span, .nav a span { display: block; font-size: 18px; }
    </style>
</head>
<body>
<div class="app">
    <div class="topbar">
        <div class="back">&#8249;</div>
        <div class="judul">
            <small>SAFEROAD</small>
            <strong>Keamanan Rute Anda</strong>
        </div>
        <div class="perisai">&#128737;</div>
    </div>

    <div class="konten">
        <!-- Skor & tingkat keamanan (SR-13) -->
        <div class="skor-card">
            <div class="skor-atas">
                <div class="skor-ring" id="skorRing"><div><div><b id="skorAngka">0</b><span>dari 100</span></div></div></div>
                <div class="skor-info">
                    <small>TINGKAT KEAMANAN RUTE</small><br>
                    <span class="badge" id="skorBadge">-</span>
                    <p id="skorKet">-</p>
                </div>
            </div>
            <div class="faktor" id="faktorList"></div>
            <div class="legenda-skor">
                <span><i style="background:var(--aman)"></i>Aman (80-100)</span>
                <span><i style="background:var(--waspada)"></i>Waspada (60-79)</span>
                <span><i style="background:var(--rawan)"></i>Rawan (&lt;60)</span>
            </div>
        </div>

        <div class="baris-judul">
            <h3>Di sekitar rute Anda</h3>
            <span>Ketintang, Surabaya</span>
        </div>

        <div class="peta-wrap">
            <div id="map"></div>
            <div class="btn-lokasi" id="btnLokasi" title="Lokasi saya">&#9678;</div>
        </div>

        <div class="legenda-baris">
            <span>&#128249; CCTV</span>
            <span>&#128161; Penerangan</span>
            <span>&#128737; Pos keamanan</span>
            <span>&#11093; Titik aman</span>
            <span>&#128308; Titik rawan</span>
        </div>

        <div id="mapStatus" class="catatan" role="status" aria-live="polite">Memuat data peta...</div>

        <div class="pos">
            <small>POS AMAN TERDEKAT</small>
            <span class="perisai-kecil">&#128737;</span>
            <b id="posNama">-</b>
            <div class="bawah">
                <span id="posKet">-</span>
                <a id="posLink" href="#" target="_blank" rel="noopener">Petunjuk &#8599;</a>
            </div>
        </div>

        <div class="catatan">Skor adalah indikator, bukan jaminan keamanan.</div>
    </div>

    <div class="nav">
        <div><span>&#128739;</span>Rute</div>
        <a class="aktif" href="{{ route('keamanan') }}"><span>&#128737;</span>Keamanan</a>
        <div><span>&#128221;</span>Laporan</div>
        <div><span>&#128105;</span>Perempuan</div>
        <div><span>&#128100;</span>Akun</div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // TODO SR-13: skor dan faktor keamanan tetap menggunakan data dummy.
    const skorRute = {
        skor: 82,
        faktor: [
            { nama: 'Cakupan CCTV',       nilai: 88 },
            { nama: 'Penerangan Jalan',   nilai: 80 },
            { nama: 'Kedekatan Pos Aman', nilai: 85 },
            { nama: 'Laporan Kejadian',   nilai: 72 }
        ]
    };

    // ----- Skor & tingkat keamanan -----
    function tingkat(skor) {
        if (skor >= 80) return { label: 'Aman',    warna: 'var(--aman)',    hex: '#16a34a', ket: 'Rute ini tergolong aman untuk dilalui.' };
        if (skor >= 60) return { label: 'Waspada', warna: 'var(--waspada)', hex: '#f59e0b', ket: 'Rute ini cukup aman, tetap waspada terutama saat malam.' };
        return                 { label: 'Rawan',   warna: 'var(--rawan)',   hex: '#ef4444', ket: 'Rute ini rawan. Pertimbangkan rute lain atau jangan sendirian.' };
    }
    const t = tingkat(skorRute.skor);
    const ring = document.getElementById('skorRing');
    ring.style.setProperty('--warna', t.warna);
    ring.style.setProperty('--persen', skorRute.skor);
    document.getElementById('skorAngka').textContent = skorRute.skor;
    const badge = document.getElementById('skorBadge');
    badge.textContent = t.label;
    badge.style.background = t.warna;
    document.getElementById('skorKet').textContent = t.ket;
    document.getElementById('faktorList').innerHTML = skorRute.faktor.map(f =>
        '<div class="faktor-baris"><div class="faktor-label"><span>' + f.nama + '</span><b>' + f.nilai +
        '</b></div><div class="bar"><i style="width:' + f.nilai + '%"></i></div></div>'
    ).join('');

    // ----- Peta fasilitas dan titik rawan SR-15 -----
    const mapOverviewUrl = "{{ url('/api/map/overview') }}";
    const markerColors = {
        cctv: '#2196F3',
        penerangan: '#f59e0b',
        pos_keamanan: '#16a34a',
        titik_aman: '#0D47A1',
        hazard: '#ef4444',
        facility: '#2196F3'
    };
    const markerSymbols = {
        cctv: '&#128249;',
        penerangan: '&#128161;',
        pos_keamanan: '&#128737;',
        titik_aman: '&#11093;',
        hazard: '&#128308;',
        facility: '&#128205;'
    };
    const mapStatus = document.getElementById('mapStatus');
    const markerLayer = L.layerGroup();
    const map = L.map('map', { zoomControl: false, minZoom: 14 }).setView([-7.315, 112.725], 16);
    let latestRequestId = 0;
    let mapReloadTimer = null;
    let mapFeatures = [];
    let currentPosition = null;
    let currentPositionMarker = null;

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.control.scale({ imperial: false, position: 'bottomleft' }).addTo(map);
    markerLayer.addTo(map);

    function setMapStatus(message) {
        mapStatus.textContent = message;
        mapStatus.hidden = message === '';
    }

    function featureKind(properties) {
        if (properties.kind === 'facility' || properties.kind === 'hazard') {
            return properties.kind;
        }

        return Object.prototype.hasOwnProperty.call(properties, 'category') ? 'hazard' : 'facility';
    }

    function pinIcon(kind, facilityType) {
        const markerType = kind === 'hazard' ? 'hazard' : (facilityType || 'facility');
        const color = markerColors[markerType] || markerColors.facility;
        const symbol = markerSymbols[markerType] || markerSymbols.facility;

        return L.divIcon({
            className: '',
            html: '<div class="pin" style="border-color:' + color + ';color:' + color + '">' + symbol + '</div>',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
    }

    function createPopup(feature, kind) {
        const properties = feature.properties || {};
        const popup = document.createElement('div');
        const heading = document.createElement('strong');
        heading.textContent = kind === 'hazard'
            ? (properties.category || 'Titik rawan')
            : (properties.name || 'Fasilitas keamanan');
        popup.appendChild(heading);

        function appendPopupText(label, value) {
            if (value === null || value === undefined || String(value).trim() === '') {
                return;
            }

            const paragraph = document.createElement('p');
            paragraph.textContent = label + ': ' + value;
            popup.appendChild(paragraph);
        }

        if (kind === 'hazard') {
            const eventTime = properties.event_time
                ? new Date(properties.event_time).toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' })
                : null;
            appendPopupText('Waktu kejadian', eventTime);
            appendPopupText('Keterangan', properties.description);
            appendPopupText('Alamat', properties.address);
        } else {
            appendPopupText('Keterangan', properties.description);
            appendPopupText('Status', properties.status);
        }

        return popup;
    }

    function renderFeatures(features) {
        markerLayer.clearLayers();

        features.forEach(feature => {
            const coordinates = feature.geometry && feature.geometry.coordinates;

            if (feature.geometry?.type !== 'Point' || !Array.isArray(coordinates) || coordinates.length < 2) {
                return;
            }

            const longitude = Number(coordinates[0]);
            const latitude = Number(coordinates[1]);

            if (!Number.isFinite(longitude) || !Number.isFinite(latitude)) {
                return;
            }

            const properties = feature.properties || {};
            const kind = featureKind(properties);
            const markerType = kind === 'hazard' ? 'hazard' : properties.type;
            const needsMaintenance = kind === 'facility' && properties.status === 'perlu_perawatan';
            const marker = L.marker([latitude, longitude], {
                icon: pinIcon(kind, markerType),
                opacity: needsMaintenance ? 0.45 : 1
            });

            marker.bindPopup(createPopup(feature, kind));
            marker.addTo(markerLayer);
        });
    }

    function jarakMeter(firstPoint, secondPoint) {
        const radiusMeter = 6371000;
        const toRadians = value => value * Math.PI / 180;
        const latitudeDifference = toRadians(secondPoint.lat - firstPoint.lat);
        const longitudeDifference = toRadians(secondPoint.lng - firstPoint.lng);
        const haversine = Math.sin(latitudeDifference / 2) ** 2
            + Math.cos(toRadians(firstPoint.lat)) * Math.cos(toRadians(secondPoint.lat))
            * Math.sin(longitudeDifference / 2) ** 2;

        return 2 * radiusMeter * Math.asin(Math.sqrt(haversine));
    }

    function updateNearestSafeSpot() {
        const nameElement = document.getElementById('posNama');
        const distanceElement = document.getElementById('posKet');
        const directionsLink = document.getElementById('posLink');

        if (!currentPosition) {
            nameElement.textContent = 'Lokasi Anda belum tersedia';
            distanceElement.textContent = 'Gunakan tombol lokasi untuk menghitung jarak.';
            directionsLink.href = '#';
            return;
        }

        const safeSpots = mapFeatures
            .filter(feature => {
                const properties = feature.properties || {};
                return featureKind(properties) === 'facility'
                    && ['pos_keamanan', 'titik_aman'].includes(properties.type)
                    && properties.status === 'aktif';
            })
            .map(feature => {
                const [longitude, latitude] = feature.geometry.coordinates;

                return {
                    name: feature.properties.name || 'Titik aman',
                    latitude: Number(latitude),
                    longitude: Number(longitude)
                };
            })
            .map(safeSpot => ({
                ...safeSpot,
                distance: jarakMeter(currentPosition, { lat: safeSpot.latitude, lng: safeSpot.longitude })
            }))
            .sort((firstSpot, secondSpot) => firstSpot.distance - secondSpot.distance);

        if (safeSpots.length === 0) {
            nameElement.textContent = 'Belum ada titik aman aktif di area peta';
            distanceElement.textContent = 'Geser peta atau periksa kembali nanti.';
            directionsLink.href = '#';
            return;
        }

        const nearestSpot = safeSpots[0];
        const walkingMinutes = Math.max(1, Math.ceil(nearestSpot.distance / 80));
        nameElement.textContent = nearestSpot.name + ' (' + Math.round(nearestSpot.distance) + ' m)';
        distanceElement.textContent = 'Perkiraan ' + walkingMinutes + ' menit berjalan';
        directionsLink.href = 'https://www.google.com/maps/dir/?api=1&destination='
            + nearestSpot.latitude + ',' + nearestSpot.longitude + '&travelmode=walking';
    }

    async function loadMapData() {
        const requestId = ++latestRequestId;
        const bounds = map.getBounds();
        const boundingBox = [
            bounds.getWest(),
            bounds.getSouth(),
            bounds.getEast(),
            bounds.getNorth()
        ].join(',');
        const endpoint = new URL(mapOverviewUrl, window.location.origin);
        endpoint.searchParams.set('bbox', boundingBox);
        setMapStatus('Memuat data peta...');

        try {
            const response = await fetch(endpoint.toString(), {
                headers: { Accept: 'application/json' }
            });

            if (!response.ok) {
                let errorPayload = null;

                try {
                    errorPayload = await response.json();
                } catch (error) {
                    errorPayload = null;
                }

                const firstValidationMessage = response.status === 422
                    ? Object.values(errorPayload?.errors || {})
                        .flat()
                        .find(message => typeof message === 'string')
                    : null;
                const backendMessage = firstValidationMessage
                    || (typeof errorPayload?.message === 'string' ? errorPayload.message : null);

                if (backendMessage) {
                    const backendError = new Error(backendMessage);
                    backendError.name = 'BackendResponseError';
                    throw backendError;
                }

                throw new Error('Respons backend tidak menyediakan pesan yang dapat ditampilkan.');
            }

            const geoJson = await response.json();

            if (requestId !== latestRequestId) {
                return;
            }

            if (geoJson.type !== 'FeatureCollection' || !Array.isArray(geoJson.features)) {
                throw new Error('Format data peta tidak valid.');
            }

            mapFeatures = geoJson.features.filter(feature => feature && feature.geometry && feature.properties);
            renderFeatures(mapFeatures);
            updateNearestSafeSpot();
            const statusMessage = mapFeatures.length >= 500
                ? 'Titik terlalu banyak untuk ditampilkan semua. Perbesar peta untuk melihat lebih rinci.'
                : mapFeatures.length === 0
                    ? 'Tidak ada fasilitas atau titik rawan di area peta ini.'
                    : '';
            setMapStatus(statusMessage);
        } catch (error) {
            if (requestId !== latestRequestId) {
                return;
            }

            mapFeatures = [];
            markerLayer.clearLayers();
            updateNearestSafeSpot();
            const statusMessage = error instanceof Error && error.name === 'BackendResponseError'
                ? error.message
                : 'Gagal memuat data peta. Periksa koneksi lalu coba lagi.';
            setMapStatus(statusMessage);
        }
    }

    function scheduleMapReload() {
        latestRequestId++;
        window.clearTimeout(mapReloadTimer);
        mapReloadTimer = window.setTimeout(loadMapData, 400);
    }

    map.on('moveend', scheduleMapReload);
    loadMapData();

    if (navigator.geolocation) {
        document.getElementById('btnLokasi').addEventListener('click', () => {
            setMapStatus('Mengambil lokasi Anda...');

            navigator.geolocation.getCurrentPosition(position => {
                currentPosition = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                if (currentPositionMarker) {
                    currentPositionMarker.setLatLng([currentPosition.lat, currentPosition.lng]);
                } else {
                    currentPositionMarker = L.circleMarker([currentPosition.lat, currentPosition.lng], {
                        radius: 8,
                        color: '#fff',
                        weight: 3,
                        fillColor: '#0D47A1',
                        fillOpacity: 1
                    }).addTo(map).bindPopup('Lokasi Anda');
                }

                map.setView([currentPosition.lat, currentPosition.lng], 17);
                updateNearestSafeSpot();
                window.clearTimeout(mapReloadTimer);
                loadMapData();
            }, () => {
                setMapStatus('Lokasi tidak dapat diakses. Periksa izin lokasi pada browser.');
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 30000
            });
        });
    } else {
        document.getElementById('btnLokasi').addEventListener('click', () => {
            setMapStatus('Browser ini tidak mendukung akses lokasi.');
        });
    }
</script>
</body>
</html>
