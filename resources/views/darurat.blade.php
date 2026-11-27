@extends('layouts.app')

@section('title', 'Pusat Informasi & Kontak Darurat · SafeRoad')

@section('content')
<div class="darurat-container">
    <!-- Header Warning Banner -->
    <header class="darurat-hero-card">
        <div class="dhc-badge">🚨 RESPON CEPAT &amp; LAYANAN DARURAT</div>
        <h1>Pusat Bantuan &amp; Kontak Darurat</h1>
        <p>
            Jika Anda atau orang di sekitar Anda sedang dalam situasi bahaya mendesak, segera hubungi nomor tanggap darurat resmi di bawah ini.
        </p>
    </header>

    <!-- Hotlines Grid -->
    <h2 class="section-heading">Nomor Telepon Darurat Nasional (Bebas Pulsa)</h2>
    <div class="hotlines-grid">
        <div class="hotline-card card-police">
            <div class="hlc-icon">🚔</div>
            <div class="hlc-body">
                <h3>Kepolisian RI (Polri)</h3>
                <p>Tindak kriminal, pembegalan, kekerasan, atau ancaman fisik.</p>
                <a href="tel:110" class="btn-call-emergency">
                    <span>Telepon</span>
                    <strong>110</strong>
                </a>
            </div>
        </div>

        <div class="hotline-card card-ambulance">
            <div class="hlc-icon">🚑</div>
            <div class="hlc-body">
                <h3>Ambulans &amp; Medis (Kemenkes)</h3>
                <p>Pertolongan medis darurat, kecelakaan lalu lintas, cedera berat.</p>
                <a href="tel:119" class="btn-call-emergency">
                    <span>Telepon</span>
                    <strong>119</strong>
                </a>
            </div>
        </div>

        <div class="hotline-card card-sos">
            <div class="hlc-icon">🆘</div>
            <div class="hlc-body">
                <h3>Layanan Terpadu Tanggap Darurat</h3>
                <p>Pusat koordinasi darurat satu pintu BNPB, BPBD, &amp; Dishub.</p>
                <a href="tel:112" class="btn-call-emergency">
                    <span>Telepon</span>
                    <strong>112</strong>
                </a>
            </div>
        </div>

        <div class="hotline-card card-women">
            <div class="hlc-icon">♀️</div>
            <div class="hlc-body">
                <h3>SAPA 129 (Perlindungan Perempuan)</h3>
                <p>Pengaduan pelecehan seksual, kekerasan fisik, &amp; pendampingan psikologis.</p>
                <a href="tel:129" class="btn-call-emergency">
                    <span>Telepon</span>
                    <strong>129</strong>
                </a>
            </div>
        </div>
    </div>

    <!-- Fitur Aksi Cepat: Sirine & Bagikan Lokasi -->
    <div class="quick-actions-row">
        <!-- Sirene Alarm Simulator -->
        <div class="action-box">
            <div class="ab-header">
                <div class="ab-icon icon-red">🔊</div>
                <div>
                    <h3>Bunyikan Sirine Peringatan Bahaya</h3>
                    <p>Putar suara sirine keras dari perangkat Anda untuk menarik perhatian warga sekitar saat situasi terdesak.</p>
                </div>
            </div>
            <div class="siren-control-area">
                <button type="button" id="btnToggleSiren" class="btn-siren-toggle">
                    <span id="sirenIcon">📢</span>
                    <span id="sirenText">Nyalakan Sirine Sekarang</span>
                </button>
            </div>
        </div>

        <!-- Bagikan Koordinat Cepat -->
        <div class="action-box">
            <div class="ab-header">
                <div class="ab-icon icon-green">📍</div>
                <div>
                    <h3>Bagikan Titik Lokasi Saya ke Kontak</h3>
                    <p>Kirimkan titik koordinat GPS terkini langsung ke kerabat melalui WhatsApp dalam format darurat siap kirim.</p>
                </div>
            </div>
            <div class="share-location-area">
                <button type="button" id="btnShareLocation" class="btn-share-wa">
                    <span>💬 Kirim Lokasi Darurat via WhatsApp</span>
                </button>
                <p id="shareStatus" class="share-status-txt"></p>
            </div>
        </div>
    </div>

    <!-- Daftar Fasilitas Keamanan Terdekat -->
    <h2 class="section-heading" style="margin-top: 36px;">Pos Polisi &amp; Rumah Sakit Terdekat</h2>
    <div class="facilities-table-card">
        <table class="facilities-table">
            <thead>
                <tr>
                    <th>Nama Fasilitas</th>
                    <th>Kategori</th>
                    <th>Alamat</th>
                    <th>Status</th>
                    <th>Aksi Navigasi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Polsek Gayungan / Ketintang</strong></td>
                    <td><span class="badge-polisi">Kantor Polisi</span></td>
                    <td>Jl. Gayungsari Barat No. 22, Surabaya</td>
                    <td><span class="badge-open">24 Jam</span></td>
                    <td><a href="https://maps.google.com/?q=Polsek+Gayungan+Surabaya" target="_blank" class="nav-map-link">Buka Rute Maps ↗</a></td>
                </tr>
                <tr>
                    <td><strong>RSUD Bhakti Dharma Husada / RSI Surabaya</strong></td>
                    <td><span class="badge-rs">Rumah Sakit (IGD)</span></td>
                    <td>Jl. Ahmad Yani No. 2-4, Surabaya</td>
                    <td><span class="badge-open">IGD Siaga</span></td>
                    <td><a href="https://maps.google.com/?q=RSI+Surabaya+A+Yani" target="_blank" class="nav-map-link">Buka Rute Maps ↗</a></td>
                </tr>
                <tr>
                    <td><strong>Pos Satpam Terpadu Kampus</strong></td>
                    <td><span class="badge-pos">Pos Keamanan</span></td>
                    <td>Jl. Ketintang Utama, Depan Gerbang Utama</td>
                    <td><span class="badge-open">Petugas Jaga</span></td>
                    <td><a href="https://maps.google.com/?q=Telkom+University+Surabaya" target="_blank" class="nav-map-link">Buka Rute Maps ↗</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.darurat-container {
    max-width: 1140px;
    margin: 32px auto 60px;
    padding: 0 20px;
}
.darurat-hero-card {
    background: linear-gradient(135deg, #7f1d1d 0%, #b91c1c 60%, #dc2626 100%);
    border-radius: 20px;
    padding: 36px clamp(20px, 4vw, 44px);
    color: #fff;
    margin-bottom: 32px;
    box-shadow: 0 15px 35px -10px rgba(185, 28, 28, 0.4);
}
.dhc-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.2);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.1em;
    color: #fee2e2;
    margin-bottom: 12px;
}
.darurat-hero-card h1 {
    font-size: clamp(24px, 3.2vw, 34px);
    font-weight: 800;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
}
.darurat-hero-card p {
    font-size: 14.5px;
    color: #fee2e2;
    max-width: 680px;
    line-height: 1.6;
    margin: 0;
}

