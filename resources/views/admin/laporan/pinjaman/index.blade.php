@extends('layouts.main')

@section('title', 'Laporan Pinjaman Bulanan')

@section('content')
<h1 class="text-xl font-bold mb-4">Laporan Pinjaman Bulanan</h1>

<form method="GET" class="mb-6">
    <label class="mr-2 font-semibold">Bulan:</label>
    <input type="month" name="bulan" value="{{ $bulan }}">
    <button class="ml-2 px-3 py-1 bg-blue-600 text-white rounded">
        Tampilkan
    </button>
</form>

{{-- RINGKASAN --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="p-4 bg-white border rounded">
        <div class="text-gray-500 text-sm">Total Pencairan</div>
        <div class="text-lg font-bold">
            Rp {{ number_format($totalPencairan) }}
        </div>
    </div>

    <div class="p-4 bg-white border rounded">
        <div class="text-gray-500 text-sm">Total Cicilan</div>
        <div class="text-lg font-bold">
            Rp {{ number_format($totalCicilan) }}
        </div>
    </div>

    <div class="p-4 bg-white border rounded">
        <div class="text-gray-500 text-sm">Pelunasan</div>
        <div class="text-lg font-bold">
            {{ $totalPelunasan }} pinjaman
        </div>
    </div>

    <div class="p-4 bg-white border rounded">
        <div class="text-gray-500 text-sm">Total Pinjaman Aktif</div>
        <div class="text-lg font-bold">
            {{ $pinjamans->where('status','aktif')->count() }}
        </div>
    </div>
</div>

{{-- RINCIAN TRANSAKSI --}}
<h2 class="text-lg font-semibold mb-3">Rincian Transaksi</h2>

<a href="{{ route('admin.laporan.pinjaman.export', ['bulan' => $bulan]) }}"
    class="inline-flex items-center px-4 py-2 mb-4 bg-green-600 text-white rounded hover:bg-green-700">
    Export Excel
</a>

<table class="w-full border mb-6">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 border">Tanggal</th>
            <th class="p-2 border">Anggota</th>
            <th class="p-2 border">Jenis</th>
            <th class="p-2 border">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transaksis as $t)
            <tr>
                <td class="p-2 border">{{ $t->tanggal->format('d-m-Y') }}</td>
                <td class="p-2 border">{{ $t->pinjaman->anggota->nama }}</td>
                <td class="p-2 border">{{ ucfirst($t->jenis) }}</td>
                <td class="p-2 border">
                    Rp {{ number_format($t->jumlah) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="p-4 text-center text-gray-500">
                    Tidak ada transaksi
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection