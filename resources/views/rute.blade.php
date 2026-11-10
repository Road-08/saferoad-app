<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Temukan Rute Aman - SafeRoad</title>

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <style>
        :root {
            --biru-muda: #E3F2FD;
            --biru-sedang: #90CAF9;
            --biru-utama: #2196F3;
            --biru-tua: #0D47A1;
            --hijau: #16a34a;
            --merah: #dc2626;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #cfe3f5;
            color: var(--biru-tua);
        }

        .app {
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            background: var(--biru-muda);
            padding-bottom: 84px;
        }

        /* =========================
           TOP BAR
        ========================= */

        .topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: var(--biru-tua);
            color: #fff;
        }

        .topbar .back {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .15);
            display: grid;
            place-items: center;
            font-size: 20px;
        }

        .topbar .judul {
            flex: 1;
        }

        .topbar small {
            display: block;
            font-size: 10px;
            letter-spacing: 1px;
            color: var(--biru-sedang);
        }

        .topbar strong {
            font-size: 16px;
        }

        .topbar .perisai {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .15);
            display: grid;
            place-items: center;
        }

        /* =========================
           KONTEN
        ========================= */

        .konten {
            padding: 16px;
        }

        /* =========================
           FORM RUTE
        ========================= */

        .form-rute {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid var(--biru-sedang);
            border-radius: 16px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .form-rute .kolom {
            flex: 1;
            min-width: 0;
        }

        .baris-input {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 0;
        }

        .baris-input + .baris-input {
            border-top: 1px solid var(--biru-muda);
        }

        .baris-input label {
            font-size: 12px;
            color: #5b7aa5;
            width: 38px;
        }

        .baris-input input {
            flex: 1;
            min-width: 0;
            border: none;
            outline: none;
            font-size: 14px;
            color: var(--biru-tua);
            background: transparent;
        }

        /* Tombol tukar asal tujuan */

        .btn-tukar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--biru-sedang);
            background: var(--biru-muda);
            cursor: pointer;
            font-size: 16px;
        }

        /* =========================
           SARAN PENCARIAN
        ========================= */

        .saran {
            position: absolute;
            left: 0;
            right: 0;
            top: 100%;
            background: #fff;
            border: 1px solid var(--biru-sedang);
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .18);
            z-index: 1500;
            overflow: hidden;
        }

        .saran:empty {
            display: none;
        }

        .item-saran {
            padding: 9px 12px;
            font-size: 12px;
            cursor: pointer;
            border-bottom: 1px solid var(--biru-muda);
        }

        .item-saran:last-child {
            border-bottom: none;
        }

        .item-saran:hover {
            background: var(--biru-muda);
        }

        /* =========================
           TOMBOL CARI
        ========================= */

        .btn-cari {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 12px;
            background: var(--biru-tua);
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 8px;
        }

        .btn-cari:disabled {
            opacity: .6;
            cursor: wait;
        }

        /* =========================
           STATUS
        ========================= */

        .status {
            min-height: 18px;
            font-size: 12px;
            text-align: center;
            color: #5b7aa5;
            margin-bottom: 8px;
        }

        .status.error {
            color: var(--merah);
        }

        /* =========================
           PETA
        ========================= */

        .peta-wrap {
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid var(--biru-sedang);
            margin-bottom: 14px;
        }

        #map {
            height: 250px;
            width: 100%;
        }

        /* =========================
           JUDUL PILIHAN RUTE
        ========================= */

        .baris-judul {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 8px;
        }

        .baris-judul h3 {
            margin: 0;
            font-size: 14px;
        }

        .baris-judul span {
            font-size: 11px;
            color: #5b7aa5;
        }

        /* =========================
           KARTU RUTE
        ========================= */

        .kartu {
            background: #fff;
            border: 2px solid var(--biru-sedang);
            border-radius: 16px;
            padding: 12px 14px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .kartu.aktif {
            border-color: var(--biru-utama);
            box-shadow: 0 2px 10px rgba(33, 150, 243, .25);
        }

        .kartu-atas {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
        }

        .kartu-atas > div:first-child {
            display: flex;
            flex: 1;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px;
            min-width: 0;
        }

        .kartu-atas b {
            font-size: 15px;
        }

        .tag {
            font-size: 10px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 999px;
            background: var(--hijau);
            color: #fff;
            margin-left: 0;
        }

        .tag.alt {
            background: var(--biru-sedang);
            color: var(--biru-tua);
        }

        .skor {
            font-size: 22px;
            font-weight: bold;
            max-width: 48%;
            text-align: right;
            overflow-wrap: anywhere;
        }

        .skor small {
            font-size: 11px;
            color: #5b7aa5;
            font-weight: normal;
        }

        .meta {
            font-size: 12px;
            color: #5b7aa5;
            margin: 4px 0 8px;
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .chip {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 999px;
            background: var(--biru-muda);
            border: 1px solid var(--biru-sedang);
            color: var(--biru-tua);
        }

        .level-badge {
            display: inline-block;
            margin-left: 6px;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: bold;
            vertical-align: middle;
        }

        .level-aman { background: var(--hijau); color: #fff; }
        .level-sedang { background: #facc15; color: #713f12; }
        .level-rawan { background: var(--merah); color: #fff; }
        .level-data-belum-cukup { background: #e5e7eb; color: #374151; }

        .perbandingan-wrap {
            overflow-x: auto;
            margin: 12px 0;
            border: 1px solid var(--biru-sedang);
            border-radius: 12px;
            background: #fff;
        }

        .perbandingan {
            width: 100%;
            min-width: 350px;
            border-collapse: collapse;
            font-size: 11px;
        }

        .perbandingan th, .perbandingan td {
            padding: 8px 6px;
            border-bottom: 1px solid var(--biru-muda);
            text-align: center;
        }

        .perbandingan th:first-child, .perbandingan td:first-child {
            text-align: left;
        }

        .perbandingan th { background: var(--biru-muda); }
        .perbandingan [data-indeks-rute].terpilih { background: #d8ecff; }
        .perbandingan tr:last-child td { border-bottom: 0; }

        .disclaimer {
            margin: 8px 0;
            font-size: 11px;
            color: #5b7aa5;
        }

        /* =========================
           TOMBOL NAVIGASI
        ========================= */

        .btn-navigasi {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 14px;
            background: var(--biru-utama);
            color: #fff;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(33, 150, 243, .4);
            margin-top: 4px;
        }

        .btn-navigasi:disabled {
            opacity: .5;
            box-shadow: none;
            cursor: not-allowed;
        }

        .catatan {
            font-size: 11px;
            text-align: center;
            color: #5b7aa5;
            margin-top: 10px;
        }

        /* =========================
           BOTTOM NAVIGATION
        ========================= */

        .nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-top: 1px solid var(--biru-sedang);
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            z-index: 1000;
        }

        .nav div {
            text-align: center;
            font-size: 11px;
            color: #5b7aa5;
            padding: 4px 10px;
            border-radius: 12px;
        }

        .nav div.aktif {
            background: var(--biru-muda);
            color: var(--biru-tua);
            font-weight: bold;
        }

        .nav div span {
            display: block;
            font-size: 18px;
        }
    </style>
</head>

<body>

<div class="app">

    <!-- =========================
         HEADER
    ========================== -->

    <div class="topbar">

        <div class="back">
            &#8249;
        </div>

        <div class="judul">
            <small>SAFEROAD</small>
            <strong>Temukan rute aman</strong>
        </div>

        <div class="perisai">
            &#128737;
        </div>

    </div>


    <!-- =========================
         KONTEN UTAMA
    ========================== -->

    <div class="konten">

        <!-- FORM ASAL DAN TUJUAN -->

        <div class="form-rute">

            <div class="kolom">

                <!-- ASAL -->

                <div class="baris-input">

                    <label>Dari</label>

                    <input
                        type="text"
                        id="inputDari"
                        value="Lokasi Saya"
                        autocomplete="off"
                    >

                    <div
                        class="saran"
                        id="saranDari"
                    ></div>

                </div>


                <!-- TUJUAN -->

                <div class="baris-input">

                    <label>Ke</label>

                    <input
                        type="text"
                        id="inputKe"
                        placeholder="Cari tujuan, mis. Telkom University Surabaya"
                        autocomplete="off"
                    >

                    <div
                        class="saran"
                        id="saranKe"
                    ></div>

                </div>

            </div>


            <!-- TOMBOL TUKAR -->

            <button
                class="btn-tukar"
                id="btnTukar"
                title="Tukar asal & tujuan"
            >
                &#8645;
            </button>

        </div>


        <!-- =========================
             TOMBOL CARI RUTE
        ========================== -->

        <button
            class="btn-cari"
            id="btnCari"
        >
            &#128269; Cari Rute
        </button>


        <!-- STATUS -->

        <div
            class="status"
            id="status"
        ></div>


        <!-- =========================
             PETA
        ========================== -->

        <div class="peta-wrap">
            <div id="map"></div>
        </div>


        <!-- =========================
             PILIHAN RUTE
        ========================== -->

        <div class="baris-judul">

            <h3>
                Pilihan rute Anda
            </h3>

            <span id="infoRute"></span>

        </div>


        <!--
            Hasil rute akan dimasukkan
            ke dalam div ini.

            Kotak:
            "Isi asal dan tujuan..."
            SUDAH DIHAPUS.
        -->

        <div id="daftarRute"></div>

        <div id="perbandinganRute"></div>

        <div class="disclaimer" id="disclaimerSkor"></div>


        <!-- =========================
             MULAI NAVIGASI
        ========================== -->

        <button
            class="btn-navigasi"
            id="btnNavigasi"
            disabled
        >
            &#10148; Mulai Navigasi
        </button>


        <div class="catatan">
            Indikator membantu memilih rute,
            bukan jaminan keamanan.
        </div>

    </div>


    <!-- =========================
         BOTTOM NAVIGATION
    ========================== -->

    <div class="nav">

        <div class="aktif">
            <span>&#128739;</span>
            Rute
        </div>

        <div>
            <span>&#128737;</span>
            Keamanan
        </div>

        <div>
            <span>&#128221;</span>
            Laporan
        </div>

        <div>
            <span>&#128105;</span>
            Perempuan
        </div>

        <div>
            <span>&#128100;</span>
            Akun
        </div>

    </div>

</div>


<!-- Leaflet JS -->

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
></script>


<script>

    // =====================================================
    // PENGATURAN AWAL
    // =====================================================

    const PUSAT = {
        lat: -7.2575,
        lng: 112.7521
    };


    const DISCLAIMER_FALLBACK =
        'Skor adalah indikator, bukan jaminan keamanan.';


    // =====================================================
    // HELPER
    // =====================================================

    const $ = id =>
        document.getElementById(id);


    const inputDari =
        $('inputDari');


    const inputKe =
        $('inputKe');


    const sleep = ms =>
        new Promise(
            resolve =>
                setTimeout(resolve, ms)
        );


    // =====================================================
    // MEMBUAT PETA
    // =====================================================

    const map = L.map(
        'map',
        {
            zoomControl: false
        }
    ).setView(
        [
            PUSAT.lat,
            PUSAT.lng
        ],
        12
    );


    L.tileLayer(
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution:
                '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);


    const layerRute =
        L.layerGroup().addTo(map);


    // =====================================================
    // PENCARIAN TEMPAT
    // =====================================================

    let terakhir = 0;


    async function cariTempat(q) {

        const tunggu =
            terakhir +
            1100 -
            Date.now();


        if (tunggu > 0) {

            await sleep(
                tunggu
            );
        }


        terakhir =
            Date.now();


        const url =
            'https://nominatim.openstreetmap.org/search' +
            '?format=json' +
            '&limit=5' +
            '&countrycodes=id' +
            '&accept-language=id' +
            '&viewbox=112.5,-7.1,113.0,-7.5' +
            '&q=' +
            encodeURIComponent(q);


        const res =
            await fetch(url);


        if (!res.ok) {

            throw new Error(
                'Layanan pencarian tempat sedang sibuk, coba lagi.'
            );
        }


        return res.json();
    }


    // =====================================================
    // DATA ASAL DAN TUJUAN
    // =====================================================

    let asal = null;
    let tujuan = null;


    // =====================================================
    // DEBOUNCE
    // =====================================================

    function debounce(fn, ms) {

        let timer;


        return (...args) => {

            clearTimeout(
                timer
            );


            timer =
                setTimeout(
                    () =>
                        fn(...args),
                    ms
                );
        };
    }


    // =====================================================
    // SARAN / AUTOCOMPLETE TEMPAT
    // =====================================================

    function pasangSaran(
        input,
        box,
        setter
    ) {

        const cari =
            debounce(
                async () => {

                    const q =
                        input.value.trim();


                    if (
                        q.length < 3 ||
                        q.toLowerCase() ===
                        'lokasi saya'
                    ) {

                        box.innerHTML = '';

                        return;
                    }


                    try {

                        const hasil =
                            await cariTempat(q);


                        box.innerHTML = '';


                        hasil.forEach(
                            h => {

                                const d =
                                    document.createElement(
                                        'div'
                                    );


                                d.className =
                                    'item-saran';


                                d.textContent =
                                    h.display_name;


                                d.addEventListener(
                                    'click',
                                    () => {

                                        input.value =
                                            h.display_name
                                                .split(',')
                                                .slice(0, 2)
                                                .join(',')
                                                .trim();


                                        setter({

                                            nama:
                                                input.value,

                                            lat:
                                                +h.lat,

                                            lng:
                                                +h.lon
                                        });


                                        box.innerHTML =
                                            '';
                                    }
                                );


                                box.appendChild(
                                    d
                                );
                            }
                        );


                    } catch (e) {

                        box.innerHTML =
                            '';
                    }

                },
                600
            );


        input.addEventListener(
            'input',
            () => {

                setter(null);

                cari();
            }
        );


        document.addEventListener(
            'click',
            e => {

                if (
                    !box.contains(
                        e.target
                    ) &&
                    e.target !== input
                ) {

                    box.innerHTML =
                        '';
                }
            }
        );
    }


    // Pasang autocomplete untuk asal

    pasangSaran(
        inputDari,
        $('saranDari'),
        value => {
            asal = value;
        }
    );


    // Pasang autocomplete untuk tujuan

    pasangSaran(
        inputKe,
        $('saranKe'),
        value => {
            tujuan = value;
        }
    );


    // =====================================================
    // LOKASI SAYA
    // =====================================================

    function lokasiSaya() {

        return new Promise(
            (resolve, reject) => {

                if (
                    !navigator.geolocation
                ) {

                    return reject(
                        new Error(
                            'Browser tidak mendukung lokasi. Ketik alamat asal.'
                        )
                    );
                }


                navigator.geolocation
                    .getCurrentPosition(

                        position => {

                            resolve({

                                nama:
                                    'Lokasi Saya',

                                lat:
                                    position.coords.latitude,

                                lng:
                                    position.coords.longitude
                            });
                        },


                        () => {

                            reject(
                                new Error(
                                    'Izin lokasi ditolak. Ketik alamat asal secara manual.'
                                )
                            );
                        },


                        {
                            timeout: 8000
                        }
                    );
            }
        );
    }


    // =====================================================
    // TENTUKAN KOORDINAT
    // =====================================================

    async function tentukan(
        input,
        terpilih
    ) {

        const teks =
            input.value.trim();


        if (!teks) {

            throw new Error(
                'Isi asal dan tujuan dulu.'
            );
        }


        // Jika pengguna memilih dari autocomplete

        if (terpilih) {

            return terpilih;
        }


        // Jika menggunakan lokasi perangkat

        if (
            teks.toLowerCase() ===
            'lokasi saya'
        ) {

            return lokasiSaya();
        }


        // Cari berdasarkan teks

        const hasil =
            await cariTempat(
                teks
            );


        if (!hasil.length) {

            throw new Error(
                'Tempat "' +
                teks +
                '" tidak ditemukan.'
            );
        }


        return {

            nama:
                teks,

            lat:
                +hasil[0].lat,

            lng:
                +hasil[0].lon
        };
    }


    // =====================================================
    // DATA RUTE
    // =====================================================

    let daftarGaris = [];

    let terpilihIdx = 0;

    let titikA = null;

    let titikB = null;

    let modeNavigasi = false;

    let penghitungPermintaanRute = 0;

    let hasilRuteAktif = [];

    let statusSkor = 'pending';

    let disclaimerAktif = DISCLAIMER_FALLBACK;


    // =====================================================
    // STATUS
    // =====================================================

    function setStatus(
        teks,
        error = false
    ) {

        $('status').textContent =
            teks || '';


        $('status').className =
            'status' +
            (
                error
                    ? ' error'
                    : ''
            );
    }


    // =====================================================
    // MEMILIH RUTE
    // =====================================================

    function pilih(i) {

        terpilihIdx = i;


        daftarGaris.forEach(
            (garis, index) => {

                const aktif =
                    index === i;


                garis.setStyle({

                    color:
                        aktif
                            ? '#2196F3'
                            : '#0D47A1',

                    weight:
                        aktif
                            ? 7
                            : 4,

                    opacity:
                        aktif
                            ? 0.95
                            : 0.5,

                    dashArray:
                        aktif
                            ? null
                            : '6 8'
                });


                if (aktif) {

                    garis.bringToFront();
                }
            }
        );


        document
            .querySelectorAll(
                '.kartu'
            )
            .forEach(
                (kartu, index) => {

                    kartu.classList.toggle(
                        'aktif',
                        index === i
                    );
                }
            );

        document
            .querySelectorAll('[data-indeks-rute]')
            .forEach(kolom => {
                kolom.classList.toggle(
                    'terpilih',
                    Number(kolom.dataset.indeksRute) === i
                );
            });


        // Jika sedang dalam mode navigasi,
        // fokuskan peta ke rute yang dipilih.

        if (
            modeNavigasi &&
            daftarGaris[i]
        ) {

            map.fitBounds(
                daftarGaris[i]
                    .getBounds()
                    .pad(0.1)
            );
        }
    }


    // =====================================================
    // TAMPILKAN HASIL RUTE
    // =====================================================

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
            data_belum_cukup: {
                label: 'Data belum cukup',
                className: 'level-data-belum-cukup'
            }
        };

        return levels[level] || null;
    }


    async function ambilSkorRute(routes) {

        const response = await fetch(
            "{{ url('/api/routes/score') }}",
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ routes })
            }
        );

        let payload = {};

        try {
            payload = await response.json();
        } catch (error) {
            payload = {};
        }

        if (!response.ok) {
            const errors = payload.errors || {};
            const pesanError = Object.values(errors)
                .flat()
                .find(pesan => typeof pesan === 'string' && pesan.trim());
            const pesan = pesanError || payload.message;

            if (response.status === 422 && pesan) {
                throw new Error(pesan);
            }

            if (response.status === 429) {
                throw new Error('Permintaan skor terlalu sering. Skor belum tersedia.');
            }

            if (response.status === 404) {
                throw new Error('Skor belum tersedia');
            }

            if (response.status >= 500) {
                throw new Error('Layanan skor sedang bermasalah. Skor belum tersedia.');
            }

            throw new Error(pesan || 'Skor belum tersedia');
        }

        return payload;
    }


    function buatPayloadSkor(routes) {

        return routes.map((route, index) => {
            const coordinates = route.geometry.coordinates;
            const langkah = Math.ceil(coordinates.length / 500);
            const dijarangkan = coordinates.filter((titik, titikIndex) =>
                titikIndex === 0 ||
                titikIndex === coordinates.length - 1 ||
                titikIndex % langkah === 0
            );

            if (dijarangkan.length > 500) {
                dijarangkan.splice(dijarangkan.length - 2, 1);
            }

            return {
                id: String.fromCharCode(65 + index),
                coordinates: dijarangkan.map(titik => [titik[0], titik[1]])
            };
        });
    }


    function buatElemen(tag, className, teks) {

        const elemen = document.createElement(tag);

        if (className) {
            elemen.className = className;
        }

        if (teks !== undefined && teks !== null) {
            elemen.textContent = teks;
        }

        return elemen;
    }


    function hitungPenanda(routes) {

        const tercepat = routes.reduce((best, route, index) =>
            best === null || route.duration < routes[best].duration ? index : best,
            null
        );
        const terpendek = routes.reduce((best, route, index) =>
            best === null || route.distance < routes[best].distance ? index : best,
            null
        );
        const tersedia = routes
            .map((route, index) => ({ index, score: route.score?.score }))
            .filter(item => typeof item.score === 'number' && Number.isFinite(item.score));

        const teraman = tersedia.reduce((best, item) => {
            if (!best || item.score > best.score) {
                return item;
            }

            if (item.score === best.score && routes[item.index].duration < routes[best.index].duration) {
                return item;
            }

            return best;
        }, null);

        return { tercepat, terpendek, teraman: teraman ? teraman.index : null };
    }


    function renderHasilRute(routes) {

        hasilRuteAktif = routes;
        const wadah = $('daftarRute');
        wadah.replaceChildren();

        const penanda = hitungPenanda(routes);

        routes.forEach((route, i) => {
            const kartu = buatElemen('div', 'kartu');
            const atas = buatElemen('div', 'kartu-atas');
            const nama = buatElemen('div');
            const skorArea = buatElemen('div', 'skor');
            const meta = buatElemen('div', 'meta');
            const chips = buatElemen('div', 'chips');
            const metric = konversiMetrikRute(route);
            const labelRute = String.fromCharCode(65 + i);
            const badges = [];

            nama.appendChild(buatElemen('b', '', 'Rute ' + labelRute));

            if (penanda.tercepat === i) badges.push('Tercepat');
            if (penanda.terpendek === i) badges.push('Terpendek');
            if (penanda.teraman === i) badges.push('Teraman', 'Disarankan');

            badges.forEach(teks => {
                nama.appendChild(buatElemen('span', 'tag', teks));
            });

            if (statusSkor === 'pending') {
                skorArea.textContent = 'Menghitung skor...';
            } else if (statusSkor === 'unavailable') {
                skorArea.textContent = 'Skor belum tersedia';
            } else {
                const hasil = route.score;
                const level = hasil && formatLevel(hasil.level);
                const angka = hasil && hasil.level === 'data_belum_cukup'
                    ? '-'
                    : (hasil && typeof hasil.score === 'number' ? String(hasil.score) : '-');

                skorArea.appendChild(document.createTextNode(angka + ' '));
                skorArea.appendChild(buatElemen('small', '', '/100'));

                if (level) {
                    skorArea.appendChild(buatElemen('span', 'level-badge ' + level.className, hasil.level_label || level.label));
                }

                if (hasil && hasil.note) {
                    skorArea.appendChild(buatElemen('small', '', hasil.note));
                }

                if (hasil && Array.isArray(hasil.factors)) {
                    hasil.factors
                        .filter(factor => factor.available === true)
                        .forEach(factor => {
                            const nilai = typeof factor.score === 'number'
                                ? ' ' + Math.round(factor.score)
                                : '';
                            chips.appendChild(buatElemen('span', 'chip', String(factor.label || factor.key || '') + nilai));
                        });
                }
            }

            meta.textContent = metric.waktuMenit + ' menit · ' + metric.jarakKm.toFixed(1) + ' km';

            // TODO SR-22: tampilkan komposisi perempuan setelah datanya tersedia.
            atas.append(nama, skorArea);
            kartu.append(atas, meta, chips);
            kartu.addEventListener('click', () => pilih(i));
            wadah.appendChild(kartu);
        });

        renderPerbandingan(routes, penanda);
        $('infoRute').textContent = routes.length + ' rute ditemukan';
        $('disclaimerSkor').textContent = disclaimerAktif || DISCLAIMER_FALLBACK;
        pilih(terpilihIdx < routes.length ? terpilihIdx : 0);
    }


    function renderPerbandingan(routes, penanda) {

        const wadah = $('perbandinganRute');
        wadah.replaceChildren();

        if (routes.length < 2) {
            return;
        }

        const wrap = buatElemen('div', 'perbandingan-wrap');
        const tabel = buatElemen('table', 'perbandingan');
        const kepala = buatElemen('thead');
        const barisKepala = buatElemen('tr');
        barisKepala.appendChild(buatElemen('th', '', ''));

        routes.forEach((route, index) => {
            const th = buatElemen('th', '', 'Rute ' + String.fromCharCode(65 + index));
            th.dataset.indeksRute = String(index);
            th.addEventListener('click', () => pilih(index));
            barisKepala.appendChild(th);
        });

        kepala.appendChild(barisKepala);
        tabel.appendChild(kepala);

        const isi = buatElemen('tbody');
        const rows = [
            ['Jarak (km)', route => konversiMetrikRute(route).jarakKm.toFixed(1)],
            ['Waktu (menit)', route => String(konversiMetrikRute(route).waktuMenit)],
            ['Level keamanan', route => route.score ? ((formatLevel(route.score.level) || {}).label || 'Belum tersedia') : 'Belum tersedia'],
            ['Skor', route => route.score && route.score.level === 'data_belum_cukup'
                ? '-'
                : (route.score && typeof route.score.score === 'number' ? String(route.score.score) : '-')]
        ];

        rows.forEach(([label, value]) => {
            const tr = buatElemen('tr');
            tr.appendChild(buatElemen('td', '', label));

            routes.forEach((route, index) => {
                const td = buatElemen('td', '', value(route));
                td.dataset.indeksRute = String(index);
                td.addEventListener('click', () => pilih(index));

                if (penanda.tercepat === index && label === 'Waktu (menit)') td.appendChild(buatElemen('small', '', ' Tercepat'));
                if (penanda.terpendek === index && label === 'Jarak (km)') td.appendChild(buatElemen('small', '', ' Terpendek'));
                if (penanda.teraman === index && label === 'Level keamanan') td.appendChild(buatElemen('small', '', ' Teraman · Disarankan'));

                tr.appendChild(td);
            });

            isi.appendChild(tr);
        });

        tabel.appendChild(isi);
        wrap.appendChild(tabel);
        wadah.appendChild(wrap);
    }


    async function muatSkor(routes, permintaan) {

        try {
            const payload = await ambilSkorRute(buatPayloadSkor(routes));

            if (permintaan !== penghitungPermintaanRute) return;

            const hasil = Array.isArray(payload.data) ? payload.data : [];
            hasilRuteAktif.forEach(route => {
                route.score = hasil.find(item => item.id === String.fromCharCode(65 + route.index)) || null;
            });

            disclaimerAktif = typeof payload.disclaimer === 'string'
                ? payload.disclaimer
                : DISCLAIMER_FALLBACK;
            statusSkor = 'available';
            renderHasilRute(hasilRuteAktif);
        } catch (error) {
            if (permintaan !== penghitungPermintaanRute) return;

            statusSkor = 'unavailable';
            disclaimerAktif = DISCLAIMER_FALLBACK;
            $('disclaimerSkor').textContent = DISCLAIMER_FALLBACK;
            renderHasilRute(hasilRuteAktif);
            if (error.message && error.message !== 'Skor belum tersedia') {
                const pesan = $('status');
                pesan.textContent = error.message;
                pesan.className = 'status';
            }
        }
    }


    function tampilkan(
        a,
        b,
        routes,
        permintaan
    ) {

        // Bersihkan rute sebelumnya

        layerRute.clearLayers();


        daftarGaris = [];


        titikA = a;

        titikB = b;

        modeNavigasi = false;


        // Reset tombol navigasi

        $('btnNavigasi').innerHTML =
            '&#10148; Mulai Navigasi';


        // =================================================
        // GAMBAR SEMUA ALTERNATIF RUTE
        // =================================================

        routes.forEach(
            route => {

                const koordinat =
                    route.geometry.coordinates.map(
                        koordinat => [
                            koordinat[1],
                            koordinat[0]
                        ]
                    );


                const garis =
                    L.polyline(
                        koordinat,
                        {
                            weight: 5
                        }
                    )
                    .addTo(
                        layerRute
                    );


                daftarGaris.push(
                    garis
                );
            }
        );


        // =================================================
        // TITIK ASAL
        // =================================================

        L.circleMarker(
            [
                a.lat,
                a.lng
            ],
            {
                radius: 8,
                color: '#fff',
                weight: 3,
                fillColor: '#0D47A1',
                fillOpacity: 1
            }
        )
        .addTo(
            layerRute
        )
        .bindPopup(
            'Asal: ' +
            a.nama
        );


        // =================================================
        // TITIK TUJUAN
        // =================================================

        L.marker(
            [
                b.lat,
                b.lng
            ]
        )
        .addTo(
            layerRute
        )
        .bindPopup(
            'Tujuan: ' +
            b.nama
        );


        // =================================================
        // SESUAIKAN ZOOM
        // =================================================

        if (
            daftarGaris.length > 0
        ) {

            const semuaBounds =
                L.featureGroup(
                    daftarGaris
                )
                .getBounds();


            map.fitBounds(
                semuaBounds.pad(
                    0.15
                )
            );
        }


        // =================================================
        // KARTU PILIHAN RUTE
        // =================================================

        hasilRuteAktif = routes.map((route, index) => ({ ...route, index, score: null }));
        statusSkor = 'pending';
        disclaimerAktif = DISCLAIMER_FALLBACK;
        terpilihIdx = 0;
        renderHasilRute(hasilRuteAktif);


        // Aktifkan tombol navigasi

        $('btnNavigasi').disabled =
            false;


        muatSkor(routes, permintaan);
    }


    // =====================================================
    // CARI RUTE MENGGUNAKAN OSRM
    // =====================================================

    async function cariRute() {

        const permintaan = ++penghitungPermintaanRute;

        // Disable tombol saat proses

        $('btnCari').disabled =
            true;


        setStatus(
            'Mencari rute...'
        );


        try {

            // Cari koordinat asal

            const a =
                await tentukan(
                    inputDari,
                    asal
                );


            // Cari koordinat tujuan

            const b =
                await tentukan(
                    inputKe,
                    tujuan
                );


            // URL OSRM

            const url =

                'https://router.project-osrm.org/route/v1/driving/' +

                a.lng +
                ',' +
                a.lat +

                ';' +

                b.lng +
                ',' +
                b.lat +

                '?alternatives=true' +
                '&overview=full' +
                '&geometries=geojson';


            // Request

            const response =
                await fetch(
                    url
                );


            if (
                !response.ok
            ) {

                throw new Error(
                    'Layanan pencarian rute sedang bermasalah.'
                );
            }


            const data =
                await response.json();


            // Validasi hasil

            if (
                data.code !== 'Ok' ||
                !data.routes ||
                !data.routes.length
            ) {

                throw new Error(
                    'Rute tidak ditemukan untuk titik tersebut.'
                );
            }


            // Maksimal 3 alternatif

            if (permintaan !== penghitungPermintaanRute) return;

            tampilkan(
                a,
                b,
                data.routes.slice(
                    0,
                    3
                ),
                permintaan
            );


            setStatus('');


        } catch (error) {

            if (permintaan !== penghitungPermintaanRute) {
                return;
            }

            setStatus(

                error.message ||

                'Gagal mencari rute. Cek koneksi internet.',

                true
            );


        } finally {

            // Aktifkan kembali tombol

            if (permintaan === penghitungPermintaanRute) {
                $('btnCari').disabled = false;
            }
        }
    }


    // =====================================================
    // EVENT TOMBOL CARI
    // =====================================================

    $('btnCari')
        .addEventListener(
            'click',
            cariRute
        );


    // =====================================================
    // ENTER UNTUK MENCARI
    // =====================================================

    [
        inputDari,
        inputKe
    ]
    .forEach(
        input => {

            input.addEventListener(
                'keydown',
                event => {

                    if (
                        event.key ===
                        'Enter'
                    ) {

                        cariRute();
                    }
                }
            );
        }
    );


    // =====================================================
    // TUKAR ASAL DAN TUJUAN
    // =====================================================

    $('btnTukar')
        .addEventListener(
            'click',
            () => {

                // Tukar isi input

                [
                    inputDari.value,
                    inputKe.value
                ] = [

                    inputKe.value,
                    inputDari.value
                ];


                // Tukar data koordinat

                [
                    asal,
                    tujuan
                ] = [

                    tujuan,
                    asal
                ];
            }
        );


    // =====================================================
    // MULAI NAVIGASI
    //
    // TIDAK MEMBUKA GOOGLE MAPS.
    // Navigasi tetap berada di SafeRoad.
    // =====================================================

    $('btnNavigasi')
        .addEventListener(
            'click',
            () => {

                // Pastikan rute sudah tersedia

                if (
                    !titikA ||
                    !titikB ||
                    !daftarGaris.length
                ) {

                    setStatus(
                        'Cari dan pilih rute terlebih dahulu.',
                        true
                    );

                    return;
                }


                // Aktifkan mode navigasi

                modeNavigasi =
                    true;


                // Ambil rute yang dipilih

                const ruteAktif =
                    daftarGaris[
                        terpilihIdx
                    ];


                if (!ruteAktif) {

                    setStatus(
                        'Rute tidak tersedia.',
                        true
                    );

                    return;
                }


                // Fokuskan map hanya ke rute
                // yang dipilih pengguna

                map.fitBounds(

                    ruteAktif
                        .getBounds()
                        .pad(0.08)

                );


                // Pastikan garis rute aktif
                // berada di paling depan

                ruteAktif
                    .bringToFront();


                // Ubah tulisan tombol

                $('btnNavigasi')
                    .innerHTML =
                    '&#128205; Navigasi Aktif';


                // Informasi ke pengguna

                setStatus(
                    'Navigasi dimulai. Ikuti rute yang dipilih pada peta.'
                );


                // Scroll otomatis ke peta

                document
                    .getElementById(
                        'map'
                    )
                    .scrollIntoView({
                        behavior:
                            'smooth',

                        block:
                            'center'
                    });
            }
        );

</script>

</body>
</html>
