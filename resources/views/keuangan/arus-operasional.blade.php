@extends('layouts.main')

@section('title', 'Arus Operasional')
@section('page-title', 'Arus Operasional')

@section('content')
<div class="space-y-6 -mt-1">
    @include('keuangan._tabs')

    @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5">
        <div class="mb-4">
            <h2 class="text-base font-semibold text-gray-900">Input Arus Operasional</h2>
            <p class="text-sm text-gray-500">Catat pemasukan atau pengeluaran operasional manual.</p>
        </div>

        <form method="POST" action="{{ route('admin.keuangan.arus.operasional.store') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @csrf

            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-600 mb-1">Tanggal</label>
                <input
                    id="tanggal"
                    type="date"
                    name="tanggal"
                    value="{{ old('tanggal', now()->toDateString()) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
            </div>

            <div>
                <label for="rekening_koperasi_id" class="block text-sm font-medium text-gray-600 mb-1">Rekening</label>
                <select
                    id="rekening_koperasi_id"
                    name="rekening_koperasi_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
                    <option value="">Pilih rekening</option>
                    @foreach ($rekenings as $rekening)
                        <option value="{{ $rekening->id }}" @selected(old('rekening_koperasi_id') == $rekening->id)>
                            {{ $rekening->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tipe" class="block text-sm font-medium text-gray-600 mb-1">Tipe</label>
                <select
                    id="tipe"
                    name="tipe"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
                    <option value="masuk" @selected(old('tipe') === 'masuk')>Masuk</option>
                    <option value="keluar" @selected(old('tipe') === 'keluar')>Keluar</option>
                </select>
            </div>

            <div>
                <label for="jumlah" class="block text-sm font-medium text-gray-600 mb-1">Jumlah</label>
                <input
                    id="jumlah"
                    type="number"
                    name="jumlah"
                    min="1"
                    step="0.01"
                    value="{{ old('jumlah') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: 50000"
                    required
                >
            </div>

            <div>
                <label for="kategori" class="block text-sm font-medium text-gray-600 mb-1">Kategori</label>
                <input
                    id="kategori"
                    type="text"
                    name="kategori"
                    value="{{ old('kategori') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: Biaya Operasional"
                    required
                >
            </div>

            <div class="md:col-span-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-600 mb-1">Keterangan</label>
                <input
                    id="keterangan"
                    type="text"
                    name="keterangan"
                    value="{{ old('keterangan') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: Transportasi ke Bank"
                >
            </div>

            <div class="xl:col-span-4 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>

    {{-- FILTER --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap gap-2 items-end">
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-600 mb-1">Bulan</label>
                <input
                    id="bulan"
                    type="month"
                    name="bulan"
                    value="{{ $bulan }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                Tampilkan
            </button>

            <a href="{{ route('admin.keuangan.arus.operasional.export', ['bulan' => $bulan]) }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition inline-flex items-center justify-center">
                Export Excel
            </a>
        </form>
    </div>

    {{-- TABEL --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Keterangan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Rekening</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide">Masuk</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide">Keluar</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @php
                    $totalMasuk = 0;
                    $totalKeluar = 0;
                @endphp

                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium whitespace-nowrap">
                            {{ $item->tanggal->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->keterangan }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <span class="inline-flex w-fit items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($item->kategori) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                            {{ $item->rekening->nama ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                            @if ($item->tipe === 'masuk')
                                @php $totalMasuk += $item->jumlah; @endphp
                                <span class="text-green-600 font-semibold">
                                    + Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                            @if ($item->tipe === 'keluar')
                                @php $totalKeluar += $item->jumlah; @endphp
                                <span class="text-red-600 font-semibold">
                                    - Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.keuangan.arus.operasional.destroy', $item) }}" onsubmit="return confirm('Hapus transaksi operasional ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                            Tidak ada data untuk periode ini
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot class="bg-gray-50 border-t text-sm font-semibold text-gray-700">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-right uppercase tracking-wide text-xs">Total</td>
                    <td class="px-4 py-3 text-right text-green-600 font-semibold whitespace-nowrap">
                        + Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right text-red-600 font-semibold whitespace-nowrap">
                        - Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
