@extends('layouts.app')

@section('title', 'Laporan Insiden · SafeRoad')

@push('leaflet-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')

{{-- ===== Page Wrapper ===== --}}
<div class="laporkan-page">

    {{-- Header --}}
    <div class="laporkan-header">
        <div class="laporkan-back-row">
            <a href="{{ url('/dashboard') }}" class="back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
        <div class="header-badge">
            <span class="badge-dot"></span>
            <span>LAPORAN INSIDEN</span>
        </div>
        <h1>Laporkan Kejadian di Rute Anda</h1>
        <p>Bantu komunitas SafeRoad tetap aman. Isi formulir di bawah dan tandai lokasi kejadian pada peta.</p>
    </div>

    {{-- Restriksi Akses Role (Hanya Masyarakat yang membuat laporan warga!) --}}
    <div id="roleRestrictedNotice" class="role-restricted-card" style="display: none;">
        <div class="rrc-icon-box" id="rrcIconBox">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <div class="rrc-content">
            <span class="rrc-badge" id="rrcBadge">HAK AKSES KHUSUS MASYARAKAT</span>
            <h2 id="rrcTitle">Fitur Pelaporan Ini Diperuntukkan Bagi Warga Masyarakat</h2>
            <p id="rrcDesc">
                Role Anda saat ini adalah <strong>Petugas Keamanan</strong>. Petugas keamanan tidak membuat laporan mandiri, melainkan bertugas menerima, memverifikasi di TKP, dan menangani aduan yang dikirimkan oleh warga.
            </p>
            <div class="rrc-btn-wrap">
                <a href="{{ route('keamanan.dashboard') }}" id="rrcBtnAction" class="rrc-btn">
                    Buka Dashboard Tugas Petugas Keamanan
                </a>
                <a href="{{ url('/rute') }}" class="rrc-btn-secondary">
                    Lihat Peta Rute
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="laporkan-content">

        {{-- Form Card --}}
        <div class="form-card">
            <form id="incidentForm" novalidate>

                {{-- Kategori --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <div class="section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <span>Jenis Kejadian</span>
                    </div>
                    <select id="category" name="category" class="form-select" required>
                        <option value="">-- Pilih Jenis Kejadian --</option>
                        <option value="Pelecehan / Gangguan Keamanan">Pelecehan / Gangguan Keamanan</option>
                        <option value="Pencurian">Tindak Kriminal / Pencurian / Begal</option>
                        <option value="Kondisi Jalan">Kondisi Jalan / Lampu Mati / Rawan Kecelakaan</option>
                        <option value="Fasilitas Keamanan">Kerusakan Fasilitas Keamanan / CCTV</option>
                        <option value="Lainnya">Kondisi Mencurigakan / Lainnya</option>
                    </select>
                    <p id="cat-error" class="field-error" hidden>Pilih salah satu kategori kejadian.</p>
                </div>

                {{-- Waktu & Tanggal Kejadian --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <div class="section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <span>Waktu Kejadian</span>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="datetime-local" id="incidentTime" name="incident_time" class="form-input-text" required style="flex: 1;">
                        <button type="button" id="btnNow" class="btn-time-now" style="padding: 10px 14px; background: var(--primary-50); color: var(--primary-700); border: 1px solid var(--primary-200); border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; white-space: nowrap;">Sekarang</button>
                    </div>
                </div>

                {{-- Alamat / Patokan Lokasi --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <div class="section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <span>Alamat / Patokan Lokasi</span>
                    </div>
                    <input type="text" id="address" name="address" class="form-input-text" placeholder="Contoh: Jl. Ketintang No. 45, depan mini market..." required>
                </div>

                {{-- Deskripsi --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <div class="section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="17" y1="10" x2="3" y2="10"/>
                                <line x1="21" y1="6" x2="3" y2="6"/>
                                <line x1="21" y1="14" x2="3" y2="14"/>
                                <line x1="17" y1="18" x2="3" y2="18"/>
                            </svg>
                        </div>
                        <span>Deskripsi <em class="optional-tag">opsional</em></span>
                    </div>
                    <textarea id="description" name="description" rows="4"
                        class="form-textarea"
                        placeholder="Ceritakan apa yang terjadi secara singkat — contoh: ada orang tak dikenal mengikuti saya dari Jl. Sudirman ke arah utara..."></textarea>
                    <div class="char-counter"><span id="charCount">0</span> / 500 karakter</div>
                </div>

                {{-- Foto --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <div class="section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </div>
                        <span>Foto Bukti <em class="optional-tag">opsional</em></span>
                    </div>
                    <label class="photo-dropzone" for="photo" id="photoDropzone">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="dropzone-icon">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <span class="dropzone-label">Klik untuk unggah foto</span>
                        <span class="dropzone-hint">JPG, PNG atau WEBP · maks. 5 MB</span>
                        <input type="file" id="photo" name="photo" accept="image/*" hidden>
                    </label>
                    <div id="photoPreviewWrap" class="photo-preview-wrap" style="display:none;">
                        <img id="photoPreview" alt="Preview foto" class="photo-preview-img" src="">
                        <button type="button" id="removePhoto" class="remove-photo-btn" aria-label="Hapus foto">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Lokasi GPS --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <div class="section-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <span>Lokasi Kejadian</span>
                    </div>

                    <div class="location-status" id="locationStatus">
                        <div class="loc-idle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>Lokasi belum ditandai</span>
                        </div>
                        <div class="loc-found" hidden>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span id="coordsText">Lokasi ditemukan</span>
                        </div>
                    </div>

                    <button type="button" id="btnGetLocation" class="btn-gps">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>
                        </svg>
                        <span id="gpsLabel">Dapatkan Lokasi Saya</span>
                    </button>

                    <div id="mapContainer" class="map-container" hidden>
                        <div class="map-hint">Geser penanda pada peta untuk menyesuaikan titik lokasi</div>
                        <div id="map"></div>
                    </div>
                </div>

                {{-- Pelaporan Anonim --}}
                <div class="form-section anonymous-section">
                    <label class="anon-checkbox-label">
                        <input type="checkbox" id="isAnonymous" name="is_anonymous" value="1">
                        <div>
                            <strong>Laporkan Secara Anonim</strong>
                            <p>Nama dan identitas akun Anda tidak akan ditampilkan kepada publik demi keamanan dan privasi.</p>
                        </div>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    Kirim Laporan
                </button>

                <div class="after-submit-links" style="display:flex; justify-content:center; gap:16px; margin-top:14px;">
                    <a href="{{ url('/laporan/riwayat') }}" style="font-size:13px; font-weight:700; color:var(--primary-600); text-decoration:none;">
                        Lihat Riwayat Laporan Saya
                    </a>
                </div>

            </form>
        </div>{{-- /form-card --}}

        {{-- Info Panel --}}
        <aside class="info-panel">
            <div class="info-card">
                <div class="info-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Panduan Pelaporan
                </div>
                <ul class="info-list">
                    <li>Pilih kategori yang paling sesuai dengan kejadian.</li>
                    <li>Tekan <strong>Dapatkan Lokasi Saya</strong> untuk menandai titik kejadian di peta.</li>
                    <li>Tambahkan foto sebagai bukti pendukung jika memungkinkan.</li>
                    <li>Laporan dikirim tanpa mencantumkan identitas pribadi Anda.</li>
                    <li>Jangan melaporkan kejadian saat Anda dalam bahaya — prioritaskan keselamatan.</li>
                </ul>
            </div>

            <div class="info-card info-card--blue">
                <div class="info-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .84h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 8.5a16 16 0 006.29 6.29l1.1-1.1a2 2 0 012.11-.45c.9.35 1.84.6 2.81.7A2 2 0 0122 16.92z"/>
                    </svg>
                    Butuh Bantuan Darurat?
                </div>
                <p class="info-emergency-text">Jika Anda dalam situasi darurat, segera hubungi:</p>
                <div class="emergency-numbers">
                    <a href="tel:110" class="emergency-btn">Polisi: <strong>110</strong></a>
                    <a href="tel:119" class="emergency-btn">Ambulans: <strong>119</strong></a>
                    <a href="tel:112" class="emergency-btn">Darurat Terpadu: <strong>112</strong></a>
                </div>
            </div>
        </aside>

    </div>{{-- /laporkan-content --}}
</div>{{-- /laporkan-page --}}

{{-- Toast Notification --}}
<div id="toast" class="toast" role="alert" aria-live="polite" hidden></div>

<style>
/* ===== Role Restricted Notice ===== */
.role-restricted-card {
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 36px 32px;
    display: flex;
    align-items: center;
    gap: 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    max-width: 820px;
    margin: 20px auto 40px;
}
.rrc-icon-box {
    font-size: 52px;
    line-height: 1;
    flex-shrink: 0;
    width: 88px;
    height: 88px;
    border-radius: 20px;
    display: grid;
    place-items: center;
    background: #eff6ff;
    border: 1.5px solid #bfdbfe;
}
.rrc-content {
    flex: 1;
}
.rrc-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 999px;
    background: #fee2e2;
    color: #b91c1c;
    margin-bottom: 8px;
}
.rrc-content h2 {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
}
.rrc-content p {
    font-size: 14px;
    color: #475569;
    line-height: 1.6;
    margin-bottom: 20px;
}
.rrc-btn-wrap {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.rrc-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 20px;
    background: #1e3a8a;
    color: #ffffff;
    font-weight: 700;
    font-size: 13.5px;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.15s ease;
}
.rrc-btn:hover {
    background: #0f2d59;
    transform: translateY(-1px);
}
.rrc-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 18px;
    background: #f1f5f9;
    color: #334155;
    font-weight: 700;
    font-size: 13.5px;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.15s ease;
}
.rrc-btn-secondary:hover {
    background: #e2e8f0;
}

/* ===== Page Layout ===== */
.laporkan-page {
    max-width: 1140px;
    margin: 0 auto;
    padding: 28px 20px 64px;
}

/* ===== Header ===== */
.laporkan-header {
    margin-bottom: 24px;
}

.laporkan-back-row {
    display: block;
    margin-bottom: 16px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--primary-600);
    text-decoration: none;
    padding: 6px 10px;
    margin-left: -10px;
    border-radius: 8px;
    transition: background-color .15s, color .15s;
}
.back-link:hover {
    background-color: var(--primary-50);
    color: var(--primary-700);
}
.back-link svg { width: 16px; height: 16px; flex-shrink: 0; }

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 10px;
    border-radius: 6px;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    color: #1e40af;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.badge-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #2563eb;
}

.laporkan-header h1 {
    font-size: clamp(22px, 2.8vw, 28px);
    font-weight: 800;
    color: var(--text-heading);
    letter-spacing: -.025em;
    margin-bottom: 6px;
}

.laporkan-header > p {
    font-size: 14px;
    color: var(--text-muted);
    max-width: 580px;
    line-height: 1.6;
}

/* ===== Two-col Layout ===== */
.laporkan-content {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 860px) {
    .laporkan-content { grid-template-columns: 1fr; }
}

/* ===== Form Card ===== */
.form-card {
    background: var(--bg-card);
    border: 1px solid var(--border-soft);
    border-radius: 14px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
}

/* ===== Form Sections ===== */
.form-section {
    margin-bottom: 22px;
}

.form-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 700;
    color: var(--text-heading);
    margin-bottom: 10px;
}

.section-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: var(--primary-50);
    color: var(--primary-600);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.section-icon svg { width: 16px; height: 16px; }

.optional-tag {
    font-weight: 400;
    font-size: 12px;
    color: var(--text-muted);
    font-style: normal;
    margin-left: 4px;
}

/* ===== Category Grid ===== */
.category-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
@media (max-width: 540px) {
    .category-grid { grid-template-columns: repeat(2, 1fr); }
}

.cat-option input { display: none; }

.cat-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 14px 10px;
    border: 2px solid var(--border-soft);
    border-radius: 12px;
    cursor: pointer;
    transition: all .15s;
    text-align: center;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-body);
}
.cat-card:hover {
    border-color: var(--primary-500);
    background: var(--primary-50);
}
.cat-option input:checked ~ .cat-card {
    border-color: var(--primary-600);
    background: var(--primary-50);
    color: var(--primary-700);
    box-shadow: 0 0 0 3px rgba(37,99,235,.12);
}

.cat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
}
.cat-icon svg { width: 20px; height: 20px; }

.cat-icon--red     { background: #fee2e2; color: #dc2626; }
.cat-icon--orange  { background: #ffedd5; color: #ea580c; }
.cat-icon--yellow  { background: #fef9c3; color: #ca8a04; }
.cat-icon--brown   { background: #fdf4e7; color: #92400e; }
.cat-icon--indigo  { background: #eef2ff; color: #4f46e5; }
.cat-icon--blue    { background: var(--primary-50); color: var(--primary-600); }

.form-select {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--border-soft);
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    color: var(--text-body);
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 14px center;
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    transition: border-color .2s, box-shadow .2s;
    box-sizing: border-box;
}
.form-select:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}

.form-input-text {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--border-soft);
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    color: var(--text-body);
    background-color: #fff;
    box-sizing: border-box;
    transition: border-color .2s;
}
.form-input-text:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}

.anonymous-section {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px;
}
.anon-checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    cursor: pointer;
}
.anon-checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--primary-600);
    margin-top: 2px;
    cursor: pointer;
}
.anon-checkbox-label strong {
    display: block;
    font-size: 13.5px;
    color: var(--text-heading);
    margin-bottom: 2px;
}
.anon-checkbox-label p {
    font-size: 12px;
    color: var(--text-muted);
    margin: 0;
    line-height: 1.4;
}

