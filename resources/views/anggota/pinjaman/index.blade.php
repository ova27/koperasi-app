@extends('layouts.main')

@section('title', 'Pinjaman Saya')

@section('content')

<h1 class="text-xl font-semibold mb-4">Pinjaman Saya</h1>

@forelse($pinjaman as $p)
    <div class="mb-6 p-4 bg-white border rounded">
        <div class="font-semibold mb-2">
            Pinjaman {{ $p->tanggal_pinjam->format('d-m-Y') }}
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <div class="text-gray-500 text-sm">Jumlah</div>
                Rp {{ number_format($p->jumlah_pinjaman,0,',','.') }}
            </div>
            <div>
                <div class="text-gray-500 text-sm">Sisa</div>
                Rp {{ number_format($p->sisa_pinjaman,0,',','.') }}
            </div>
            <div>
                <div class="text-gray-500 text-sm">Status</div>
                {{ ucfirst($p->status) }}
            </div>
        </div>

        <div class="text-sm font-semibold mb-2">Riwayat</div>

        <table class="w-full text-sm">
            @foreach($p->transaksis as $t)
                <tr class="border-t">
                    <td class="py-1">{{ $t->tanggal->format('d-m-Y') }}</td>
                    <td class="py-1">{{ ucfirst($t->jenis) }}</td>
                    <td class="py-1">
                        Rp {{ number_format($t->jumlah,0,',','.') }}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@empty
    <div class="text-gray-500">
        Belum ada pinjaman.
    </div>
@endforelse

@endsection
