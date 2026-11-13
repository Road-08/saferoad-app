@extends('layouts.app')

@section('title', 'Verifikasi Identitas · SafeRoad')

@section('content')
<div class="verify-wrapper">
    <!-- Breadcrumb & Tombol Kembali -->
    <div class="nav-back-row">
        <a href="{{ route('profile.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Profil</span>
        </a>
    </div>

    <!-- Header Verifikasi -->
    <header class="verify-header">
        <div class="verify-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <span>VERIFIKASI KEAMANAN</span>
        </div>
        <h1>Verifikasi Identitas Pengguna</h1>
        <p>Lengkapi informasi identitas Anda untuk membantu menciptakan komunitas perjalanan yang lebih aman, terpercaya, dan akuntabel.</p>
    </header>

    @if (session('success'))
        <div class="alert-box success" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <div>
                <strong>Berhasil!</strong>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <form id="verificationForm" action="#" method="POST" enctype="multipart/form-data" class="verify-form" onsubmit="event.preventDefault(); alert('Verifikasi identitas Anda telah berhasil dikirim untuk proses peninjauan sistem SafeRoad.');">
        @csrf

        <!-- Langkah 1: Unggah Dokumen KTP -->
        <section class="step-card">
            <div class="step-card-header">
                <div class="step-circle">01</div>
                <div>
                    <h2>Unggah Dokumen KTP</h2>
                    <p>Pastikan foto dokumen KTP terlihat jelas, tidak buram, dan nama sesuai akun.</p>
                </div>
            </div>

            <label for="ktp" class="dropzone-area" id="dropzoneLabel">
                <div class="dropzone-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                </div>
                <strong class="dropzone-title" id="uploadTitle">Pilih berkas atau seret ke sini</strong>
                <span class="dropzone-desc" id="uploadDescription">Format yang didukung: JPG, PNG, atau PDF</span>
                <span class="dropzone-limit">Ukuran berkas maksimal 5 MB</span>

                <div id="filePreviewBox" class="file-preview-box" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <span id="previewFileName">nama_file.jpg</span>
                    <span class="preview-badge-ok">Terpilih</span>
                </div>
            </label>

            <input type="file" id="ktp" name="ktp" accept=".jpg,.jpeg,.png,.pdf" required style="display: none;">
            <p class="field-error-notice" id="uploadError" style="display: none;"></p>
        </section>

        <!-- Langkah 2: Informasi Gender -->
        <section class="step-card">
            <div class="step-card-header">
                <div class="step-circle">02</div>
                <div>
                    <h2>Informasi Preferensi Gender</h2>
                    <p>Digunakan untuk mengoptimalkan rekomendasi rute perlindungan Women's Zone dan statistik keselamatan.</p>
                </div>
            </div>

            <div class="form-group-field">
                <label for="gender">Pilih Gender</label>
                <div class="select-wrapper">
                    <select name="gender" id="gender" required>
                        <option value="">-- Pilih salah satu --</option>
                        <option value="perempuan">Perempuan</option>
                        <option value="laki-laki">Laki-laki</option>
                        <option value="lainnya">Lainnya</option>
                        <option value="tidak_menyebutkan">Tidak ingin menyebutkan</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- Langkah 3: Persetujuan Penggunaan Data -->
        <section class="step-card consent-highlight">
            <div class="step-card-header">
                <div class="step-circle" style="background-color: var(--primary-600); color: #fff;">03</div>
                <div>
                    <h2>Persetujuan Penggunaan Data</h2>
                    <p>Privasi dan perlindungan data pribadi Anda adalah prioritas utama SafeRoad.</p>
                </div>
            </div>

            <label class="consent-checkbox-wrap" for="genderConsent">
                <input type="checkbox" name="gender_consent" id="genderConsent" value="1" required>
                <div>
                    <strong>Saya menyetujui pemrosesan data identitas untuk verifikasi keamanan</strong>
                    <p>Data identitas Anda tidak akan pernah dipublikasikan secara terbuka atau dijual ke pihak ketiga. Data semata-mata digunakan untuk validasi akun dan peningkatan akurasi penilaian rute aman.</p>
                </div>
            </label>
        </section>

        <!-- Tombol Aksi Simpan -->
        <div class="action-submit-row">
            <button type="submit" class="btn-submit-verify" id="btnSubmitVerify">
                <span>Kirim Dokumen Verifikasi</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </div>
    </form>
</div>

