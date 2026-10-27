<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Akun SafeRoad') | SafeRoad</title>

    @vite([
        'resources/css/app.css',
        'resources/css/auth.css'
    ])
</head>

<body>
<main class="auth-page">
    <div class="glow glow-one" aria-hidden="true"></div>
    <div class="glow glow-two" aria-hidden="true"></div>

    <section class="auth-shell">
        <aside class="story-panel">
            <a class="brand brand-link"
               href="{{ route('home') }}"
               aria-label="SafeRoad beranda">

                <span class="brand-mark">
                    @include('auth.partials.icon', [
                        'name' => 'shield'
                    ])
                </span>

                <span>SAFE<span>ROAD</span></span>
            </a>

            <div class="story-copy">
                <span class="eyebrow">
                    @include('auth.partials.icon', [
                        'name' => 'spark',
                        'class' => 'small-icon'
                    ])
                    NAVIGASI YANG PEDULI
                </span>

                <h1>
                    Perjalanan aman<br>
                    dimulai dari <em>percaya.</em>
                </h1>

                <p>
                    Temukan rute yang lebih aman, pantau kondisi
                    sekitar, dan saling menjaga bersama komunitas
                    SafeRoad.
                </p>

                <div class="trust-list">
                    <div>
                        <span>
                            @include('auth.partials.icon', [
                                'name' => 'check'
                            ])
                        </span>

                        <p>
                            <b>Rute berbasis keamanan</b>
                            <small>
                                Pilih perjalanan berdasarkan data nyata.
                            </small>
                        </p>
                    </div>

                    <div>
                        <span>
                            @include('auth.partials.icon', [
                                'name' => 'check'
                            ])
                        </span>

                        <p>
                            <b>Privasi selalu utama</b>
                            <small>
                                Identitas Anda dijaga dan aksesnya dibatasi.
                            </small>
                        </p>
                    </div>

                    <div>
                        <span>
                            @include('auth.partials.icon', [
                                'name' => 'check'
                            ])
                        </span>

                        <p>
                            <b>Komunitas yang saling menjaga</b>
                            <small>
                                Informasi terbaru dari pengguna sekitar.
                            </small>
                        </p>
                    </div>
                </div>
            </div>

            <p class="quote">
                “Lebih banyak wawasan. Lebih siap di perjalanan.”
            </p>
        </aside>

        <div class="form-panel">
            <a class="mobile-brand brand brand-link"
               href="{{ route('home') }}"
               aria-label="SafeRoad beranda">

                <span class="brand-mark">
                    @include('auth.partials.icon', [
                        'name' => 'shield'
                    ])
                </span>

                <span>SAFE<span>ROAD</span></span>
            </a>

            <div class="form-card">
                <nav class="mode-switch" aria-label="Pilih halaman akun">
                    <a href="{{ route('login') }}"
                       class="{{ request()->routeIs('login') ? 'active' : '' }}"
                       @if(request()->routeIs('login'))
                           aria-current="page"
                       @endif>
                        Masuk
                    </a>

                    <a href="{{ route('register') }}"
                       class="{{ request()->routeIs('register') ? 'active' : '' }}"
                       @if(request()->routeIs('register'))
                           aria-current="page"
                       @endif>
                        Daftar
                    </a>
                </nav>

                @if (session('status'))
                    <div class="notice notice-success" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('form-content')
            </div>

            <p class="terms">
                Dengan melanjutkan, Anda menyetujui Syarat Layanan
                dan Kebijakan Privasi SafeRoad.
            </p>
        </div>
    </section>
</main>

<script>
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(
                button.dataset.passwordToggle
            );

            if (!input) {
                return;
            }

            const show = input.type === 'password';

            input.type = show ? 'text' : 'password';

            button.setAttribute('aria-pressed', String(show));

            button.setAttribute(
                'aria-label',
                show
                    ? 'Sembunyikan kata sandi'
                    : 'Tampilkan kata sandi'
            );
        });
    });
</script>

@stack('scripts')
</body>
</html>
