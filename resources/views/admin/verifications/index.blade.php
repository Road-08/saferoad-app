@extends('auth.layout')

@section('title', 'Verifikasi Identitas')

@section('form-content')
<div class="verification-page">
    <span class="mini-label">SAFEROAD ADMIN</span>

    <h2>Verifikasi identitas</h2>

    <p class="verification-intro">
        Periksa dokumen secara terbatas. Setelah keputusan
        disimpan, sistem menghapus file KTP dan mempertahankan
        status verifikasi serta jenis kelamin hasil pemeriksaan.
    </p>

    @if ($errors->any())
        <div class="notice notice-warning" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('status'))
        <div class="notice notice-success" role="status">
            {{ session('status') }}
        </div>
    @endif

    @if ($users->isEmpty())
        <div class="notice">
            Tidak ada pendaftaran yang menunggu verifikasi
            saat ini.
        </div>
    @else
        <div class="verification-list">
            @foreach ($users as $pendingUser)
                <article class="verification-item">
                    <h3>{{ $pendingUser->name }}</h3>

                    <p>{{ $pendingUser->email }}</p>

                    <small>
                        Terdaftar
                        {{ $pendingUser->created_at?->format('d M Y, H:i') ?? '-' }}
                    </small>

                    <a
                        class="document-link"
                        href="{{ route(
                            'admin.verifications.ktp',
                            $pendingUser
                        ) }}"
                    >
                        Unduh dokumen KTP (akses admin)
                    </a>

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.verifications.approve',
                            $pendingUser
                        ) }}"
                    >
                        @csrf

                        <label
                            class="field"
                            for="verified_gender_{{ $pendingUser->id }}"
                        >
                            <span>Jenis kelamin sesuai KTP</span>

                            <select
                                id="verified_gender_{{ $pendingUser->id }}"
                                name="verified_gender"
                                required
                            >
                                <option value="">
                                    Pilih hasil pemeriksaan
                                </option>

                                <option value="female">
                                    Perempuan
                                </option>

                                <option value="male">
                                    Laki-laki
                                </option>
                            </select>
                        </label>

                        <button class="primary" type="submit">
                            Setujui verifikasi
                        </button>
                    </form>

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.verifications.reject',
                            $pendingUser
                        ) }}"
                    >
                        @csrf

                        <label
                            class="field"
                            for="verification_note_{{ $pendingUser->id }}"
                        >
                            <span>Catatan penolakan (opsional)</span>

                            <input
                                class="plain-input"
                                id="verification_note_{{ $pendingUser->id }}"
                                name="verification_note"
                                maxlength="1000"
                                placeholder="Contoh: dokumen tidak terbaca"
                            >
                        </label>

                        <button
                            class="secondary-button"
                            type="submit"
                        >
                            Tolak dan hapus KTP
                        </button>
                    </form>
                </article>
            @endforeach
        </div>
    @endif

    <a
        class="back-link"
        href="{{ route('admin.dashboard') }}"
    >
        ← Kembali ke dashboard admin
    </a>
</div>
@endsection
