@extends('layouts.main')

@section('title', 'Simpanan Bulanan & Manual')

@section('content')
<div class="container">

    <h1 class="text-lg font-semibold mb-4">
        Simpanan Bulanan & Manual
    </h1>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- ================= --}}
    {{-- A. SIMPANAN WAJIB --}}
    {{-- ================= --}}
    <div class="mb-6 p-4 border rounded">

        <h4 class="font-semibold mb-2">
            Simpanan Wajib Bulanan
        </h4>

        <p class="text-sm text-gray-500 mb-3">
            Bulan aktif: <strong>{{ $bulan }}</strong>
        </p>

        @if ($sudahGenerateWajib)
            <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded">
                Simpanan wajib bulan ini sudah digenerate.
            </div>
        @else
            <div class="p-3 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded mb-3">
                Simpanan wajib bulan ini belum digenerate.
            </div>

            <form action="{{ route('admin.simpanan.generate-wajib') }}" method="POST">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <button
                    type="submit"
                    class="px-4 py-2 border rounded hover:bg-gray-100"
                >
                    Generate Simpanan Wajib Bulan Ini
                </button>
            </form>
        @endif
    </div>

    {{-- ======================== --}}
    {{-- B. INPUT SIMPANAN MANUAL --}}
    {{-- ======================== --}}
    <div class="p-4 border rounded">

        <h4 class="font-semibold mb-3">
            Input Simpanan Manual
        </h4>

        <p class="text-sm text-gray-500 mb-4">
            Digunakan untuk:
            <br>• Simpanan sukarela
            <br>• Simpanan wajib anggota baru / terlewat
            <br>• Simpanan pokok (1x di awal keanggotaan)
        </p>

        <form method="POST" action="{{ route('admin.simpanan.store-manual') }}">
            @csrf

            {{-- ANGGOTA --}}
            <div class="mb-3">
                <label class="text-xs text-gray-500">Anggota</label>
                <select name="anggota_id" class="w-full border rounded px-2 py-1" required>
                    <option value="" disabled selected>Pilih Anggota</option>
                    @foreach ($anggotas as $anggota)
                        <option value="{{ $anggota->id }}">
                            {{ $anggota->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- JENIS --}}
            <div class="mb-3">
                <label class="text-xs text-gray-500">Jenis Simpanan</label>
                <select name="jenis_simpanan" class="w-full border rounded px-2 py-1">
                    <option value="" disabled selected>Pilih Jenis Simpanan</option>
                    <option value="pokok">Pokok</option>
                    <option value="wajib">Wajib</option>
                    <option value="sukarela">Sukarela</option>
                </select>
            </div>

            {{-- JUMLAH --}}
            <div class="mb-3">
                <label class="text-xs text-gray-500">Jumlah</label>
                <input
                    type="number"
                    name="jumlah"
                    class="w-full border rounded px-2 py-1"
                    value="{{ old('jumlah') }}"
                >
            </div>

            {{-- KETERANGAN --}}
            <div class="mb-4">
                <label class="text-xs text-gray-500">Keterangan (opsional)</label>
                <input
                    type="text"
                    name="keterangan"
                    class="w-full border rounded px-2 py-1"
                    value="{{ old('keterangan') }}"
                >
            </div>

            <button
                type="submit"
                class="px-4 py-2 border rounded hover:bg-gray-100"
            >
                Simpan Manual
            </button>
        </form>

    </div>
</div>
@endsection