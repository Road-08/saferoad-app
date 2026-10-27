@extends('auth.layout')

@section('title', 'Daftar')

@section('form-content')
<header class="form-header">
    <span class="mini-label">SAFEROAD ACCOUNT</span>

    <h2>Buat akun SafeRoad</h2>

    <p>
        Lengkapi data untuk bergabung dengan ekosistem
        perjalanan aman.
    </p>
</header>

<form
    method="POST"
    action="{{ route('register.store') }}"
    enctype="multipart/form-data"
>
    @csrf

    <label class="field" for="name">
        <span>Nama lengkap</span>

        <div class="input-wrap">
            @include('auth.partials.icon', [
                'name' => 'user'
            ])

            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                placeholder="Masukkan nama lengkap"
                autocomplete="name"
                maxlength="255"
                required
                autofocus
                aria-describedby="name-error"
            >
        </div>

        @error('name')
            <small class="field-error" id="name-error">
                {{ $message }}
            </small>
        @enderror
    </label>

    <label class="field" for="email">
        <span>Email</span>

        <div class="input-wrap">
            @include('auth.partials.icon', [
                'name' => 'mail'
            ])

            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                placeholder="nama@email.com"
                autocomplete="email"
                maxlength="255"
                required
                aria-describedby="email-error"
            >
        </div>

        @error('email')
            <small class="field-error" id="email-error">
                {{ $message }}
            </small>
        @enderror
    </label>

    <label class="field" for="password">
        <span>Kata sandi</span>

        <div class="input-wrap">
            @include('auth.partials.icon', [
                'name' => 'lock'
            ])

            <input
                id="password"
                name="password"
                type="password"
                placeholder="Minimal 8 karakter"
                autocomplete="new-password"
                minlength="8"
                required
                aria-describedby="password-error"
            >

            <button
                type="button"
                class="eye"
                data-password-toggle="password"
                aria-label="Tampilkan kata sandi"
                aria-pressed="false"
            >
                @include('auth.partials.icon', [
                    'name' => 'eye'
                ])
            </button>
        </div>

        @error('password')
            <small class="field-error" id="password-error">
                {{ $message }}
            </small>
        @enderror
    </label>

    <label class="field" for="password_confirmation">
        <span>Konfirmasi kata sandi</span>

        <div class="input-wrap">
            @include('auth.partials.icon', [
                'name' => 'lock'
            ])

            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                placeholder="Ulangi kata sandi"
                autocomplete="new-password"
                minlength="8"
                required
            >

            <button
                type="button"
                class="eye"
                data-password-toggle="password_confirmation"
                aria-label="Tampilkan konfirmasi kata sandi"
                aria-pressed="false"
            >
                @include('auth.partials.icon', [
                    'name' => 'eye'
                ])
            </button>
        </div>
    </label>

    <label class="field" for="gender">
        <span>
            Jenis kelamin
            <small class="optional">(opsional)</small>
        </span>

        <select id="gender" name="gender">
            <option
                value=""
                {{ old('gender', '') === '' ? 'selected' : '' }}
            >
                Pilih jenis kelamin (opsional)
            </option>

            <option
                value="female"
                {{ old('gender') === 'female' ? 'selected' : '' }}
            >
                Perempuan
            </option>

            <option
                value="male"
                {{ old('gender') === 'male' ? 'selected' : '' }}
            >
                Laki-laki
            </option>

            <option
                value="prefer_not_to_say"
                {{ old('gender') === 'prefer_not_to_say' ? 'selected' : '' }}
            >
                Tidak ingin menyebutkan
            </option>
        </select>

        @error('gender')
            <small class="field-error" id="gender-error">
                {{ $message }}
            </small>
        @enderror
    </label>

    <div class="field">
        <span>Dokumen identitas (KTP)</span>

        <label class="upload" for="ktp">
            <span class="upload-icon">
                @include('auth.partials.icon', [
                    'name' => 'upload'
                ])
            </span>

            <span class="upload-copy">
                <b id="ktp-file-name">Unggah KTP</b>
                <small>JPG, PNG, atau PDF · Maks. 5 MB</small>
            </span>
        </label>

        <input
            id="ktp"
            name="ktp"
            type="file"
            accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf"
            class="file-input"
            required
            aria-describedby="ktp-help ktp-error"
        >

        <small id="ktp-help" class="field-hint">
            KTP hanya untuk verifikasi administrator dan disimpan
            secara privat. File dihapus setelah keputusan verifikasi.
        </small>

        @error('ktp')
            <small class="field-error" id="ktp-error">
                {{ $message }}
            </small>
        @enderror
    </div>

    <label class="consent">
        <input
            type="checkbox"
            name="gender_statistics_consent"
            value="1"
            {{ old('gender_statistics_consent') ? 'checked' : '' }}
        >

        <span>
            Saya setuju data gender yang saya berikan dapat digunakan
            dalam statistik anonim. Persetujuan ini opsional.
        </span>
    </label>

    <button class="primary" type="submit">
        Daftar &amp; kirim verifikasi

        @include('auth.partials.icon', [
            'name' => 'arrow'
        ])
    </button>

    <p class="secure">
        @include('auth.partials.icon', [
            'name' => 'lock'
        ])

        Data akun diproses secara aman dan dokumen identitas
        tidak dapat diakses publik.
    </p>
</form>
@endsection

@push('scripts')
<script>
    (() => {
        const fileInput = document.getElementById('ktp');
        const fileName = document.getElementById('ktp-file-name');

        const password = document.getElementById('password');
        const confirmation = document.getElementById(
            'password_confirmation'
        );

        const updatePasswordValidity = () => {
            if (!password || !confirmation) {
                return;
            }

            confirmation.setCustomValidity(
                confirmation.value &&
                confirmation.value !== password.value
                    ? 'Konfirmasi kata sandi tidak sama.'
                    : ''
            );
        };

        password?.addEventListener(
            'input',
            updatePasswordValidity
        );

        confirmation?.addEventListener(
            'input',
            updatePasswordValidity
        );

        if (fileInput && fileName) {
            fileInput.addEventListener('change', () => {
                const file = fileInput.files?.[0];

                if (!file) {
                    fileName.textContent = 'Unggah KTP';
                    return;
                }

                const allowed = [
                    'image/jpeg',
                    'image/png',
                    'application/pdf',
                ];

                if (
                    !allowed.includes(file.type) ||
                    file.size > 5 * 1024 * 1024
                ) {
                    fileInput.value = '';
                    fileName.textContent = 'Unggah KTP';

                    window.alert(
                        'Pilih file JPG, PNG, atau PDF ' +
                        'dengan ukuran maksimal 5 MB.'
                    );

                    return;
                }

                fileName.textContent = file.name;
            });
        }
    })();
</script>
@endpush
