@extends('layouts.main')

@section('title', 'Detail Anggota')

@section('content')
<div class="max-w-6xl mx-auto space-y-4">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $anggota->nama }}

                <x-status-anggota 
                    :status="$anggota->status"
                    :alasan="$alasanKeluarMap[$anggota->id] ?? null"/>
            </h1>   
            
            @if ($anggota->status !== 'aktif')
            <span class="text-sm italic text-gray-500 border-l-4 border-red-500 pl-2 mt-1 block">
                Seluruh transaksi dibekukan.
            </span>
            @endif         
        </div>

        {{-- KEMBALI KE INDEX --}}
        <a href="{{ route('admin.anggota.index') }}"
           class="text-sm text-gray-600 hover:underline">
            ← Kembali ke Daftar Anggota
        </a>
    </div>

    <div x-data="{ tab: 'profil' }" class="mt-6">

    {{-- TAB NAV --}}
    <div class="flex gap-6 border-b text-sm">

        <button @click="tab = 'profil'"
            :class="tab === 'profil' ? 'border-b-2 border-black text-black font-medium' : 'text-gray-400'"
            class="pb-2">
            Profil
        </button>

        <button @click="tab = 'simpanan'"
            :class="tab === 'simpanan' ? 'border-b-2 border-black text-black font-medium' : 'text-gray-400'"
            class="pb-2">
            Simpanan
        </button>

        <button @click="tab = 'pinjaman'"
            :class="tab === 'pinjaman' ? 'border-b-2 border-black text-black font-medium' : 'text-gray-400'"
            class="pb-2">
            Pinjaman
        </button>

    </div>

    {{-- PROFIL --}}
    <div x-show="tab === 'profil'" x-transition class="mt-6">
        <div class="bg-white border rounded-lg grid grid-cols-2 md:grid-cols-2 gap-y-6 px-4 py-3 text-sm">
            <div>
                <div class="text-gray-500">Nama</div>
                <div class="font-medium">{{ $anggota->nama }}</div>
            </div>

            <div>
                <div class="text-gray-500">NIP</div>
                <div class="font-medium">{{ $anggota->nip }}</div>
            </div>

            <div>
                <div class="text-gray-500">Email</div>
                <div class="font-medium">
                    {{ $anggota->user->email ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Jenis Kelamin</div>
                <div class="font-medium">{{ $anggota->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
            </div>
            
            <div>
                <div class="text-gray-500">Jabatan</div>
                <div class="font-medium">{{ $anggota->jabatan }}</div>
            </div>

            <div>
                <div class="text-gray-500">Bank</div>
                <div class="font-medium">(Nama Bank)</div>
            </div>
            <div>
                <div class="text-gray-500">No Rekening</div>
                <div class="font-medium">(No Rekening)</div>
            </div>

            <div>
                <div class="text-gray-500">Tanggal Masuk</div>
                <div class="font-medium">
                    {{ \Carbon\Carbon::parse($anggota->tanggal_masuk)->format('d-m-Y') }}
                </div>
            </div>

            @if($anggota->status === 'tidak_aktif')
            <div>
                <div class="text-gray-500">Tanggal Keluar</div>
                <div class="font-medium">
                    {{ $anggota->tanggal_keluar ? \Carbon\Carbon::parse($anggota->tanggal_keluar)->format('d-m-Y') : '-' }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- SIMPANAN --}}
    <div x-show="tab === 'simpanan'" x-transition class="mt-6 space-y-4">  
        @if($canViewFullDetails)
        
        @php
            $pokok = $saldoSimpanan['pokok'] ?? 0;
            $wajib = $saldoSimpanan['wajib'] ?? 0;
            $sukarela = $saldoSimpanan['sukarela'] ?? 0;
            $total = $pokok + $wajib + $sukarela;
        @endphp

        <div 
            x-data="{open: {{ (request()->has('simpanan_page') || request()->has('jenis')) ? 'true' : 'false' }}}"
            class="bg-white border rounded-lg px-5 py-4"
        >
            <div class="flex items-start justify-between mb-3">

                {{-- TITLE + STATUS --}}
                <div>
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        Ringkasan Simpanan
                    </h2>

                    <p class="text-xs text-gray-500">
                        Informasi simpanan anggota
                    </p>
                </div>

                {{-- TOGGLE BUTTON --}}
                <button 
                    @click="open = !open"
                    class="flex items-center gap-2 text-xs px-3 py-1.5 rounded-full border 
                        bg-white hover:bg-gray-50 text-gray-700 transition"
                >
                    <span x-text="open ? 'Sembunyikan' : 'Riwayat Transaksi Simpanan'"></span>

                    <svg 
                        :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            {{-- RINGKASAN CARD --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">

                <div class="stat-card stat-info">
                    <p class="text-gray-500 text-xs">Pokok</p>
                    <p class="font-semibold text-gray-800 mt-1">
                        Rp {{ number_format($pokok, 0, ',', '.') }}
                    </p>
                </div>

                <div class="stat-card stat-info">
                    <p class="text-gray-500 text-xs">Wajib</p>
                    <p class="font-semibold text-gray-800 mt-1">
                        Rp {{ number_format($wajib, 0, ',', '.') }}
                    </p>
                </div>

                <div class="stat-card stat-info">
                    <p class="text-gray-500 text-xs">Sukarela</p>
                    <p class="font-semibold text-gray-800 mt-1">
                        Rp {{ number_format($sukarela, 0, ',', '.') }}
                    </p>
                </div>

                <div class="stat-card stat-info">
                    <p class="text-gray-600 text-xs">TOTAL</p>
                    <p class="font-bold text-gray-800 mt-1 text-base">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </p>
                </div>

            </div>

            {{-- ==== RIWAYAT (DROPDOWN) ==== --}}
            <div 
                x-show="open"
                x-transition
                class="mt-3"
            >

                <div class="bg-gray-50 border rounded-lg p-3">
                    
                    <div class="flex items-center justify-between">
                        {{-- LABEL SECTION --}}
                        <div>
                            <h4 class="font-semibold text-sm px-1 mb-2 text-gray-600">
                                Riwayat Transaksi Simpanan
                            </h4>
                        </div>
                
                        {{--  DROPDOWN FILTER JENIS SIMPANAN --}}
                        @php 
                            $currentJenis = request('jenis'); 
                            $labelJenis = match($currentJenis) {
                                'pokok' => 'Pokok',
                                'wajib' => 'Wajib',
                                'sukarela' => 'Sukarela',
                                default => 'Semua'
                            };
                        @endphp
                        <div x-data="{ openFilter: false }" class="relative inline-block mb-2">

                            {{-- BUTTON --}}
                            <button 
                                @click="openFilter = !openFilter"
                                class="flex items-center gap-2 px-3 py-1.5 text-xs border rounded-lg bg-white hover:bg-gray-50"
                            >
                                <span>Jenis: <span class="font-semibold text-gray-800">{{ $labelJenis }}</span></span>

                                <svg 
                                    :class="openFilter ? 'rotate-180' : ''"
                                    class="w-4 h-4 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- DROPDOWN --}}
                            <div 
                                x-show="openFilter"
                                @click.outside="openFilter = false"
                                x-transition
                                class="absolute z-10 mt-2 w-40 bg-white border rounded-lg shadow-md overflow-hidden"
                            >
                                <a href="{{ request()->url() }}"
                                    class="block px-3 py-2 text-xs hover:bg-gray-100 {{ !$currentJenis ? 'bg-gray-100 font-semibold' : '' }}">
                                    Semua
                                </a>

                                <a href="{{ request()->fullUrlWithQuery(['jenis' => 'pokok', 'simpanan_page' => 1]) }}"
                                    class="block px-3 py-2 text-xs hover:bg-gray-100 {{ $currentJenis === 'pokok' ? 'bg-gray-100 font-semibold' : '' }}">
                                    Pokok
                                </a>

                                <a href="{{ request()->fullUrlWithQuery(['jenis' => 'wajib', 'simpanan_page' => 1]) }}"
                                    class="block px-3 py-2 text-xs hover:bg-gray-100 {{ $currentJenis === 'wajib' ? 'bg-gray-100 font-semibold' : '' }}">
                                    Wajib
                                </a>

                                <a href="{{ request()->fullUrlWithQuery(['jenis' => 'sukarela', 'simpanan_page' => 1]) }}"
                                    class="block px-3 py-2 text-xs hover:bg-gray-100 {{ $currentJenis === 'sukarela' ? 'bg-gray-100 font-semibold' : '' }}">
                                    Sukarela
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border text-sm">
                            <thead class="bg-gray-100 border-b text-xs uppercase text-gray-500 tracking-wide">
                                <tr>
                                    <th class="px-3 py-2 text-left">Tanggal</th>
                                    <th class="px-3 py-2 text-center">Jenis</th>
                                    <th class="px-3 py-2 text-right">Jumlah</th>
                                    <th class="px-3 py-2 text-left">Sumber</th>
                                    <th class="px-3 py-2 text-center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($simpanans as $simpanan)
                                    <tr id="simpanan-row-{{ $simpanan->id }}" class="border-b hover:bg-gray-100 transition">
                                        <td class="px-3 py-2 text-left">
                                            {{ \Carbon\Carbon::parse($simpanan->tanggal)->format('d-m-Y') }}
                                        </td>
                                        <td class="px-3 py-2 capitalize text-center">
                                            {{ $simpanan->jenis_simpanan }}
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium">
                                            @if ($simpanan->jumlah < 0)
                                                <span class="text-red-600">
                                                    - Rp {{ number_format(abs($simpanan->jumlah), 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-green-600">
                                                    + Rp {{ number_format($simpanan->jumlah, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-left text-gray-700">
                                            {{ $simpanan->sumber }}
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            {{ $simpanan->keterangan ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-4 py-6 text-center text-gray-500">
                                            Belum ada transaksi simpanan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    @if($simpanans->hasPages())
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-2 px-2">
                            <p class="text-sm text-gray-600">
                                Menampilkan
                                <span class="font-semibold text-gray-900">{{ $simpanans->firstItem() ?? 0 }}</span>
                                sampai
                                <span class="font-semibold text-gray-900">{{ $simpanans->lastItem() ?? 0 }}</span>
                                dari
                                <span class="font-semibold text-gray-900">{{ $simpanans->total() }}</span>
                                data
                            </p>

                            <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                                {{ $simpanans->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- PINJAMAN --}}
    <div x-show="tab === 'pinjaman'" x-transition class="mt-6 space-y-4">
        @if($canViewFullDetails)
            @php
                $aktif = $ringkasanPinjaman['aktif'] ?? 0;
                $lunas = $ringkasanPinjaman['lunas'] ?? 0;
                $sisa = $ringkasanPinjaman['sisa'] ?? 0;
            @endphp

            <div 
                x-data="{open: {{ (request()->has('pinjaman_page') || request()->has('status_pinjaman')) ? 'true' : 'false' }}}"
                class="bg-white border rounded-lg px-5 py-4"
            >
                <div class="flex items-start justify-between mb-3">
                    {{-- TITLE + STATUS --}}
                    <div>
                        <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                            Ringkasan Pinjaman
                        </h2>
                        <p class="text-xs text-gray-500">
                            Informasi pinjaman anggota
                        </p>
                    </div>

                    {{-- TOGGLE BUTTON --}}
                    <button 
                        @click="open = !open"
                        class="flex items-center gap-2 text-xs px-3 py-1.5 rounded-full border 
                            bg-white hover:bg-gray-50 text-gray-700 transition"
                    >
                        <span x-text="open ? 'Sembunyikan' : 'Riwayat Pinjaman'"></span>
                        <svg 
                            :class="open ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                {{-- RINGKASAN CARD --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                    <div class="stat-card stat-info">
                        <p class="text-gray-500 text-xs">Pinjaman Aktif</p>
                        <p class="font-semibold text-gray-800 mt-1">
                            {{ $aktif }}
                        </p>
                    </div>

                    <div class="stat-card stat-info">
                        <p class="text-gray-500 text-xs">Pinjaman Lunas</p>
                        <p class="font-semibold text-gray-800 mt-1">
                            {{ $lunas }}
                        </p>
                    </div>

                    <div class="stat-card stat-info">
                        <p class="text-gray-600 text-xs">SISA PINJAMAN</p>
                        <p class="font-bold text-gray-800 mt-1 text-base">
                            Rp {{ number_format($sisa, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- ==== RIWAYAT (DROPDOWN) ==== --}}
                <div 
                    x-show="open"
                    x-transition
                    class="mt-3"
                >
                    <div class="bg-gray-50 border rounded-lg p-3">
                        <div class="flex items-center justify-between mb-3">
                            {{-- LABEL SECTION --}}
                            <h4 class="font-semibold text-sm text-gray-600">
                                Riwayat Pinjaman
                            </h4>
                    
                            {{-- DROPDOWN FILTER STATUS PINJAMAN --}}
                            @php 
                                $statusPinjaman = request('status_pinjaman');
                                $labelStatus = match($statusPinjaman) {
                                    'aktif' => 'Aktif',
                                    'lunas' => 'Lunas',
                                    default => 'Semua'
                                };
                            @endphp

                            <div x-data="{ openFilter: false }" class="relative inline-block">
                                {{-- BUTTON --}}
                                <button 
                                    @click="openFilter = !openFilter"
                                    class="flex items-center gap-2 px-3 py-1.5 text-xs border rounded-lg bg-white hover:bg-gray-50"
                                >
                                    <span>Status: <span class="font-semibold text-gray-800">{{ $labelStatus }}</span></span>
                                    <svg 
                                        :class="openFilter ? 'rotate-180' : ''"
                                        class="w-4 h-4 transition-transform"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                {{-- DROPDOWN --}}
                                <div 
                                    x-show="openFilter"
                                    @click.outside="openFilter = false"
                                    x-transition
                                    class="absolute z-10 mt-2 w-40 bg-white border rounded-lg shadow-md overflow-hidden right-0"
                                >
                                    <a href="{{ request()->fullUrlWithQuery(['status_pinjaman' => null, 'pinjaman_page' => 1]) }}"
                                        class="block px-3 py-2 text-xs hover:bg-gray-100 {{ !$statusPinjaman ? 'bg-gray-100 font-semibold' : '' }}">
                                        Semua
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['status_pinjaman' => 'aktif', 'pinjaman_page' => 1]) }}"
                                        class="block px-3 py-2 text-xs hover:bg-gray-100 {{ $statusPinjaman === 'aktif' ? 'bg-gray-100 font-semibold' : '' }}">
                                        Aktif
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['status_pinjaman' => 'lunas', 'pinjaman_page' => 1]) }}"
                                        class="block px-3 py-2 text-xs hover:bg-gray-100 {{ $statusPinjaman === 'lunas' ? 'bg-gray-100 font-semibold' : '' }}">
                                        Lunas
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full border text-sm">
                                <thead class="bg-gray-100 border-b text-xs uppercase text-gray-500 tracking-wide">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Tanggal</th>
                                        <th class="px-3 py-2 text-right">Jumlah Pinjaman</th>
                                        <th class="px-3 py-2 text-center">Status</th>
                                        <th class="px-3 py-2 text-right">Sisa</th>
                                        <th class="px-3 py-2 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pinjamans as $pinjaman)
                                        <tr id="pinjaman-row-{{ $pinjaman->id }}" class="border-b hover:bg-gray-100 transition">
                                            <td class="px-3 py-2 text-left">
                                                {{ \Carbon\Carbon::parse($pinjaman->tanggal_pinjam)->format('d-m-Y') }}
                                            </td>
                                            <td class="px-3 py-2 text-right font-medium">
                                                Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                @php
                                                    $statusClass = match($pinjaman->status) {
                                                        'aktif' => 'bg-yellow-100 text-yellow-800',
                                                        'lunas' => 'bg-green-100 text-green-800',
                                                        'dibatalkan' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-600'
                                                    };
                                                @endphp

                                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                                    {{ ucfirst($pinjaman->status) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-right font-medium text-red-600">
                                                Rp {{ number_format($pinjaman->sisa_pinjaman ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if($pinjaman->transaksi->isNotEmpty())
                                                    <button
                                                        onclick="toggleCicilan({{ $pinjaman->id }})"
                                                        id="btn-lihat-{{ $pinjaman->id }}"
                                                        class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium px-3 py-1.5 rounded-md transition-all duration-200 inline-flex items-center gap-1 transform hover:scale-105">
                                                        <svg class="w-3 h-3 transition-transform duration-200" id="icon-lihat-{{ $pinjaman->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                        Lihat
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">-</span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- ========================= --}}
                                        {{-- RIWAYAT CICILAN --}}
                                        {{-- ========================= --}}
                                        @if($pinjaman->transaksi->isNotEmpty())
                                            <tr id="cicilan-{{ $pinjaman->id }}" class="hidden">
                                                <td colspan="8" class="px-6 py-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg shadow-inner">

                                                    <div class="text-sm font-semibold mb-3 text-gray-700">
                                                        Riwayat Transaksi
                                                    </div>

                                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                                        <table class="w-full text-sm">
                                                            <thead class="bg-gray-100 text-gray-600">
                                                                <tr>
                                                                    <th class="px-4 py-2 text-left">Tanggal</th>
                                                                    <th class="px-4 py-2 text-center">Jenis</th>
                                                                    <th class="px-4 py-2 text-right">Jumlah</th>
                                                                    <th class="px-4 py-2 text-right">Sisa Pinjaman</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    // Sort transactions: pencairan first (by date asc for progression), then others by date asc
                                                                    $pencairanTransactions = $pinjaman->transaksi->where('jenis', 'pencairan')->sortBy('tanggal');
                                                                    $otherTransactions = $pinjaman->transaksi->where('jenis', '!=', 'pencairan')->sortBy('tanggal');
                                                                @endphp

                                                                {{-- Pencairan transactions first --}}
                                                                @php
                                                                    $previousSisa = 0; // Start with 0 for pencairan
                                                                @endphp
                                                                @foreach($pencairanTransactions as $t)
                                                                    @php
                                                                        $selisih = ($t->sisa_setelah ?? 0) - $previousSisa;
                                                                        $previousSisa = $t->sisa_setelah ?? 0;
                                                                    @endphp
                                                                    <tr class="border-t">
                                                                        <td class="px-4 py-2">
                                                                            {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                                                                        </td>

                                                                        <td class="px-4 py-2 text-center capitalize">
                                                                            {{ ucfirst($t->jenis) }}
                                                                        </td>

                                                                        <td class="px-4 py-2 text-right">
                                                                            Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                                                        </td>

                                                                        <td class="px-4 py-2 text-right font-medium">
                                                                            Rp {{ number_format($t->sisa_setelah ?? 0, 0, ',', '.') }}
                                                                            @if($selisih != 0)
                                                                                <span class="text-xs {{ $selisih < 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                                    ({{ $selisih < 0 ? '-' : '+' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }})
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                                {{-- Other transactions in chronological order --}}
                                                                @php
                                                                    // Continue from last pencairan sisa, or original loan amount if no pencairan
                                                                    $previousSisa = $previousSisa ?: $pinjaman->jumlah_pinjaman;
                                                                    $counters = []; // Initialize counters for each jenis
                                                                @endphp
                                                                @foreach($otherTransactions as $t)
                                                                    @php
                                                                        $selisih = ($t->sisa_setelah ?? 0) - $previousSisa;
                                                                        $previousSisa = $t->sisa_setelah ?? 0;
                                                                        
                                                                        // Initialize counter for this jenis if not exists
                                                                        if(!isset($counters[$t->jenis])) {
                                                                            $counters[$t->jenis] = 1;
                                                                        }
                                                                    @endphp
                                                                    <tr class="border-t">
                                                                        <td class="px-4 py-2">
                                                                            {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                                                                        </td>

                                                                        <td class="px-4 py-2 text-center capitalize">
                                                                            @if(in_array($t->jenis, ['pencairan', 'topup','pelunasan']))
                                                                                {{ ucfirst($t->jenis) }}
                                                                            @else
                                                                                {{ ucfirst($t->jenis) }} ke-{{ $counters[$t->jenis] }}
                                                                            @endif
                                                                        </td>

                                                                        <td class="px-4 py-2 text-right">
                                                                            Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                                                        </td>

                                                                        <td class="px-4 py-2 text-right font-medium">
                                                                            Rp {{ number_format($t->sisa_setelah ?? 0, 0, ',', '.') }}
                                                                            @if($selisih != 0)
                                                                                <span class="text-xs {{ $selisih < 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                                    ({{ $selisih < 0 ? '-' : '+' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }})
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @php
                                                                        $counters[$t->jenis]++;
                                                                    @endphp
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                                Tidak ada data
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- PAGINATION --}}
                        @if($pinjamans->hasPages())
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-2 px-2">
                                <p class="text-sm text-gray-600">
                                    Menampilkan
                                    <span class="font-semibold text-gray-900">{{ $pinjamans->firstItem() ?? 0 }}</span>
                                    sampai
                                    <span class="font-semibold text-gray-900">{{ $pinjamans->lastItem() ?? 0 }}</span>
                                    dari
                                    <span class="font-semibold text-gray-900">{{ $pinjamans->total() }}</span>
                                    data
                                </p>
                                <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                                    {{ $pinjamans->links('vendor.pagination.custom') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

<script>
    function toggleCicilan(id) {
        const row = document.getElementById('cicilan-' + id);
        const mainRow = document.getElementById('pinjaman-row-' + id);
        const button = document.getElementById('btn-lihat-' + id);
        const icon = document.getElementById('icon-lihat-' + id);

        // Toggle visibility
        row.classList.toggle('hidden');

        // Change main row background when detail is open
        if (!row.classList.contains('hidden')) {
            mainRow.classList.add('bg-blue-100', 'border-blue-300');
            mainRow.classList.remove('hover:bg-gray-50', 'border-transparent');
            mainRow.classList.add('border-blue-400');
            button.innerHTML = `
                <svg class="w-3 h-3 transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
                Tutup
            `;
            button.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            button.classList.add('bg-red-500', 'hover:bg-red-600');
        } else {
            mainRow.classList.remove('bg-blue-100', 'border-blue-300', 'border-blue-400');
            mainRow.classList.add('hover:bg-gray-50', 'border-transparent');
            button.innerHTML = `
                <svg class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                Lihat
            `;
            button.classList.remove('bg-red-500', 'hover:bg-red-600');
            button.classList.add('bg-blue-500', 'hover:bg-blue-600');
        }
    }
</script>