.field-error {
    font-size: 12px;
    color: #dc2626;
    margin-top: 6px;
}

/* ===== Textarea ===== */
.form-textarea {
    width: 100%;
    border: 1.5px solid var(--border-soft);
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 14px;
    font-family: inherit;
    color: var(--text-body);
    resize: vertical;
    transition: border-color .2s;
    box-sizing: border-box;
}
.form-textarea:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}

.char-counter {
    text-align: right;
    font-size: 11.5px;
    color: var(--text-muted);
    margin-top: 4px;
}

/* ===== Photo Dropzone ===== */
.photo-dropzone {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 28px 20px;
    border: 2px dashed var(--border-soft);
    border-radius: 12px;
    cursor: pointer;
    transition: all .2s;
    background: #fafafa;
}
.photo-dropzone:hover {
    border-color: var(--primary-500);
    background: var(--primary-50);
}

.dropzone-icon { width: 40px; height: 40px; color: var(--text-muted); }
.dropzone-label { font-size: 14px; font-weight: 600; color: var(--text-heading); }
.dropzone-hint  { font-size: 12px; color: var(--text-muted); }

.photo-preview-wrap {
    position: relative;
    margin-top: 12px;
    display: inline-block;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid var(--border-soft);
}
.photo-preview-img {
    display: block;
    max-height: 200px;
    max-width: 100%;
    object-fit: cover;
}
.remove-photo-btn {
    position: absolute;
    top: 8px; right: 8px;
    width: 28px; height: 28px;
    border-radius: 50%;
    background: rgba(0,0,0,.55);
    border: none;
    color: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
}
.remove-photo-btn svg { width: 14px; height: 14px; }

