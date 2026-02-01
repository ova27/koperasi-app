<ul class="menu">

    <li class="menu-item">
        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>
    </li>

    <li class="menu-header">Data Saya</li>

    @can('view simpanan saya')
    <li class="menu-item">
        <a href="{{ route('anggota.simpanan.index') }}"
           class="{{ request()->routeIs('anggota.simpanan.*') ? 'active' : '' }}">
            Simpanan Saya
        </a>
    </li>
    @endcan

    @can('view pinjaman saya')
    <li class="menu-item">
        <a href="{{ route('anggota.pinjaman.index') }}"
           class="{{ request()->routeIs('anggota.pinjaman.index') ? 'active' : '' }}">
            Pinjaman Saya
        </a>
    </li>
    @endcan

    @can('create pinjaman')
    <li class="menu-item">
        <a href="{{ route('anggota.pinjaman.ajukan') }}"
           class="{{ request()->routeIs('anggota.pinjaman.ajukan') ? 'active' : '' }}">
            Ajukan Pinjaman
        </a>
    </li>
    @endcan

    {{-- ================= LAPORAN PRIBADI ================= --}}
    @canany(['view laporan simpanan pribadi', 'view laporan pinjaman pribadi'])
    <li class="menu-header">Laporan Saya</li>

        @can('view laporan simpanan pribadi')
        <li class="menu-item">
            <a href="{{ route('anggota.laporan.simpanan') }}"
               class="{{ request()->routeIs('anggota.laporan.simpanan') ? 'active' : '' }}">
                Laporan Simpanan
            </a>
        </li>
        @endcan

        @can('view laporan pinjaman pribadi')
        <li class="menu-item">
            <a href="{{ route('anggota.laporan.pinjaman') }}"
               class="{{ request()->routeIs('anggota.laporan.pinjaman') ? 'active' : '' }}">
                Laporan Pinjaman
            </a>
        </li>
        @endcan
    @endcanany

    <li class="menu-header">Akun</li>

    <li class="menu-item">
        <a href="{{ route('profile.edit') }}"
           class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
            Profil
        </a>
    </li>

</ul>
