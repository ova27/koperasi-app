@extends('layouts.main')

@section('title', 'Simpanan Saya')

@section('content')

<h1 class="text-xl font-semibold mb-4">Simpanan Saya</h1>

{{-- SALDO --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach($saldo as $jenis => $jumlah)
        <div class="p-4 bg-white rounded border">
            <div class="text-gray-500 uppercase text-sm">{{ ucfirst($jenis) }}</div>
            <div class="text-lg font-bold">
                Rp {{ number_format($jumlah, 0, ',', '.') }}
            </div>
        </div>
    @endforeach
</div>

{{-- RIWAYAT --}}
<div class="bg-white rounded border">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2">Jenis</th>
                <th class="px-3 py-2">Jumlah</th>
                <th class="px-3 py-2">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($simpanan as $s)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $s->tanggal->format('d-m-Y') }}</td>
                    <td class="px-3 py-2">{{ ucfirst($s->jenis_simpanan) }}</td>
                    <td class="px-3 py-2">
                        Rp {{ number_format($s->jumlah, 0, ',', '.') }}
                    </td>
                    <td class="px-3 py-2">{{ $s->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
