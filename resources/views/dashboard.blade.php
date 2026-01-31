@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ========================= --}}
    {{-- HEADER --}}
    {{-- ========================= --}}
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h1 class="text-2xl font-bold text-gray-800">
            Dashboard
        </h1>

        <p class="mt-1 text-gray-600">
            Selamat datang,
            <span class="font-semibold">{{ auth()->user()->name }}</span>
        </p>

        <p class="mt-1 text-sm text-gray-500">
            Role:
            <span class="font-medium">
                {{ auth()->user()->getRoleNames()->implode(', ') }}
            </span>
        </p>
    </div>

    {{-- ========================= --}}
    {{-- QUICK STATS --}}
    {{-- ========================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- SIMPANAN --}}
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
            <div class="text-sm text-blue-700">
                Total Simpanan
            </div>
            <div class="mt-1 text-xl font-bold text-blue-900">
                Rp {{ number_format($totalSimpanan ?? 0, 0, ',', '.') }}
            </div>
        </div>

        {{-- PINJAMAN --}}
        <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg">
            <div class="text-sm text-orange-700">
                Sisa Pinjaman
            </div>
            <div class="mt-1 text-xl font-bold text-orange-900">
                Rp {{ number_format($sisaPinjaman ?? 0, 0, ',', '.') }}
            </div>
        </div>

        {{-- ARUS KAS --}}
        @can('lihat-keuangan')
            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                <div class="text-sm text-green-700">
                    Saldo Kas
                </div>
                <div class="mt-1 text-xl font-bold text-green-900">
                    Rp {{ number_format($saldoKas ?? 0, 0, ',', '.') }}
                </div>
            </div>
        @endcan

        {{-- ANGGOTA --}}
        @can('lihat-keuangan')
            <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg">
                <div class="text-sm text-purple-700">
                    Total Anggota Aktif
                </div>
                <div class="mt-1 text-xl font-bold text-purple-900">
                    {{ $totalAnggota ?? 0 }}
                </div>
            </div>
        @endcan

    </div>

    {{-- ========================= --}}
    {{-- QUICK ACTIONS --}}
    {{-- ========================= --}}
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Akses Cepat
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- ANGGOTA --}}
            @role('anggota')
                <a href="{{ route('anggota.simpanan.index') }}"
                   class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                    <div class="font-semibold text-gray-800">
                        Simpanan Saya
                    </div>
                    <div class="text-sm text-gray-600">
                        Lihat detail simpanan pribadi
                    </div>
                </a>

                <a href="{{ route('anggota.pinjaman.index') }}"
                   class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                    <div class="font-semibold text-gray-800">
                        Pinjaman Saya
                    </div>
                    <div class="text-sm text-gray-600">
                        Status pinjaman & pengajuan
                    </div>
                </a>
            @endrole

            {{-- BENDAHARA --}}
            @role('bendahara')
                <a href="{{ route('admin.simpanan.index') }}"
                   class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                    <div class="font-semibold text-gray-800">
                        Kelola Simpanan
                    </div>
                    <div class="text-sm text-gray-600">
                        Generate & input simpanan
                    </div>
                </a>

                <a href="{{ route('admin.pinjaman.pencairan.index') }}"
                   class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                    <div class="font-semibold text-gray-800">
                        Pencairan Pinjaman
                    </div>
                    <div class="text-sm text-gray-600">
                        Proses pinjaman disetujui
                    </div>
                </a>
            @endrole

            {{-- KETUA / ADMIN --}}
            @can('lihat-laporan')
                <a href="{{ route('admin.laporan.simpanan-bulanan') }}"
                   class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                    <div class="font-semibold text-gray-800">
                        Laporan Simpanan
                    </div>
                    <div class="text-sm text-gray-600">
                        Rekap simpanan bulanan
                    </div>
                </a>

                <a href="{{ route('admin.laporan.pinjaman.index') }}"
                   class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                    <div class="font-semibold text-gray-800">
                        Laporan Pinjaman
                    </div>
                    <div class="text-sm text-gray-600">
                        Status & histori pinjaman
                    </div>
                </a>
            @endcan

        </div>
    </div>

</div>
@endsection
