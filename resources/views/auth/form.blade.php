<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $mode === 'register' ? 'Daftar Akun' : 'Masuk' }} · SafeRoad</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

            /* Warna Netral & Elemen */
            --bg-canvas: #f8fafc;
            --bg-card: #ffffff;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-soft: #e2e8f0;
            --border-focus: #3b82f6;

            /* Indikator Status */
            --danger-600: #dc2626;
            --danger-100: #fee2e2;
            --danger-50:  #fef2f2;
            --success-600: #16a34a;
            --success-100: #dcfce7;
            --success-50:  #f0fdf4;
            --warning-500: #f59e0b;

            --shadow-card: 0 20px 40px -15px rgba(30, 58, 138, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8);
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
        }

        button, input {
            font-family: inherit;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* Container Pembungkus */
        .auth-container {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(380px, 1fr) minmax(440px, 1.15fr);
            max-width: 1680px;
            margin: 0 auto;
            background-color: var(--bg-card);
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.1);
        }

        /* ====================================================
           PANEL KIRI: Story & Visual SafeRoad (Nuansa Biru)
           ==================================================== */
        .hero-panel {
            position: relative;
            background: linear-gradient(145deg, #091a36 0%, #173874 50%, #1e40af 100%);
            color: #ffffff;
            padding: 48px clamp(32px, 5vw, 64px) 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            isolation: isolate;
        }

        /* Elemen Dekoratif Rute Biru di Latar */
        .hero-panel::before {
            content: "";
            position: absolute;
            inset: -40%;
            background: radial-gradient(circle at 30% 20%, rgba(59, 130, 246, 0.25), transparent 45%),
                        radial-gradient(circle at 80% 80%, rgba(96, 165, 250, 0.2), transparent 40%);
            z-index: -2;
            pointer-events: none;
        }

        .route-line-bg {
            position: absolute;
            right: -60px;
            bottom: 40px;
            width: 480px;
            height: 380px;
            opacity: 0.18;
            z-index: -1;
            pointer-events: none;
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #ffffff;
            width: fit-content;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: grid;
            place-items: center;
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        }

        .brand-icon svg {
            width: 24px;
            height: 24px;
        }

        .hero-content {
            margin: 40px 0;
            max-width: 520px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--primary-100);
            margin-bottom: 24px;
        }

        .hero-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #60a5fa;
            box-shadow: 0 0 8px #60a5fa;
        }

        .hero-title {
            font-size: clamp(34px, 4.2vw, 48px);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -0.04em;
            margin-bottom: 18px;
            color: #ffffff;
        }

        .hero-title em {
            font-style: normal;
            background: linear-gradient(90deg, #93c5fd, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 15px;
            line-height: 1.65;
            color: #cbd5e1;
            margin-bottom: 32px;
        }

        /* Daftar Fitur Keunggulan */
        .features-list {
            display: grid;
            gap: 16px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(8px);
        }

        .feature-icon-circle {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(59, 130, 246, 0.25);
            border: 1px solid rgba(96, 165, 250, 0.35);
            display: grid;
            place-items: center;
            color: #93c5fd;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .feature-icon-circle svg {
            width: 18px;
            height: 18px;
        }

        .feature-item h3 {
            font-size: 13px;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 2px;
        }

        .feature-item p {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.45;
        }

        /* Footer Kiri: Informasi Darurat */
        .hero-emergency-notice {
            margin-top: 24px;
            padding: 12px 16px;
            background: rgba(220, 38, 38, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            color: #fca5a5;
        }

        .hero-emergency-notice strong {
            color: #fecaca;
        }

        .hero-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #94a3b8;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* ====================================================
           PANEL KANAN: Formulir Interaktif Masuk / Daftar
           ==================================================== */
        .form-panel {
            padding: 40px clamp(28px, 5.5vw, 76px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: var(--bg-card);
        }

        .form-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 36px;
        }

        .mobile-brand {
            display: none;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 800;
            color: var(--primary-900);
        }

        .mobile-brand .brand-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
        }

        .switch-prompt {
            font-size: 13px;
            color: var(--text-muted);
            margin-left: auto;
        }

        .switch-prompt a {
            color: var(--primary-600);
            font-weight: 700;
            margin-left: 4px;
        }

        .switch-prompt a:hover {
            text-decoration: underline;
        }

        .form-center-box {
            width: 100%;
            max-width: 440px;
            margin: auto;
            padding: 24px 0;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-subtitle-tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--primary-600);
            margin-bottom: 6px;
        }

        .form-header h2 {
            font-size: clamp(26px, 2.5vw, 32px);
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .form-header p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* Navigasi Tab Segmented */
        .segmented-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
            padding: 4px;
            background: #f1f5f9;
            border-radius: 12px;
            margin-bottom: 24px;
            border: 1px solid var(--border-soft);
        }

        .segmented-tab {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            border-radius: 9px;
            transition: all 0.18s ease;
        }

        .segmented-tab.active {
            background-color: #ffffff;
            color: var(--primary-700);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08), 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        /* Alert Notifikasi */
        .alert-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.45;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: var(--danger-50);
            border: 1px solid var(--danger-100);
            color: var(--danger-600);
        }

        .alert-success {
            background-color: var(--success-50);
            border: 1px solid var(--success-100);
            color: var(--success-600);
        }

        .alert-box svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* Struktur Form & Kolom */
        .auth-form {
            display: grid;
            gap: 16px;
        }

        .form-field {
            display: grid;
            gap: 6px;
        }

        .form-field label {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 14px;
            width: 18px;
            height: 18px;
            color: #94a3b8;
            pointer-events: none;
            display: grid;
            place-items: center;
        }

        .input-icon-left svg {
            width: 100%;
            height: 100%;
        }

        .input-control {
            width: 100%;
            height: 48px;
            padding: 0 14px 0 42px;
            background-color: #ffffff;
            border: 1.5px solid var(--border-soft);
            border-radius: 11px;
            font-size: 14px;
            color: var(--text-heading);
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .input-control::placeholder {
            color: #94a3b8;
            font-size: 13.5px;
        }

        .input-control:focus {
            border-color: var(--primary-600);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
        }

        .input-control.has-error {
            border-color: var(--danger-600);
            background-color: #fffbfa;
        }

        .input-group.with-toggle .input-control {
            padding-right: 46px;
        }

        .btn-toggle-pass {
            position: absolute;
            right: 8px;
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border: none;
            background: transparent;
            color: #64748b;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.15s, color 0.15s;
        }

        .btn-toggle-pass:hover {
            background-color: #f1f5f9;
            color: var(--primary-700);
        }

        .btn-toggle-pass svg {
            width: 18px;
            height: 18px;
        }

        .field-error-msg {
            font-size: 12px;
            color: var(--danger-600);
            font-weight: 500;
        }

        .field-helper {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Baris Opsi (Remember & Bantuan) */
        .form-options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 2px;
            font-size: 13px;
        }

        .remember-checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-body);
            cursor: pointer;
            user-select: none;
        }

        .remember-checkbox input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            accent-color: var(--primary-600);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--primary-600);
            font-weight: 600;
            font-size: 12.5px;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Tombol Submit Utama */
        .btn-submit {
            margin-top: 8px;
            height: 49px;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: none;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: var(--shadow-btn);
            transition: transform 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px 0 rgba(37, 99, 235, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-submit svg {
            width: 17px;
            height: 17px;
            transition: transform 0.15s ease;
        }

        .btn-submit:hover svg {
            transform: translateX(2px);
        }

        /* Keamanan & Privasi */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 18px;
            font-size: 11.5px;
            color: var(--text-muted);
            text-align: center;
        }

        .security-badge svg {
            width: 15px;
            height: 15px;
            color: var(--success-600);
            flex-shrink: 0;
        }

        /* Footer Kanan */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px solid var(--border-soft);
            font-size: 12px;
            color: #94a3b8;
        }

        .form-footer a {
            color: #64748b;
        }

        .form-footer a:hover {
            color: var(--primary-600);
        }

        /* ====================================================
           RESPONSIVITAS (Mobile & Tablet)
           ==================================================== */
        @media (max-width: 960px) {
            .auth-container {
                grid-template-columns: 1fr;
            }

            .hero-panel {
                display: none;
            }

            .form-panel {
                min-height: 100vh;
                padding: 24px 20px;
            }

            .mobile-brand {
                display: inline-flex;
            }

            .form-center-box {
                padding: 16px 0 28px;
            }
        }

        @media (max-width: 440px) {
            .form-header h2 {
                font-size: 24px;
            }

            .form-options-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
@php($isRegister = ($mode === 'register'))

<main class="auth-container">
    <!-- PANEL KIRI: Story & Keunggulan SafeRoad (Tema Biru) -->
    <aside class="hero-panel" aria-label="Informasi SafeRoad">
        <!-- SVG Latar Rute Abstrak -->
        <svg class="route-line-bg" viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M20 380 C 120 340, 150 260, 230 240 C 310 220, 360 140, 480 80" stroke="#60a5fa" stroke-width="24" stroke-linecap="round" stroke-linejoin="round" opacity="0.3"/>
            <path d="M20 380 C 120 340, 150 260, 230 240 C 310 220, 360 140, 480 80" stroke="#93c5fd" stroke-width="3" stroke-linecap="round" stroke-dasharray="8 12"/>
            <circle cx="20" cy="380" r="10" fill="#3b82f6"/>
            <circle cx="230" cy="240" r="8" fill="#60a5fa"/>
            <circle cx="480" cy="80" r="12" fill="#93c5fd"/>
        </svg>

        <div>
            <a class="brand-link" href="{{ route('home') }}" aria-label="Beranda SafeRoad">
                <span class="brand-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="M12 8v4"/>
                        <path d="M12 16h.01"/>
                    </svg>
                </span>
                <span>SafeRoad</span>
            </a>
        </div>

        <div class="hero-content">
            <span class="hero-badge">
                <span class="hero-badge-dot"></span>
                Sistem Keamanan Perjalanan
            </span>

            <h1 class="hero-title">
                Perjalanan lebih tenang, <em>ketahui kondisi rute Anda.</em>
            </h1>

            <p class="hero-desc">
                SafeRoad membantu masyarakat memantau titik rawan, fasilitas keamanan, dan rute terlindungi secara real-time sebelum bepergian.
            </p>

            <div class="features-list">
                <div class="feature-item">
                    <span class="feature-icon-circle" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </span>
                    <div>
                        <h3>Analisis Rute & Skor Keamanan</h3>
                        <p>Dapatkan rekomendasi rute teraman berdasarkan pencahayaan, sebaran CCTV, dan riwayat insiden.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <span class="feature-icon-circle" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2a5 5 0 0 1 5 5c0 3-5 9-5 9s-5-6-5-9a5 5 0 0 1 5-5z"/>
                            <circle cx="12" cy="7" r="2"/>
                        </svg>
                    </span>
                    <div>
                        <h3>Women's Zone & Fasilitas Terdekat</h3>
                        <p>Akses informasi keamanan spesifik perempuan serta titik pos kepolisian dan rumah sakit aktif.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <span class="feature-icon-circle" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </span>
                    <div>
                        <h3>Pelaporan Warga Terverifikasi</h3>
                        <p>Laporkan gangguan keamanan secara cepat dengan perlindungan privasi dan opsi anonim.</p>
                    </div>
                </div>
            </div>

            <div class="hero-emergency-notice" role="note">
                <span style="font-size: 16px;">🚨</span>
                <div>
                    <strong>Panggilan Darurat:</strong> Hubungi <strong>112</strong> untuk respon darurat terpadu atau hubungi pos keamanan terdekat.
                </div>
            </div>
        </div>

        <div class="hero-footer">
            <span>© {{ date('Y') }} SafeRoad Indonesia</span>
            <span>Platform Terverifikasi</span>
        </div>
    </aside>

    <!-- PANEL KANAN: Formulir Masuk / Daftar -->
    <section class="form-panel" aria-label="Formulir Akun SafeRoad">
        <div class="form-top-bar">
            <a class="mobile-brand" href="{{ route('home') }}" aria-label="SafeRoad Beranda">
                <span class="brand-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </span>
                <span>SafeRoad</span>
            </a>

            <div class="switch-prompt">
                @if ($isRegister)
                    <span>Sudah punya akun?</span>
                    <a href="{{ route('login') }}">Masuk</a>
                @else
                    <span>Belum memiliki akun?</span>
                    <a href="{{ route('register') }}">Daftar</a>
                @endif
            </div>
        </div>

        <div class="form-center-box">
            <header class="form-header">
                <span class="form-subtitle-tag">PORTAL AKUN SAFEROAD</span>
                <h2>{{ $isRegister ? 'Mulai perjalananmu' : 'Selamat datang kembali' }}</h2>
                <p>
                    {{ $isRegister
                        ? 'Daftarkan akun untuk mengakses fitur navigasi aman dan berpartisipasi menjaga lingkungan rute.'
                        : 'Masuk dengan kredensial terdaftar untuk melanjutkan akses fitur keamanan perjalanan Anda.' }}
                </p>
            </header>

            <!-- Tab Masuk / Daftar -->
            <nav class="segmented-tabs" aria-label="Pilih Jenis Akses">
                <a href="{{ route('login') }}" class="segmented-tab {{ !$isRegister ? 'active' : '' }}" aria-current="{{ !$isRegister ? 'page' : 'false' }}">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="segmented-tab {{ $isRegister ? 'active' : '' }}" aria-current="{{ $isRegister ? 'page' : 'false' }}">
                    Daftar Baru
                </a>
            </nav>

            <!-- Alert Notifikasi / Pesan Kesalahan -->
            @if ($errors->any())
                <div class="alert-box alert-danger" role="alert" aria-live="assertive">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>
                        <strong>Terjadi kendala:</strong>
                        <p>{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            @if (session('status'))
                <div class="alert-box alert-success" role="status" aria-live="polite">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <div>
                        <p>{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- Formulir Autentikasi -->
            <form class="auth-form" id="authForm" method="POST" action="{{ $isRegister ? route('register.store') : route('login.store') }}" novalidate>
                @csrf

                <!-- Field Nama Lengkap (Khusus Mode Registrasi) -->
                @if ($isRegister)
                    <div class="form-field">
                        <label for="name">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-icon-left" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                class="input-control @error('name') has-error @enderror"
                                value="{{ old('name') }}"
                                placeholder="Contoh: Budi Santoso"
                                autocomplete="name"
                                maxlength="255"
                                required
                                @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                            >
                        </div>
                        @error('name')
                            <p class="field-error-msg" id="name-error">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Field Email -->
                <div class="form-field">
                    <label for="email">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-icon-left" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            class="input-control @error('email') has-error @enderror"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            autocomplete="email"
                            maxlength="255"
                            required
                            @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        >
                    </div>
                    @error('email')
                        <p class="field-error-msg" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field Kata Sandi -->
                <div class="form-field">
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <label for="password">Kata Sandi</label>
                        @if ($isRegister)
                            <span class="field-helper">Minimal 8 karakter</span>
                        @endif
                    </div>
                    <div class="input-group with-toggle">
                        <span class="input-icon-left" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="input-control @error('password') has-error @enderror"
                            placeholder="{{ $isRegister ? 'Buat kata sandi minimal 8 karakter' : 'Masukkan kata sandi akun' }}"
                            autocomplete="{{ $isRegister ? 'new-password' : 'current-password' }}"
                            {{ $isRegister ? 'minlength=8' : '' }}
                            required
                            @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                        >
                        <button
                            type="button"
                            class="btn-toggle-pass"
                            data-toggle="password"
                            aria-label="Tampilkan kata sandi"
                            aria-pressed="false"
                        >
                            <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-error-msg" id="password-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field Konfirmasi Kata Sandi (Khusus Registrasi) -->
                @if ($isRegister)
                    <div class="form-field">
                        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <div class="input-group with-toggle">
                            <span class="input-icon-left" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <polyline points="9 12 11 14 15 10"/>
                                </svg>
                            </span>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="input-control"
                                placeholder="Ulangi kata sandi Anda"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >
                            <button
                                type="button"
                                class="btn-toggle-pass"
                                data-toggle="password_confirmation"
                                aria-label="Tampilkan konfirmasi kata sandi"
                                aria-pressed="false"
                            >
                                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Baris Remember Me & Lupa Password (Login) -->
                    <div class="form-options-row">
                        <label class="remember-checkbox">
                            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                        <a href="javascript:void(0)" onclick="alert('Untuk pemulihan akun atau kendala login, silakan hubungi pusat bantuan atau administrator sistem SafeRoad.')" class="forgot-link">
                            Lupa kata sandi?
                        </a>
                    </div>
                @endif

                <!-- Tombol Submit -->
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span id="btnSubmitText">{{ $isRegister ? 'Daftar Akun SafeRoad' : 'Masuk ke SafeRoad' }}</span>
                    <svg id="btnSubmitIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            <div class="security-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>Akses aman terenkripsi · Privasi data Anda selalu terjaga</span>
            </div>
        </div>

        <footer class="form-footer">
            <span>SafeRoad Versi 1.0</span>
            <div style="display: flex; gap: 14px;">
                <a href="{{ route('home') }}">Beranda</a>
                <a href="javascript:void(0)" onclick="alert('SafeRoad: Platform pemetaan keamanan rute perjalanan masyarakat.')">Tentang</a>
            </div>
        </footer>
    </section>
</main>

<script>
    // Interaktivitas Show / Hide Password dengan SVG ikon mata & coret
    const eyeOpenSvg = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
    const eyeSlashSvg = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;

    document.querySelectorAll('[data-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.toggle;
            const input = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            button.setAttribute('aria-pressed', String(isPassword));
            button.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');

            const iconSvg = button.querySelector('svg');
            if (iconSvg) {
                iconSvg.innerHTML = isPassword ? eyeSlashSvg : eyeOpenSvg;
            }
        });
    });

    // Loading State saat Formulir Disubmit
    const form = document.getElementById('authForm');
    const submitBtn = document.getElementById('btnSubmit');
    const btnText = document.getElementById('btnSubmitText');
    const btnIcon = document.getElementById('btnSubmitIcon');

    if (form && submitBtn) {
        form.addEventListener('submit', function (e) {
            // Jika validasi native browser lolos
            if (form.checkValidity()) {
                submitBtn.disabled = true;
                btnText.textContent = 'Memproses autentikasi...';
                if (btnIcon) {
                    btnIcon.style.display = 'none';
                }
            }
        });
    }
</script>
</body>
</html>
