@extends('layouts.main')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">
            Laporan Pinjaman
        </h1>

        @can('export laporan pinjaman')
            <a href="{{ route('admin.laporan.pinjaman.export', ['bulan' => $bulan]) }}"
               class="px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                Export Excel
            </a>
        @endcan
    </div>

    {{-- FILTER BULAN --}}
    <form method="GET" class="mb-6 flex items-end gap-2">
        <div>
            <label class="text-sm text-gray-600">Bulan</label>
            <input
                type="month"
                name="bulan"
                value="{{ $bulan }}"
                class="border rounded px-3 py-1"
            >
        </div>
        <button class="px-4 py-1 bg-blue-600 text-white rounded">
            Tampilkan
        </button>
    </form>

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">
                Total Pencairan
            </div>
            <div class="text-lg font-semibold">
                Rp {{ number_format($totalPencairan ?? 0, 0, ',', '.') }}
            </div>
        </div>

        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">
                Total Cicilan
            </div>
            <div class="text-lg font-semibold">
                Rp {{ number_format($totalCicilan ?? 0, 0, ',', '.') }}
            </div>
        </div>

        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">
                Pinjaman Lunas
            </div>
            <div class="text-lg font-semibold">
                {{ $totalPelunasan ?? 0 }} Pinjaman
            </div>
        </div>
    </div>

    {{-- TABEL TRANSAKSI --}}
    <div class="mb-10">
        <h2 class="text-lg font-semibold mb-3">
            Transaksi Pinjaman Bulan Ini
        </h2>

        <table class="min-w-full border bg-white text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-left">Tanggal</th>
                    <th class="border px-3 py-2 text-left">Anggota</th>
                    <th class="border px-3 py-2">Jenis</th>
                    <th class="border px-3 py-2 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $trx)
                    <tr>
                        <td class="border px-3 py-2">
                            {{ \Carbon\Carbon::parse($trx->tanggal)->format('d-m-Y') }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ $trx->pinjaman->anggota->nama ?? '-' }}
                        </td>
                        <td class="border px-3 py-2 text-center capitalize">
                            {{ $trx->jenis }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border px-3 py-4 text-center text-gray-500">
                            Tidak ada transaksi pinjaman pada bulan ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- SNAPSHOT PINJAMAN --}}
    <div>
        <h2 class="text-lg font-semibold mb-3">
            Posisi Pinjaman Anggota
        </h2>

        <table class="min-w-full border bg-white text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-left">Anggota</th>
                    <th class="border px-3 py-2">Status</th>
                    <th class="border px-3 py-2 text-right">Sisa Pinjaman</th>
                    <th class="border px-3 py-2 text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pinjamans as $pinjaman)
                    <tr>
                        <td class="border px-3 py-2">
                            {{ $pinjaman->anggota->nama ?? '-' }}
                        </td>
                        <td class="border px-3 py-2 text-center capitalize">
                            {{ $pinjaman->status }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            Rp {{ number_format($pinjaman->sisa_pinjaman ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="border px-3 py-2 text-center">
                            <a href="{{ route('admin.laporan.pinjaman.show', $pinjaman) }}"
                               class="text-blue-600 hover:underline">
                                Lihat
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
