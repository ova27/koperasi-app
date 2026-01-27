@extends('layouts.main')

@section('title', 'Detail Anggota')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Detail Anggota</h1>

    {{-- PROFIL --}}
    <div class="bg-white border rounded p-4 mb-6">
        <h2 class="font-semibold mb-2">Profil</h2>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><strong>Nama:</strong> {{ $anggota->nama }}</div>
            <div><strong>Email:</strong> {{ $anggota->email }}</div>
            <div><strong>Status:</strong> {{ $anggota->status }}</div>
            <div><strong>Tanggal Masuk:</strong> {{ $anggota->tanggal_masuk }}</div>
        </div>
    </div>

    {{-- SIMPANAN --}}
    <div class="bg-white border rounded p-4 mb-6">
        <h2 class="font-semibold mb-3">Ringkasan Simpanan</h2>

        @php
            $pokok = $saldoSimpanan['pokok'] ?? 0;
            $wajib = $saldoSimpanan['wajib'] ?? 0;
            $sukarela = $saldoSimpanan['sukarela'] ?? 0;
            $total = $pokok + $wajib + $sukarela;
        @endphp

        <a href="{{ route('admin.simpanan.create', $anggota) }}"
        class="inline-block mt-3 text-sm text-blue-600 hover:underline">
            + Tambah Simpanan
        </a>

        <table class="text-sm w-full">
            <tr>
                <td>Simpanan Pokok</td>
                <td class="text-right">Rp {{ number_format($pokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Simpanan Wajib</td>
                <td class="text-right">Rp {{ number_format($wajib, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Simpanan Sukarela</td>
                <td class="text-right">Rp {{ number_format($sukarela, 0, ',', '.') }}</td>
            </tr>
            <tr class="font-semibold border-t">
                <td>Total</td>
                <td class="text-right">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>


    {{-- PINJAMAN --}}
    <div class="bg-white border rounded p-4">
        <h2 class="font-semibold mb-3">Ringkasan Pinjaman</h2>

        <table class="text-sm w-full">
            <tr>
                <td>Pinjaman Aktif</td>
                <td class="text-right">{{ $ringkasanPinjaman['aktif'] }}</td>
            </tr>
            <tr>
                <td>Pinjaman Lunas</td>
                <td class="text-right">{{ $ringkasanPinjaman['lunas'] }}</td>
            </tr>
            <tr class="font-semibold border-t">
                <td>Sisa Pinjaman</td>
                <td class="text-right">
                    Rp {{ number_format($ringkasanPinjaman['sisa'], 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

@endsection
