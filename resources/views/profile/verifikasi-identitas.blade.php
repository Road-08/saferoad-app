
@extends('layouts.app')

@section('title', 'Verifikasi Identitas - SafeRoad')

@section('content')
<div class="verification-page">

    <header class="verification-header">
        <a href="{{ route('profile.index') }}" class="back-button">‹</a>
        <div>
            <p>Pengaturan Akun</p>
            <h1>Verifikasi Identitas</h1>
        </div>
    </header>

    <div class="verification-intro">
        <div class="intro-icon">♧</div>
        <h2>Kenali Pengguna SafeRoad</h2>
        <p>
            Lengkapi informasi identitasmu untuk membantu kami
            menciptakan lingkungan perjalanan yang lebih aman.
        </p>
    </div>

    <form id="verificationForm" action="#" method="POST"
          enctype="multipart/form-data">

        @csrf

        <section class="verification-card">
            <div class="section-heading">
                <span class="step-number">01</span>
                <div>
                    <h3>Dokumen Identitas</h3>
                    <p>Unggah foto atau file KTP kamu</p>
                </div>
            </div>

            <label for="ktp" class="upload-area">
                <div class="upload-icon">↑</div>
                <strong id="uploadTitle">Pilih file KTP</strong>
                <span id="uploadDescription">
                    Klik untuk mengunggah dokumen
                </span>
                <small>JPG, PNG, atau PDF · Maksimal 5 MB</small>
            </label>

            <input
                type="file"
                id="ktp"
                name="ktp"
                accept=".jpg,.jpeg,.png,.pdf"
                hidden
            >

            <p class="upload-error" id="uploadError"></p>
        </section>

        <section class="verification-card">
            <div class="section-heading">
                <span class="step-number">02</span>
                <div>
                    <h3>Informasi Gender</h3>
                    <p>Pilih informasi gender kamu</p>
                </div>
            </div>

            <label for="gender" class="field-label">
                Gender
            </label>

            <select name="gender" id="gender" required>
                <option value="">Pilih gender</option>
                <option value="perempuan">Perempuan</option>
                <option value="laki-laki">Laki-laki</option>
                <option value="lainnya">Lainnya</option>
                <option value="tidak_menyebutkan">
                    Tidak ingin menyebutkan
                </option>
            </select>
        </section>

        <section class="consent-card">
            <div class="consent-heading">
                <span class="consent-icon">♧</span>
                <div>
                    <h3>Persetujuan Penggunaan Data</h3>
                    <p>Privasi kamu tetap menjadi prioritas kami.</p>
                </div>
            </div>

            <label class="consent-option">
                <input
                    type="checkbox"
                    name="gender_consent"
                    id="genderConsent"
                    value="1"
                    required
                >
                <span>
                    Saya menyetujui penggunaan data gender saya
                    dalam bentuk anonim untuk keperluan statistik
                    dan pengembangan layanan SafeRoad.
                </span>
            </label>
        </section>

        <button type="submit" class="submit-button">
            Simpan Verifikasi
        </button>

        <p class="privacy-note">
            Data identitas kamu harus dikelola secara aman dan
            hanya digunakan sesuai tujuan yang dijelaskan.
        </p>

    </form>
</div>
@endsection
