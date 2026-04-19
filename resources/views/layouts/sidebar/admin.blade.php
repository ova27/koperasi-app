<div class="sidebar-shell h-full">

    <div class="space-y-2">
        {{-- MENU --}}
        <nav class="space-y-1">
            <ul class="menu">
            {{-- ================= DASHBOARD ================= --}}
            @can('view dashboard')
            <li class="menu-item">
                <a href="{{ route('dashboard') }}"
                class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            @endcan

            {{-- ================= DATA SAYA ================= --}}
            @canany(['view pinjaman saya', 'view simpanan saya'])
            <li class="menu-header">Data Saya</li>

            @can('view pinjaman saya')
            <li class="menu-item">
                <a href="{{ route('anggota.profil.index') }}"
                class="menu-link {{ request()->routeIs('anggota.profil.*') ? 'active' : '' }}">
                    <span class="menu-text">Profil</span>
                </a>
            </li>
            @endcan

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
            @endcanany

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

            @canany([
                'view anggota list',
                'manage users',
                'manage simpanan anggota',
                'pencairan pinjaman',
                'manage cicilan pinjaman',
                'view pengajuan pinjaman',
                'view saldo',
                'view arus koperasi',
                'view arus operasional',
                'view laporan arus kas',
                'view laporan simpanan bulanan',
                'view laporan pinjaman'
            ])
            <li class="menu-header">ADMIN</li>

            @canany(['view anggota list', 'manage users'])
            <li class="menu-item" x-data="{ open: {{ request()->routeIs('admin.anggota.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.master.rekening-koperasi.*') ? 'true' : 'false' }} }">
                <button
                    type="button"
                    @click="open = !open"
                    class="menu-link menu-toggle-btn w-full flex items-center justify-between transition-colors duration-200">
                    <div class="flex items-center gap-3">
                        <span class="menu-text">Master</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-transition class="ml-6 space-y-1 border-l-2 border-slate-200 pl-2">
                    <a href="{{ auth()->user()->can('manage users') ? route('admin.users.index') : route('admin.anggota.index') }}"
                        class="sub-menu-link {{ request()->routeIs('admin.anggota.*') || request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        Data
                    </a>
                    @if(auth()->user()->hasAnyRole(['admin', 'ketua', 'bendahara']))
                    <a href="{{ route('admin.master.rekening-koperasi.index') }}"
                        class="sub-menu-link {{ request()->routeIs('admin.master.rekening-koperasi.*') ? 'active' : '' }}">
                        Rekening Koperasi
                    </a>
                    @endif
                </div>
            </li>
            @endcanany

            @canany([
                'manage simpanan anggota',
                'pencairan pinjaman',
                'manage cicilan pinjaman',
                'view pengajuan pinjaman'
            ])
            <li class="menu-item" x-data="{ open: {{ request()->routeIs('admin.simpanan.*') || request()->routeIs('admin.pinjaman.data-anggota.*') || request()->routeIs('admin.pinjaman.pencairan.*') || request()->routeIs('admin.pinjaman.pengajuan.*') ? 'true' : 'false' }} }">
                <button
                    type="button"
                    @click="open = !open"
                    class="menu-link menu-toggle-btn w-full flex items-center justify-between transition-colors duration-200">
                    <div class="flex items-center gap-3">
                        <span class="menu-text">Transaksi</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-transition class="ml-6 space-y-1 border-l-2 border-slate-200 pl-2">
                    @canany(['view simpanan anggota', 'manage simpanan anggota'])
                    <a href="{{ route('admin.simpanan.index') }}"
                        class="sub-menu-link {{ request()->routeIs('admin.simpanan.*') ? 'active' : '' }}">
                        Simpanan Anggota
                    </a>
                    @endcanany

                    <a href="{{ route('admin.pinjaman.data-anggota.index') }}"
                        class="sub-menu-link {{ request()->routeIs('admin.pinjaman.data-anggota.*') ? 'active' : '' }}">
                        Pinjaman Anggota
                    </a>
                    @can('view pengajuan pinjaman')
                    <a href="{{ route('admin.pinjaman.pengajuan.index') }}"
                        class="sub-menu-link {{ request()->routeIs('admin.pinjaman.pengajuan.*')  ? 'active' : '' }}">
                        Approval Pengajuan Pinjaman
                    </a>
                    @endcan
                    <a href="{{ route('admin.pinjaman.pencairan.index') }}"
                        class="sub-menu-link {{ request()->routeIs('admin.pinjaman.pencairan.*')  ? 'active' : '' }}">
                        Pencairan Pinjaman
                    </a>
                </div>
            </li>
            @endcanany

            @canany([
                'view saldo',
                'view arus koperasi',
                'view arus operasional',
                'view laporan arus kas',
                'view laporan simpanan bulanan',
                'view laporan pinjaman'
            ])
            <li class="menu-item" x-data="{ open: {{ request()->routeIs('admin.keuangan.*') || request()->routeIs('admin.laporan.simpanan-bulanan') || request()->routeIs('admin.laporan.pinjaman.*') ? 'true' : 'false' }} }">
                <button
                    type="button"
                    @click="open = !open"
                    class="menu-link menu-toggle-btn w-full flex items-center justify-between transition-colors duration-200">
                    <div class="flex items-center gap-3">
                        <span class="menu-text">Keuangan</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-transition class="ml-6 space-y-1 border-l-2 border-slate-200 pl-2">
                    
                    @can('view laporan pinjaman')
                    <a href="{{ route('admin.laporan.potongan-bulanan.index') }}"
                        class="sub-menu-link {{ request()->routeIs('admin.laporan.potongan-bulanan.*') ? 'active' : '' }}">
                        Potongan Bulanan
                    </a>
                    @endcan
                    @can('view saldo')
                    <a href="{{ route('admin.keuangan.saldo') }}"
                        class="sub-menu-link {{ request()->routeIs('admin.keuangan.saldo') || request()->routeIs('admin.keuangan.arus.koperasi') || request()->routeIs('admin.keuangan.arus.operasional') ? 'active' : '' }}">
                        Transaksi & Neraca
                    </a>
                    @endcan
                    @canany([
                        'view laporan arus kas',
                        'view laporan simpanan bulanan',
                        'view laporan pinjaman'
                    ])
                    <a href="{{ route('admin.keuangan.arus-kas') }}"
                        class="sub-menu-link {{ request()->routeIs('admin.keuangan.arus-kas') || request()->routeIs('admin.laporan.simpanan-bulanan') || request()->routeIs('admin.laporan.pinjaman.*') ? 'active' : '' }}">
                        Rekap Bulanan
                    </a>
                    @endcanany

                </div>
            </li>
            @endcanany
            @endcanany
            </ul>
        </nav>
    </div>
</div>
