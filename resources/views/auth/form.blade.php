<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($mode ?? 'login') === 'register' ? 'Daftar Akun' : 'Masuk Akun' }} · SafeRoad</title>

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
            grid-template-columns: minmax(380px, 1fr) minmax(440px, 1.25fr);
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
            transition: background 0.4s ease;
        }

        .hero-panel.role-theme-keamanan {
            background: linear-gradient(145deg, #06192e 0%, #0c2d48 50%, #145a55 100%);
        }

        .hero-panel.role-theme-admin {
            background: linear-gradient(145deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
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
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #dbeafe;
            margin-bottom: 20px;
            backdrop-filter: blur(8px);
        }

        .hero-badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #38bdf8;
            box-shadow: 0 0 8px #38bdf8;
        }

        .hero-title {
            font-size: clamp(28px, 3.2vw, 38px);
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.025em;
            margin-bottom: 16px;
        }

        .hero-title em {
            font-style: normal;
            background: linear-gradient(120deg, #93c5fd, #bfdbfe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 14.5px;
            color: #cbd5e1;
            line-height: 1.6;
            margin-bottom: 28px;
        }

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
            padding: 36px clamp(24px, 4.5vw, 64px);
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
            width: 32px;
            height: 32px;
            border-radius: 8px;
        }

        .switch-prompt {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-left: auto;
        }

        .switch-prompt a {
            color: var(--primary-600);
            font-weight: 700;
            margin-left: 4px;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background-color 0.15s;
        }

        .switch-prompt a:hover {
            background-color: var(--primary-50);
            text-decoration: underline;
        }

        .form-center-box {
            max-width: 520px;
            width: 100%;
            margin: 0 auto;
            padding: 16px 0;
        }

        /* ====================================================
           ROLE SELECTOR CARDS (Masyarakat vs Keamanan vs Admin)
           ==================================================== */
        .role-portal-box {
            background: #f8fafc;
            border: 1.5px solid var(--border-soft);
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 22px;
        }

        .role-portal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .role-portal-title {
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .role-active-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1e40af;
        }

        .role-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .role-card-btn {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            color: #334155;
        }

        .role-card-btn:hover {
            border-color: #93c5fd;
            background: #f0f7ff;
            transform: translateY(-1px);
        }

        .role-card-btn.active {
            border-color: var(--primary-600);
            background: #eff6ff;
            color: var(--primary-900);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12), inset 0 0 0 1px var(--primary-600);
        }

        .role-card-btn.role-keamanan.active {
            border-color: #0f2d59;
            background: #f1f5f9;
            color: #0f2d59;
            box-shadow: 0 4px 12px rgba(15, 45, 89, 0.15), inset 0 0 0 1px #0f2d59;
        }

        .role-card-btn.role-admin.active {
            border-color: #4f46e5;
            background: #f5f3ff;
            color: #312e81;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15), inset 0 0 0 1px #4f46e5;
        }

        .role-icon-box {
            font-size: 20px;
            line-height: 1;
        }

        .role-name {
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
        }

        .role-sub {
            font-size: 10px;
            color: #64748b;
        }

        /* Role Notice Box */
        .role-notice-banner {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            line-height: 1.45;
            margin-bottom: 20px;
        }

        .role-notice-masyarakat {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }

        .role-notice-keamanan {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .role-notice-admin {
            background-color: #fdf4ff;
            border: 1px solid #f5d0fe;
            color: #86198f;
        }

        /* Demo Quick-Fill Bar */
        .demo-credentials-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 18px;
        }

        .demo-cred-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 6px;
        }

        .demo-cred-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-demo-fill {
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 6px;
            border: 1px solid #bfdbfe;
            background: #ffffff;
            color: #1d4ed8;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-demo-fill:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        .btn-demo-jump {
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 6px;
            border: 1px solid #86efac;
            background: #f0fdf4;
            color: #15803d;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-demo-jump:hover {
            background: #dcfce7;
        }

        /* Header Form */
        .form-header {
            margin-bottom: 20px;
        }

        .form-subtitle-tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--primary-600);
            margin-bottom: 6px;
        }

        .form-header h2 {
            font-size: clamp(24px, 2.5vw, 30px);
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 13.5px;
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
            margin-bottom: 20px;
            border: 1px solid var(--border-soft);
        }

        .segmented-tab {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
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
            margin-bottom: 18px;
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
            gap: 15px;
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

        .input-control {
            width: 100%;
            height: 48px;
            padding: 0 16px 0 44px;
            font-size: 14px;
            color: var(--text-heading);
            background-color: #ffffff;
            border: 1.5px solid var(--border-soft);
            border-radius: 11px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .input-control:hover {
            border-color: #cbd5e1;
        }

        .input-control:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3.5px rgba(59, 130, 246, 0.15);
        }

        .input-control.has-error {
            border-color: var(--danger-600);
            background-color: #fffbfa;
        }

        .input-group.with-toggle .input-control {
            padding-right: 48px;
        }

        .btn-toggle-pass {
            position: absolute;
            right: 8px;
            width: 34px;
            height: 34px;
            border: none;
            background: transparent;
            color: #94a3b8;
            display: grid;
            place-items: center;
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
            margin-top: 6px;
            height: 48px;
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

        .btn-submit.btn-role-keamanan {
            background: linear-gradient(135deg, #0f2d59, #1e3a8a);
            box-shadow: 0 4px 14px rgba(15, 45, 89, 0.3);
        }

        .btn-submit.btn-role-keamanan:hover {
            background: linear-gradient(135deg, #0a1f3d, #142850);
            box-shadow: 0 6px 18px rgba(15, 45, 89, 0.4);
        }

        .btn-submit.btn-role-admin {
            background: linear-gradient(135deg, #4338ca, #312e81);
            box-shadow: 0 4px 14px rgba(67, 56, 202, 0.3);
        }

        .btn-submit.btn-role-admin:hover {
            background: linear-gradient(135deg, #3730a3, #1e1b4b);
            box-shadow: 0 6px 18px rgba(67, 56, 202, 0.4);
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
            margin-top: 16px;
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
            padding-top: 18px;
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
                font-size: 22px;
            }

            .role-cards-grid {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .role-card-btn {
                flex-direction: row;
                justify-content: flex-start;
                padding: 10px 14px;
                gap: 12px;
                text-align: left;
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
@php
    $isRegister = (($mode ?? 'login') === 'register');
    $presetRole = $initialRole ?? request()->query('role', 'masyarakat');
    if (!in_array($presetRole, ['masyarakat', 'keamanan', 'admin'])) {
        $presetRole = 'masyarakat';
    }
@endphp

<main class="auth-container">
    <!-- PANEL KIRI: Story & Keunggulan SafeRoad (Tema Biru Disesuaikan Role) -->
    <aside class="hero-panel" id="heroPanel" aria-label="Informasi SafeRoad">
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
            <span class="hero-badge" id="heroBadge">
                <span class="hero-badge-dot"></span>
                <span id="heroBadgeText">Sistem Keamanan Perjalanan</span>
            </span>

            <h1 class="hero-title" id="heroTitle">
                Perjalanan lebih tenang, <em>ketahui kondisi rute Anda.</em>
            </h1>

            <p class="hero-desc" id="heroDesc">
                SafeRoad membantu masyarakat memantau titik rawan, fasilitas keamanan, dan rute terlindungi secara real-time sebelum bepergian.
            </p>

            <div class="features-list" id="heroFeaturesList">
                <div class="feature-item" id="featItem1">
                    <span class="feature-icon-circle" id="featIcon1" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </span>
                    <div>
                        <h3 id="featTitle1">Analisis Rute &amp; Skor Keamanan</h3>
                        <p id="featDesc1">Dapatkan rekomendasi rute teraman berdasarkan pencahayaan, sebaran CCTV, dan riwayat insiden.</p>
                    </div>
                </div>

                <div class="feature-item" id="featItem2">
                    <span class="feature-icon-circle" id="featIcon2" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2a5 5 0 0 1 5 5c0 3-5 9-5 9s-5-6-5-9a5 5 0 0 1 5-5z"/>
                            <circle cx="12" cy="7" r="2"/>
                        </svg>
                    </span>
                    <div>
                        <h3 id="featTitle2">Women's Zone &amp; Fasilitas Terdekat</h3>
                        <p id="featDesc2">Akses informasi keamanan spesifik perempuan serta titik pos kepolisian dan rumah sakit aktif.</p>
                    </div>
                </div>

                <div class="feature-item" id="featItem3">
                    <span class="feature-icon-circle" id="featIcon3" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </span>
                    <div>
                        <h3 id="featTitle3">Pelaporan Warga Terverifikasi</h3>
                        <p id="featDesc3">Laporkan gangguan keamanan secara cepat dengan perlindungan privasi dan opsi anonim.</p>
                    </div>
                </div>
            </div>

            <div class="hero-emergency-notice" id="heroEmergencyNotice" role="note">
                <span style="font-size: 16px;">🚨</span>
                <div id="heroEmergencyText">
                    <strong>Panggilan Darurat:</strong> Hubungi <strong>112</strong> untuk respon darurat terpadu atau hubungi pos keamanan terdekat.
                </div>
            </div>
        </div>

        <div class="hero-footer">
            <span>© {{ date('Y') }} SafeRoad Indonesia</span>
            <span id="heroFooterRole">Portal Multi-Role Terpadu</span>
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
                    <a href="{{ route('register') }}" id="linkRegisterPrompt">Daftar</a>
                @endif
            </div>
        </div>

        <div class="form-center-box">

            <!-- PEMILIHAN ROLE LOGIN (Masyarakat, Pihak Keamanan, Admin) -->
            @if (!$isRegister)
                <div class="role-portal-box" aria-label="Pilih Role Pengguna">
                    <div class="role-portal-header">
                        <span class="role-portal-title">Pilih Role Akses Login:</span>
                        <span class="role-active-indicator" id="roleActiveIndicator">👤 Mode: Masyarakat</span>
                    </div>

                    <div class="role-cards-grid">
                        <!-- Role 1: Masyarakat -->
                        <button type="button" class="role-card-btn role-masyarakat active" id="btnRoleMasyarakat" onclick="switchLoginRole('masyarakat')">
                            <span class="role-icon-box">👤</span>
                            <span class="role-name">Masyarakat</span>
                            <span class="role-sub">Warga &amp; Rute</span>
                        </button>

                        <!-- Role 2: Pihak Keamanan -->
                        <button type="button" class="role-card-btn role-keamanan" id="btnRoleKeamanan" onclick="switchLoginRole('keamanan')">
                            <span class="role-icon-box">🛡️</span>
                            <span class="role-name">Keamanan</span>
                            <span class="role-sub">Petugas Lapangan</span>
                        </button>

                        <!-- Role 3: Administrator -->
                        <button type="button" class="role-card-btn role-admin" id="btnRoleAdmin" onclick="switchLoginRole('admin')">
                            <span class="role-icon-box">⚡</span>
                            <span class="role-name">Admin</span>
                            <span class="role-sub">Konsol Sistem</span>
                        </button>
                    </div>
                </div>

                <!-- Box Bantuan Akun Demo Cepat -->
                <div class="demo-credentials-box" id="demoCredentialsBox">
                    <div class="demo-cred-header">
                        <span>Akses Demo Frontend:</span>
                        <span style="font-weight: 500; color: #94a3b8;">Klik untuk kemudahan pengujian</span>
                    </div>
                    <div class="demo-cred-actions">
                        <button type="button" class="btn-demo-fill" id="btnFillDemo" onclick="fillDemoCredentials()">
                            👤 Isi Akun Warga Demo
                        </button>
                        <button type="button" class="btn-demo-jump" id="btnJumpDashboard" onclick="jumpToActiveDashboard()">
                            🚀 Buka Dashboard Warga
                        </button>
                    </div>
                </div>
            @endif

            <header class="form-header">
                <span class="form-subtitle-tag" id="formSubtitleTag">
                    {{ $isRegister ? 'REGISTRASI WARGA SAFEROAD' : 'PORTAL WARGA & MASYARAKAT' }}
                </span>
                <h2 id="formTitle">{{ $isRegister ? 'Mulai perjalananmu' : 'Selamat datang kembali' }}</h2>
                <p id="formDesc">
                    {{ $isRegister
                        ? 'Daftarkan akun untuk mengakses fitur navigasi aman dan berpartisipasi menjaga lingkungan rute.'
                        : 'Masuk dengan kredensial terdaftar untuk melanjutkan akses fitur keamanan perjalanan Anda.' }}
                </p>
            </header>

            <!-- Role Notice Banner -->
            <div class="role-notice-banner role-notice-masyarakat" id="roleNoticeBanner" style="{{ $isRegister ? 'display:none;' : '' }}">
                <span id="roleNoticeIcon">💡</span>
                <div id="roleNoticeText">
                    <strong>Akses Masyarakat Umum:</strong> Gunakan akun warga terdaftar Anda untuk melihat peta rute aman, Women's Zone, dan membuat laporan kejadian.
                </div>
            </div>

            <!-- Tab Masuk / Daftar -->
            <nav class="segmented-tabs" id="authSegmentedTabs" aria-label="Pilih Jenis Akses">
                <a href="{{ route('login') }}" class="segmented-tab {{ !$isRegister ? 'active' : '' }}" aria-current="{{ !$isRegister ? 'page' : 'false' }}">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="segmented-tab {{ $isRegister ? 'active' : '' }}" id="tabDaftarBaru" aria-current="{{ $isRegister ? 'page' : 'false' }}">
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
                <input type="hidden" name="role" id="inputSelectedRole" value="{{ $presetRole }}">

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

                <!-- Field Email / ID Petugas / Email Admin -->
                <div class="form-field">
                    <label for="email" id="labelEmail">
                        {{ $isRegister ? 'Alamat Email' : 'Alamat Email Warga' }}
                    </label>
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
                        <label for="password" id="labelPassword">Kata Sandi</label>
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
                    <span id="btnSubmitText">{{ $isRegister ? 'Daftar Akun SafeRoad' : 'Masuk sebagai Masyarakat' }}</span>
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
                <span id="securityBadgeText">Akses aman terenkripsi · Privasi data Anda selalu terjaga</span>
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
    // State role saat ini
    let currentRole = "{{ $presetRole }}";
    const isRegisterMode = {{ $isRegister ? 'true' : 'false' }};

    // Data konfigurasi setiap role
    const roleConfig = {
        masyarakat: {
            themeClass: '',
            badgeText: 'Sistem Keamanan Perjalanan',
            heroTitle: 'Perjalanan lebih tenang, <em>ketahui kondisi rute Anda.</em>',
            heroDesc: 'SafeRoad membantu masyarakat memantau titik rawan, fasilitas keamanan, dan rute terlindungi secara real-time sebelum bepergian.',
            feat1: {
                title: 'Analisis Rute & Skor Keamanan',
                desc: 'Dapatkan rekomendasi rute teraman berdasarkan pencahayaan, sebaran CCTV, dan riwayat insiden.'
            },
            feat2: {
                title: 'Women\'s Zone & Fasilitas Terdekat',
                desc: 'Akses informasi keamanan spesifik perempuan serta titik pos kepolisian dan rumah sakit aktif.'
            },
            feat3: {
                title: 'Pelaporan Warga Terverifikasi',
                desc: 'Laporkan gangguan keamanan secara cepat dengan perlindungan privasi dan opsi anonim.'
            },
            emergencyText: '<strong>Panggilan Darurat:</strong> Hubungi <strong>112</strong> untuk respon darurat terpadu atau hubungi pos keamanan terdekat.',
            subtitleTag: 'PORTAL WARGA & MASYARAKAT',
            formTitle: 'Selamat datang kembali',
            formDesc: 'Masuk dengan kredensial terdaftar untuk melanjutkan akses fitur keamanan perjalanan Anda.',
            labelEmail: 'Alamat Email Warga',
            placeholderEmail: 'nama@email.com',
            labelPassword: 'Kata Sandi Akun',
            placeholderPassword: 'Masukkan kata sandi akun',
            btnSubmit: 'Masuk sebagai Masyarakat',
            btnSubmitClass: '',
            noticeIcon: '💡',
            noticeClass: 'role-notice-masyarakat',
            noticeText: '<strong>Akses Masyarakat Umum:</strong> Gunakan akun warga terdaftar Anda untuk melihat peta rute aman, Women\'s Zone, dan membuat laporan kejadian.',
            demoEmail: 'nikenkaziasitumeangg@gmail.com',
            demoPass: 'password',
            btnFillText: '👤 Isi Akun Warga Demo',
            btnJumpText: '🚀 Buka Dashboard Warga',
            dashboardUrl: "{{ route('dashboard') }}?mode_warga=1",
            indicator: '👤 Mode: Masyarakat'
        },
        keamanan: {
            themeClass: 'role-theme-keamanan',
            badgeText: 'Pusat Koordinasi Petugas Lapangan',
            heroTitle: 'Siaga respon cepat, <em>verifikasi langsung kondisi lapangan.</em>',
            heroDesc: 'Platform operasional bagi personil kepolisian, satpam kawasan, dan tim respon darurat untuk meninjau laporan kejadian di TKP.',
            feat1: {
                title: 'Verifikasi Laporan Lapangan TKP',
                desc: 'Cek langsung aduan insiden warga, inspeksi lokasi kejadian, dan update status investigasi resmi.'
            },
            feat2: {
                title: 'Penanganan & Flag Laporan',
                desc: 'Tandai laporan mencurigakan atau validasi keabsahan data untuk pemutakhiran peta keamanan publik.'
            },
            feat3: {
                title: 'Koordinasi Pos & Patroli Wilayah',
                desc: 'Pantau sebaran titik rawan dan atur jadwal siaga pos keamanan secara tersinkronisasi.'
            },
            emergencyText: '<strong>Jalur Khusus Aparat:</strong> Hubungi radio komando sektor atau Pos Siaga Terdekat untuk bantuan unit taktis.',
            subtitleTag: 'PORTAL PETUGAS KEAMANAN & PATROLI RESMI',
            formTitle: 'Masuk Portal Petugas Keamanan',
            formDesc: 'Otentikasi khusus personil pos kepolisian, satpam kawasan, dan satuan patroli lapangan.',
            labelEmail: 'ID Petugas / Email Kedinasan',
            placeholderEmail: 'petugas.keamanan@saferoad.id atau NIP/KTA',
            labelPassword: 'Kata Sandi / PIN Kedinasan',
            placeholderPassword: 'Masukkan kata sandi dinas patroli',
            btnSubmit: 'Masuk sebagai Petugas Keamanan',
            btnSubmitClass: 'btn-role-keamanan',
            noticeIcon: '🛡️',
            noticeClass: 'role-notice-keamanan',
            noticeText: '<strong>Akses Terbatas Petugas Resmi:</strong> Portal ini khusus bagi aparat kepolisian, satuan pengamanan berwenang, dan petugas patroli lapangan.',
            demoEmail: 'petugas.keamanan@saferoad.id',
            demoPass: 'keamanan123',
            btnFillText: '🛡️ Isi Akun Petugas Demo',
            btnJumpText: '⚡ Masuk Langsung ke Dashboard Keamanan',
            dashboardUrl: "{{ route('keamanan.dashboard') }}",
            indicator: '🛡️ Mode: Petugas Keamanan'
        },
        admin: {
            themeClass: 'role-theme-admin',
            badgeText: 'Sistem Kontrol & Audit Master',
            heroTitle: 'Kendali sistem terpadu, <em>kelola keamanan publik secara transparan.</em>',
            heroDesc: 'Konsol kontrol utama SafeRoad untuk moderasi laporan masyarakat, manajemen pengguna, pemeliharaan data peta, dan analitik keamanan.',
            feat1: {
                title: 'Validasi & Moderasi Laporan Masuk',
                desc: 'Tinjau, setujui, atau tolak laporan insiden dari warga sebelum diterbitkan pada peta publik.'
            },
            feat2: {
                title: 'Manajemen Data Peta & Fasilitas',
                desc: 'Kelola sebaran titik pos pengamanan, CCTV, penerangan jalan umum, dan nomor kontak darurat.'
            },
            feat3: {
                title: 'Analitik Tren & Audit Sistem',
                desc: 'Pantau statistik KPI, distribusi kategori laporan, dan log aktivitas operasional SafeRoad.'
            },
            emergencyText: '<strong>Konsol Master:</strong> Sesi administrator dipantau dan diaudit otomatis oleh protokol keamanan sistem.',
            subtitleTag: 'PORTAL KONSOL ADMINISTRATOR UTAMA',
            formTitle: 'Masuk Konsol Administrator',
            formDesc: 'Akses khusus pengelola pusat SafeRoad untuk validasi laporan, analitik kejadian, dan manajemen master data.',
            labelEmail: 'Email Administrator Resmi',
            placeholderEmail: 'admin@saferoad.id',
            labelPassword: 'Kata Sandi Master Administrator',
            placeholderPassword: 'Masukkan kata sandi master admin',
            btnSubmit: 'Masuk sebagai Administrator',
            btnSubmitClass: 'btn-role-admin',
            noticeIcon: '⚙️',
            noticeClass: 'role-notice-admin',
            noticeText: '<strong>Konsol Kontrol Master:</strong> Akses administrator memerlukan otorisasi level master. Seluruh perubahan tercatat pada audit log.',
            demoEmail: 'admin@saferoad.id',
            demoPass: 'admin123',
            btnFillText: '⚡ Isi Akun Admin Demo',
            btnJumpText: '🚀 Masuk Langsung ke Dashboard Admin',
            dashboardUrl: "{{ route('admin.dashboard') }}",
            indicator: '⚡ Mode: Administrator'
        }
    };

    // Fungsi ganti role
    function switchLoginRole(role) {
        if (!roleConfig[role]) return;
        currentRole = role;

        // Simpan role di localStorage
        localStorage.setItem('saferoad_active_role', role);

        // Update hidden input
        const inputRole = document.getElementById('inputSelectedRole');
        if (inputRole) inputRole.value = role;

        // Update tombol tab role
        document.querySelectorAll('.role-card-btn').forEach(btn => btn.classList.remove('active'));
        const activeBtn = document.getElementById('btnRole' + role.charAt(0).toUpperCase() + role.slice(1));
        if (activeBtn) activeBtn.classList.add('active');

        const cfg = roleConfig[role];

        // Update Hero Panel
        const heroPanel = document.getElementById('heroPanel');
        if (heroPanel) {
            heroPanel.classList.remove('role-theme-keamanan', 'role-theme-admin');
            if (cfg.themeClass) heroPanel.classList.add(cfg.themeClass);
        }

        const heroBadgeText = document.getElementById('heroBadgeText');
        if (heroBadgeText) heroBadgeText.textContent = cfg.badgeText;

        const heroTitle = document.getElementById('heroTitle');
        if (heroTitle) heroTitle.innerHTML = cfg.heroTitle;

        const heroDesc = document.getElementById('heroDesc');
        if (heroDesc) heroDesc.textContent = cfg.heroDesc;

        const featTitle1 = document.getElementById('featTitle1');
        const featDesc1 = document.getElementById('featDesc1');
        if (featTitle1) featTitle1.textContent = cfg.feat1.title;
        if (featDesc1) featDesc1.textContent = cfg.feat1.desc;

        const featTitle2 = document.getElementById('featTitle2');
        const featDesc2 = document.getElementById('featDesc2');
        if (featTitle2) featTitle2.textContent = cfg.feat2.title;
        if (featDesc2) featDesc2.textContent = cfg.feat2.desc;

        const featTitle3 = document.getElementById('featTitle3');
        const featDesc3 = document.getElementById('featDesc3');
        if (featTitle3) featTitle3.textContent = cfg.feat3.title;
        if (featDesc3) featDesc3.textContent = cfg.feat3.desc;

        const heroEmergencyText = document.getElementById('heroEmergencyText');
        if (heroEmergencyText) heroEmergencyText.innerHTML = cfg.emergencyText;

        // Update Form Texts
        const indicator = document.getElementById('roleActiveIndicator');
        if (indicator) indicator.textContent = cfg.indicator;

        const subtitleTag = document.getElementById('formSubtitleTag');
        if (subtitleTag && !isRegisterMode) subtitleTag.textContent = cfg.subtitleTag;

        const formTitle = document.getElementById('formTitle');
        if (formTitle && !isRegisterMode) formTitle.textContent = cfg.formTitle;

        const formDesc = document.getElementById('formDesc');
        if (formDesc && !isRegisterMode) formDesc.textContent = cfg.formDesc;

        const labelEmail = document.getElementById('labelEmail');
        if (labelEmail && !isRegisterMode) labelEmail.textContent = cfg.labelEmail;

        const inputEmail = document.getElementById('email');
        if (inputEmail && !isRegisterMode) inputEmail.placeholder = cfg.placeholderEmail;

        const labelPassword = document.getElementById('labelPassword');
        if (labelPassword && !isRegisterMode) labelPassword.textContent = cfg.labelPassword;

        const inputPassword = document.getElementById('password');
        if (inputPassword && !isRegisterMode) inputPassword.placeholder = cfg.placeholderPassword;

        const btnSubmitText = document.getElementById('btnSubmitText');
        if (btnSubmitText && !isRegisterMode) btnSubmitText.textContent = cfg.btnSubmit;

        const btnSubmit = document.getElementById('btnSubmit');
        if (btnSubmit && !isRegisterMode) {
            btnSubmit.classList.remove('btn-role-keamanan', 'btn-role-admin');
            if (cfg.btnSubmitClass) btnSubmit.classList.add(cfg.btnSubmitClass);
        }

        // Update Notice Banner
        const noticeBanner = document.getElementById('roleNoticeBanner');
        const noticeIcon = document.getElementById('roleNoticeIcon');
        const noticeText = document.getElementById('roleNoticeText');
        if (noticeBanner) {
            noticeBanner.className = 'role-notice-banner ' + cfg.noticeClass;
        }
        if (noticeIcon) noticeIcon.textContent = cfg.noticeIcon;
        if (noticeText) noticeText.innerHTML = cfg.noticeText;

        // Update Demo Buttons
        const btnFillDemo = document.getElementById('btnFillDemo');
        if (btnFillDemo) btnFillDemo.textContent = cfg.btnFillText;

        const btnJumpDashboard = document.getElementById('btnJumpDashboard');
        if (btnJumpDashboard) btnJumpDashboard.textContent = cfg.btnJumpText;

        // Kelola Tab Daftar Baru jika bukan Masyarakat
        const tabDaftar = document.getElementById('tabDaftarBaru');
        const linkPrompt = document.getElementById('linkRegisterPrompt');
        if (role !== 'masyarakat') {
            if (tabDaftar) {
                tabDaftar.style.display = 'none';
            }
            if (linkPrompt) {
                linkPrompt.style.display = 'none';
            }
        } else {
            if (tabDaftar) {
                tabDaftar.style.display = 'flex';
            }
            if (linkPrompt) {
                linkPrompt.style.display = 'inline';
            }
        }

        // Update URL query tanpa reload browser
        if (window.history && window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('role', role);
            window.history.replaceState({}, '', url);
        }
    }

    // Isi Otomatis Kredensial Demo Sesuai Role
    function fillDemoCredentials() {
        const cfg = roleConfig[currentRole];
        if (!cfg) return;

        const emailEl = document.getElementById('email');
        const passEl = document.getElementById('password');

        if (emailEl) {
            emailEl.value = cfg.demoEmail;
            emailEl.focus();
        }
        if (passEl) {
            passEl.value = cfg.demoPass;
        }
    }

    // Loncat Langsung ke Dashboard Role Terpilih (Mode Frontend)
    function jumpToActiveDashboard() {
        const cfg = roleConfig[currentRole];
        if (!cfg) return;
        localStorage.setItem('saferoad_active_role', currentRole);
        window.location.href = cfg.dashboardUrl;
    }

    // Inisialisasi saat Halaman Dimuat
    document.addEventListener('DOMContentLoaded', function () {
        // Cek parameter role di URL atau localStorage
        const urlParams = new URLSearchParams(window.location.search);
        let activeRole = urlParams.get('role') || "{{ $presetRole }}" || localStorage.getItem('saferoad_active_role') || 'masyarakat';

        if (roleConfig[activeRole]) {
            switchLoginRole(activeRole);
        } else {
            switchLoginRole('masyarakat');
        }
    });

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
            // Pastikan role tersimpan di localStorage sebelum submit
            localStorage.setItem('saferoad_active_role', currentRole);

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
