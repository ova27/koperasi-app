{{-- SIDEBAR HEADER --}}
<div class="flex items-center gap-3 px-2 mb-4">

    {{-- ICON / LOGO --}}
    <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-600 text-white font-semibold text-sm">
        K
    </div>

    {{-- TITLE & ROLE --}}
    <div>
        <div class="text-[13px] font-semibold tracking-wide text-slate-800 uppercase">
            Koperasi Simpatik
        </div>
        <div class="text-[11px] text-slate-500 capitalize">
            {{ auth()->user()->getRoleNames()->implode(', ') }}
        </div>

    </div>

</div>

<hr class="border-slate-200 mb-4">

<div class="space-y-6">

    {{-- MENU --}}
    <nav class="space-y-1">
        <ul class="menu">
            {{-- ================= DASHBOARD ================= --}}
            @can('view dashboard')
            <li class="menu-item">
                <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>
            @endcan


            {{-- ================= DATA MASTER ================= --}}
            @can('view anggota list')
            <li class="menu-header">Data Master</li>

            <li class="menu-item">
                <a href="{{ route('admin.anggota.index') }}"
                class="{{ request()->routeIs('admin.anggota.*') ? 'active' : '' }}">
                    Data Anggota
                </a>
            </li>
            @endcan


            {{-- ================= PROSES & APPROVAL ================= --}}
            @can('view pengajuan pinjaman')
            <li class="menu-header">Proses & Approval</li>

            <li class="menu-item">
                <a href="{{ route('admin.pinjaman.pengajuan.index') }}"
                class="{{ request()->routeIs('admin.pinjaman.pengajuan.*') ? 'active' : '' }}">
                    Pengajuan Pinjaman
                </a>
            </li>
            @endcan


            {{-- ================= TRANSAKSI ================= --}}
            @canany([
                'manage simpanan anggota',
                'pencairan pinjaman',
                'manage cicilan pinjaman'
            ])
            <li class="menu-header">Transaksi</li>

                @can('manage simpanan anggota')
                <li class="menu-item">
                    <a href="{{ route('admin.simpanan.index') }}"
                    class="{{ request()->routeIs('admin.simpanan.*') ? 'active' : '' }}">
                        Simpanan Anggota
                    </a>
                </li>
                @endcan

                @can('pencairan pinjaman')
                <li class="menu-item">
                    <a href="{{ route('admin.pinjaman.pencairan.index') }}"
                    class="{{ request()->routeIs('admin.pinjaman.pencairan.*') ? 'active' : '' }}">
                        Pencairan Pinjaman
                    </a>
                </li>
                @endcan

                @can('manage cicilan pinjaman')
                <li class="menu-item">
                    <a href="{{ route('admin.pinjaman.aktif.index') }}"
                    class="{{ request()->routeIs('admin.pinjaman.aktif.*') ? 'active' : '' }}">
                        Cicilan Pinjaman
                    </a>
                </li>
                @endcan
            @endcanany


            {{-- ================= KEUANGAN ================= --}}
            @canany([
                'view saldo',
                'view arus koperasi',
                'view arus operasional'
            ])
            <li class="menu-header">Keuangan</li>

                @can('view saldo')
                <li class="menu-item">
                    <a href="{{ route('admin.keuangan.saldo') }}"
                    class="{{ request()->routeIs('admin.keuangan.saldo') ? 'active' : '' }}">
                        Saldo Koperasi
                    </a>
                </li>
                @endcan

                @can('view arus koperasi')
                <li class="menu-item">
                    <a href="{{ route('admin.keuangan.arus.koperasi') }}"
                    class="{{ request()->routeIs('admin.keuangan.arus.koperasi') ? 'active' : '' }}">
                        Arus Koperasi
                    </a>
                </li>
                @endcan

                @can('view arus operasional')
                <li class="menu-item">
                    <a href="{{ route('admin.keuangan.arus.operasional') }}"
                    class="{{ request()->routeIs('admin.keuangan.arus.operasional') ? 'active' : '' }}">
                        Arus Operasional
                    </a>
                </li>
                @endcan
            @endcanany


            {{-- ================= LAPORAN ================= --}}
            @canany([
                'view laporan arus kas',
                'view laporan simpanan bulanan',
                'view laporan pinjaman'
            ])
            <li class="menu-header">Laporan</li>

                @can('view laporan arus kas')
                <li class="menu-item">
                    <a href="{{ route('admin.keuangan.arus-kas') }}"
                    class="{{ request()->routeIs('admin.keuangan.arus-kas') ? 'active' : '' }}">
                        Laporan Arus Kas
                    </a>
                </li>
                @endcan

                @can('view laporan simpanan bulanan')
                <li class="menu-item">
                    <a href="{{ route('admin.laporan.simpanan-bulanan') }}"
                    class="{{ request()->routeIs('admin.laporan.simpanan-bulanan') ? 'active' : '' }}">
                        Simpanan Bulanan
                    </a>
                </li>
                @endcan

                @can('view laporan pinjaman')
                <li class="menu-item">
                    <a href="{{ route('admin.laporan.pinjaman.index') }}"
                    class="{{ request()->routeIs('admin.laporan.pinjaman.*') ? 'active' : '' }}">
                        Laporan Pinjaman
                    </a>
                </li>
                @endcan
            @endcanany


            {{-- ================= PENGATURAN ================= --}}
            @canany(['edit profil', 'manage users'])
            <li class="menu-header">Pengaturan</li>

                @can('edit profil')
                <li class="menu-item">
                    <a href="{{ route('profile.edit') }}"
                    class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        Profil
                    </a>
                </li>
                @endcan

                @can('manage users')
                <li class="menu-item">
                    <a href="{{ route('admin.users.index') }}"
                    class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        Manajemen Pengguna
                    </a>
                </li>
                @endcan
            @endcanany

        </ul>
    </nav>

</div>


