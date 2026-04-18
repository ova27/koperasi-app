@extends('layouts.main')
@section('title', 'Transaksi Arus Koperasi')
@section('page-title', 'Transaksi Arus Koperasi')
@section('page-description', 'Daftar transaksi detail untuk simpanan, pinjaman, dan mutasi kas anggota pada periode yang dipilih.')

@section('content')
<div class="space-y-6 -mt-1">
    @include('keuangan._tabs')

    @php
        [$tahunAktif, $bulanAktif] = array_pad(explode('-', $bulan), 2, null);

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

        $labelPeriode = ($namaBulan[(int) $bulanAktif] ?? 'Bulan') . ' ' . ($tahunAktif ?: now()->format('Y'));
        $saldoBersih = $totalMasukBulan - $totalKeluarBulan;
    @endphp

    <div class="rounded-2xl border border-sky-100 bg-gradient-to-r from-sky-50 via-white to-cyan-50 p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex w-fit items-center rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-sky-700">
                    Detail transaksi anggota
                </div>
                <h2 class="mt-3 text-lg font-semibold text-slate-900">Arus kas koperasi untuk {{ $labelPeriode }}</h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Halaman ini menampilkan transaksi satu per satu agar bendahara bisa mengecek sumber dana, tujuan dana, dan pihak anggota yang terkait.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:min-w-[34rem]">
                <div class="rounded-xl border border-white/70 bg-white/90 p-4 shadow-sm">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Kas masuk</p>
                    <p class="mt-2 text-xl font-semibold text-emerald-700">Rp {{ number_format($totalMasukBulan, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-white/70 bg-white/90 p-4 shadow-sm">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Kas keluar</p>
                    <p class="mt-2 text-xl font-semibold text-rose-700">Rp {{ number_format($totalKeluarBulan, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-white/70 bg-white/90 p-4 shadow-sm">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Saldo bersih</p>
                    <p class="mt-2 text-xl font-semibold {{ $saldoBersih >= 0 ? 'text-sky-700' : 'text-rose-700' }}">
                        Rp {{ number_format($saldoBersih, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <!-- Filter Section -->
        <form method="GET" class="flex flex-wrap gap-2 items-end">
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-600 mb-1">Periode transaksi</label>
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
            <a href="{{ route('admin.keuangan.arus.koperasi.export', ['bulan' => $bulan]) }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition inline-flex items-center justify-center">
                Export Excel
            </a>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <div class="border-b border-gray-200 px-4 py-4 sm:px-5">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Rincian transaksi koperasi</h3>
                    <p class="text-sm text-slate-500">Tampilkan transaksi anggota untuk menelusuri asal dan tujuan dana secara detail.</p>
                </div>
                <div class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                    Fokus: transaksi detail per anggota
                </div>
            </div>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Keterangan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Sumber/Tujuan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">Masuk</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">Keluar</th>
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
                        <td class="px-4 py-3 text-sm">
                            @if ($item->kategori === 'simpanan')
                                <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    {{ ucfirst($item->kategori) }}
                                </span>
                            @elseif ($item->kategori === 'pinjaman')
                                <span class="inline-flex items-center px-2.5 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                                    {{ ucfirst($item->kategori) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    {{ ucfirst($item->kategori) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->anggota->nama ?? 'Tidak Diketahui' }}</td>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                            Tidak ada data untuk periode ini
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <!-- Footer Total -->
            <tfoot class="bg-gray-50 border-t text-sm font-semibold text-gray-700">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-right uppercase tracking-wide text-xs">Total</td>
                    <td class="px-4 py-3 text-right text-green-600 font-semibold whitespace-nowrap">
                        + Rp {{ number_format($totalMasukBulan, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right text-red-600 font-semibold whitespace-nowrap">
                        - Rp {{ number_format($totalKeluarBulan, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($items->hasPages())
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-2">
            <p class="text-sm text-gray-600">
                Menampilkan
                <span class="font-semibold text-gray-900">{{ $items->firstItem() ?? 0 }}</span>
                sampai
                <span class="font-semibold text-gray-900">{{ $items->lastItem() ?? 0 }}</span>
                dari
                <span class="font-semibold text-gray-900">{{ $items->total() }}</span>
                data
            </p>

            <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                {{ $items->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        </div>
    @endif

</div>
@endsection
