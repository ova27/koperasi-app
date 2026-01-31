@extends('layouts.main')

@section('title', 'Data Pencairan Pinjaman')

@section('content')
@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

<h1 class="text-xl font-bold mb-4">Pencairan Pinjaman</h1>

<table class="w-full border">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 border">Anggota</th>
            <th class="p-2 border">Jumlah</th>
            <th class="p-2 border">Tanggal Disetujui</th>
            <th class="p-2 border">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pengajuans as $p)
            <tr>
                <td class="p-2 border">{{ $p->anggota->nama }}</td>
                <td class="p-2 border">Rp {{ number_format($p->jumlah_diajukan) }}</td>
                <td class="p-2 border">
                    {{ $p->tanggal_persetujuan ? $p->tanggal_persetujuan->format('d-m-Y') : '-' }}
                </td>
                <td class="p-2 border">
                    <form method="POST"
                          action="{{ route('admin.pinjaman.pencairan.process', $p) }}">
                        @csrf
                        <button
                            class="px-3 py-1 bg-green-600 text-white rounded"
                            onclick="return confirm('Yakin cairkan pinjaman ini?')">
                            Cairkan
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="p-4 text-center text-gray-500">
                    Tidak ada pinjaman siap dicairkan
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection