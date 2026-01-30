@extends('layouts.main')

@section('title', 'Data Pengajuan Pinjaman')

@section('content')
@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

<h1 class="text-xl font-bold mb-4">Pengajuan Pinjaman</h1>

<table class="w-full border">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 border">Anggota</th>
            <th class="p-2 border">Jumlah</th>
            <th class="p-2 border">Tanggal</th>
            <th class="p-2 border">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pengajuans as $p)
            <tr>
                <td class="p-2 border">{{ $p->anggota->nama }}</td>
                <td class="p-2 border">Rp {{ number_format($p->jumlah_diajukan) }}</td>
                <td class="p-2 border">{{ $p->tanggal_pengajuan->format('d-m-Y') }}</td>
                <td class="p-2 border">
                    <a href="{{ route('admin.pinjaman.pengajuan.show', $p) }}"
                       class="text-blue-600 underline">
                        Detail
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="p-4 text-center text-gray-500">
                    Tidak ada pengajuan menunggu persetujuan
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
