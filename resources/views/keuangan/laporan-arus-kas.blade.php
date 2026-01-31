@extends('layouts.main')

@section('title', 'Laporan Arus Kas Bulanan')

@section('content')
<div class="container">

    <h1 class="text-2xl font-bold mb-4">Laporan Arus Kas Bulanan</h1>

    {{-- FILTER --}}
    <form method="GET" class="mt-3 mb-4">
        <label class="text-sm text-gray-500">Bulan</label>
        <input type="month" name="bulan" value="{{ $bulan }}"
            class="border rounded px-2 py-1 ml-2">

        <button class="ml-2 px-4 py-1 bg-blue-600 text-white rounded">
            Tampilkan
        </button>

        <a href="{{ route('admin.keuangan.laporan.arus-kas.export', ['bulan' => $bulan]) }}"
            class="inline-flex items-center px-4 py-1 mb-4 bg-green-600 text-white rounded hover:bg-green-700">
            Export Excel
        </a>
    </form>

    {{-- PEMASUKAN --}}
    <h6>Pemasukan</h6>
    <table class="table table-bordered mb-4">
        <tr>
            <th>Jenis</th>
            <th class="text-end">Jumlah</th>
        </tr>
        @foreach ($masuk as $jenis => $jumlah)
            <tr>
                <td>{{ ucfirst($jenis) }}</td>
                <td class="text-end">Rp {{ number_format($jumlah, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr class="fw-bold">
            <td>Total Pemasukan</td>
            <td class="text-end">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- PENGELUARAN --}}
    <h6>Pengeluaran</h6>
    <table class="table table-bordered mb-4">
        <tr>
            <th>Jenis</th>
            <th class="text-end">Jumlah</th>
        </tr>
        @foreach ($keluar as $jenis => $jumlah)
            <tr>
                <td>{{ ucfirst($jenis) }}</td>
                <td class="text-end">Rp {{ number_format($jumlah, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr class="fw-bold">
            <td>Total Pengeluaran</td>
            <td class="text-end">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- HASIL --}}
    <h6>Hasil Bulan Ini</h6>
    <table class="table table-bordered">
        <tr class="fw-bold">
            <td>Surplus / Defisit</td>
            <td class="text-end">
                Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}
            </td>
        </tr>
    </table>

</div>
@endsection