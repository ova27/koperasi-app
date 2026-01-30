@php
    $user = auth()->user();
@endphp

<div class="p-4 border-b font-bold text-lg">
    Koperasi
</div>

<nav class="p-4 space-y-2 text-sm">

    {{-- DASHBOARD --}}
    <a href="{{ route('dashboard') }}"
        class="block px-3 py-2 rounded hover:bg-gray-100
        {{ request()->routeIs('dashboard') ? 'bg-gray-100 font-semibold' : '' }}">
        Dashboard
    </a>

<div>
    {{-- ADMIN --}}
    @role('admin')
        <div class="mt-4 text-gray-400 uppercase text-xs">Admin</div>

        <a href="{{ route('admin.anggota.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.anggota*') ? 'bg-gray-100 font-semibold' : '' }}">
            Data Anggota
        </a>
    @endrole
    
    {{-- KETUA --}}
    @role('ketua')
        <div class="mt-4 text-gray-400 uppercase text-xs">Ketua</div>

        <a href="{{ route('admin.pinjaman.pengajuan.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.pinjaman.pengajuan*') ? 'bg-gray-100 font-semibold' : '' }}">
            Persetujuan Pinjaman
        </a>
    @endrole

    {{-- BENDAHARA --}}
    @role('bendahara')
        <div class="mt-4 text-gray-400 uppercase text-xs">Bendahara</div>

        <a href="{{ route('admin.simpanan.generate-wajib') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.simpanan.generate-wajib*') ? 'bg-gray-100 font-semibold' : '' }}">
            Generate Simpanan Wajib
        </a>

        <a href="{{ route('admin.pinjaman.pencairan.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.pinjaman.pencairan*') ? 'bg-gray-100 font-semibold' : '' }}">
            Pencairan Pinjaman
        </a>

        <a href="{{ route('admin.pinjaman.aktif.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.pinjaman.cicil*','admin.pinjaman.aktif*') ? 'bg-gray-100 font-semibold' : '' }}">
            Cicilan Pinjaman
        </a>

        {{-- LAPORAN --}}
        <div class="mt-4 text-gray-400 uppercase text-xs">Laporan</div>

        <a href="{{ route('admin.laporan.simpanan-bulanan') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.laporan.simpanan-bulanan*') ? 'bg-gray-100 font-semibold' : '' }}">
            Laporan Simpanan
        </a>

        <a href="{{ route('admin.laporan.pinjaman.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
            {{ request()->routeIs('admin.laporan.pinjaman*') ? 'bg-gray-100 font-semibold' : '' }}">
            Laporan Pinjaman
        </a>
    @endrole

    {{-- ANGGOTA --}}
    @role('anggota')
        <div class="mt-4 text-gray-400 uppercase text-xs">Anggota</div>

        <a href="{{ route('anggota.simpanan.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100">
            Simpanan Saya
        </a>

        <a href="{{ route('anggota.pinjaman.index') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100">
            Pinjaman Saya
        </a>

        <a href="{{ route('anggota.pinjaman.create') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100">
            Ajukan Pinjaman
        </a>
    @endrole

</div>
</nav>
