@extends('layouts.main')

@section('title', 'Arus Operasional')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">Arus Operasional</h1>

    {{-- FILTER --}}
    <form method="GET" class="mt-3 mb-4">
        <label class="text-sm text-gray-500">Bulan</label>
        <input
            type="month"
            name="bulan"
            value="{{ $bulan }}"
            class="border rounded px-2 py-1 ml-2"
        >

        <button class="ml-2 px-4 py-1 bg-blue-600 text-white rounded">
            Tampilkan
        </button>

        <a href="{{ route('admin.keuangan.arus.operasional.export', ['bulan' => $bulan]) }}"
            class="inline-flex items-center px-4 py-1 mb-4 bg-green-600 text-white rounded hover:bg-green-700">
            Export Excel
        </a>
    </form>

    {{-- TABEL --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Kategori</th>
                <th class="text-end">Masuk</th>
                <th class="text-end">Keluar</th>
            </tr>
        </thead>

        <tbody>
            @php
                $totalMasuk = 0;
                $totalKeluar = 0;
            @endphp

            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>{{ ucfirst($item->kategori) }}</td>

                    <td class="text-end">
                        @if ($item->tipe === 'masuk')
                            @php $totalMasuk += $item->jumlah; @endphp
                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                        @endif
                    </td>

                    <td class="text-end">
                        @if ($item->tipe === 'keluar')
                            @php $totalKeluar += $item->jumlah; @endphp
                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>

        <tfoot class="fw-bold">
            <tr>
                <td colspan="3">TOTAL</td>
                <td class="text-end">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </td>
                <td class="text-end">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection