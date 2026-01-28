@php
    $user = auth()->user();
@endphp

<div class="p-4 border-b font-bold text-lg">
    Koperasi
</div>

<nav class="p-4 space-y-2 text-sm">

    <a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">
        Dashboard
    </a>

    {{-- ADMIN --}}
    @if($user?->hasRole('admin'))
        <div class="mt-4 text-gray-400 uppercase text-xs">Admin</div>

        <a href="{{ route('admin.anggota.index') }}" 
            class="block px-3 py-2 rounded hover:bg-gray-100">
            Data Anggota
        </a>
    @endif

    {{-- KETUA --}}
    @if($user?->hasRole('ketua'))
        <div class="mt-4 text-gray-400 uppercase text-xs">Ketua</div>

        <a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">
            Persetujuan Pinjaman
        </a>
    @endif

    {{-- BENDAHARA --}}
    @if($user?->hasRole('bendahara'))
        <div class="mt-4 text-gray-400 uppercase text-xs">Bendahara</div>

        <a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">
            Pencairan Pinjaman
        </a>
    @endif

    @role('bendahara')
        <a href="{{ route('admin.simpanan.generate-wajib') }}"
            class="px-3 py-2 rounded hover:bg-gray-100">
            Generate Simpanan Wajib
        </a>
    @endrole

    {{-- ANGGOTA --}}
    @if($user?->hasRole('anggota'))
        <div class="mt-4 text-gray-400 uppercase text-xs">Anggota</div>

        <a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">
            Simpanan Saya
        </a>

        <a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">
            Pinjaman Saya
        </a>
    @endif

    @if(auth()->check() && auth()->user()->hasRole('bendahara'))
        
        {{-- LAPORAN --}}
        <div class="mt-4 text-gray-400 uppercase text-xs">
            Laporan
        </div>

        <a
            href="{{ route('admin.laporan.simpanan-bulanan') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
                {{ request()->routeIs('admin.laporan.simpanan-bulanan*') ? 'bg-gray-100 font-semibold' : '' }}">
            Laporan Simpanan
        </a>

        <a
            href="{{ route('admin.laporan.pinjaman') }}"
            class="block px-3 py-2 rounded hover:bg-gray-100
                {{ request()->routeIs('admin.laporan.pinjaman*') ? 'bg-gray-100 font-semibold' : '' }}">
            Laporan Pinjaman
        </a>

    @endif

</nav>