/* ===== Location ===== */
.location-status {
    padding: 12px 14px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid var(--border-soft);
    font-size: 13px;
    margin-bottom: 12px;
}
.loc-idle, .loc-found {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
}
.loc-found { color: var(--success-600); font-weight: 600; }
.loc-idle svg, .loc-found svg { width: 16px; height: 16px; flex-shrink: 0; }

.btn-gps {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 40px;
    padding: 0 16px;
    border-radius: 8px;
    background: var(--primary-600);
    color: #fff;
    font-size: 13.5px;
    font-weight: 600;
    font-family: inherit;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    transition: background .15s;
}
.btn-gps:hover { background: var(--primary-700); }
.btn-gps svg { width: 16px; height: 16px; flex-shrink: 0; }

.map-container {
    margin-top: 16px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--border-soft);
}
.map-hint {
    padding: 8px 14px;
    background: #eff6ff;
    border-bottom: 1px solid #dbeafe;
    font-size: 12.5px;
    color: #1e40af;
    font-weight: 500;
}
#map { height: 300px; width: 100%; }

/* ===== Submit ===== */
.btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    height: 44px;
    padding: 0 20px;
    border-radius: 10px;
    background: #2563eb;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    transition: background-color .15s;
    margin-top: 8px;
}
.btn-submit:hover { background: #1e40af; }
.btn-submit:disabled { opacity: .6; cursor: not-allowed; }
.btn-submit svg { width: 17px; height: 17px; flex-shrink: 0; }

.demo-notice {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    color: var(--text-muted);
    text-align: center;
    justify-content: center;
    margin-top: 10px;
}
.demo-notice svg { width: 13px; height: 13px; flex-shrink: 0; }

/* ===== Info Panel ===== */
.info-panel { display: flex; flex-direction: column; gap: 16px; }

.info-card {
    background: var(--bg-card);
    border: 1px solid var(--border-soft);
    border-radius: 14px;
    padding: 20px;
    box-shadow: var(--shadow-sm);
}
.info-card--blue {
    background: #1e40af;
    border-color: #1e40af;
    color: #fff;
}

.info-card-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: var(--text-heading);
    margin-bottom: 12px;
}
.info-card--blue .info-card-title { color: #fff; }
.info-card-title svg { width: 16px; height: 16px; flex-shrink: 0; }

.info-list {
    list-style: none;
    padding: 0; margin: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.info-list li {
    padding-left: 20px;
    position: relative;
    font-size: 13px;
    color: var(--text-body);
    line-height: 1.5;
}
.info-list li::before {
    content: "•";
    position: absolute;
    left: 0;
    color: var(--primary-500);
    font-weight: 700;
}

.info-emergency-text {
    font-size: 13px;
    color: rgba(255,255,255,.8);
    margin-bottom: 10px;
}

.emergency-numbers { display: flex; flex-direction: column; gap: 8px; }

.emergency-btn {
    display: flex;
    align-items: center;
    padding: 10px 14px;
    border-radius: 10px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff;
    font-size: 13px;
    text-decoration: none;
    transition: background .2s;
}
.emergency-btn:hover { background: rgba(255,255,255,.22); }
.emergency-btn strong { margin-left: auto; font-size: 15px; }

/* ===== Toast ===== */
.toast {
    position: fixed;
    bottom: 28px;
    left: 50%;
    transform: translateX(-50%);
    padding: 14px 24px;
    border-radius: 12px;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    z-index: 9999;
    box-shadow: 0 10px 30px rgba(0,0,0,.2);
    animation: slideUp .3s ease;
}
@keyframes slideUp {
    from { transform: translateX(-50%) translateY(20px); opacity: 0; }
    to   { transform: translateX(-50%) translateY(0); opacity: 1; }
}
</style>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const $ = id => document.getElementById(id);
    let incidentMarker = null;

    // Pengecekan Hak Akses Role: Hanya Masyarakat yang membuat laporan warga
    const activeRole = localStorage.getItem('saferoad_active_role') || 'masyarakat';
    if (activeRole === 'keamanan' || activeRole === 'admin') {
        const formContent = document.querySelector('.laporkan-content');
        const notice = $('roleRestrictedNotice');
        if (formContent) formContent.style.display = 'none';
        if (notice) notice.style.display = 'flex';

        if (activeRole === 'keamanan') {
            $('rrcBadge').textContent = 'PERAN: PETUGAS KEAMANAN';
            $('rrcBadge').style.background = '#eff6ff';
            $('rrcBadge').style.color = '#1e40af';
            $('rrcTitle').textContent = 'Petugas Keamanan Tidak Membuat Laporan Warga';
            $('rrcDesc').innerHTML = 'Sebagai <strong>Petugas Keamanan</strong>, fungsi utama Anda adalah meninjau laporan masuk dari masyarakat, melakukan verifikasi TKP, dan menandai status penanganan.<br>Silakan buka Dashboard Keamanan untuk melihat daftar laporan yang perlu diperiksa.';
            $('rrcBtnAction').href = "{{ route('keamanan.dashboard') }}";
            $('rrcBtnAction').textContent = 'Buka Dashboard Tugas Keamanan';
        } else if (activeRole === 'admin') {
            $('rrcBadge').textContent = 'PERAN: ADMINISTRATOR';
            $('rrcBadge').style.background = '#eff6ff';
            $('rrcBadge').style.color = '#1e40af';
            $('rrcTitle').textContent = 'Administrator Tidak Membuat Laporan Warga';
            $('rrcDesc').innerHTML = 'Sebagai <strong>Administrator</strong>, fungsi utama Anda adalah memvalidasi, menyetujui, atau menolak laporan masyarakat pada modul moderasi sistem.<br>Silakan buka Dashboard Admin untuk mengelola validasi laporan.';
            $('rrcBtnAction').href = "{{ route('admin.dashboard') }}";
            $('rrcBtnAction').textContent = 'Buka Konsol Moderasi Admin';
        }
        return; // Hentikan inisialisasi peta dan listener form jika bukan masyarakat
    }

    /* ---- Toast ---- */
    function showToast(msg, type = 'success') {
        const toast = $('toast');
        toast.textContent = msg;
        toast.style.background = type === 'error' ? '#dc2626' : '#2563eb';
        toast.hidden = false;
        clearTimeout(toast._t);
        toast._t = setTimeout(() => { toast.hidden = true; }, 4000);
    }

    /* ---- Waktu Kejadian Otomatis ---- */
    let userModifiedTime = false;
    function formatDateTimeLocal(d) {
        const pad = n => String(n).padStart(2, '0');
        const year = d.getFullYear();
        const month = pad(d.getMonth() + 1);
        const day = pad(d.getDate());
        const hours = pad(d.getHours());
        const minutes = pad(d.getMinutes());
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    function updateTimeLimitsAndDefault() {
        const now = new Date();
        const sevenDaysAgo = new Date(now.getTime() - (7 * 24 * 60 * 60 * 1000));
        const incidentTimeInput = $('incidentTime');
        if (incidentTimeInput) {
            incidentTimeInput.max = formatDateTimeLocal(now);
            incidentTimeInput.min = formatDateTimeLocal(sevenDaysAgo);
            if (!userModifiedTime) {
                incidentTimeInput.value = formatDateTimeLocal(now);
            }
        }
    }
    updateTimeLimitsAndDefault();
    const timeInterval = setInterval(updateTimeLimitsAndDefault, 60000);

    const incidentTimeEl = $('incidentTime');
    if (incidentTimeEl) {
        incidentTimeEl.addEventListener('input', () => {
            userModifiedTime = true;
        });
    }

    const btnNow = $('btnNow');
    if (btnNow) {
        btnNow.addEventListener('click', () => {
            userModifiedTime = false;
            updateTimeLimitsAndDefault();
            showToast('Waktu kejadian disetel ke waktu sekarang', 'success');
        });
    }

    /* ---- Char Counter ---- */
    const textarea = $('description');
    textarea.addEventListener('input', () => {
        const len = textarea.value.length;
        $('charCount').textContent = len;
        if (len > 500) textarea.value = textarea.value.slice(0, 500);
    });

    /* ---- Photo Preview ---- */
    $('photo').addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => {
            $('photoPreview').src = ev.target.result;
            $('photoPreviewWrap').style.display = 'inline-block';
            $('photoDropzone').style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    $('removePhoto').addEventListener('click', () => {
        $('photo').value = '';
        $('photoPreview').src = '';
        $('photoPreviewWrap').style.display = 'none';
        $('photoDropzone').style.display = '';
    });

    /* ---- GPS ---- */
    $('btnGetLocation').addEventListener('click', async () => {
        const btn = $('btnGetLocation');
        const label = $('gpsLabel');
        label.textContent = 'Mendapatkan lokasi…';
        btn.disabled = true;

        try {
            const pos = await new Promise((res, rej) => {
                if (!navigator.geolocation) return rej('GPS tidak tersedia di browser ini.');
                navigator.geolocation.getCurrentPosition(res, e => rej(e.message));
            });
            const { latitude: lat, longitude: lng } = pos.coords;

            if (!window.incidentMap) {
                window.incidentMap = L.map('map').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(incidentMap);
            } else {
                incidentMap.setView([lat, lng], 15);
            }

            if (incidentMarker) incidentMap.removeLayer(incidentMarker);
            incidentMarker = L.marker([lat, lng], { draggable: true })
                .addTo(incidentMap)
                .bindPopup('Titik kejadian — seret untuk menyesuaikan')
                .openPopup();

            $('mapContainer').hidden = false;
            $('locationStatus').querySelector('.loc-idle').hidden = true;
            $('locationStatus').querySelector('.loc-found').hidden = false;
            $('coordsText').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            label.textContent = 'Perbarui Lokasi';
        } catch (err) {
            showToast('Gagal mendapatkan lokasi: ' + err, 'error');
            label.textContent = 'Dapatkan Lokasi Saya';
        } finally {
            btn.disabled = false;
        }
    });

    /* ---- Submit ---- */
    $('incidentForm').addEventListener('submit', async e => {
        e.preventDefault();

        const category = $('category');
        if (!category.value) {
            $('cat-error').hidden = false;
            category.focus();
            return;
        }
        $('cat-error').hidden = true;

        const btn = $('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = `<svg style="width:18px;height:18px;animation:spin 1s linear infinite" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Mengirim…`;

        const payload = {
            category: category.value,
            address: $('address').value || 'Lokasi Titik Peta',
            description: $('description').value || 'Laporan kejadian tanpa deskripsi detail.',
            incident_time: $('incidentTime').value || new Date().toISOString(),
            is_anonymous: $('isAnonymous').checked ? 1 : 0,
            latitude: incidentMarker ? incidentMarker.getLatLng().lat : null,
            longitude: incidentMarker ? incidentMarker.getLatLng().lng : null,
        };

        let reportResult = null;
        try {
            const resp = await fetch('{{ url("/laporan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });
            const data = await resp.json();
            if (resp.ok && data.success) {
                reportResult = data.data;
                showToast(data.message || `Laporan ${reportResult.report_code} terkirim.`, 'success');
            } else {
                const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Gagal mengirim');
                throw new Error(errMsg);
            }
        } catch (err) {
            showToast(err.message || 'Gagal mengirim laporan.', 'error');
        }

        // Simpan ke riwayat lokal jika sukses
        if (reportResult) {
            try {
                const list = JSON.parse(localStorage.getItem('saferoad_my_reports') || '[]');
                list.unshift(reportResult);
                localStorage.setItem('saferoad_my_reports', JSON.stringify(list));
            } catch (e) {}
        }

        $('incidentForm').reset();
        userModifiedTime = false;
        updateTimeLimitsAndDefault();
        $('photoPreview').src = '';
        $('photoPreviewWrap').style.display = 'none';
        $('photoDropzone').style.display = '';
        if (incidentMarker) { incidentMap.removeLayer(incidentMarker); incidentMarker = null; }
        $('mapContainer').hidden = true;
        $('locationStatus').querySelector('.loc-idle').hidden = false;
        $('locationStatus').querySelector('.loc-found').hidden = true;
        $('charCount').textContent = '0';

        btn.disabled = false;
        btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Kirim Laporan`;
        btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Kirim Laporan`;
    });
})();
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush
