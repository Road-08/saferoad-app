@extends('auth.layout')

@section('title', 'Masuk')

@section('form-content')
<header class="form-header">
    <span class="mini-label">SAFEROAD ACCOUNT</span>

    <h2>Selamat datang kembali</h2>

    <p>
        Masuk untuk melanjutkan perjalanan aman Anda.
    </p>
</header>

<form method="POST" action="{{ route('login.store') }}">
    @csrf

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
                required
                autofocus
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
                placeholder="Masukkan kata sandi"
                autocomplete="current-password"
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

    <div class="form-options">
        <label>
            <input
                type="checkbox"
                name="remember"
                value="1"
                {{ old('remember') ? 'checked' : '' }}
            >
            Ingat saya
        </label>

        <span
            class="muted-option"
            title="Fitur pemulihan kata sandi belum dikonfigurasi"
        >
            Lupa kata sandi?
        </span>
    </div>

    <button class="primary" type="submit">
        Masuk ke SafeRoad

        @include('auth.partials.icon', [
            'name' => 'arrow'
        ])
    </button>

    <p class="secure">
        @include('auth.partials.icon', [
            'name' => 'lock'
        ])

        Data akun diproses secara aman dan tidak ditampilkan
        ke pengguna lain.
    </p>
</form>
@endsection
