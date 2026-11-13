<nav class="bottom-navigation" aria-label="Navigasi Bawah Ponsel">
    @auth
        <a href="{{ route('dashboard') }}" class="nav-tab {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-tab-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
            </span>
            <span class="nav-tab-label">Dashboard</span>
        </a>
    @endauth

    <a href="{{ url('/rute') }}" class="nav-tab {{ request()->is('rute') ? 'active' : '' }}">
        <span class="nav-tab-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </span>
        <span class="nav-tab-label">Cari Rute</span>
    </a>

    <a href="{{ url('/rute/bandingkan') }}" class="nav-tab {{ request()->is('rute/bandingkan') ? 'active' : '' }}">
        <span class="nav-tab-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 3h5v5"/>
                <path d="M4 20L21 3"/>
                <path d="M21 16v5h-5"/>
                <path d="M15 15l6 6"/>
                <path d="M4 4l5 5"/>
            </svg>
        </span>
        <span class="nav-tab-label">Bandingkan</span>
    </a>

    <a href="{{ url('/profil') }}" class="nav-tab {{ request()->is('profil*') || request()->is('verifikasi*') ? 'active' : '' }}">
        <span class="nav-tab-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </span>
        <span class="nav-tab-label">Profil</span>
    </a>
</nav>

<style>
    .bottom-navigation {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 68px;
        background-color: #ffffff;
        border-top: 1px solid var(--border-soft);
        padding: 0 12px;
        justify-content: space-around;
        align-items: center;
        z-index: 1000;
        box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.06);
    }

    .nav-tab {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        flex: 1;
        height: 100%;
        color: var(--text-muted);
        transition: color 0.15s ease;
        text-decoration: none;
    }

    .nav-tab-icon {
        width: 22px;
        height: 22px;
        display: grid;
        place-items: center;
    }

    .nav-tab-icon svg {
        width: 100%;
        height: 100%;
        transition: transform 0.15s ease;
    }

    .nav-tab-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: -0.01em;
    }

    .nav-tab.active {
        color: var(--primary-600);
    }

    .nav-tab.active .nav-tab-icon svg {
        transform: scale(1.1);
    }

    .nav-tab.active .nav-tab-label {
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .bottom-navigation {
            display: flex;
        }
    }
</style>
