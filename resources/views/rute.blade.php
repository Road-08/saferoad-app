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
            align-items: center;
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
            margin-left: 6px;
        }

        .tag.alt {
            background: var(--biru-sedang);
            color: var(--biru-tua);
        }

        .skor {
            font-size: 22px;
            font-weight: bold;
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


    // Hanya true untuk perempuan terverifikasi.
    // Nantinya ditentukan dari backend.
    const tampilKomposisi = true;


    // =====================================================
    // DATA KEAMANAN SEMENTARA / DUMMY
    // =====================================================

    function nilaiDummy(r, i) {

        return {

            skor: Math.max(
                50,
                95 - i * 13 -
                (Math.round(r.distance / 1000) % 4)
            ),

            penerangan: Math.max(
                40,
                90 - i * 20
            ),

            cctv: i === 0,

            perempuan: Math.max(
                20,
                60 - i * 20
            )
        };
    }


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

    function tampilkan(
        a,
        b,
        routes
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

        const wadah =
            $('daftarRute');


        wadah.innerHTML =
            '';


        routes.forEach(
            (route, i) => {

                const nilai =
                    nilaiDummy(
                        route,
                        i
                    );


                const chips = [

                    '&#128161; ' +
                    nilai.penerangan +
                    '% penerangan',

                    nilai.cctv

                        ? '&#128249; Ada CCTV'

                        : '&#128249; Minim CCTV'
                ];


                if (
                    tampilKomposisi
                ) {

                    chips.push(

                        '&#128105; Dominan perempuan ' +

                        nilai.perempuan +

                        '% agregat'
                    );
                }


                const kartu =
                    document.createElement(
                        'div'
                    );


                kartu.className =
                    'kartu';


                const namaRute =
                    String.fromCharCode(
                        65 + i
                    );


                const label =

                    i === 0

                        ? '<span class="tag">Disarankan</span>'

                        : '<span class="tag alt">Alternatif</span>';


                kartu.innerHTML =

                    '<div class="kartu-atas">' +

                        '<div>' +

                            '<b>Rute ' +
                            namaRute +
                            '</b>' +

                            label +

                        '</div>' +

                        '<div class="skor">' +

                            nilai.skor +

                            '<small> /100</small>' +

                        '</div>' +

                    '</div>' +


                    '<div class="meta">' +

                        Math.max(
                            1,
                            Math.round(
                                route.duration / 60
                            )
                        ) +

                        ' menit &middot; ' +

                        (
                            route.distance / 1000
                        ).toFixed(1) +

                        ' km' +

                    '</div>' +


                    '<div class="chips">' +

                        chips.map(
                            chip =>

                                '<span class="chip">' +

                                chip +

                                '</span>'

                        ).join('') +

                    '</div>';


                // Klik kartu untuk memilih rute

                kartu.addEventListener(
                    'click',
                    () => {

                        pilih(i);
                    }
                );


                wadah.appendChild(
                    kartu
                );
            }
        );


        // Informasi jumlah rute

        $('infoRute').textContent =
            routes.length +
            ' rute ditemukan';


        // Aktifkan tombol navigasi

        $('btnNavigasi').disabled =
            false;


        // Otomatis pilih rute pertama

        pilih(0);
    }


    // =====================================================
    // CARI RUTE MENGGUNAKAN OSRM
    // =====================================================

    async function cariRute() {

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

            tampilkan(
                a,
                b,
                data.routes.slice(
                    0,
                    3
                )
            );


            setStatus('');


        } catch (error) {

            setStatus(

                error.message ||

                'Gagal mencari rute. Cek koneksi internet.',

                true
            );


        } finally {

            // Aktifkan kembali tombol

            $('btnCari').disabled =
                false;
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
