<nav class="bottom-navigation" aria-label="Navigasi Bawah Ponsel">
    @php
        $currentRole = auth()->user()?->role ?? 'masyarakat';
    @endphp

    @if($currentRole === 'admin')
        {{-- Grup Mobile: Administrator --}}
        <div id="bnavGroupAdmin" class="bnav-group">
            <a href="{{ route('admin.dashboard') }}" class="nav-tab {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-tab-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </span>
                <span class="nav-tab-label">Konsol</span>
            </a>
            <a href="{{ route('admin.validasi') }}" class="nav-tab {{ request()->routeIs('admin.validasi') ? 'active' : '' }}">
                <span class="nav-tab-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </span>
                <span class="nav-tab-label">Validasi</span>
            </a>
            <a href="{{ route('admin.peta') }}" class="nav-tab {{ request()->routeIs('admin.peta') ? 'active' : '' }}">
                <span class="nav-tab-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                        <line x1="8" y1="2" x2="8" y2="18"></line>
                        <line x1="16" y1="6" x2="16" y2="22"></line>
                    </svg>
                </span>
                <span class="nav-tab-label">Peta</span>
            </a>
            <a href="{{ route('admin.statistik') }}" class="nav-tab {{ request()->routeIs('admin.statistik') ? 'active' : '' }}">
                <span class="nav-tab-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                </span>
                <span class="nav-tab-label">Statistik</span>
            </a>
            <a href="{{ url('/darurat') }}" class="nav-tab {{ request()->is('darurat*') ? 'active' : '' }}">
                <span class="nav-tab-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </span>
                <span class="nav-tab-label">Darurat</span>
            </a>
        </div>
    @elseif($currentRole === 'keamanan')
        {{-- Grup Mobile: Petugas Keamanan --}}
        <div id="bnavGroupKeamanan" class="bnav-group">
            <a href="{{ route('keamanan.dashboard') }}" class="nav-tab {{ request()->is('keamanan*') ? 'active' : '' }}">
                <span class="nav-tab-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </span>
                <span class="nav-tab-label">Komando</span>
            </a>
            <a href="{{ route('keamanan.dashboard') }}#tabelAduan" class="nav-tab">
                <span class="nav-tab-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </span>
                <span class="nav-tab-label">Aduan TKP</span>
            </a>
            <a href="{{ url('/darurat') }}" class="nav-tab {{ request()->is('darurat*') ? 'active' : '' }}">
                <span class="nav-tab-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </span>
                <span class="nav-tab-label">Hotline</span>
            </a>
        </div>
    @else
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

            @auth
            <a href="{{ url('/profil') }}" class="nav-tab {{ request()->is('profil*') ? 'active' : '' }}">
                <span class="nav-tab-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <span class="nav-tab-label">Profil</span>
            </a>
            @endauth
        </div>
    @endif
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
