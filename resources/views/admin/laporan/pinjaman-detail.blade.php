@extends('layouts.main')
@section('title', 'Laporan Pinjaman')
@section('content')
<div class="max-w-5xl mx-auto">

    <h1 class="text-xl font-semibold mb-4">
        Detail Pinjaman
    </h1>

    {{-- INFO PINJAMAN --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white border rounded p-4">
            <div class="text-sm text-gray-500">Anggota</div>
            <div class="font-semibold">{{ $pinjaman->anggota->nama }}</div>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="text-sm text-gray-500">Status</div>
            <div class="font-semibold">{{ ucfirst($pinjaman->status) }}</div>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="text-sm text-gray-500">Jumlah Pinjaman</div>
            <div class="font-semibold">
                Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
            </div>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="text-sm text-gray-500">Sisa Pinjaman</div>
            <div class="font-semibold">
                Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- RIWAYAT CICILAN --}}
    <div class="bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">Tanggal</th>
                    <th class="border px-3 py-2">Jenis</th>
                    <th class="border px-3 py-2">Jumlah</th>
                    <th class="border px-3 py-2">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pinjaman->transaksi as $t)
                    <tr>
                        <td class="border px-3 py-2">
                            {{ $t->tanggal->format('Y-m-d') }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ ucfirst($t->jenis) }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="border px-3 py-2">
                            {{ $t->keterangan }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            Belum ada transaksi cicilan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
