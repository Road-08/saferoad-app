```blade
@extends('auth.layout')

@section('title', 'Pengelolaan Profil')

@section('form-content')

<div class="auth-heading">
    <span class="eyebrow">SAFEROAD ACCOUNT</span>

    <h2>Pengelolaan Profil</h2>

    <p>
        Kelola informasi akun dan preferensi privasimu
        dengan mudah melalui halaman profil SafeRoad.
    </p>
</div>

{{-- Pesan berhasil --}}
@if (session('success'))
    <div class="notice notice-success" role="status">
        {{ session('success') }}
    </div>
@endif

{{-- Pesan kesalahan --}}
@if ($errors->any())
    <div class="notice notice-error" role="alert">
        <p>Periksa kembali data yang kamu masukkan:</p>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- INFORMASI PROFIL --}}
<section class="profile-section">
    <h3>Informasi Profil</h3>

    <p class="profile-description">
        Pastikan informasi akunmu selalu sesuai.
    </p>

    <form
        method="POST"
        action="{{ route('profile.update') }}"
    >
        @csrf
        @method('PUT')

        {{-- Nama lengkap --}}
        <div class="field">
            <label for="name">Nama lengkap</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user->name) }}"
                maxlength="255"
                autocomplete="name"
                required
            >
        </div>

        {{-- Email --}}
        <div class="field">
            <label for="email">Alamat email</label>

            <input
                type="email"
                id="email"
                value="{{ $user->email }}"
                autocomplete="email"
                disabled
            >

            <small>
                Alamat email belum dapat diubah melalui halaman ini.
            </small>
        </div>

        {{-- Jenis kelamin --}}
        <div class="field">
            <label for="gender">Jenis kelamin</label>

            <select id="gender" name="gender">
                <option
                    value=""
                    @selected(
                        old('gender', $user->gender ?? '') === ''
                    )
                >
                    Pilih jenis kelamin
                </option>

                <option
                    value="male"
                    @selected(
                        old('gender', $user->gender ?? '') === 'male'
                    )
                >
                    Laki-laki
                </option>

                <option
                    value="female"
                    @selected(
                        old('gender', $user->gender ?? '') === 'female'
                    )
                >
                    Perempuan
                </option>

                <option
                    value="prefer_not_to_say"
                    @selected(
                        old('gender', $user->gender ?? '')
                        === 'prefer_not_to_say'
                    )
                >
                    Memilih untuk tidak menyebutkan
                </option>
            </select>

            <small>
                Pilihan ini merupakan informasi profil dan tidak
                otomatis mengubah hasil verifikasi identitas.
            </small>
        </div>

        <button type="submit" class="primary">
            Simpan Perubahan Profil
        </button>
    </form>
</section>

{{-- PERSETUJUAN DATA GENDER --}}
<section class="profile-section">
    <h3>Persetujuan Data Gender</h3>

    <p class="profile-description">
        Atur persetujuan penggunaan data gender untuk keperluan
        statistik sesuai tujuan dan kebijakan privasi SafeRoad.
        Kamu dapat menarik persetujuan kapan saja.
    </p>

    {{-- Status persetujuan yang tersimpan di database --}}
    <div class="consent-status">
        <span>Status persetujuan:</span>

        <strong>
            {{ $user->gender_statistics_consent
                ? 'Disetujui'
                : 'Belum disetujui' }}
        </strong>
    </div>

    <form
        method="POST"
        action="{{ route('profile.consent.update') }}"
    >
        @csrf
        @method('PATCH')

        {{-- Nilai 0 dikirim jika checkbox tidak dicentang --}}
        <input
            type="hidden"
            name="gender_statistics_consent"
            value="0"
        >

        <label class="consent-option">
            <input
                type="checkbox"
                id="gender_statistics_consent"
                name="gender_statistics_consent"
                value="1"
                @checked(
                    (bool) old(
                        'gender_statistics_consent',
                        $user->gender_statistics_consent
                    )
                )
            >

            <span>
                Saya menyetujui penggunaan data gender untuk
                keperluan statistik sesuai tujuan yang telah dijelaskan.
            </span>
        </label>

        <button type="submit" class="primary">
            Simpan Persetujuan
        </button>
    </form>
</section>

{{-- KEMBALI KE DASHBOARD SESUAI ROLE --}}
<div class="profile-navigation">
    @if ($user->role === 'admin')
        <a class="profile-back" href="{{ route('admin.dashboard') }}">
            &larr; Kembali ke Dashboard
        </a>
    @elseif ($user->role === 'security')
        <a class="profile-back" href="{{ route('security.dashboard') }}">
            &larr; Kembali ke Dashboard
        </a>
    @else
        <a class="profile-back" href="{{ route('dashboard') }}">
            &larr; Kembali ke Dashboard
        </a>
    @endif
</div>

@endsection
```
