@extends('layouts.app')

@section('title', 'Pusat Informasi & Kontak Darurat · SafeRoad')

@section('content')
<div class="darurat-container">
    <!-- Header Warning Banner -->
    <header class="darurat-hero-card">
        <div class="dhc-badge">RESPON CEPAT &amp; LAYANAN DARURAT</div>
        <h1>Pusat Bantuan &amp; Kontak Darurat</h1>
        <p>
            Jika Anda atau orang di sekitar Anda sedang dalam situasi bahaya mendesak, segera hubungi nomor tanggap darurat resmi di bawah ini.
        </p>
    </header>

    <!-- Hotlines Grid -->
    <h2 class="section-heading">Nomor Telepon Darurat Nasional (Bebas Pulsa)</h2>
    <div class="hotlines-grid">
        <div class="hotline-card card-police">
            <div class="hlc-icon" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
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
            <div class="hlc-icon" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
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
            <div class="hlc-icon" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
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
            <div class="hlc-icon" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
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
                <div class="ab-icon icon-red" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                </div>
                <div>
                    <h3>Bunyikan Sirine Peringatan Bahaya</h3>
                    <p>Putar suara sirine keras dari perangkat Anda untuk menarik perhatian warga sekitar saat situasi terdesak.</p>
                </div>
            </div>
            <div class="siren-control-area">
                <button type="button" id="btnToggleSiren" class="btn-siren-toggle">
                    <span id="sirenText">Nyalakan Sirine Sekarang</span>
                </button>
            </div>
        </div>

        <!-- Bagikan Koordinat Cepat -->
        <div class="action-box">
            <div class="ab-header">
                <div class="ab-icon icon-blue" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div>
                    <h3>Bagikan Titik Lokasi Saya ke Kontak</h3>
                    <p>Kirimkan titik koordinat GPS terkini langsung ke kerabat melalui WhatsApp dalam format darurat siap kirim.</p>
                </div>
            </div>
            <div class="share-location-area">
                <button type="button" id="btnShareLocation" class="btn-share-wa">
                    <span>Kirim Lokasi Darurat via WhatsApp</span>
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
    background-color: #1e3a8a;
    border: 1px solid #1e40af;
    border-radius: 14px;
    padding: 32px clamp(20px, 3.5vw, 40px);
    color: #fff;
    margin-bottom: 32px;
    box-shadow: var(--shadow-card);
}
.dhc-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.14);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: #dbeafe;
    margin-bottom: 12px;
}
.darurat-hero-card h1 {
    font-size: clamp(24px, 3.2vw, 32px);
    font-weight: 800;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
}
.darurat-hero-card p {
    font-size: 14.5px;
    color: #dbeafe;
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
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.hotline-card {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 14px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: var(--shadow-sm);
    transition: border-color 0.15s ease;
}
.hotline-card:hover {
    border-color: #2563eb;
}
.hlc-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
}
.card-sos .hlc-icon {
    background: #fee2e2;
}
.hlc-icon svg {
    width: 20px;
    height: 20px;
}
.hlc-body h3 {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-heading);
    margin-bottom: 6px;
}
.hlc-body p {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.5;
    margin-bottom: 18px;
}
.btn-call-emergency {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 40px;
    padding: 0 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
    transition: background-color 0.15s;
}
.btn-call-emergency strong { font-size: 15px; font-weight: 700; }

.card-police .btn-call-emergency { background: #1e40af; }
.card-police .btn-call-emergency:hover { background: #1e3a8a; }
.card-ambulance .btn-call-emergency { background: #2563eb; }
.card-ambulance .btn-call-emergency:hover { background: #1e40af; }
.card-sos .btn-call-emergency { background: #dc2626; }
.card-sos .btn-call-emergency:hover { background: #b91c1c; }
.card-women .btn-call-emergency { background: #2563eb; }
.card-women .btn-call-emergency:hover { background: #1e40af; }

/* Quick Actions */
.quick-actions-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 28px;
}
@media (max-width: 768px) {
    .quick-actions-row { grid-template-columns: 1fr; }
}
.action-box {
    background: #fff;
    border: 1px solid var(--border-soft);
    border-radius: 14px;
    padding: 20px;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.ab-header {
    display: flex;
    gap: 14px;
    margin-bottom: 16px;
}
.ab-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.icon-red { background: #fee2e2; }
.icon-blue { background: #eff6ff; }
.ab-header h3 {
    font-size: 15px;
    font-weight: 700;
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
    height: 42px;
    padding: 0 16px;
    border-radius: 8px;
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #dc2626;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    white-space: nowrap;
    transition: all 0.15s;
}
.btn-siren-toggle.playing {
    background: #dc2626;
    color: #fff;
}

.btn-share-wa {
    width: 100%;
    height: 42px;
    padding: 0 16px;
    border-radius: 8px;
    background: #2563eb;
    border: none;
    color: #fff;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.15s;
}
.btn-share-wa:hover { background: #1e40af; }
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
    border-radius: 14px;
    overflow-x: auto;
    box-shadow: var(--shadow-sm);
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
.badge-polisi { background: #eff6ff; color: #1e40af; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }
.badge-rs     { background: #eff6ff; color: #1e40af; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }
.badge-pos    { background: #eff6ff; color: #1e40af; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }
.badge-open   { background: #f8fafc; color: #0f172a; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; }
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
                const msg = encodeURIComponent(`PESAN DARURAT SAFEROAD\nSaya membutuhkan bantuan segera. Titik lokasi GPS saya saat ini:\n${mapLink}\n(Waktu: ${new Date().toLocaleTimeString('id-ID')})`);
                shareStatus.textContent = `Lokasi diperoleh (${lat}, ${lng}). Mengarahkan ke WhatsApp...`;
                window.open(`https://api.whatsapp.com/send?text=${msg}`, '_blank');
            },
            err => {
                const defaultLink = `https://www.google.com/maps?q=-7.3140,112.7280`;
                const msg = encodeURIComponent(`PESAN DARURAT SAFEROAD\nSaya membutuhkan bantuan segera. Lokasi terakhir di sekitar Ketintang Surabaya:\n${defaultLink}`);
                window.open(`https://api.whatsapp.com/send?text=${msg}`, '_blank');
            },
            { timeout: 6000 }
        );
    });
});
</script>
@endpush
@endsection
