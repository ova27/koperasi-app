<div class="sidebar-shell h-full">
    {{-- SIDEBAR HEADER --}}
    <div class="sidebar-brand mb-5 flex items-center gap-3 px-1 py-1">

        {{-- ICON / LOGO --}}
        <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 via-blue-500 to-indigo-500 text-base font-bold text-white shadow-md ring-2 ring-white">
            K
        </div>

        {{-- TITLE & ROLE --}}
        <div class="sidebar-title min-w-0">
            <div class="truncate text-[11px] font-semibold uppercase tracking-[0.15em] text-slate-400">Koperasi</div>
            <div class="truncate text-sm font-bold tracking-wide text-slate-800 uppercase">
                Simpatik
            </div>

            <div class="truncate text-xs text-slate-500 capitalize">
                BPS Provinsi Banten
            </div>
        </div>
    </div>

    <hr class="mb-5 border-slate-200">

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
</div>

