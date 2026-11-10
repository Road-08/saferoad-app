<nav class="bottom-navigation">
    <a href="/" class="nav-item">
        <span class="nav-icon">⌂</span>
        <span>Rute</span>
    </a>

    <a href="#" class="nav-item">
        <span class="nav-icon">♡</span>
        <span>Keamanan</span>
    </a>

    <a href="#" class="nav-item">
        <span class="nav-icon">＋</span>
        <span>Laporan</span>
    </a>

    <a href="#" class="nav-item">
        <span class="nav-icon">♀</span>
        <span>Perempuan</span>
    </a>

    <a href="{{ route('profile.index') }}"
       class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <span class="nav-icon">♙</span>
        <span>Akun</span>
    </a>
</nav>
