<div class="sidebar-shell h-full">
    
    <div class="space-y-2">

        {{-- MENU --}}
        <nav class="space-y-1">
            <ul class="menu">
            <li class="menu-item">
                <a href="{{ route('dashboard') }}"
                class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            <li class="menu-header">Data Saya</li>

            <li class="menu-item">
                <a href="{{ route('anggota.profil.index') }}"
                class="menu-link {{ request()->routeIs('anggota.profil.*') ? 'active' : '' }}">
                    <span class="menu-text">Profil</span>
                </a>
            </li>

            @can('view simpanan saya')
            <li class="menu-item">
                <a href="{{ route('anggota.simpanan.index') }}"
                class="menu-link {{ request()->routeIs('anggota.simpanan.*') ? 'active' : '' }}">
                    <span class="menu-text">Simpanan Saya</span>
                </a>
            </li>
            @endcan

            @can('view pinjaman saya')
                <li class="menu-item">
                    <a href="{{ route('anggota.pinjaman.index') }}"
                    class="menu-link {{ request()->routeIs('anggota.pinjaman.index') || request()->routeIs('anggota.pinjaman.ajukan') ? 'active' : '' }}">
                        <span class="menu-text">Pinjaman Saya</span>
                    </a>
                </li>
            @endcan
            {{-- ================= DATA ANGGOTA ================= --}}
            @can('view anggota list')
            <li class="menu-header">Master</li>
            <li class="menu-item">
                <a href="{{ route('admin.anggota.index') }}"
                class="menu-link {{ request()->routeIs('admin.anggota.*') ? 'active' : '' }}">
                    <span class="menu-text">Data Anggota</span>
                </a>
            </li>
            @endcan

            {{-- ================= LAPORAN PRIBADI ================= --}}
            @canany(['view laporan simpanan pribadi', 'view laporan pinjaman pribadi'])
            <li class="menu-header">Laporan Saya</li>

                @can('view laporan simpanan pribadi')
                <li class="menu-item">
                    <a href="{{ route('anggota.laporan.simpanan') }}"
                    class="menu-link {{ request()->routeIs('anggota.laporan.simpanan') ? 'active' : '' }}">
                        <span class="menu-text">Laporan Simpanan</span>
                    </a>
                </li>
                @endcan

                @can('view laporan pinjaman pribadi')
                <li class="menu-item">
                    <a href="{{ route('anggota.laporan.pinjaman') }}"
                    class="menu-link {{ request()->routeIs('anggota.laporan.pinjaman') ? 'active' : '' }}">
                       <span class="menu-text">Laporan Pinjaman</span>
                    </a>
                </li>
                @endcan
            @endcanany

            @can('view laporan pinjaman')
            <li class="menu-header">Laporan</li>
            <li class="menu-item">
                <a href="{{ route('admin.laporan.potongan-bulanan.index') }}"
                class="menu-link {{ request()->routeIs('admin.laporan.potongan-bulanan.*') ? 'active' : '' }}">
                    <span class="menu-text">Rincian Potongan Bulanan</span>
                </a>
            </li>
            @endcan
            </ul>
        </nav>

    </div>
</div>

