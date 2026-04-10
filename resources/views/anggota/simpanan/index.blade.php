@extends('layouts.main')

@section('title', 'Simpanan Saya')
@section('page-title', 'Simpanan Saya')

@section('content')
<div class="space-y-10">

    {{-- SALDO --}}
    <div>
        @if(empty($saldo) || count($saldo) == 0)
            <div class="bg-white border border-gray-200 rounded-xl p-6 text-center text-gray-500 mb-8">
                Belum ada saldo simpanan.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                @foreach($saldo as $jenis => $jumlah)
                    <div class="stat-card stat-info">
                        <div class="stat-label">
                            Total Simpanan {{ ucfirst($jenis) }}
                        </div>
                        <div class="stat-value">
                            Rp {{ number_format($jumlah, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- RIWAYAT SIMPANAN --}}
    <div>
        <h2 class="section-title">
            Riwayat Simpanan
        </h2>

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Desktop View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Tanggal</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Jenis</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Jumlah</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody divide-y divide-gray-200>
                        @forelse($simpanan as $s)
                            <tr class="hover:bg-blue-50 transition-colors duration-150 border-t">
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $s->tanggal->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $badge = match($s->jenis_simpanan) {
                                            'pokok'    => 'bg-blue-100 text-blue-800',
                                            'wajib'    => 'bg-purple-100 text-purple-800',
                                            'sukarela' => 'bg-green-100 text-green-800',
                                            default    => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $badge }}">
                                        {{ ucfirst($s->jenis_simpanan) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($s->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 text-xs">
                                    {{ $s->keterangan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Belum ada riwayat simpanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile View --}}
            <div class="md:hidden">
                @forelse($simpanan as $s)
                    <div class="border-b border-gray-200 p-4 hover:bg-blue-50 transition-colors duration-150">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="text-xs text-gray-500 font-medium">{{ $s->tanggal->format('d M Y') }}</div>
                                <div class="text-sm font-semibold text-gray-900 mt-1">
                                    Rp {{ number_format($s->jumlah, 0, ',', '.') }}
                                </div>
                            </div>
                            @php
                                $badge = match($s->jenis_simpanan) {
                                    'pokok'    => 'bg-blue-100 text-blue-800',
                                    'wajib'    => 'bg-purple-100 text-purple-800',
                                    'sukarela' => 'bg-green-100 text-green-800',
                                    default    => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $badge }}">
                                {{ ucfirst($s->jenis_simpanan) }}
                            </span>
                        </div>
                        @if($s->keterangan)
                            <div class="text-xs text-gray-600">{{ $s->keterangan }}</div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Belum ada riwayat simpanan.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($simpanan->hasPages())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-3 px-3 py-2 bg-white border border-gray-200 rounded-lg shadow-sm">

                {{-- INFO --}}
                <p class="text-sm text-gray-600">
                    Menampilkan
                    <span class="font-semibold text-gray-900">
                        {{ $simpanan->firstItem() ?? 0 }}
                    </span>
                    –
                    <span class="font-semibold text-gray-900">
                        {{ $simpanan->lastItem() ?? 0 }}
                    </span>
                    dari
                    <span class="font-semibold text-gray-900">
                        {{ $simpanan->total() }}
                    </span>
                    data
                </p>

                {{-- PAGINATION --}}
                <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                    {{ $simpanan->links('vendor.pagination.custom') }}
                </div>

            </div>
        @endif
    </div>

</div>
@endsection