@extends('layouts.main')

@section('title', 'Setoran Bank')
@section('page-title', 'Setoran Bank')

@section('content')
<div class="space-y-3">
    @include('admin.laporan._tabs_potongan')
    @php
        $canExportWord = $isFixed && $namaBank !== '' && $selectedRekeningKoperasiId !== null;
    @endphp

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="{{ $isFixed ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-amber-200 bg-amber-50 text-amber-800' }} rounded-lg border px-4 py-3 text-sm">
        @if($isFixed)
            Setoran bank bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanPotongan)->translatedFormat('F Y') }} sudah memakai data fixed.
        @elseif($canManagePotongan)
            Setoran bank ini masih draft dan hanya terlihat untuk Bendahara sampai potongan difix.
        @else
            Setoran bank bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanPotongan)->translatedFormat('F Y') }} belum difix oleh Bendahara.
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-2 mb-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Bulan (Potongan)</label>
                <input type="month" name="bulan" value="{{ $bulanPotongan }}" max="{{ $batasBulanPotongan }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                @error('bulan')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Nama Bank</label>
                <select name="nama_bank" class="border border-gray-300 rounded-lg px-3 py-2 text-sm min-w-48 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                    <option value="">Semua Bank</option>
                    @foreach($bankOptions as $bankOption)
                        <option value="{{ $bankOption }}" {{ $namaBank === $bankOption ? 'selected' : '' }}>
                            {{ $bankOption }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                Tampilkan
            </button>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="bg-gradient-to-r from-blue-50 to-white border border-blue-100 rounded-lg p-3 shadow-sm">
                <div class="text-xs uppercase tracking-wide text-gray-500">Total Setoran Bank</div>
                <div class="text-sm font-semibold text-blue-700">Rp {{ number_format($totalSetoranBank, 0, ',', '.') }}</div>
            </div>
            <div class="bg-gradient-to-r from-blue-50 to-white border border-blue-100 rounded-lg p-3 shadow-sm">
                <div class="text-xs uppercase tracking-wide text-gray-500">Jumlah Anggota Aktif</div>
                <div class="text-sm font-semibold text-blue-700">{{ $rows->count() }} anggota</div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 pb-4 border-b border-gray-100">
            <h2 class="text-sm font-medium text-gray-700">Daftar Setoran Anggota</h2>
            @can('export laporan pinjaman')
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    @if($isFixed)
                        <a href="{{ route('admin.laporan.potongan-bulanan.bank.export', ['bulan' => $bulanPotongan, 'nama_bank' => $namaBank]) }}"
                            class="inline-flex items-center justify-center px-3.5 py-2 rounded-md text-sm font-medium bg-green-600 text-white hover:bg-green-700 transition-colors duration-150">
                            Export Excel
                        </a>
                    @else
                        <button type="button" disabled class="inline-flex items-center justify-center px-3.5 py-2 rounded-md text-sm font-medium bg-gray-200 text-gray-500 cursor-not-allowed">
                            Export Excel
                        </button>
                    @endif
                    @if($canExportWord)
                        <a href="{{ route('admin.laporan.potongan-bulanan.bank.export-word', ['bulan' => $bulanPotongan, 'nama_bank' => $namaBank, 'rekening_koperasi_id' => $selectedRekeningKoperasiId]) }}"
                            class="inline-flex items-center justify-center px-3.5 py-2 rounded-md text-sm font-medium bg-slate-700 text-white hover:bg-slate-800 transition-colors duration-150">
                            Export Word Surat Kuasa
                        </a>
                    @else
                        <button type="button" disabled
                            title="Pilih bank yang sudah dimapping ke Master Rekening Koperasi"
                            class="inline-flex items-center justify-center px-3.5 py-2 rounded-md text-sm font-medium bg-gray-200 text-gray-500 cursor-not-allowed">
                            Export Word Surat Kuasa
                        </button>
                    @endif
                </div>
            @endcan
        </div>
        @if(!$canExportWord)
            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-md px-3 py-2 mb-3">
                Pilih bank untuk bisa export surat kuasa dalam format Word.
            </p>
        @endif

        <div class="hidden md:block rounded-lg border border-gray-100 overflow-auto max-h-[65vh]">
            <table class="min-w-full text-sm">
            <thead class="bg-gradient-to-r from-blue-50 to-blue-100 border-b-2 border-blue-300 sticky top-0 z-10">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">No</th>
                    <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">Nama Anggota</th>
                    <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">Bank</th>
                    <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">Nomor Rekening</th>
                    <th class="px-3 py-2 text-right font-semibold text-xs text-blue-900 uppercase tracking-widest">Total Setoran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rows as $index => $row)
                    <tr class="even:bg-blue-50 hover:bg-blue-100 transition-all duration-200">
                        <td class="px-3 py-2 text-xs text-gray-700">{{ $index + 1 }}</td>
                        <td class="px-3 py-2 text-xs font-medium text-gray-800">{{ $row['nama'] }}</td>
                        <td class="px-3 py-2 text-xs text-gray-700">{{ $row['bank'] }}</td>
                        <td class="px-3 py-2 text-xs text-gray-700">{{ $row['nomor_rekening'] }}</td>
                        <td class="px-3 py-2 text-right text-xs font-bold text-blue-700">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-center text-gray-500">
                            Tidak ada data anggota aktif
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($rows->isNotEmpty())
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <th colspan="4" class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Jumlah</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-blue-700">Rp {{ number_format($totalSetoranBank, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            @endif
        </table>
        </div>

        <div class="md:hidden space-y-2.5">
            @forelse($rows as $index => $row)
                <div class="bg-gradient-to-r from-blue-50 to-white border-l-4 border-l-blue-600 border border-blue-100 rounded-lg p-3.5 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs text-gray-500">{{ $index + 1 }}. {{ $row['nama'] }}</div>
                            <div class="text-xs text-gray-600">{{ $row['bank'] }} - {{ $row['nomor_rekening'] }}</div>
                        </div>
                        <div class="text-sm font-bold text-blue-700">Rp {{ number_format($row['total'], 0, ',', '.') }}</div>
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-400 text-sm">
                    Tidak ada data anggota aktif
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
