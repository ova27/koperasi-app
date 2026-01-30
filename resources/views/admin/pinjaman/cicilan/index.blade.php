@extends('layouts.main')

@section('title', 'Data Cicilan Pinjaman')

@section('content')
@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

<h1 class="text-xl font-bold mb-4">Pinjaman Aktif</h1>

<table class="w-full border">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 border">Anggota</th>
            <th class="p-2 border">Total</th>
            <th class="p-2 border">Sisa</th>
            <th class="p-2 border">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pinjamans as $p)
            <tr>
                <td class="p-2 border">{{ $p->anggota->nama }}</td>
                <td class="p-2 border">Rp {{ number_format($p->jumlah_pinjaman) }}</td>
                <td class="p-2 border">Rp {{ number_format($p->sisa_pinjaman) }}</td>
                <td class="p-2 border">
                    <a href="{{ route('admin.pinjaman.cicil.create', $p) }}"
                       class="text-blue-600 underline">
                        Cicil
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="p-4 text-center text-gray-500">
                    Tidak ada pinjaman aktif
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection