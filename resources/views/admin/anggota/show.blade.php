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

    {{-- ================= PROFIL ================= --}}
    <div class="bg-white border rounded-lg p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
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

    {{-- ================= RINGKASAN SIMPANAN ================= --}}
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
                                <tr class="border-b hover:bg-gray-100 transition">
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

    {{-- ================= RINGKASAN PINJAMAN ================= --}}
    @if($canViewFullDetails)
        <div class="bg-white border rounded-lg p-5">
            <h2 class="font-semibold mb-4">
                Ringkasan Pinjaman
            </h2>

            <table class="text-sm w-full">
                <tr>
                    <td class="text-gray-600">Pinjaman Aktif</td>
                    <td class="text-right">
                        {{ $ringkasanPinjaman['aktif'] }}
                    </td>
                </tr>
                <tr>
                    <td class="text-gray-600">Pinjaman Lunas</td>
                    <td class="text-right">
                        {{ $ringkasanPinjaman['lunas'] }}
                    </td>
                </tr>
                <tr class="font-semibold border-t">
                    <td>Sisa Pinjaman</td>
                    <td class="text-right">
                        Rp {{ number_format($ringkasanPinjaman['sisa'], 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
    @endif
</div>
@endsection
