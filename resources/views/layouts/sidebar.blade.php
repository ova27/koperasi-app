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

</nav>
