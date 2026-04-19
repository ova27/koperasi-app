@extends('layouts.main')

@section('title', 'Laporan Rekap Arus Kas')
@section('page-title', 'Laporan Rekap Arus Kas')
@section('page-description', 'Ringkasan bulanan untuk membandingkan total arus koperasi dan arus operasional dalam satu tampilan.')

@section('content')
<div class="space-y-6 -mt-1">
    @include('admin.laporan._tabs')

    @php
        [$tahunAktif, $bulanAktif] = array_pad(explode('-', $bulan), 2, null);
        $tahunAktif = (int) ($tahunAktif ?: now()->format('Y'));
        $bulanAktif = (int) ($bulanAktif ?: now()->format('n'));

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $tahunSekarang = (int) now()->format('Y');
        $daftarTahun = range($tahunSekarang + 1, $tahunSekarang - 5);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Total Masuk</p>
            <p class="mt-2 text-2xl font-semibold text-green-700">
                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Total Keluar</p>
            <p class="mt-2 text-2xl font-semibold text-red-700">
                Rp {{ number_format($totalKeluar, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Saldo Bersih</p>
            <p class="mt-2 text-2xl font-semibold {{ $saldoBersih >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                Rp {{ number_format($saldoBersih, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-2 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Arus Masuk</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                        
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($masuk as $jenis => $jumlah)
                        <tr>
                            <td class="px-4 py-3 text-gray-700">{{ ucfirst($jenis) }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500">Tidak ada pemasukan.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t">
                    <tr>
                        <td class="px-4 py-3 font-semibold text-gray-900">Total</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-700 whitespace-nowrap">
                            Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-2 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Arus Keluar</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($keluar as $jenis => $jumlah)
                        <tr>
                            <td class="px-4 py-3 text-gray-700">{{ ucfirst($jenis) }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500">Tidak ada pengeluaran.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t">
                    <tr>
                        <td class="px-4 py-3 font-semibold text-gray-900">Total</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-700 whitespace-nowrap">
                            Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label for="bulan_filter" class="block text-sm font-medium text-gray-600 mb-1">Bulan</label>
                    <select
                        id="bulan_filter"
                        name="bulan_filter"
                        class="border border-gray-300 rounded-lg pl-3 pr-9 py-2 min-w-[6.5rem] text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        @foreach ($namaBulan as $nomorBulan => $labelBulan)
                            <option value="{{ $nomorBulan }}" @selected($bulanAktif === $nomorBulan)>{{ $labelBulan }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tahun_filter" class="block text-sm font-medium text-gray-600 mb-1">Tahun</label>
                    <select
                        id="tahun_filter"
                        name="tahun_filter"
                        class="border border-gray-300 rounded-lg pl-3 pr-9 py-2 min-w-[6.5rem] text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        @foreach ($daftarTahun as $tahunItem)
                            <option value="{{ $tahunItem }}" @selected($tahunAktif === (int) $tahunItem) class="text-left">{{ $tahunItem }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter" class="block text-sm font-medium text-gray-600 mb-1">Jenis Arus</label>
                    <select
                        id="filter"
                        name="filter"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="semua" @selected($filter === 'semua')>Semua</option>
                        <option value="koperasi" @selected($filter === 'koperasi')>Arus Koperasi</option>
                        <option value="operasional" @selected($filter === 'operasional')>Arus Operasional</option>
                    </select>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Tampilkan
                </button>

                <a href="{{ route('admin.keuangan.laporan.arus-kas.export', ['bulan_filter' => $bulanAktif, 'tahun_filter' => $tahunAktif, 'bulan' => $bulan, 'filter' => $filter]) }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition inline-flex items-center justify-center">
                    Export Excel
                </a>
            </form>
        </div>

        <div class="max-h-[28rem] overflow-auto">
            <table class="min-w-full text-sm">
            <thead class="bg-[#9fb2de] border-b border-gray-300 sticky top-0 z-10">
                    <tr class="text-gray-900">
                        <th class="px-4 py-3 text-left text-sm font-semibold">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal Transaksi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Rincian Transaksi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Jenis Arus</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">Masuk (Rp.)</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">Keluar (Rp.)</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($items as $index => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-700">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-gray-900 whitespace-nowrap">{{ $item->tanggal->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-900">
                                {{ $item->keterangan ?: ucfirst($item->kategori) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $item->jenis_arus === 'koperasi' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($item->jenis_arus) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-green-700 whitespace-nowrap">
                                @if ($item->tipe === 'masuk')
                                    {{ number_format($item->jumlah, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-red-700 whitespace-nowrap">
                                @if ($item->tipe === 'keluar')
                                    {{ number_format($item->jumlah, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                @php
                                    $detail = collect([
                                        $item->sub_kategori ? ucfirst($item->sub_kategori) : null,
                                        $item->anggota?->nama ? 'Anggota: ' . $item->anggota->nama : null,
                                        $item->rekening?->nama ? 'Rekening: ' . $item->rekening->nama : null,
                                    ])->filter()->implode(' | ');
                                @endphp
                                {{ $detail ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                Tidak ada transaksi untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-yellow-50 border-t">
                    <tr>
                        <td colspan="4" class="px-4 py-3 font-semibold text-gray-900">Saldo Berjalan Bulan Ini</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-700 whitespace-nowrap">
                            Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-red-700 whitespace-nowrap">
                            Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $saldoBersih >= 0 ? 'text-blue-700' : 'text-red-700' }} whitespace-nowrap">
                            Rp {{ number_format($saldoBersih, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
