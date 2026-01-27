@extends('layouts.main')

@section('title', 'Input Simpanan')

@section('content')
    <h1 class="text-2xl font-bold mb-4">
        Input Simpanan – {{ $anggota->nama }}
    </h1>

    @if ($errors->any())
        <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.simpanan.store', $anggota) }}"
          class="bg-white border rounded p-4 max-w-lg">

        @csrf

        <div class="mb-4">
            <label class="block text-sm mb-1">Jenis Simpanan</label>
            <select name="jenis_simpanan" class="w-full border rounded px-3 py-2">
                <option value="pokok">Pokok</option>
                <option value="wajib">Wajib</option>
                <option value="sukarela">Sukarela</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm mb-1">Jumlah</label>
            <input type="number"
                   name="jumlah"
                   class="w-full border rounded px-3 py-2"
                   placeholder="contoh: 500000">
            @error('jumlah')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror

        </div>

        <div class="mb-4">
            <label class="block text-sm mb-1">Keterangan</label>
            <textarea name="keterangan"
                      class="w-full border rounded px-3 py-2"
                      rows="2"></textarea>
        </div>

        <div class="flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Simpan
            </button>

            <a href="{{ route('admin.anggota.show', $anggota) }}"
               class="px-4 py-2 border rounded">
                Batal
            </a>
        </div>
    </form>
@endsection
