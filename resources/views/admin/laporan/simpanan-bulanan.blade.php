@extends('layouts.main')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">
            Laporan Simpanan Bulanan
        </h1>

        <div class="flex gap-2">
            @can('export laporan simpanan')
                <a href="{{ route('admin.laporan.simpanan-bulanan.export', ['bulan' => $bulan]) }}"
                   class="px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                    Export Excel
                </a>
            @endcan

            @can('lock laporan simpanan')
                @if(! $isLocked)
                    <form method="POST"
                          action="{{ route('admin.laporan.simpanan-bulanan.lock') }}"
                          onsubmit="return confirm('Yakin ingin menutup bulan ini? Setelah ditutup, data tidak bisa diubah.')">
                        @csrf
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <button
                            type="submit"
                            class="px-4 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                            Tutup Bulan
                        </button>
                    </form>
                @else
                    <span class="px-4 py-1 bg-gray-200 text-gray-700 rounded text-sm">
                        🔒 Bulan sudah ditutup
                    </span>
                @endif
            @endcan
        </div>
    </div>

    {{-- FILTER BULAN --}}
    <form method="GET" class="mb-6 flex items-end gap-2">
        <div>
            <label class="text-sm text-gray-600">Bulan</label>
            <input
                type="month"
                name="bulan"
                value="{{ $bulan }}"
                class="border rounded px-3 py-1"
            >
        </div>
        <button class="px-4 py-1 bg-blue-600 text-white rounded">
            Tampilkan
        </button>
    </form>

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        @foreach (['pokok', 'wajib', 'sukarela'] as $jenis)
            <div class="border rounded p-4 bg-white">
                <div class="text-sm text-gray-500 capitalize">
                    Simpanan {{ $jenis }}
                </div>
                <div class="text-lg font-semibold">
                    Rp {{ number_format($data[$jenis]->total ?? 0, 0, ',', '.') }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- RINCIAN --}}
    <div>
        <h2 class="text-lg font-semibold mb-3">
            Rincian Simpanan per Anggota
        </h2>

        <table class="min-w-full border bg-white text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-left">Anggota</th>
                    <th class="border px-3 py-2">Pokok</th>
                    <th class="border px-3 py-2">Wajib</th>
                    <th class="border px-3 py-2">Sukarela</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rincian->groupBy('anggota_id') as $items)
                    @php
                        $anggota = $items->first()->anggota;
                        $byJenis = $items->keyBy('jenis_simpanan');
                    @endphp
                    <tr>
                        <td class="border px-3 py-2">
                            {{ $anggota->nama ?? '-' }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            Rp {{ number_format($byJenis['pokok']->total ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            Rp {{ number_format($byJenis['wajib']->total ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            Rp {{ number_format($byJenis['sukarela']->total ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
