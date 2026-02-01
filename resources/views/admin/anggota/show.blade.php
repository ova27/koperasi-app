@extends('layouts.main')

@section('title', 'Detail Anggota')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $anggota->nama }}
            </h1>
            <div class="mt-1">
                <x-status-anggota :status="$anggota->status" />
            </div>
        </div>

        {{-- KEMBALI KE INDEX --}}
        <a href="{{ route('admin.anggota.index') }}"
           class="text-sm text-gray-600 hover:underline">
            ← Kembali ke Daftar Anggota
        </a>
    </div>

    {{-- ================= PROFIL ================= --}}
    <div class="bg-white border rounded-lg p-5">
        <h1 class="font-semibold mb-4">
            Profil
            <hr>
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div>
                <div class="text-gray-500">Nama</div>
                <div class="font-medium">{{ $anggota->nama }}</div>
            </div>

            <div>
                <div class="text-gray-500">NIP</div>
                <div class="font-medium">{{ $anggota->nip }}</div>
            </div>
            
            <div>
                <div class="text-gray-500">Email</div>
                <div class="font-medium">
                    {{ $anggota->user->email ?? '-' }}
                </div>
            </div>
            
            <div>
                <div class="text-gray-500">Jabatan</div>
                <div class="font-medium">{{ $anggota->jabatan }}</div>
            </div>

            <div>
                <div class="text-gray-500">Bank</div>
                <div class="font-medium">(Nama Bank)</div>
            </div>
            <div>
                <div class="text-gray-500">No Rekening</div>
                <div class="font-medium">(No Rekening)</div>
            </div>

            <div>
                <div class="text-gray-500">Tanggal Masuk</div>
                <div class="font-medium">
                    {{ \Carbon\Carbon::parse($anggota->tanggal_masuk)->format('d-m-Y') }}
                </div>
            </div>
        </div>
    </div>
     <div>
        @if($anggota->status !== 'aktif')
        <div class="bg-yellow-50 border border-yellow-300 p-4 rounded text-sm">
            Status anggota <b>{{ $anggota->status }}</b>.<br>
            Seluruh transaksi keuangan dibekukan.
        </div>
        @endif

    </div>

    {{-- ================= RINGKASAN SIMPANAN ================= --}}
    @php
        $pokok = $saldoSimpanan['pokok'] ?? 0;
        $wajib = $saldoSimpanan['wajib'] ?? 0;
        $sukarela = $saldoSimpanan['sukarela'] ?? 0;
        $total = $pokok + $wajib + $sukarela;
    @endphp

    <div class="bg-white border rounded-lg p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold">
                Ringkasan Simpanan
            </h2>

            <div class="flex gap-2">
                @can('manage simpanan anggota')
                    <a href="{{ route('admin.simpanan.create', $anggota) }}"
                       class="text-sm text-blue-600 hover:underline">
                        + Tambah Simpanan
                    </a>
                @endcan

                @if ($anggota->status === 'aktif')
                    @can('nonaktifkan anggota')
                        <a href="{{ route('admin.anggota.keluar.confirm', $anggota) }}"
                           class="text-sm bg-red-600 text-white px-3 py-1 rounded">
                            Proses Pensiun / Mutasi
                        </a>
                    @endcan
                @endif
            </div>
        </div>

        <table class="w-full text-sm">
            <tr>
                <td class="text-gray-600">Simpanan Pokok</td>
                <td class="text-right">
                    Rp {{ number_format($pokok, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td class="text-gray-600">Simpanan Wajib</td>
                <td class="text-right">
                    Rp {{ number_format($wajib, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td class="text-gray-600">Simpanan Sukarela</td>
                <td class="text-right">
                    Rp {{ number_format($sukarela, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="font-semibold border-t">
                <td>Total</td>
                <td class="text-right">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= AMBIL SIMPANAN ================= --}}
    @can('manage simpanan anggota')
    <div class="bg-white border rounded-lg p-5">
        <h2 class="font-semibold mb-4">
            Pengambilan Simpanan Sukarela
        </h2>

        @if ($errors->any())
            <div class="mb-3 text-sm text-red-600">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.simpanan.ambil', $anggota) }}"
              class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-600">Jumlah</label>
                <input type="number" name="jumlah"
                       class="border rounded w-full px-3 py-2"
                       required>
            </div>

            <div>
                <label class="block text-sm text-gray-600">Keterangan</label>
                <input type="text" name="keterangan"
                       class="border rounded w-full px-3 py-2"
                       required>
            </div>

            <div class="md:col-span-2">
                <button class="bg-red-600 text-white px-4 py-2 rounded">
                    Ambil Simpanan
                </button>
            </div>
        </form>
    </div>
    @endcan

    {{-- ================= RIWAYAT SIMPANAN ================= --}}
    <div class="bg-white border rounded-lg p-5">
        <h2 class="font-semibold mb-4">
            Riwayat Simpanan
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
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
                                class="px-4 py-6 text-center text-gray-500">
                                Belum ada transaksi simpanan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= RINGKASAN PINJAMAN ================= --}}
    <div class="bg-white border rounded-lg p-5">
        <h2 class="font-semibold mb-4">
            Ringkasan Pinjaman
        </h2>

        <table class="text-sm w-full">
            <tr>
                <td class="text-gray-600">Pinjaman Aktif</td>
                <td class="text-right">
                    {{ $ringkasanPinjaman['aktif'] }}
                </td>
            </tr>
            <tr>
                <td class="text-gray-600">Pinjaman Lunas</td>
                <td class="text-right">
                    {{ $ringkasanPinjaman['lunas'] }}
                </td>
            </tr>
            <tr class="font-semibold border-t">
                <td>Sisa Pinjaman</td>
                <td class="text-right">
                    Rp {{ number_format($ringkasanPinjaman['sisa'], 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

</div>
@endsection
