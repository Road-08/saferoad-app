<nav class="bottom-navigation" aria-label="Navigasi Bawah Ponsel">
    {{-- Grup Mobile: Masyarakat --}}
    <div id="bnavGroupMasyarakat" class="bnav-group">
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

        <a href="{{ url('/rute') }}" class="nav-tab {{ request()->is('rute*') ? 'active' : '' }}">
            <span class="nav-tab-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </span>
            <span class="nav-tab-label">Rute</span>
        </a>

        @if(auth()->user()?->canAccessWomenZone())
        <a href="{{ url('/womens-zone') }}" class="nav-tab {{ request()->is('womens-zone*') ? 'active' : '' }}" style="{{ request()->is('womens-zone*') ? 'color: #be185d;' : '' }}">
            <span class="nav-tab-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="9" r="6"/>
                    <path d="M12 15v7M9 19h6"/>
                </svg>
            </span>
            <span class="nav-tab-label" style="font-weight: 700;">Women's</span>
        </a>
        @endif

        <a href="{{ url('/laporkan') }}" class="nav-tab {{ request()->is('laporkan*') ? 'active' : '' }}">
            <span class="nav-tab-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </span>
            <span class="nav-tab-label">Lapor</span>
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
    </div>

    {{-- Grup Mobile: Petugas Keamanan --}}
    <div id="bnavGroupKeamanan" class="bnav-group" style="display: none;">
        <a href="{{ route('keamanan.dashboard') }}" class="nav-tab {{ request()->is('keamanan*') ? 'active' : '' }}">
            <span class="nav-tab-icon">🛡️</span>
            <span class="nav-tab-label">Komando</span>
        </a>
        <a href="{{ route('keamanan.dashboard') }}#tabelAduan" class="nav-tab">
            <span class="nav-tab-icon">📋</span>
            <span class="nav-tab-label">Aduan TKP</span>
        </a>
        <a href="{{ url('/darurat') }}" class="nav-tab {{ request()->is('darurat*') ? 'active' : '' }}">
            <span class="nav-tab-icon">🚨</span>
            <span class="nav-tab-label">Hotline</span>
        </a>
    </div>

    {{-- Grup Mobile: Administrator (Halaman Terpisah) --}}
    <div id="bnavGroupAdmin" class="bnav-group" style="display: none;">
        <a href="{{ route('admin.dashboard') }}" class="nav-tab {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-tab-icon">⚡</span>
            <span class="nav-tab-label">Konsol</span>
        </a>
        <a href="{{ route('admin.validasi') }}" class="nav-tab {{ request()->routeIs('admin.validasi') ? 'active' : '' }}">
            <span class="nav-tab-icon">⚖️</span>
            <span class="nav-tab-label">Validasi</span>
        </a>
        <a href="{{ route('admin.peta') }}" class="nav-tab {{ request()->routeIs('admin.peta') ? 'active' : '' }}">
            <span class="nav-tab-icon">🗺️</span>
            <span class="nav-tab-label">Peta</span>
        </a>
        <a href="{{ route('admin.statistik') }}" class="nav-tab {{ request()->routeIs('admin.statistik') ? 'active' : '' }}">
            <span class="nav-tab-icon">📊</span>
            <span class="nav-tab-label">Statistik</span>
        </a>
        <a href="{{ url('/darurat') }}" class="nav-tab {{ request()->is('darurat*') ? 'active' : '' }}">
            <span class="nav-tab-icon">🚨</span>
            <span class="nav-tab-label">Darurat</span>
        </a>
    </div>
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
        align-items: center;
        z-index: 1000;
        box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.06);
    }

    .bnav-group {
        display: flex;
        width: 100%;
        height: 100%;
        justify-content: space-around;
        align-items: center;
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
        font-size: 16px;
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

    .nav-tab:hover,
    .nav-tab.active {
        color: var(--primary-600);
    }

    .nav-tab.active .nav-tab-icon svg {
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .bottom-navigation {
            display: flex;
        }
    }
</style>

<script>
    (function() {
        const path = window.location.pathname;
        let role = localStorage.getItem('saferoad_active_role');

        if (path.startsWith('/keamanan')) {
            role = 'keamanan';
        } else if (path.startsWith('/admin')) {
            role = 'admin';
        } else if (!role) {
            role = 'masyarakat';
        }

        const gMasy = document.getElementById('bnavGroupMasyarakat');
        const gKeam = document.getElementById('bnavGroupKeamanan');
        const gAdm = document.getElementById('bnavGroupAdmin');

        if (gMasy) gMasy.style.display = (role === 'masyarakat') ? 'flex' : 'none';
        if (gKeam) gKeam.style.display = (role === 'keamanan') ? 'flex' : 'none';
        if (gAdm) gAdm.style.display = (role === 'admin') ? 'flex' : 'none';
    })();
</script>
