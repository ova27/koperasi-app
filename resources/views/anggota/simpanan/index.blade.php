@extends('layouts.main')

@section('title', 'Simpanan Saya')
@section('page-title', 'Simpanan Saya')
@section('page-description', 'Pantau saldo dan riwayat simpanan pokok, wajib, serta sukarela Anda.')

@section('content')
<div class="member-finance-page space-y-5">

    {{-- SALDO --}}
    <div class="surface-card p-5 sm:p-6">
        @if(empty($saldo) || count($saldo) == 0)
            <div class="text-center text-gray-500 py-8">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Belum ada saldo simpanan.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @php
                    $jenisConfig = [
                        'pokok' => ['icon' => 'M12 8c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm0 2c2.67 0 8 1.34 8 4v2H4v-2c0-2.66 5.33-4 8-4z', 'color' => 'blue'],
                        'wajib' => ['icon' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z', 'color' => 'purple'],
                        'sukarela' => ['icon' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z', 'color' => 'green'],
                    ];
                @endphp

                @php
                    $urutanJenis = ['pokok', 'wajib', 'sukarela'];
                @endphp

                @foreach($urutanJenis as $jenis)
                    @php
                        $jumlah = $saldo[$jenis] ?? 0;
                        $config = $jenisConfig[$jenis] ?? $jenisConfig['pokok'];
                        $colorGradient = match($jenis) {
                            'pokok' => 'from-blue-50 to-blue-100 border-blue-200',
                            'wajib' => 'from-purple-50 to-purple-100 border-purple-200',
                            'sukarela' => 'from-green-50 to-green-100 border-green-200',
                            default => 'from-gray-50 to-gray-100 border-gray-200',
                        };
                        $bgColor = match($jenis) {
                            'pokok' => 'bg-blue-200',
                            'wajib' => 'bg-purple-200',
                            'sukarela' => 'bg-green-200',
                            default => 'bg-gray-200',
                        };
                        $textColor = match($jenis) {
                            'pokok' => 'text-slate-800',
                            'wajib' => 'text-purple-600',
                            'sukarela' => 'text-green-600',
                            default => 'text-gray-600',
                        };
                    @endphp
                    <div class="member-summary-card savings-summary-card rounded-xl border px-5 py-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="summary-icon flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="{{ $config['icon'] }}" />
                                </svg>
                            </div>
                            <div class="text-sm font-medium text-gray-600">Total Simpanan {{ ucfirst($jenis) }}</div>
                        </div>
                        <div class="text-2xl font-bold text-gray-900">
                            Rp {{ number_format($jumlah, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- RIWAYAT SIMPANAN --}}
    <div class="surface-card p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h2 class="text-base font-semibold text-gray-700">
                Riwayat Simpanan
            </h2>
            
            {{-- FILTER BUTTONS --}}
            <div class="finance-filter-tabs flex flex-wrap gap-1 rounded-lg bg-slate-100 p-1">
                <a href="{{ route('anggota.simpanan.index') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(!request('filter')) bg-slate-800 text-white @else bg-transparent text-slate-600 hover:bg-white @endif">
                    Semua
                </a>
                
                <a href="{{ route('anggota.simpanan.index', ['filter' => 'pokok']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request('filter') === 'pokok') bg-slate-800 text-white @else bg-transparent text-slate-600 hover:bg-white @endif">
                    Pokok
                </a>
                
                <a href="{{ route('anggota.simpanan.index', ['filter' => 'wajib']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request('filter') === 'wajib') bg-slate-800 text-white @else bg-transparent text-slate-600 hover:bg-white @endif">
                    Wajib
                </a>
                
                <a href="{{ route('anggota.simpanan.index', ['filter' => 'sukarela']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 @if(request('filter') === 'sukarela') bg-slate-800 text-white @else bg-transparent text-slate-600 hover:bg-white @endif">
                    Sukarela
                </a>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="finance-table-shell overflow-hidden rounded-xl border border-slate-200">
            {{-- Desktop View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-2.5 text-left font-semibold text-xs text-slate-600">Tanggal</th>
                            <th class="px-5 py-2.5 text-center font-semibold text-xs text-slate-600">Jenis</th>
                            <th class="px-5 py-2.5 text-right font-semibold text-xs text-slate-600">Jumlah</th>
                            <th class="px-5 py-2.5 text-center font-semibold text-xs text-slate-600">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($simpanan as $s)
                            <tr class="@if($loop->odd) bg-white @else bg-blue-50 @endif hover:bg-blue-100 transition-all duration-300 group">
                                <td class="px-5 py-2.5 text-gray-800 font-medium text-xs">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $s->tanggal->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-2.5 text-center">
                                    @php
                                        $badge = match($s->jenis_simpanan) {
                                            'pokok'    => 'bg-slate-100 text-slate-700 border border-slate-200',
                                            'wajib'    => 'bg-slate-100 text-slate-700 border border-slate-200',
                                            'sukarela' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                            default    => 'bg-gray-100 text-gray-700 border border-gray-300 shadow-sm',
                                        };
                                    @endphp

                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
                                        {{ ucfirst($s->jenis_simpanan) }}
                                    </span>
                                </td>
                                <td class="px-5 py-2.5 text-right font-bold text-gray-900 text-sm">
                                    <span class="text-slate-800">Rp {{ number_format($s->jumlah, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-5 py-2.5 text-center">
                                    @if($s->keterangan)
                                        <span class="inline-block bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs font-medium">{{ $s->keterangan }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">Belum ada riwayat simpanan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile View --}}
            <div class="md:hidden space-y-2.5">
                @forelse($simpanan as $s)
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                        <div class="flex justify-between items-start gap-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-3.5 h-3.5 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div class="text-xs text-gray-600 font-semibold">{{ $s->tanggal->format('d M Y') }}</div>
                                </div>
                                <div class="text-lg font-bold text-slate-800">
                                    Rp {{ number_format($s->jumlah, 0, ',', '.') }}
                                </div>
                            </div>
                            @php
                                $badge = match($s->jenis_simpanan) {
                                    'pokok'    => 'bg-slate-100 text-slate-700 border border-slate-200',
                                    'wajib'    => 'bg-slate-100 text-slate-700 border border-slate-200',
                                    'sukarela' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    default    => 'bg-gray-100 text-gray-700 border border-gray-300 shadow-sm',
                                };
                            @endphp

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badge }} whitespace-nowrap">
                                {{ ucfirst($s->jenis_simpanan) }}
                            </span>
                        </div>
                        @if($s->keterangan)
                            <div class="text-xs text-gray-700 bg-gray-200 rounded px-2 py-1.5 mt-2">
                                <span class="font-semibold">Keterangan:</span> {{ $s->keterangan }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm font-medium">Belum ada riwayat simpanan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($simpanan->hasPages())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-4 pt-4 border-t border-gray-200">
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