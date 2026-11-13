<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SafeRoad · Navigasi Keamanan Perjalanan')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet CSS jika halaman membutuhkan peta -->
    @stack('leaflet-css')

    <style>
        :root {
            /* Tema Biru SafeRoad */
            --primary-900: #0f2d59;
            --primary-800: #1e3a8a;
            --primary-700: #1d4ed8;
            --primary-600: #2563eb;
            --primary-500: #3b82f6;
            --primary-100: #dbeafe;
            --primary-50:  #eff6ff;

            /* Latar & Netral */
            --bg-canvas: #f8fafc;
            --bg-card: #ffffff;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-soft: #e2e8f0;

            /* Status & Peringatan */
            --success-600: #16a34a;
            --success-100: #dcfce7;
            --success-50:  #f0fdf4;
            --danger-600:  #dc2626;
            --danger-100:  #fee2e2;
            --danger-50:   #fef2f2;
            --warning-500: #f59e0b;
            --warning-100: #fef3c7;

            /* Efek Bayangan */
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-card: 0 10px 25px -5px rgba(30, 58, 138, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.8);
            --shadow-btn: 0 4px 14px 0 rgba(37, 99, 235, 0.28);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            background-color: var(--bg-canvas);
            color: var(--text-body);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
            line-height: 1.5;
            display: flex;
            flex-direction: column;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button, input, select, textarea {
            font-family: inherit;
        }

        /* ====================================================
           NAVBAR UTAMA (Header Biru SafeRoad)
           ==================================================== */
        .topbar {
            height: 72px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-soft);
            padding: 0 clamp(20px, 5vw, 64px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--primary-900);
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--primary-600), var(--primary-800));
            display: grid;
            place-items: center;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .brand-icon svg {
            width: 22px;
            height: 22px;
        }

        .nav-center-menu {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-pill {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-body);
            transition: all 0.15s ease;
        }

        .nav-link-pill:hover {
            background-color: var(--primary-50);
            color: var(--primary-700);
        }

        .nav-link-pill.active {
            background-color: var(--primary-50);
            color: var(--primary-700);
            font-weight: 700;
        }

        .top-user-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 6px;
            background-color: var(--primary-50);
            border: 1px solid var(--primary-100);
            border-radius: 9999px;
        }

        .avatar-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--primary-600);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 13px;
            font-weight: 700;
        }

        .user-name-text {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary-900);
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-logout {
            padding: 8px 16px;
            border-radius: 9px;
            border: 1px solid var(--border-soft);
            background-color: #ffffff;
            color: var(--danger-600);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-logout:hover {
            background-color: #fef2f2;
            border-color: #fecaca;
        }

        .btn-logout svg {
            width: 15px;
            height: 15px;
        }

        /* Area Konten */
        .app-container {
            flex: 1;
            width: 100%;
        }

        /* Footer Standar */
        .global-footer {
            background-color: #ffffff;
            border-top: 1px solid var(--border-soft);
            padding: 24px clamp(20px, 5vw, 64px);
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .nav-center-menu {
                display: none;
            }

            .user-name-text {
                display: none;
            }

            .global-footer {
                flex-direction: column;
                gap: 10px;
                text-align: center;
                padding-bottom: 90px; /* Ruang untuk bottom nav */
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <header class="topbar">
        <a class="brand-link" href="{{ auth()->check() ? route('dashboard') : route('home') }}" aria-label="SafeRoad Beranda">
            <span class="brand-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="M12 8v4"/>
                    <path d="M12 16h.01"/>
                </svg>
            </span>
            <span>SafeRoad</span>
        </a>

        <nav class="nav-center-menu" aria-label="Menu Utama">
            @auth
                <a href="{{ route('dashboard') }}" class="nav-link-pill {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
            @endauth
            <a href="{{ url('/rute') }}" class="nav-link-pill {{ request()->is('rute') ? 'active' : '' }}">
                Cari Rute
            </a>
            <a href="{{ url('/rute/bandingkan') }}" class="nav-link-pill {{ request()->is('rute/bandingkan') ? 'active' : '' }}">
                Bandingkan Rute
            </a>
            <a href="{{ url('/profil') }}" class="nav-link-pill {{ request()->is('profil*') || request()->is('verifikasi*') ? 'active' : '' }}">
                Profil Saya
            </a>
        </nav>

        <div class="top-user-actions">
            @auth
                <div class="user-pill" title="{{ auth()->user()->email }}">
                    <div class="avatar-circle">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="user-name-text">{{ auth()->user()->name }}</span>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout" aria-label="Keluar dari akun">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link-pill active" style="background-color: var(--primary-600); color: #fff;">
                    Masuk
                </a>
            @endauth
        </div>
    </header>

    <main class="app-container">
        @yield('content')
    </main>

    @include('components.bottom-navigation')

    <footer class="global-footer">
        <div>
            © {{ date('Y') }} SafeRoad Indonesia · Sistem Navigasi Keamanan Perjalanan
        </div>
        <div>
            Pusat Bantuan & Respon Darurat: <strong>112</strong>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
