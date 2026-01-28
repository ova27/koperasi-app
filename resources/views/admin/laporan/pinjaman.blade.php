@extends('layouts.main')

@section('content')
<div class="max-w-5xl mx-auto">

    <h1 class="text-xl font-semibold mb-6">
        Laporan Pinjaman
    </h1>

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">
                Pinjaman Aktif
            </div>
            <div class="text-2xl font-semibold">
                {{ $jumlahAktif }}
            </div>
        </div>

        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">
                Total Sisa Pinjaman
            </div>
            <div class="text-2xl font-semibold">
                Rp {{ number_format($totalSisa, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <a href="{{ route('admin.laporan.pinjaman.export') }}"
        class="inline-flex items-center px-4 py-2 mb-4 bg-green-600 text-white rounded hover:bg-green-700">
        Export Excel
    </a>

    {{-- TABEL --}}
    <div class="bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border">Anggota</th>
                    <th class="px-3 py-2 border">Tanggal Pinjam</th>
                    <th class="px-3 py-2 border">Jumlah</th>
                    <th class="px-3 py-2 border">Sisa</th>
                    <th class="px-3 py-2 border">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pinjamans as $p)
                    <tr>
                        <td class="px-3 py-2 border">
                            <a href="{{ route('admin.laporan.pinjaman.show', $p) }}"
                                class="text-blue-600 hover:underline">
                                {{ $p->anggota->nama ?? '-' }}
                            </a>
                        </td>
                        <td class="px-3 py-2 border">
                            {{ optional($p->tanggal_pinjam)->format('Y-m-d') }}
                        </td>
                        <td class="px-3 py-2 border text-right">
                            Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 border text-right">
                            Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 border">
                            {{ ucfirst($p->status) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Belum ada data pinjaman
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
