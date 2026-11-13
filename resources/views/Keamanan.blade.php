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
        .nav div { text-align: center; font-size: 11px; color: #5b7aa5; padding: 4px 10px; border-radius: 12px; }
        .nav div.aktif { background: var(--biru-muda); color: var(--biru-tua); font-weight: bold; }
        .nav div span { display: block; font-size: 18px; }
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
            <span>&#128161; Lampu Jalan</span>
            <span>&#128737; Pos Aman</span>
        </div>

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
        <div class="aktif"><span>&#128737;</span>Keamanan</div>
        <div><span>&#128221;</span>Laporan</div>
        <div><span>&#128105;</span>Perempuan</div>
        <div><span>&#128100;</span>Akun</div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ===== DATA DUMMY - nanti diganti data dari endpoint Back end =====
    const skorRute = {
        skor: 82,
        faktor: [
            { nama: 'Cakupan CCTV',       nilai: 88 },
            { nama: 'Penerangan Jalan',   nilai: 80 },
            { nama: 'Kedekatan Pos Aman', nilai: 85 },
            { nama: 'Laporan Kejadian',   nilai: 72 }
        ]
    };
    const lokasiUser = { lat: -7.3160, lng: 112.7290 };
    const tujuan     = { lat: -7.3125, lng: 112.7255, nama: 'Telkom University' };

    const titik = [
        { nama: 'CCTV Simpang Ketintang', jenis: 'cctv',  lat: -7.3152, lng: 112.7280 },
        { nama: 'CCTV Jalan Jetis',       jenis: 'cctv',  lat: -7.3138, lng: 112.7268 },
        { nama: 'Lampu Jalan Jetis',      jenis: 'lampu', lat: -7.3145, lng: 112.7275 },
        { nama: 'Lampu Jalan Kampus',     jenis: 'lampu', lat: -7.3132, lng: 112.7262 },
        { nama: 'Pos Jaga Jetis Kulon',   jenis: 'pos',   lat: -7.3147, lng: 112.7291 },
        { nama: 'Pos Satpam Kampus',      jenis: 'pos',   lat: -7.3126, lng: 112.7258 }
    ];
    const rutePilihan = [
        [lokasiUser.lat, lokasiUser.lng], [-7.3160, 112.7275], [-7.3145, 112.7275],
        [-7.3145, 112.7262], [-7.3125, 112.7262], [tujuan.lat, tujuan.lng]
    ];
    // ==================================================================

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

    // ----- Peta -----
    const warna = { cctv: '#2196F3', lampu: '#f59e0b', pos: '#16a34a' };
    const emoji = { cctv: '&#128249;', lampu: '&#128161;', pos: '&#128737;' };

    const map = L.map('map', { zoomControl: false });
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.control.scale({ imperial: false, position: 'bottomleft' }).addTo(map);

    function pinIcon(jenis) {
        return L.divIcon({
            className: '',
            html: '<div class="pin" style="border-color:' + warna[jenis] + '">' + emoji[jenis] + '</div>',
            iconSize: [28, 28], iconAnchor: [14, 14]
        });
    }

    // Warna garis rute mengikuti tingkat keamanan
    L.polyline(rutePilihan, { color: t.hex, weight: 6, opacity: .9 }).addTo(map);

    titik.forEach(p => {
        L.marker([p.lat, p.lng], { icon: pinIcon(p.jenis) }).addTo(map).bindPopup('<b>' + p.nama + '</b>');
    });

    L.marker([tujuan.lat, tujuan.lng], {
        icon: L.divIcon({ className: '', html: '<div class="pin" style="border-color:#0D47A1;background:#0D47A1;color:#fff">&#128205;</div>', iconSize: [28, 28], iconAnchor: [14, 14] })
    }).addTo(map).bindTooltip(tujuan.nama, { permanent: true, direction: 'top', offset: [0, -14], className: 'tujuan-tip' });

    L.circleMarker([lokasiUser.lat, lokasiUser.lng], {
        radius: 8, color: '#fff', weight: 3, fillColor: '#0D47A1', fillOpacity: 1
    }).addTo(map).bindPopup('Lokasi Anda');

    map.fitBounds(L.latLngBounds(rutePilihan).pad(0.25));
    document.getElementById('btnLokasi').addEventListener('click', () => map.setView([lokasiUser.lat, lokasiUser.lng], 17));

    // ----- Pos aman terdekat (haversine) -----
    function jarakMeter(a, b) {
        const R = 6371000, rad = x => x * Math.PI / 180;
        const dLat = rad(b.lat - a.lat), dLng = rad(b.lng - a.lng);
        const h = Math.sin(dLat / 2) ** 2 + Math.cos(rad(a.lat)) * Math.cos(rad(b.lat)) * Math.sin(dLng / 2) ** 2;
        return 2 * R * Math.asin(Math.sqrt(h));
    }
    const posList = titik.filter(p => p.jenis === 'pos')
        .map(p => ({ ...p, jarak: jarakMeter(lokasiUser, p) }))
        .sort((a, b) => a.jarak - b.jarak);
    if (posList.length) {
        const p = posList[0];
        const menit = Math.max(1, Math.ceil(p.jarak / 80));
        document.getElementById('posNama').textContent = p.nama + ' (' + Math.round(p.jarak) + ' m)';
        document.getElementById('posKet').textContent = 'Petugas siaga \u00B7 \u00B1' + menit + ' menit berjalan';
        document.getElementById('posLink').href = 'https://www.google.com/maps/dir/?api=1&destination=' + p.lat + ',' + p.lng + '&travelmode=walking';
    }
</script>
</body>
</html>
