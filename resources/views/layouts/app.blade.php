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
            /* Standarisasi Monochromatic Blue Palette SafeRoad */
            --primary-900: #1e3a8a;
            --primary-800: #1e40af;
            --primary-700: #1e40af;
            --primary-600: #2563eb;
            --primary-500: #3b82f6;
            --primary-200: #bfdbfe;
            --primary-100: #dbeafe;
            --primary-50:  #eff6ff;

            /* Latar & Netral */
            --bg-canvas: #f8fafc;
            --bg-card: #ffffff;
            --text-heading: #0f172a;
            --text-body: #0f172a;
            --text-muted: #64748b;
            --border-soft: #e2e8f0;

            /* Status & Peringatan (Merah khusus bahaya/darurat, lainnya biru/netral) */
            --success-600: #2563eb;
            --success-100: #dbeafe;
            --success-50:  #eff6ff;
            --danger-600:  #dc2626;
            --danger-100:  #fee2e2;
            --danger-50:   #fef2f2;
            --warning-500: #64748b;
            --warning-100: #f1f5f9;

            /* Efek Bayangan Minimalis & Natural */
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04);
            --shadow-card: 0 1px 3px rgba(15, 23, 42, 0.05);
            --shadow-btn: 0 1px 2px rgba(37, 99, 235, 0.12);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
        }

        body {
            min-height: 100vh;
            background-color: var(--bg-canvas);
            color: var(--text-body);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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

        /* Standarisasi Ukuran Ikon SVG pada Tombol & Link Aksi */
        button svg,
        a[class*="btn"] svg,
        .btn-create-report svg,
        .btn-primary-action svg,
        .back-link svg,
        .btn-back-dash svg,
        .btn-back-link svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        /* ====================================================
           NAVBAR UTAMA (Clean Minimalist Blue Identity)
           ==================================================== */
        .topbar {
            height: 68px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-soft);
            padding: 0 clamp(20px, 4vw, 48px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: var(--text-heading);
            flex-shrink: 0;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background-color: var(--primary-600);
            display: grid;
            place-items: center;
            color: #ffffff;
        }

        .brand-icon svg {
            width: 20px;
            height: 20px;
        }

        .nav-center-menu {
            display: flex;
            align-items: center;
            gap: 4px;
            overflow-x: auto;
            max-width: 100%;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .nav-center-menu::-webkit-scrollbar {
            display: none;
        }

        .nav-role-group {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            flex-wrap: nowrap;
        }

        .nav-link-pill {
            padding: 7px 13px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-muted);
            transition: background-color 0.15s ease, color 0.15s ease;
            white-space: nowrap;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-pill:hover {
            background-color: #f1f5f9;
            color: var(--text-heading);
        }

        .nav-link-pill.active {
            background-color: var(--primary-50);
            color: var(--primary-700);
            font-weight: 700;
        }

        .nav-link-pill.nav-link-danger {
            color: var(--danger-600);
            background-color: var(--danger-50);
        }

        .nav-link-pill.nav-link-danger:hover {
            background-color: var(--danger-100);
            color: #b91c1c;
        }

        .nav-link-pill.nav-link-danger.active {
            background-color: var(--danger-600);
            color: #ffffff;
        }

        .top-user-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px 4px 5px;
            background-color: #f8fafc;
            border: 1px solid var(--border-soft);
            border-radius: 9999px;
        }

        .avatar-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: var(--primary-600);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 700;
        }

        .user-name-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-heading);
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-logout {
            padding: 7px 13px;
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            background-color: #ffffff;
            color: var(--text-body);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-logout:hover {
            background-color: var(--danger-50);
            border-color: #fecaca;
            color: var(--danger-600);
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
            padding: 20px clamp(20px, 4vw, 48px);
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
                gap: 8px;
                text-align: center;
                padding-bottom: 88px;
            }
        }

        /* Badge Role Pengguna Aktif */
        .active-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 999px;
            background: var(--primary-50);
            color: var(--primary-700);
            border: 1px solid var(--primary-100);
            user-select: none;
        }
        .active-role-badge.role-badge-keamanan {
            background: #eff6ff;
            color: #1e40af;
            border-color: #dbeafe;
        }
        .active-role-badge.role-badge-admin {
            background: #eff6ff;
            color: #1e40af;
            border-color: #dbeafe;
        }
        .role-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: currentColor;
        }

        /* Floating SOS Button */
        .floating-sos-btn {
            position: fixed;
            bottom: 28px;
            right: 24px;
            z-index: 1050;
            background-color: var(--danger-600);
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.28);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: background-color 0.15s ease, transform 0.15s ease;
            text-decoration: none;
        }
        .floating-sos-btn:hover {
            background-color: #b91c1c;
            transform: translateY(-1px);
            color: #fff;
        }
        .sos-pulse-ring {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ffffff;
        }
        @media (max-width: 768px) {
            .floating-sos-btn {
                bottom: 80px;
                right: 16px;
                padding: 9px 15px;
                font-size: 12px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <header class="topbar">
        @php
            $currentRole = auth()->user()?->role ?? 'masyarakat';
            $homeRoute = match ($currentRole) {
                'admin' => route('admin.dashboard'),
                'keamanan' => route('keamanan.dashboard'),
                default => route('home'),
            };
        @endphp

        <a class="brand-link" href="{{ $homeRoute }}" aria-label="SafeRoad Beranda">
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
            @if($currentRole === 'admin')
                {{-- Navigasi Role: Administrator --}}
                <div id="navGroupAdmin" class="nav-role-group">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link-pill {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.validasi') }}" class="nav-link-pill {{ request()->routeIs('admin.validasi') ? 'active' : '' }}">
                        Validasi Laporan
                        <span id="navBadgePendingValidation" style="display:none; background:#2563eb; color:#fff; font-size:10px; font-weight:700; padding:1px 6px; border-radius:999px;" title="Menunggu Validasi">0</span>
                        <span id="navBadgeReadyToFinalize" style="display:none; background:#1e40af; color:#fff; font-size:10px; font-weight:700; padding:1px 6px; border-radius:999px;" title="Siap Difinalisasi">0</span>
                    </a>
                    <a href="{{ route('admin.peta') }}" class="nav-link-pill {{ request()->routeIs('admin.peta') ? 'active' : '' }}">
                        Pantau Peta
                    </a>
                    <a href="{{ route('admin.statistik') }}" class="nav-link-pill {{ request()->routeIs('admin.statistik') ? 'active' : '' }}">
                        Statistik &amp; Rekap
                    </a>
                    <a href="{{ url('/darurat') }}" class="nav-link-pill nav-link-danger {{ request()->is('darurat*') ? 'active' : '' }}">
                        Pengaturan Darurat
                    </a>
                </div>
            @elseif($currentRole === 'keamanan')
                {{-- Navigasi Role: Petugas Keamanan --}}
                <div id="navGroupKeamanan" class="nav-role-group">
                    <a href="{{ route('keamanan.dashboard') }}" class="nav-link-pill {{ request()->is('keamanan*') ? 'active' : '' }}">
                        Dashboard Keamanan
                    </a>
                    <a href="{{ route('keamanan.dashboard') }}#tabelAduan" class="nav-link-pill">
                        Laporan Masuk &amp; TKP
                        <span id="navBadgeKeamananUnconfirmed" style="display:none; background:#2563eb; color:#fff; font-size:10px; font-weight:700; padding:1px 6px; border-radius:999px;" title="Belum Diperiksa">0</span>
                    </a>
                    <a href="{{ url('/darurat') }}" class="nav-link-pill nav-link-danger {{ request()->is('darurat*') ? 'active' : '' }}">
                        Hotline Siaga
                    </a>
                </div>
            @else
                {{-- Navigasi Role: Masyarakat --}}
                <div id="navGroupMasyarakat" class="nav-role-group">
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-link-pill {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            Dashboard
                        </a>
                    @endauth
                    <a href="{{ url('/rute') }}" class="nav-link-pill {{ request()->is('rute*') ? 'active' : '' }}">
                        Cari Rute
                    </a>

                    <a href="{{ url('/laporkan') }}" class="nav-link-pill {{ request()->is('laporkan*') ? 'active' : '' }}">
                        Laporkan
                    </a>

                    <a href="{{ url('/laporan/riwayat') }}" class="nav-link-pill {{ request()->is('laporan/riwayat*') ? 'active' : '' }}">
                        Riwayat
                    </a>

                    <a href="{{ url('/darurat') }}" class="nav-link-pill nav-link-danger {{ request()->is('darurat*') ? 'active' : '' }}">
                        Darurat
                    </a>

                    @auth
                        <a href="{{ url('/profil') }}" class="nav-link-pill {{ request()->is('profil*') ? 'active' : '' }}">
                            Profil
                        </a>
                    @endauth
                </div>
            @endif
        </nav>

        <div class="top-user-actions">
            <!-- Badge Role Aktif Pengguna (Ditentukan 100% dari Server) -->
            @if($currentRole === 'admin')
                <div class="active-role-badge role-badge-admin" id="topActiveRoleBadge" title="Role Anda: Administrator">
                    <span class="role-badge-dot"></span>
                    <span id="topActiveRoleText">Administrator</span>
                </div>
            @elseif($currentRole === 'keamanan')
                <div class="active-role-badge role-badge-keamanan" id="topActiveRoleBadge" title="Role Anda: Petugas Keamanan">
                    <span class="role-badge-dot"></span>
                    <span id="topActiveRoleText">Petugas Keamanan</span>
                </div>
            @else
                <div class="active-role-badge role-badge-masyarakat" id="topActiveRoleBadge" title="Role Anda: Masyarakat">
                    <span class="role-badge-dot"></span>
                    <span id="topActiveRoleText">Masyarakat</span>
                </div>
            @endif

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
            Pusat Bantuan &amp; Respon Darurat: <strong>112</strong>
        </div>
    </footer>

    <!-- Floating Quick SOS Button (Global) -->
    <a href="{{ url('/darurat') }}" class="floating-sos-btn" id="btnFloatingSos" title="Panggilan Darurat Cepat SafeRoad">
        <span class="sos-pulse-ring"></span>
        <span>SOS DARURAT</span>
    </a>

    <script>
        // Bersihkan data localStorage lama yang tidak lagi digunakan
        try {
            localStorage.removeItem('saferoad_active_role');
            localStorage.removeItem('saferoad_gender');
            localStorage.removeItem('saferoad_verified');
        } catch (e) {}
    </script>

    @stack('scripts')
</body>
</html>
