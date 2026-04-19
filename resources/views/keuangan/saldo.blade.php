@extends('layouts.main')

@section('title', 'Neraca Saldo')
@section('page-title', 'Neraca Saldo')

@section('content')
<div class="space-y-4 -mt-1">
    @include('keuangan._tabs')

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#dfe8d4] border-b border-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left text-base font-semibold text-gray-900">No.</th>
                            <th class="px-4 py-3 text-left text-base font-semibold text-gray-900">Simpanan dan Modal</th>
                            <th class="px-4 py-3 text-right text-base font-semibold text-gray-900">Nilai (Rupiah)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($rincianKoperasiKiri as $index => $item)
                            <tr>
                                <td class="px-4 py-3 text-gray-700 align-top w-16">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $item['label'] }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                    {{ number_format($item['nilai'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-gray-300 bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-lg font-semibold text-gray-900">Jumlah</td>
                            <td class="px-4 py-4 text-right text-lg font-semibold text-gray-900 whitespace-nowrap">
                                {{ number_format(collect($rincianKoperasiKiri)->sum('nilai'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#dfe8d4] border-b border-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left text-base font-semibold text-gray-900">No.</th>
                            <th class="px-4 py-3 text-left text-base font-semibold text-gray-900">Piutang dan Kas</th>
                            <th class="px-4 py-3 text-right text-base font-semibold text-gray-900">Nilai (Rupiah)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($rincianKoperasiKanan as $index => $item)
                            <tr>
                                <td class="px-4 py-3 text-gray-700 align-top w-16">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $item['label'] }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                    {{ number_format($item['nilai'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-gray-300 bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-lg font-semibold text-gray-900">Jumlah</td>
                            <td class="px-4 py-4 text-right text-lg font-semibold text-gray-900 whitespace-nowrap">
                                {{ number_format(collect($rincianKoperasiKanan)->sum('nilai'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <hr>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-2 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Ringkasan Saldo</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">No.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Ringkasan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide">Nilai (Rupiah)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($ringkasanSaldo as $index => $item)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700 w-16">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['label'] }}</td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($item['nilai'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-2 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Alokasi Saldo Koperasi</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Uraian</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide">Nilai (Rupiah)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($alokasiSaldoKoperasi as $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-900">{{ $item['label'] }}</td>
                            <td class="px-4 py-3 text-right font-medium {{ $item['label'] === 'Saldo Riil untuk Dipinjam Anggota' ? 'text-green-700' : 'text-gray-900' }} whitespace-nowrap">
                                Rp {{ number_format($item['nilai'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4">
        <p class="text-sm font-semibold text-amber-900">Catatan Perhitungan</p>
        <p class="mt-1 text-sm text-amber-800">
            Saldo kas koperasi dihitung dari total simpanan dan modal dikurangi piutang ke anggota.
        </p>
        <p class="mt-1 text-sm text-amber-700">
            Saldo riil untuk dipinjam anggota dihitung dari saldo kas koperasi dikurangi simpanan sukarela.
        </p>
    </div>

</div>
@endsection
