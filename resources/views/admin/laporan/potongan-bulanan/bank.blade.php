@extends('layouts.main')

@section('title', 'Setoran Bank')
@section('page-title', 'Setoran Bank')

@section('content')
<div class="space-y-6 -mt-1">
    @include('admin.laporan._tabs_potongan')

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">Setoran Bank</h1>
            <p class="text-sm text-gray-500">
                Halaman ini khusus rekap setoran bank untuk bulan
                {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanPotongan)->translatedFormat('F Y') }}.
            </p>
        </div>

        @can('export laporan pinjaman')
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.laporan.potongan-bulanan.bank.export', ['bulan' => $bulanPotongan, 'nama_bank' => $namaBank]) }}"
                    class="px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                    Export Excel Setoran Bank
                </a>
                @if($namaBank !== '')
                    <a href="{{ route('admin.laporan.potongan-bulanan.bank.export-word', ['bulan' => $bulanPotongan, 'nama_bank' => $namaBank]) }}"
                        class="px-4 py-1 bg-slate-700 text-white rounded hover:bg-slate-800">
                        Export Word Surat Kuasa
                    </a>
                @endif
            </div>
        @endcan
    </div>

    <form method="GET" class="mb-2 flex flex-wrap items-end gap-2">
        <div>
            <label class="text-sm text-gray-600">Bulan (Potongan)</label>
            <input type="month" name="bulan" value="{{ $bulanPotongan }}" class="border rounded px-3 py-1">
        </div>
        <div>
            <label class="text-sm text-gray-600">Nama Bank</label>
            <select name="nama_bank" class="border rounded px-3 py-1 min-w-48">
                <option value="">Semua Bank</option>
                @foreach($bankOptions as $bankOption)
                    <option value="{{ $bankOption }}" {{ $namaBank === $bankOption ? 'selected' : '' }}>
                        {{ $bankOption }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-1 bg-blue-600 text-white rounded">Tampilkan</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">Total Setoran Bank</div>
            <div class="text-lg font-semibold">Rp {{ number_format($totalSetoranBank, 0, ',', '.') }}</div>
        </div>
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">Jumlah Anggota Aktif</div>
            <div class="text-lg font-semibold">{{ $rows->count() }} anggota</div>
        </div>
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">Jumlah Bank</div>
            <div class="text-lg font-semibold">{{ $ringkasanBank->count() }} bank</div>
        </div>
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">Rata-rata per Anggota</div>
            <div class="text-lg font-semibold">
                Rp {{ number_format($rows->count() > 0 ? (int) ($totalSetoranBank / $rows->count()) : 0, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="border rounded bg-white p-4">
        <h2 class="text-sm font-semibold text-gray-700 mb-2">Ringkasan Setoran per Bank</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @forelse($ringkasanBank as $bank => $item)
                <div class="rounded border p-3">
                    <div class="text-xs uppercase text-gray-500">{{ $bank }}</div>
                    <div class="text-sm">{{ $item['jumlah_anggota'] }} anggota</div>
                    <div class="font-semibold">Rp {{ number_format($item['total'], 0, ',', '.') }}</div>
                    @can('export laporan pinjaman')
                        <a href="{{ route('admin.laporan.potongan-bulanan.bank.export-word', ['bulan' => $bulanPotongan, 'nama_bank' => $bank]) }}"
                            class="mt-2 inline-flex text-xs text-slate-700 hover:text-slate-900 underline">
                            Export Word {{ $bank }}
                        </a>
                    @endcan
                </div>
            @empty
                <div class="text-sm text-gray-500">Belum ada data.</div>
            @endforelse
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse border border-gray-400 bg-white text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-left">No</th>
                    <th class="border px-3 py-2 text-left">Nama Anggota</th>
                    <th class="border px-3 py-2 text-left">Bank</th>
                    <th class="border px-3 py-2 text-left">Nomor Rekening</th>
                    <th class="border px-3 py-2 text-right">Total Setoran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $index => $row)
                    <tr>
                        <td class="border px-3 py-2">{{ $index + 1 }}</td>
                        <td class="border px-3 py-2">{{ $row['nama'] }}</td>
                        <td class="border px-3 py-2">{{ $row['bank'] }}</td>
                        <td class="border px-3 py-2">{{ $row['nomor_rekening'] }}</td>
                        <td class="border px-3 py-2 text-right font-semibold">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border px-3 py-4 text-center text-gray-500">
                            Tidak ada data anggota aktif
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($rows->isNotEmpty())
                <tfoot class="bg-gray-50">
                    <tr>
                        <th colspan="4" class="border px-3 py-2 text-right">Jumlah</th>
                        <th class="border px-3 py-2 text-right">Rp {{ number_format($totalSetoranBank, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
