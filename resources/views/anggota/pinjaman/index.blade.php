@extends('layouts.main')

@section('title', 'Pinjaman Saya')
@section('page-title', 'Pinjaman Saya')

@section('content')
<div class="space-y-10">

    {{-- ========================= --}}
    {{-- RINGKASAN PINJAMAN --}}
    {{-- ========================= --}}
    <div>
        <h2 class="section-title">Ringkasan Pinjaman Saya</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            {{-- TOTAL PINJAMAN --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                <div class="text-sm text-gray-600 mb-1">
                    Total Pinjaman
                </div>
                <div class="text-xl font-semibold text-gray-900">
                    Rp {{ number_format($totalPinjamanSaya ?? 0, 0, ',', '.') }}
                </div>
            </div>

            {{-- SISA PINJAMAN --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                <div class="text-sm text-gray-600 mb-1">
                    Sisa Pinjaman
                </div>
                <div class="text-xl font-semibold text-gray-900">
                    Rp {{ number_format($sisaPinjamanSaya ?? 0, 0, ',', '.') }}
                </div>
            </div>

            {{-- STATUS --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                <div class="text-sm text-gray-600 mb-1">
                    Status Pinjaman
                </div>

                @if($pinjamanAktifSaya)
                    <span class="inline-block px-3 py-1 rounded-full text-sm
                        bg-green-100 text-green-800">
                        Aktif
                    </span>
                @else
                    <span class="inline-block px-3 py-1 rounded-full text-sm
                        bg-gray-100 text-gray-700">
                        Tidak Ada Pinjaman Aktif
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- RIWAYAT PINJAMAN --}}
    {{-- ========================= --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="section-title">
                Riwayat Pinjaman
            </h2>

            @can('create pinjaman')
                <a href="{{ route('anggota.pinjaman.ajukan') }}"
                class="px-3 py-1.5 rounded-md text-sm font-medium
                        bg-orange-100 text-orange-700
                        hover:bg-orange-200 transition">
                    + Ajukan Pinjaman
                </a>
            @endcan
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left w-[20%]">
                            Tanggal
                        </th>
                        <th class="px-4 py-3 text-center w-[20%]">
                            Status
                        </th>
                        <th class="px-4 py-3 text-right w-[30%]">
                            Jumlah
                        </th>
                        <th class="px-4 py-3 text-right w-[30%]">
                            Sisa
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pinjaman as $p)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">
                                {{ $p->tanggal_pinjam->format('d M Y') }}
                            </td>

                            <td class="px-4 py-2 text-center">
                                @php
                                    $badge = match($p->status) {
                                        'aktif' => 'bg-green-100 text-green-800',
                                        'pengajuan' => 'bg-yellow-100 text-yellow-800',
                                        'disetujui' => 'bg-blue-100 text-blue-800',
                                        'ditolak' => 'bg-red-100 text-red-800',
                                        'lunas' => 'bg-gray-100 text-gray-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span class="px-3 py-1 rounded-full text-xs {{ $badge }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-2 text-right font-medium">
                                Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-2 text-right font-medium">
                                Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="px-4 py-6 text-center text-gray-500">
                                Belum ada pinjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