<style>
    .verify-wrapper {
        width: min(100% - 32px, 780px);
        margin: 32px auto 60px;
    }

    .nav-back-row {
        margin-bottom: 20px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--primary-600);
        transition: transform 0.15s ease;
    }

    .btn-back:hover {
        transform: translateX(-3px);
    }

    .btn-back svg {
        width: 16px;
        height: 16px;
    }

    .verify-header {
        margin-bottom: 28px;
    }

    .verify-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background-color: var(--primary-50);
        border: 1px solid var(--primary-100);
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 800;
        color: var(--primary-700);
        margin-bottom: 12px;
    }

    .verify-badge svg {
        width: 14px;
        height: 14px;
    }

    .verify-header h1 {
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 800;
        color: var(--text-heading);
        margin-bottom: 8px;
        letter-spacing: -0.03em;
    }

    .verify-header p {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .verify-form {
        display: grid;
        gap: 20px;
    }

    /* Kartu Langkah */
    .step-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 18px;
        padding: 24px;
        box-shadow: var(--shadow-sm);
    }

    .step-card-header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 20px;
    }

    .step-circle {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background-color: var(--primary-50);
        color: var(--primary-600);
        font-size: 14px;
        font-weight: 800;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .step-card h2 {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-heading);
        margin-bottom: 4px;
    }

    .step-card p {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* Area Unggah Dropzone */
    .dropzone-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 32px 24px;
        border: 2px dashed var(--primary-500);
        border-radius: 14px;
        background-color: var(--primary-50);
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: center;
    }

    .dropzone-area:hover {
        background-color: #e0f2fe;
        border-color: var(--primary-600);
    }

    .dropzone-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background-color: #ffffff;
        color: var(--primary-600);
        display: grid;
        place-items: center;
        margin-bottom: 12px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }

    .dropzone-icon svg {
        width: 24px;
        height: 24px;
    }

    .dropzone-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-heading);
        margin-bottom: 4px;
    }

    .dropzone-desc {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-bottom: 4px;
    }

    .dropzone-limit {
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
    }

    .file-preview-box {
        margin-top: 14px;
        padding: 8px 16px;
        background-color: #ffffff;
        border: 1px solid var(--border-soft);
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 12.5px;
        color: var(--primary-900);
        font-weight: 600;
    }

    .file-preview-box svg {
        width: 16px;
        height: 16px;
        color: var(--primary-600);
    }

    .preview-badge-ok {
        background-color: var(--success-100);
        color: var(--success-600);
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 10.5px;
        font-weight: 700;
    }

    .field-error-notice {
        margin-top: 8px;
        font-size: 12px;
        color: var(--danger-600);
        font-weight: 600;
    }

    /* Form Field */
    .form-group-field {
        display: grid;
        gap: 8px;
    }

    .form-group-field label {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
    }

    .select-wrapper select {
        width: 100%;
        height: 48px;
        padding: 0 16px;
        background-color: #ffffff;
        border: 1.5px solid var(--border-soft);
        border-radius: 11px;
        font-size: 14px;
        color: var(--text-heading);
        outline: none;
        transition: border-color 0.15s ease;
    }

    .select-wrapper select:focus {
        border-color: var(--primary-600);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    /* Consent Checkbox */
    .consent-highlight {
        background-color: #f8fafc;
        border: 1.5px solid #cbd5e1;
    }

    .consent-checkbox-wrap {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        cursor: pointer;
    }

    .consent-checkbox-wrap input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-top: 3px;
        accent-color: var(--primary-600);
        cursor: pointer;
        flex-shrink: 0;
    }

    .consent-checkbox-wrap strong {
        display: block;
        font-size: 13.5px;
        color: var(--text-heading);
        margin-bottom: 4px;
    }

    .consent-checkbox-wrap p {
        font-size: 12.5px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* Tombol Aksi */
    .action-submit-row {
        margin-top: 10px;
    }

    .btn-submit-verify {
        width: 100%;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-size: 14.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        box-shadow: var(--shadow-btn);
        transition: transform 0.15s ease, background 0.15s ease;
    }

    .btn-submit-verify:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        transform: translateY(-1px);
    }

    .btn-submit-verify svg {
        width: 18px;
        height: 18px;
    }
</style>

<script>
    const ktpInput = document.getElementById('ktp');
    const uploadTitle = document.getElementById('uploadTitle');
    const uploadDesc = document.getElementById('uploadDescription');
    const previewBox = document.getElementById('filePreviewBox');
    const previewName = document.getElementById('previewFileName');
    const uploadError = document.getElementById('uploadError');

    ktpInput.addEventListener('change', function () {
        uploadError.style.display = 'none';
        uploadError.textContent = '';

        if (!this.files || !this.files[0]) {
            previewBox.style.display = 'none';
            uploadTitle.style.display = 'block';
            uploadDesc.style.display = 'block';
            return;
        }

        const file = this.files[0];
        const allowed = ['image/jpeg', 'image/png', 'application/pdf'];
        const maxSize = 5 * 1024 * 1024; // 5 MB

        if (!allowed.includes(file.type)) {
            uploadError.textContent = 'Format berkas tidak valid. Gunakan file JPG, PNG, atau PDF.';
            uploadError.style.display = 'block';
            this.value = '';
            previewBox.style.display = 'none';
            return;
        }

        if (file.size > maxSize) {
            uploadError.textContent = 'Ukuran berkas melebihi 5 MB. Harap perkecil ukuran berkas Anda.';
            uploadError.style.display = 'block';
            this.value = '';
            previewBox.style.display = 'none';
            return;
        }

        // Tampilkan preview nama berkas
        previewName.textContent = file.name;
        previewBox.style.display = 'inline-flex';
        uploadTitle.style.display = 'none';
        uploadDesc.style.display = 'none';
    });
</script>
@endsection
