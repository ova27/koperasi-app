@extends('layouts.main')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-10">
    
    {{-- ========================= --}}
    {{-- RINGKASAN KOPERASI --}}
    {{-- ========================= --}}
    <div>
        <h2 class="section-title">
            Ringkasan Koperasi
            <span class="section-subtitle">
                ({{ $bulan }} {{ $tahun }})
            </span>
        </h2>

        {{-- ========================= --}}
        {{-- RINGKASAN UTAMA --}}
        {{-- ========================= --}}
        <p class="text-xs uppercase tracking-wide text-gray-400 mb-3">
            Ringkasan Utama
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">

            {{-- ANGGOTA --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-1">
                    {{-- ICON ANGGOTA --}}
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6 text-blue-600 opacity-90"
                         fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 19.128a9.38 9.38 0 002.625.372
                                 9.337 9.337 0 004.121-.952
                                 4.125 4.125 0 00-7.533-2.493
                                 M9 5.25a3 3 0 116 0 3 3 0 01-6 0z" />
                    </svg>
                    <div class="text-sm text-gray-600">Anggota Aktif</div>
                </div>
                <div class="text-2xl font-semibold text-gray-900">
                    {{ $anggotaAktif ?? 0 }}
                </div>
            </div>

            {{-- TOTAL SIMPANAN --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-1">
                    {{-- ICON SIMPANAN --}}
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6 text-green-600 opacity-90"
                         fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 10.5h18m-18 0A2.25 2.25 0 015.25 8.25
                                 h13.5A2.25 2.25 0 0121 10.5m-18 0v3
                                 A2.25 2.25 0 005.25 15.75h13.5
                                 A2.25 2.25 0 0021 13.5v-3
                                 m-9 1.5h.008v.008H12V12z" />
                    </svg>
                    <div class="text-sm text-gray-600">Total Simpanan</div>
                </div>
                <div class="text-2xl font-semibold text-gray-900">
                    Rp {{ number_format($totalSimpanan ?? 0, 0, ',', '.') }}
                </div>
            </div>

            {{-- TOTAL PINJAMAN --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-1">
                    {{-- ICON PINJAMAN --}}
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6 text-yellow-600 opacity-90"
                         fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 8.25h19.5M4.5 6h15
                                 a2.25 2.25 0 012.25 2.25v7.5
                                 A2.25 2.25 0 0119.5 18h-15
                                 a2.25 2.25 0 01-2.25-2.25v-7.5
                                 A2.25 2.25 0 014.5 6z" />
                    </svg>
                    <div class="text-sm text-gray-600">Total Pinjaman</div>
                </div>
                <div class="text-2xl font-semibold text-gray-900">
                    Rp {{ number_format($totalPinjaman ?? 0, 0, ',', '.') }}
                </div>
            </div>

        </div>

        {{-- ========================= --}}
        {{-- DETAIL RINGKASAN --}}
        {{-- ========================= --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 space-y-10">

            {{-- SIMPANAN --}}
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">
                    Simpanan Seluruh Anggota
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                        <div class="text-sm text-green-800">Simpanan Pokok</div>
                        <div class="text-xl font-semibold text-green-900">
                            Rp {{ number_format($simpananPokok ?? 0, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                        <div class="text-sm text-green-800">Simpanan Wajib</div>
                        <div class="text-xl font-semibold text-green-900">
                            Rp {{ number_format($simpananWajib ?? 0, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                        <div class="text-sm text-green-800">Simpanan Sukarela</div>
                        <div class="text-xl font-semibold text-green-900">
                            Rp {{ number_format($simpananSukarela ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- PINJAMAN --}}
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">
                    Pinjaman Seluruh Anggota
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                        <div class="text-sm text-yellow-800">Pinjaman Aktif</div>
                        <div class="text-xl font-semibold text-yellow-900">
                            {{ $pinjamanAktif ?? 0 }}
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                        <div class="text-sm text-yellow-800">Antrian Pinjaman</div>
                        <div class="text-xl font-semibold text-yellow-900">
                            {{ $antrianPinjaman ?? 0 }}
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                        <div class="text-sm text-yellow-800">Sisa Pinjaman Aktif</div>
                        <div class="text-xl font-semibold text-yellow-900">
                            Rp {{ number_format($sisaPinjamanAktif ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ========================= --}}
    {{-- LAST UPDATED --}}
    {{-- ========================= --}}
    <div class="text-right text-xs text-gray-400">
        Terakhir diperbarui:
        @if($lastUpdated)
            {{ $lastUpdated->format('d M Y H:i') }}
        @else
            -
        @endif
    </div>

</div>
@endsection
