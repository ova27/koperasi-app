@extends('layouts.main')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-10">

    {{-- ========================= --}}
    {{-- WELCOME --}}
    {{-- ========================= --}}
    <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
        <p class="text-gray-800">
            Selamat datang,
            <span class="font-semibold text-gray-900">
                {{ auth()->user()->name }}
            </span>
        </p>

        <p class="mt-1 text-sm text-gray-600">
            Semoga aktivitas hari ini berjalan lancar.
        </p>
    </div>

    {{-- ========================= --}}
    {{-- STATISTIK UTAMA --}}
    {{-- ========================= --}}
    <div>
        <h2 class="section-title">
            Ringkasan Koperasi
            <span class="section-subtitle">
                ({{ $bulan }} {{ $tahun }})
            </span>
        </h2>

        {{-- ========================= --}}
        {{-- ANGGOTA --}}
        {{-- ========================= --}}
        <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">
            Anggota
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="stat-card stat-success">
                <div class="stat-label">Anggota Aktif</div>
                <div class="stat-value">
                    {{ $anggotaAktif ?? 0 }}
                </div>
            </div>
        </div>

        {{-- ========================= --}}
        {{-- SIMPANAN --}}
        {{-- ========================= --}}
        <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">
            Simpanan Seluruh Anggota
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="stat-card stat-primary">
                <div class="stat-label">Simpanan Pokok</div>
                <div class="stat-value">
                    Rp {{ number_format($simpananPokok ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="stat-card stat-primary">
                <div class="stat-label">Simpanan Wajib</div>
                <div class="stat-value">
                    Rp {{ number_format($simpananWajib ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="stat-card stat-primary">
                <div class="stat-label">Simpanan Sukarela</div>
                <div class="stat-value">
                    Rp {{ number_format($simpananSukarela ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- ========================= --}}
        {{-- PINJAMAN --}}
        {{-- ========================= --}}
        <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">
            Pinjaman Seluruh Anggota
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="stat-card stat-warning">
                <div class="stat-label">Pinjaman Aktif</div>
                <div class="stat-value">
                    {{ $pinjamanAktif ?? 0 }}
                </div>
            </div>

            <div class="stat-card stat-warning">
                <div class="stat-label">Antrian Pinjaman</div>
                <div class="stat-value">
                    {{ $antrianPinjaman ?? 0 }}
                </div>
            </div>

            <div class="stat-card stat-warning">
                <div class="stat-label">
                    Sisa Pinjaman Aktif
                </div>
                <div class="stat-value">
                    Rp {{ number_format($sisaPinjamanAktif ?? 0, 0, ',', '.') }}
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
