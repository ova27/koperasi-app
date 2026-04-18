@extends('layouts.main')

@section('title', 'Edit Rekening Koperasi')
@section('page-title', 'Master Data')

@section('content')
<div class="space-y-3">
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm max-w-2xl">
        <div class="mb-4">
            <h2 class="text-base font-semibold text-gray-800">Edit Rekening Koperasi</h2>
            <p class="text-sm text-gray-500">Perbarui data master rekening bank koperasi.</p>
        </div>

        <form method="POST" action="{{ route('admin.master.rekening-koperasi.update', $rekeningKoperasi) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm text-gray-600 mb-1">Nama Bank</label>
                <input type="text" name="nama" value="{{ old('nama', $rekeningKoperasi->nama) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                @error('nama')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">No Rekening</label>
                <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening', $rekeningKoperasi->nomor_rekening) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                @error('nomor_rekening')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Nama Pemilik</label>
                <input type="text" name="nama_pemilik" value="{{ old('nama_pemilik', $rekeningKoperasi->nama_pemilik) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                @error('nama_pemilik')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <div class="text-xs text-gray-500">Jenis</div>
                <div class="text-sm font-medium text-gray-800">Bank</div>
            </div>

            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="aktif" value="1" {{ old('aktif', $rekeningKoperasi->aktif) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Aktif</span>
            </label>

            <div class="pt-2 flex gap-2 justify-end">
                <a href="{{ route('admin.master.rekening-koperasi.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
