@php
    $user = auth()->user();
@endphp

<div class="p-4 border-b font-bold text-lg">
    Koperasi
</div>

<nav class="p-4 space-y-2 text-sm">

    {{-- DASHBOARD (SEMUA ROLE) --}}
    <a href="{{ route('dashboard') }}"
        class="block px-3 py-2 rounded hover:bg-gray-100
        {{ request()->routeIs('dashboard') ? 'bg-gray-100 font-semibold text-blue-600' : '' }}">
        Dashboard
    </a>

    {{-- MENU ADMIN & BENDAHARA (MANAJEMEN ANGGOTA) --}}
    @role('admin|bendahara')
        <div class="mt-4 pt-4 border-t border-gray-100 text-gray-400 uppercase text-[10px] tracking-wider font-bold">Master Data</div>
        
        <a href="{{ route('admin.anggota.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.anggota.*') ? 'bg-gray-100 font-semibold' : '' }}">
            Daftar Anggota
        </a>
    @endrole

    {{-- MENU KEUANGAN (ADMIN, KETUA, BENDAHARA) --}}
    @can('lihat-keuangan-global')
        <div class="mt-4 pt-4 border-t border-gray-100 text-gray-400 uppercase text-[10px] tracking-wider font-bold">Keuangan Kas</div>

        <a href="{{ route('admin.keuangan.saldo') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.keuangan.saldo*') ? 'bg-gray-100 font-semibold' : '' }}">
            Saldo Kas & Bank
        </a>

        <a href="{{ route('admin.keuangan.arus.koperasi') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.keuangan.arus.koperasi*') ? 'bg-gray-100 font-semibold' : '' }}">
            Arus Kas Koperasi
        </a>

        <a href="{{ route('admin.keuangan.arus.operasional') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.keuangan.arus.operasional*') ? 'bg-gray-100 font-semibold' : '' }}">
            Arus Operasional
        </a>

        <a href="{{ route('admin.keuangan.laporan.arus-kas') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.keuangan.laporan.arus-kas*') ? 'bg-gray-100 font-semibold' : '' }}">
            Laporan Arus Kas
        </a>

        <div class="mt-2 text-gray-400 uppercase text-[10px] font-bold">Laporan Tahunan/Bulanan</div>
            
            <a href="{{ route('admin.laporan.simpanan-bulanan') }}"
                class="block px-3 py-2 rounded hover:bg-gray-100
                {{ request()->routeIs('admin.laporan.simpanan-bulanan*') ? 'bg-gray-100 font-semibold' : '' }}">
                Laporan Simpanan Bulanan
            </a>

            <a href="{{ route('admin.laporan.pinjaman.index') }}"
                class="block px-3 py-2 rounded hover:bg-gray-100
                {{ request()->routeIs('admin.laporan.pinjaman.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Laporan Pinjaman
            </a>
    @endcan

    {{-- KHUSUS KETUA (APPROVAL) --}}
    @role('ketua')
        <div class="mt-4 pt-4 border-t border-gray-100 text-gray-400 uppercase text-[10px] tracking-wider font-bold">Persetujuan</div>
        
        <a href="{{ route('admin.pinjaman.pengajuan.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.pinjaman.pengajuan.*') ? 'bg-gray-100 font-semibold' : '' }}">
            Pengajuan Pinjaman
        </a>
    @endrole

    {{-- KHUSUS BENDAHARA (OPERASIONAL PINJAMAN & SIMPANAN) --}}
    @role('bendahara')
        <div class="mt-4 pt-4 border-t border-gray-100 text-gray-400 uppercase text-[10px] tracking-wider font-bold">
            Operasional Bendahara
        </div>

        {{-- Input Simpanan Bulanan --}}
        <a href="{{ route('admin.simpanan.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.simpanan.index*') ? 'bg-gray-100 font-semibold' : '' }}">
            Simpanan Bulanan & Manual
        </a>

        {{-- Cicilan pinjaman --}}
        <a href="{{ route('admin.pinjaman.aktif.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.pinjaman.aktif*','admin.pinjaman.cicil*') ? 'bg-gray-100 font-semibold' : '' }}">
            Cicilan Pinjaman Aktif
        </a>

        {{-- Pencairan pinjaman --}}
        <a href="{{ route('admin.pinjaman.pencairan.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.pinjaman.pencairan*') ? 'bg-gray-100 font-semibold' : '' }}">
            Pencairan Pinjaman
        </a>
    @endrole


    {{-- MENU ANGGOTA (DATA PRIBADI) --}}
    @role('anggota')
        <div class="mt-4 pt-4 border-t border-gray-100 text-gray-400 uppercase text-[10px] tracking-wider font-bold">Layanan Anggota</div>

        <a href="{{ route('anggota.simpanan.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('anggota.simpanan*') ? 'bg-gray-100 font-semibold' : '' }}">
            Simpanan Saya
        </a>

        <a href="{{ route('anggota.pinjaman.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('anggota.pinjaman*') ? 'bg-gray-100 font-semibold' : '' }}">
            Pinjaman Saya
        </a>
    @endrole

</nav>
