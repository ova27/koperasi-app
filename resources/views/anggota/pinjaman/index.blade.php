@extends('layouts.main')

@section('title', 'Pinjaman Saya')
@section('page-title', 'Pinjaman Saya')

@section('content')
<div class="space-y-7 -mt-1">
    @include('anggota.pinjaman._tabs')

    {{-- ========================= --}}
    {{-- RINGKASAN PINJAMAN --}}
    {{-- ========================= --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- TOTAL PINJAMAN --}}
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl px-4 py-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-yellow-200 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2.25 8.25h19.5M4.5 6h15a2.25 2.25 0 012.25 2.25v7.5A2.25 2.25 0 0119.5 18h-15a2.25 2.25 0 01-2.25-2.25v-7.5A2.25 2.25 0 014.5 6z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-gray-600">Total Pinjaman</div>
                </div>
                <div class="text-2xl font-bold text-gray-900">
                    Rp {{ number_format($totalPinjamanSaya ?? 0, 0, ',', '.') }}
                </div>
            </div>

            {{-- SISA PINJAMAN --}}
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 border border-orange-200 rounded-xl px-4 py-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-orange-200 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 1.5a10.5 10.5 0 100 21 10.5 10.5 0 000-21zM8.625 12a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-gray-600">Sisa Pinjaman</div>
                </div>
                <div class="text-2xl font-bold text-gray-900">
                    Rp {{ number_format($sisaPinjamanSaya ?? 0, 0, ',', '.') }}
                </div>
            </div>

            {{-- STATUS --}}
            <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl px-4 py-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-green-200 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-gray-600">Status Pinjaman</div>
                </div>

                @if($pinjamanAktifSaya)
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                        Aktif
                    </span>
                @else
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-700 border border-gray-300">
                        Tidak Ada Pinjaman Aktif
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- TABEL PINJAMAN AKTIF--}}
    {{-- ========================= --}}
    @if ($pinjamanAktif->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-700 mb-6">
                Pinjaman Aktif
            </h2>

            <div class="overflow-hidden rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-orange-50 to-orange-100 border-b-2 border-orange-300">
                            <tr>
                                <th class="px-5 py-2.5 text-left font-semibold text-xs text-orange-900 uppercase tracking-widest">Tanggal</th>
                                <th class="px-5 py-2.5 text-center font-semibold text-xs text-orange-900 uppercase tracking-widest">Status</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-xs text-orange-900 uppercase tracking-widest">Jumlah</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-xs text-orange-900 uppercase tracking-widest">Tenor</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-xs text-orange-900 uppercase tracking-widest">Cicilan/Bulan</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-xs text-orange-900 uppercase tracking-widest">Sisa</th>
                                <th class="px-5 py-2.5 text-center font-semibold text-xs text-orange-900 uppercase tracking-widest">Detail</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($pinjamanAktif as $p)
                                {{-- ========================= --}}
                                {{-- BARIS PINJAMAN UTAMA --}}
                                {{-- ========================= --}}
                                <tr class="@if($loop->odd) bg-white @else bg-orange-50 @endif hover:bg-orange-100 transition-all duration-300 group" id="pinjaman-row-{{ $p->id }}">
                                    <td class="px-5 py-2.5 text-gray-800 font-medium text-xs">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $p->tanggal_pinjam->format('d M Y') }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-2.5 text-center">
                                        @php
                                            $badge = match($p->status) {
                                                'aktif' => 'bg-green-100 text-green-700 border border-green-300 shadow-sm',
                                                'pengajuan' => 'bg-yellow-100 text-yellow-700 border border-yellow-300 shadow-sm',
                                                'disetujui' => 'bg-blue-100 text-blue-700 border border-blue-300 shadow-sm',
                                                'ditolak' => 'bg-red-100 text-red-700 border border-red-300 shadow-sm',
                                                'lunas' => 'bg-gray-100 text-gray-700 border border-gray-300 shadow-sm',
                                                default => 'bg-gray-100 text-gray-700 border border-gray-300 shadow-sm',
                                            };
                                        @endphp

                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-2.5 text-right font-bold text-gray-900 text-sm">
                                        <span class="text-orange-600">Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-5 py-2.5 text-right text-xs">
                                        {{ $p->tenor }} bulan
                                    </td>
                                    <td class="px-5 py-2.5 text-right text-xs">
                                        Rp {{ number_format($p->cicilan_per_bulan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-2.5 text-right font-bold text-gray-900 text-sm">
                                        Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}
                                    </td>

                                    <td class="px-5 py-2.5 text-center">
                                        @if($p->transaksi->isNotEmpty())
                                            <button
                                                onclick="toggleCicilan({{ $p->id }})"
                                                id="btn-lihat-{{ $p->id }}"
                                                class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200 inline-flex items-center gap-1 hover:shadow-md">
                                                <svg class="w-3 h-3 transition-transform duration-200" id="icon-lihat-{{ $p->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            @if($p->transaksi->isNotEmpty())
                                <tr id="cicilan-{{ $p->id }}" class="hidden">
                                    <td colspan="8" class="px-6 py-4 bg-gray-50 border-l-4 border-gray-300 shadow-inner">

                                        <div class="text-sm font-semibold mb-3 text-gray-700">
                                            Riwayat Transaksi
                                        </div>

                                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                            <table class="w-full text-sm">
                                                <thead class="bg-gray-100 border-b border-gray-200">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left font-medium text-xs text-gray-500 uppercase tracking-wider">Tanggal</th>
                                                        <th class="px-4 py-2 text-center font-medium text-xs text-gray-500 uppercase tracking-wider">Jenis</th>
                                                        <th class="px-4 py-2 text-right font-medium text-xs text-gray-500 uppercase tracking-wider">Jumlah</th>
                                                        <th class="px-4 py-2 text-right font-medium text-xs text-gray-500 uppercase tracking-wider">Sisa Pinjaman</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        // Sort transactions: pencairan first (by date asc for progression), then others by date asc
                                                        $pencairanTransactions = $p->transaksi->where('jenis', 'pencairan')->sortBy('tanggal');
                                                        $otherTransactions = $p->transaksi->where('jenis', '!=', 'pencairan')->sortBy('tanggal');
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
                                                        <tr class="@if($loop->odd) bg-white @else bg-gray-50 @endif hover:bg-gray-100 transition-colors duration-150 border-t border-gray-100">
                                                            <td class="px-4 py-2 text-xs text-gray-700">
                                                                {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                                                            </td>

                                                            <td class="px-4 py-2 text-center">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">
                                                                    {{ ucfirst($t->jenis) }}
                                                                </span>
                                                            </td>

                                                            <td class="px-4 py-2 text-right text-xs font-medium text-gray-800">
                                                                Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                                            </td>

                                                            <td class="px-4 py-2 text-right text-xs font-bold text-gray-900">
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
                                                        $previousSisa = $previousSisa ?: $p->jumlah_pinjaman;
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
                                                        <tr class="@if($loop->odd) bg-white @else bg-gray-50 @endif hover:bg-gray-100 transition-colors duration-150 border-t border-gray-100">
                                                            <td class="px-4 py-2 text-xs text-gray-700">
                                                                {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                                                            </td>

                                                            <td class="px-4 py-2 text-center">
                                                                @php
                                                                    $jenisBadge = match($t->jenis) {
                                                                        'pencairan', 'topup' => 'bg-blue-50 text-blue-600 border border-blue-200',
                                                                        'cicilan' => 'bg-green-50 text-green-600 border border-green-200',
                                                                        'pelunasan' => 'bg-purple-50 text-purple-600 border border-purple-200',
                                                                        default => 'bg-gray-100 text-gray-600 border border-gray-200',
                                                                    };
                                                                @endphp
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $jenisBadge }}">
                                                                    @if(in_array($t->jenis, ['pencairan', 'topup','pelunasan']))
                                                                        {{ ucfirst($t->jenis) }}
                                                                    @else
                                                                        {{ ucfirst($t->jenis) }} ke-{{ $counters[$t->jenis] }}
                                                                    @endif
                                                                </span>
                                                            </td>

                                                            <td class="px-4 py-2 text-right text-xs font-medium text-gray-800">
                                                                Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                                            </td>

                                                            <td class="px-4 py-2 text-right text-xs font-bold text-gray-900">
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
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    @endif
    

    {{-- ========================= --}}
    {{-- RIWAYAT PINJAMAN LUNAS --}}
    {{-- ========================= --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-700 mb-6">
            Riwayat Pinjaman Lunas
        </h2>

        <div class="overflow-hidden rounded-lg">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-5 py-2.5 text-left font-semibold text-xs text-gray-900 uppercase tracking-widest">Tanggal Pinjam</th>
                            <th class="px-5 py-2.5 text-left font-semibold text-xs text-gray-900 uppercase tracking-widest">Tanggal Lunas</th>
                            <th class="px-5 py-2.5 text-right font-semibold text-xs text-gray-900 uppercase tracking-widest">Jumlah</th>
                            <th class="px-5 py-2.5 text-center font-semibold text-xs text-gray-900 uppercase tracking-widest">Detail</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($pinjamanLunas as $p)
                            {{-- ========================= --}}
                            {{-- BARIS PINJAMAN LUNAS --}}
                            {{-- ========================= --}}
                            <tr class="@if($loop->odd) bg-white @else bg-gray-50 @endif hover:bg-gray-100 transition-all duration-300 group">
                                <td class="px-5 py-2.5 text-gray-800 font-medium text-xs">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $p->tanggal_pinjam->format('d M Y') }}
                                    </div>
                                </td>

                                <td class="px-5 py-2.5 text-gray-800 font-medium text-xs">
                                    @php
                                        $tanggalLunas = $p->transaksi->where('jenis', 'cicilan')->sortByDesc('tanggal')->first()?->tanggal;
                                    @endphp
                                    @if($tanggalLunas)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($tanggalLunas)->format('d M Y') }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="px-5 py-2.5 text-right font-bold text-gray-900 text-sm">
                                    Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-2.5 text-center">
                                    @if($p->transaksi->isNotEmpty())
                                        <button
                                            onclick="toggleCicilan({{ $p->id }})"
                                            id="btn-lihat-{{ $p->id }}"
                                            class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200 inline-flex items-center gap-1 hover:shadow-md">
                                            <svg class="w-3 h-3 transition-transform duration-200" id="icon-lihat-{{ $p->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                            Lihat
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        {{-- RIWAYAT CICILAN --}}
                        {{-- ========================= --}}
                        @if($p->transaksi->isNotEmpty())
                            <tr id="cicilan-{{ $p->id }}" class="hidden">
                                <td colspan="5" class="px-6 py-4 bg-gray-50 border-l-4 border-gray-400 rounded-r-lg shadow-inner">

                                    <div class="text-sm font-semibold mb-3 text-gray-700">
                                        Riwayat Transaksi Lengkap
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
                                                    // Sort transactions: pencairan first, then others by date descending
                                                    $pencairanTransactions = $p->transaksi->where('jenis', 'pencairan')->sortBy('tanggal');
                                                    $otherTransactions = $p->transaksi->where('jenis', '!=', 'pencairan')->sortBy('tanggal');
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
                                                    $previousSisa = $previousSisa ?: $p->jumlah_pinjaman;
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
                            <td colspan="4" class="px-5 py-8 text-center text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm font-medium">Belum ada riwayat pinjaman lunas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($pinjamanLunas->hasPages())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Menampilkan
                    <span class="font-semibold text-gray-900">{{ $pinjamanLunas->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-semibold text-gray-900">{{ $pinjamanLunas->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold text-gray-900">{{ $pinjamanLunas->total() }}</span>
                    data
                </p>

                <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                    {{ $pinjamanLunas->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>

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
