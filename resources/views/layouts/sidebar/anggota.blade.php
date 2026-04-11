{{-- SIDEBAR HEADER --}}
<div class="flex items-center gap-3 px-2 mb-4">

    {{-- ICON / LOGO --}}
    <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-blue-400 to-blue-500 text-white font-bold text-lg shadow-md">
        K
    </div>

    {{-- TITLE & ROLE --}}
    <div class="sidebar-title">
        <div class="text-sm font-bold tracking-wide text-slate-800 uppercase">
            Koperasi Simpatik
        </div>

        <div class="text-xs text-slate-500 capitalize">
            BPS Provinsi Banten
        </div>
    </div>
</div>

<hr class="border-slate-300 mb-6">

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
        </ul>
    </nav>

</div>

