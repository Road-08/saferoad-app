<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Akun · SafeRoad</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-900: #0f2d59;
            --primary-800: #1e3a8a;
            --primary-700: #1d4ed8;
            --primary-600: #2563eb;
            --primary-500: #3b82f6;
            --primary-100: #dbeafe;
            --primary-50:  #eff6ff;

            --bg-canvas: #f8fafc;
            --bg-card: #ffffff;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-soft: #e2e8f0;

            --success-600: #16a34a;
            --success-100: #dcfce7;
            --success-50:  #f0fdf4;
            --danger-600:  #dc2626;

            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-card: 0 10px 25px -5px rgba(30, 58, 138, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.8);
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

        button {
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
            z-index: 50;
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
            gap: 8px;
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

        .top-user-actions {
            display: flex;
            align-items: center;
            gap: 16px;
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
            max-width: 160px;
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

        /* ====================================================
           KONTEN UTAMA
           ==================================================== */
        .page-container {
            width: min(100% - 40px, 1140px);
            margin: 40px auto;
            flex: 1;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            border-radius: 20px;
            padding: 36px clamp(24px, 4vw, 44px);
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px -10px rgba(37, 99, 235, 0.3);
            margin-bottom: 32px;
        }

        .welcome-banner::after {
            content: "";
            position: absolute;
            right: -20px;
            bottom: -30px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .eyebrow-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.15);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #dbeafe;
            margin-bottom: 14px;
        }

        .welcome-banner h1 {
            font-size: clamp(26px, 3.2vw, 36px);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }

        .welcome-banner p {
            font-size: 15px;
            color: #dbeafe;
            max-width: 600px;
            line-height: 1.6;
        }

        /* Status Sesi Bar */
        .session-status-strip {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 13px;
        }

        .status-pill-live {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px;
            background: rgba(22, 163, 74, 0.25);
            border: 1px solid rgba(74, 222, 128, 0.4);
            border-radius: 9999px;
            color: #bbf7d0;
            font-weight: 700;
            font-size: 12px;
        }

        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #4ade80;
            box-shadow: 0 0 6px #4ade80;
        }

        /* Grid Kartu Fitur */
        .section-heading {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .action-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: var(--shadow-card);
            transition: all 0.2s ease;
        }

        .action-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary-500);
            box-shadow: 0 16px 30px -10px rgba(37, 99, 235, 0.15);
        }

        .action-card-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background-color: var(--primary-50);
            color: var(--primary-600);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .card-icon svg {
            width: 22px;
            height: 22px;
        }

        .action-card h3 {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 4px;
        }

        .action-card p {
            font-size: 13.5px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .card-footer-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: var(--primary-600);
            margin-top: 16px;
        }

        .card-footer-link svg {
            width: 16px;
            height: 16px;
            transition: transform 0.15s ease;
        }

        .action-card:hover .card-footer-link svg {
            transform: translateX(4px);
        }

        /* Detail Profil & Akun Card */
        .account-details-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-card);
        }

        .account-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 16px;
        }

        .detail-item {
            padding: 14px 16px;
            background-color: var(--bg-canvas);
            border-radius: 12px;
            border: 1px solid var(--border-soft);
        }

        .detail-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-heading);
        }

        /* Footer */
        .page-footer {
            background-color: #ffffff;
            border-top: 1px solid var(--border-soft);
            padding: 24px clamp(20px, 5vw, 64px);
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @media (max-width: 768px) {
            .nav-center-menu {
                display: none;
            }

            .user-name-text {
                display: none;
            }

            .welcome-banner {
                padding: 28px 20px;
            }

            .page-footer {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand-link" href="{{ route('dashboard') }}" aria-label="SafeRoad Dashboard">
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
            <a href="{{ url('/rute') }}" class="nav-link-pill">Cari Rute</a>
            <a href="{{ url('/rute/bandingkan') }}" class="nav-link-pill">Bandingkan Rute</a>
            <a href="{{ url('/profil') }}" class="nav-link-pill">Profil Saya</a>
        </nav>

        <div class="top-user-actions">
            <div class="user-pill" title="Akun Terhubung: {{ auth()->user()->email }}">
                <div class="avatar-circle">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <span class="user-name-text">{{ auth()->user()->name }}</span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout" aria-label="Keluar dari akun SafeRoad">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </header>

    <main class="page-container">
        <!-- Banner Selamat Datang -->
        <section class="welcome-banner">
            <span class="eyebrow-tag">PORTAL MASYARAKAT SAFEROAD</span>
            <h1>Selamat Datang, {{ auth()->user()->name }} 👋</h1>
            <p>
                Sesi akun SafeRoad Anda aktif. Jelajahi peta keamanan perjalanan, cari alternatif rute terlindungi, dan pantau titik fasilitas di sekitar wilayah Anda.
            </p>

            <div class="session-status-strip">
                <span class="status-pill-live">
                    <span class="live-dot"></span>
                    Sesi Akun Aktif
                </span>
                <span>Terhubung sebagai <strong>{{ auth()->user()->email }}</strong></span>
            </div>
        </section>

        <!-- Kartu Navigasi Fitur Cepat -->
        <h2 class="section-heading">Layanan & Fitur Keamanan Rute</h2>
        <div class="feature-grid">
            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <h3>Cari Rute Aman</h3>
                            <p>Eksplorasi jalur perjalanan dengan visualisasi peta interaktif dan indikator titik fasilitas.</p>
                        </div>
                    </div>
                </div>
                <a href="{{ url('/rute') }}" class="card-footer-link">
                    Buka Navigasi Rute
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>

            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 3h5v5"/>
                                <path d="M4 20L21 3"/>
                                <path d="M21 16v5h-5"/>
                                <path d="M15 15l6 6"/>
                                <path d="M4 4l5 5"/>
                            </svg>
                        </div>
                        <div>
                            <h3>Bandingkan Rute</h3>
                            <p>Bandingkan 2–3 opsi perjalanan sekaligus untuk melihat rute tercepat vs rute teraman.</p>
                        </div>
                    </div>
                </div>
                <a href="{{ url('/rute/bandingkan') }}" class="card-footer-link">
                    Mulai Perbandingan Rute
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>

            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <polyline points="9 12 11 14 15 10"/>
                            </svg>
                        </div>
                        <div>
                            <h3>Verifikasi Identitas</h3>
                            <p>Lengkapi dokumen identitas dan data gender untuk pengalaman perjalanan yang optimal.</p>
                        </div>
                    </div>
                </div>
                <a href="{{ url('/verifikasi-identitas') }}" class="card-footer-link">
                    Lengkapi Verifikasi
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Detail Informasi Akun -->
        <section class="account-details-card" aria-label="Informasi Akun Anda">
            <h2 style="font-size: 16px; font-weight: 800; color: var(--text-heading);">Informasi Kredensial Akun</h2>
            <div class="account-details-grid">
                <div class="detail-item">
                    <div class="detail-label">Nama Pengguna</div>
                    <div class="detail-value">{{ auth()->user()->name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Alamat Email</div>
                    <div class="detail-value">{{ auth()->user()->email }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Peran Akun</div>
                    <div class="detail-value" style="color: var(--primary-600);">
                        {{ ucfirst(auth()->user()->role ?? 'Masyarakat') }}
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status Proteksi</div>
                    <div class="detail-value" style="color: var(--success-600); display: flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Terotentikasi Aman
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="page-footer">
        <div>
            © {{ date('Y') }} SafeRoad Indonesia · Sistem Navigasi Keamanan Perjalanan
        </div>
        <div>
            Pusat Informasi & Bantuan Darurat: <strong>112</strong>
        </div>
    </footer>
</body>
</html>
