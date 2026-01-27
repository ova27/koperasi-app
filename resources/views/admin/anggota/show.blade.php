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

        {{-- Proses Pensiun Mutasi --}}
        @if ($anggota->status === 'aktif')
            <a href="{{ route('admin.anggota.keluar.confirm', $anggota) }}"
            class="inline-block bg-red-500 text-white px-3 py-1 rounded text-sm">
                Proses Pensiun / Mutasi
            </a>
        @endif

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

        {{-- Ambil Simpanan --}}
        <div class="bg-white border rounded p-4 mb-6">
            <h2 class="font-semibold mb-3">Pengambilan Simpanan Sukarela</h2>

            @if ($errors->any())
                <div class="mb-3 text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.simpanan.ambil', $anggota) }}">
                @csrf

                <div class="mb-3">
                    <label class="block text-sm">Jumlah</label>
                    <input type="number" name="jumlah"
                        class="border rounded w-full px-2 py-1"
                        required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm">Keterangan</label>
                    <input type="text" name="keterangan"
                        class="border rounded w-full px-2 py-1"
                        required>
                </div>

                <button class="bg-red-600 text-white px-4 py-2 rounded">
                    Ambil Simpanan
                </button>
            </form>
        </div>
        
    </div>

    {{-- RIWAYAT SIMPANAN --}}
    <div class="bg-white border rounded p-4 mb-6">
        <h2 class="font-semibold mb-3">Riwayat Simpanan</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Tanggal</th>
                        <th class="px-3 py-2 text-left">Jenis</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                        <th class="px-3 py-2 text-left">Sumber</th>
                        <th class="px-3 py-2 text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($anggota->simpanans as $simpanan)
                        <tr class="border-b">
                            <td class="px-3 py-2">
                                {{ \Carbon\Carbon::parse($simpanan->tanggal)->format('d-m-Y') }}
                            </td>
                            <td class="px-3 py-2 capitalize">
                                {{ $simpanan->jenis_simpanan }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                Rp {{ number_format($simpanan->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2">
                                {{ $simpanan->sumber }}
                            </td>
                            <td class="px-3 py-2">
                                {{ $simpanan->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="px-3 py-4 text-center text-gray-500">
                                Belum ada transaksi simpanan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
