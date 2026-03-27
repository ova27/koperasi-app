@extends('layouts.main')

@section('title', 'Simpanan Saya')
@section('page-title', 'Simpanan Saya')

@section('content')
<div class="space-y-10">

    {{-- SALDO --}}
    <div>
        <h2 class="section-title">Saldo Simpanan Saya</h2>

        @if(empty($saldo) || count($saldo) == 0)
            <div class="bg-white border border-gray-200 rounded-xl p-6 text-center text-gray-500 mb-8">
                Belum ada saldo simpanan.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                @foreach($saldo as $jenis => $jumlah)
                    <div class="stat-card stat-info">
                        <div class="stat-label">
                            {{ ucfirst($jenis) }}
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        @if($s->jenis_simpanan === 'pokok') bg-blue-100 text-blue-800
                                        @elseif($s->jenis_simpanan === 'wajib') bg-purple-100 text-purple-800
                                        @elseif($s->jenis_simpanan === 'sukarela') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-700
                                        @endif
                                    ">
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($s->jenis_simpanan === 'pokok') bg-blue-100 text-blue-800
                                @elseif($s->jenis_simpanan === 'wajib') bg-purple-100 text-purple-800
                                @elseif($s->jenis_simpanan === 'sukarela') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-700
                                @endif
                            ">
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
            <div class="mt-6 flex justify-center">
                <div class="flex items-center gap-2">
                    {{-- Previous --}}
                    @if($simpanan->onFirstPage())
                        <span class="px-3 py-2 text-gray-400 cursor-not-allowed">← Sebelumnya</span>
                    @else
                        <a href="{{ $simpanan->previousPageUrl() }}" class="px-3 py-2 text-blue-600 hover:bg-blue-50 rounded transition-colors">← Sebelumnya</a>
                    @endif

                    {{-- Page Numbers --}}
                    <div class="flex gap-1">
                        @foreach($simpanan->getUrlRange(1, $simpanan->lastPage()) as $page => $url)
                            @if($page == $simpanan->currentPage())
                                <span class="px-3 py-2 bg-blue-600 text-white rounded font-medium">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Next --}}
                    @if($simpanan->hasMorePages())
                        <a href="{{ $simpanan->nextPageUrl() }}" class="px-3 py-2 text-blue-600 hover:bg-blue-50 rounded transition-colors">Selanjutnya →</a>
                    @else
                        <span class="px-3 py-2 text-gray-400 cursor-not-allowed">Selanjutnya →</span>
                    @endif
                </div>
            </div>

            {{-- Page Info --}}
            <div class="mt-3 text-center text-sm text-gray-500">
                Menampilkan {{ $simpanan->firstItem() }} hingga {{ $simpanan->lastItem() }} dari {{ $simpanan->total() }} data
            </div>
        @endif
    </div>

</div>
@endsection