.section-heading {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-heading);
    margin-bottom: 16px;
}

/* Hotlines */
.hotlines-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 18px;
    margin-bottom: 32px;
}
.hotline-card {
    background: #fff;
    border: 1.5px solid var(--border-soft);
    border-radius: 18px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    transition: transform 0.2s, box-shadow 0.2s;
}
.hotline-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px -8px rgba(0,0,0,0.1);
}
.hlc-icon {
    font-size: 36px;
    margin-bottom: 12px;
}
.hlc-body h3 {
    font-size: 16px;
    font-weight: 800;
    color: var(--text-heading);
    margin-bottom: 6px;
}
.hlc-body p {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.5;
    margin-bottom: 20px;
}
.btn-call-emergency {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    border-radius: 12px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    transition: opacity 0.2s;
}
.btn-call-emergency:hover { opacity: 0.9; }
.btn-call-emergency strong { font-size: 18px; }

.card-police .btn-call-emergency { background: #1e3a8a; }
.card-ambulance .btn-call-emergency { background: #059669; }
.card-sos .btn-call-emergency { background: #dc2626; }
.card-women .btn-call-emergency { background: #be185d; }

/* Quick Actions */
.quick-actions-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 32px;
}
@media (max-width: 768px) {
    .quick-actions-row { grid-template-columns: 1fr; }
}
.action-box {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.ab-header {
    display: flex;
    gap: 14px;
    margin-bottom: 18px;
}
.ab-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.icon-red { background: #fee2e2; }
.icon-green { background: #dcfce7; }
.ab-header h3 {
    font-size: 15px;
    font-weight: 800;
    color: var(--text-heading);
    margin-bottom: 4px;
}
.ab-header p {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.5;
    margin: 0;
}

.btn-siren-toggle {
    width: 100%;
    padding: 13px;
    border-radius: 12px;
    background: #fee2e2;
    border: 1.5px solid #fca5a5;
    color: #b91c1c;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}
.btn-siren-toggle.playing {
    background: #dc2626;
    color: #fff;
    animation: blinkSiren 0.8s infinite;
}
@keyframes blinkSiren {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.btn-share-wa {
    width: 100%;
    padding: 13px;
    border-radius: 12px;
    background: #16a34a;
    border: none;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-share-wa:hover { background: #15803d; }
.share-status-txt {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 6px;
    text-align: center;
}

/* Facilities Table */
.facilities-table-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 18px;
    overflow-x: auto;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.facilities-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
    text-align: left;
}
.facilities-table th {
    background: #f8fafc;
    padding: 14px 18px;
    font-size: 12px;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid var(--border-soft);
}
.facilities-table td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border-soft);
    color: var(--text-body);
}
.badge-polisi { background: #dbeafe; color: #1e40af; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }
.badge-rs     { background: #fee2e2; color: #991b1b; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }
.badge-pos    { background: #dcfce7; color: #166534; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }
.badge-open   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; }
.nav-map-link {
    color: var(--primary-600);
    font-weight: 700;
    text-decoration: none;
}
.nav-map-link:hover { text-decoration: underline; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Simulator Sirine Audio (Web Audio API Synthesizer)
    let audioCtx = null;
    let osc = null;
    let isSirenPlaying = false;
    let sirenInterval = null;

    const btnSiren = document.getElementById('btnToggleSiren');
    const sirenText = document.getElementById('sirenText');

    btnSiren.addEventListener('click', () => {
        if (!isSirenPlaying) {
            // Mulai sirine
            try {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sawtooth';
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);

                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();

                let high = false;
                sirenInterval = setInterval(() => {
                    if (osc && audioCtx) {
                        osc.frequency.setValueAtTime(high ? 960 : 650, audioCtx.currentTime);
                        high = !high;
                    }
                }, 400);

                isSirenPlaying = true;
                btnSiren.classList.add('playing');
                sirenText.textContent = 'Matikan Sirine Peringatan';
            } catch (err) {
                alert('Audio browser tidak diizinkan atau tidak didukung.');
            }
        } else {
            // Matikan sirine
            if (osc) {
                clearInterval(sirenInterval);
                osc.stop();
                osc.disconnect();
                osc = null;
            }
            if (audioCtx) {
                audioCtx.close();
                audioCtx = null;
            }
            isSirenPlaying = false;
            btnSiren.classList.remove('playing');
            sirenText.textContent = 'Nyalakan Sirine Sekarang';
        }
    });

    // 2. Share Live Location to WhatsApp
    const btnShare = document.getElementById('btnShareLocation');
    const shareStatus = document.getElementById('shareStatus');

    btnShare.addEventListener('click', () => {
        shareStatus.textContent = 'Mendeteksi koordinat GPS...';
        if (!navigator.geolocation) {
            shareStatus.textContent = 'GPS tidak didukung pada browser Anda.';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);
                const mapLink = `https://www.google.com/maps?q=${lat},${lng}`;
                const msg = encodeURIComponent(`🚨 PESAN DARURAT SAFEROAD 🚨\nSaya membutuhkan bantuan segera. Titik lokasi GPS saya saat ini:\n${mapLink}\n(Waktu: ${new Date().toLocaleTimeString('id-ID')})`);
                shareStatus.textContent = `Lokasi diperoleh (${lat}, ${lng}). Mengarahkan ke WhatsApp...`;
                window.open(`https://api.whatsapp.com/send?text=${msg}`, '_blank');
            },
            err => {
                const defaultLink = `https://www.google.com/maps?q=-7.3140,112.7280`;
                const msg = encodeURIComponent(`🚨 PESAN DARURAT SAFEROAD 🚨\nSaya membutuhkan bantuan segera. Lokasi terakhir di sekitar Ketintang Surabaya:\n${defaultLink}`);
                window.open(`https://api.whatsapp.com/send?text=${msg}`, '_blank');
            },
            { timeout: 6000 }
        );
    });
});
</script>
@endpush
@endsection
