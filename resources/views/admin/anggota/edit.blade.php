@extends('layouts.main')

@section('title', 'Edit Anggota')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">
                Edit Data Anggota
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Perubahan akan langsung memengaruhi data keanggotaan
            </p>
        </div>

        {{-- KEMBALI KE INDEX --}}
        <a href="{{ route('admin.anggota.index') }}"
           class="text-sm text-gray-600 hover:underline">
            ← Kembali ke Daftar Anggota
        </a>
    </div>

    {{-- ================= FORM ================= --}}
    <div class="bg-white border rounded-xl p-6">
        <form id="form-edit-anggota"
              method="POST"
              action="{{ route('admin.anggota.update', $anggota) }}"
              class="space-y-4">
            @csrf
            @method('PUT')

            {{-- NAMA (READ ONLY) --}}
            <div>
                <label class="block text-sm text-gray-600">Nama</label>
                <input disabled
                       value="{{ $anggota->nama }}"
                       class="w-full border rounded px-3 py-2 bg-gray-100">
            </div>

            {{-- NIP --}}
            <div>
                <label class="block text-sm text-gray-600">NIP</label>
                <input type="text"
                       name="nip"
                       value="{{ old('nip', $anggota->nip) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- JENIS KELAMIN --}}
            <div>
                <label class="block text-sm text-gray-600">Jenis Kelamin</label>
                <select name="jenis_kelamin"
                        class="w-full border rounded px-3 py-2"
                        required>
                    <option value="L" @selected($anggota->jenis_kelamin === 'L')>
                        Laki-laki
                    </option>
                    <option value="P" @selected($anggota->jenis_kelamin === 'P')>
                        Perempuan
                    </option>
                </select>
            </div>

            {{-- JABATAN --}}
            <div>
                <label class="block text-sm text-gray-600">Jabatan</label>
                <input type="text"
                       name="jabatan"
                       value="{{ old('jabatan', $anggota->jabatan) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- STATUS (HIGH RISK) --}}
            @can('nonaktifkan anggota')
                <div class="pt-4 border-t">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Status Anggota
                    </label>

                    <select name="status"
                            class="w-full border rounded px-3 py-2"
                            required>
                        <option value="aktif" @selected($anggota->status === 'aktif')>
                            Aktif
                        </option>
                        <option value="cuti" @selected($anggota->status === 'cuti')>
                            Cuti
                        </option>
                        <option value="tugas_belajar" @selected($anggota->status === 'tugas_belajar')>
                            Tugas Belajar
                        </option>
                        <option value="tidak_aktif" @selected($anggota->status === 'tidak_aktif')>
                            Pensiun / Mutasi
                        </option>
                    </select>

                    <p class="text-xs text-red-600 mt-1">
                        Mengubah status dapat memengaruhi hak transaksi dan akses login anggota.
                    </p>
                </div>
            @endcan

            {{-- ================= FOOTER ================= --}}
            <div class="flex justify-between items-center pt-6 border-t">
                <a href="{{ route('admin.anggota.index') }}"
                   class="text-sm text-gray-600 hover:underline">
                    ← Batal & Kembali
                </a>

                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
