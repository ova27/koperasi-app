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
    </nav>

</div>


