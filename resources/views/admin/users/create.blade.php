@extends('layouts.main')
@section('title', 'Tambah')
@section('content')
<div class="max-w-xl mx-auto">

    <h1 class="text-xl font-semibold mb-6">
        Tambah
    </h1>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4" autocomplete="off">
        @csrf

        {{-- NAMA --}}
        <div>
            <label class="text-sm text-gray-600">Nama</label>
            <input
                type="text"
                name="name"
                autocomplete="off"
                value=""
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        <div>
            <label class="text-sm text-gray-600">NIP</label>
            <input type="text" name="anggota_nip" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="text-sm text-gray-600">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="w-full border rounded px-3 py-2" required>
                <option value="">Pilih jenis kelamin</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>

        <div>
            <label class="text-sm text-gray-600">Jabatan</label>
            <input type="text" name="jabatan" class="w-full border rounded px-3 py-2" required>
        </div>

        {{-- EMAIL --}}
        <div>
            <label class="text-sm text-gray-600">Email</label>
            <input
                type="email"
                name="email"
                autocomplete="new-password"
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        {{-- PASSWORD --}}
        <div>
            <label class="text-sm text-gray-600">Password</label>
            <input
                type="password"
                name="password"
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        <div>
            <label class="text-sm text-gray-600">Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" value="{{ now()->toDateString() }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="text-sm text-gray-600">Nama Bank</label>
            <input type="text" name="nama_bank" class="w-full border rounded px-3 py-2" placeholder="Contoh: BANK BRI">
        </div>

        <div>
            <label class="text-sm text-gray-600">Nomor Rekening</label>
            <input type="text" name="nomor_rekening" class="w-full border rounded px-3 py-2" placeholder="Masukkan nomor rekening">
        </div>

        <div>
            <label class="text-sm text-gray-600">Nama Pemilik Rekening</label>
            <input type="text" name="nama_pemilik" class="w-full border rounded px-3 py-2" placeholder="Nama pemilik rekening">
        </div>

        {{-- ROLE --}}
        <div>
            <label class="text-sm text-gray-600">Role</label>
            @foreach($roles as $role)
                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        name="roles[]"
                        value="{{ $role->name }}"
                        data-create-role="{{ $role->name }}"
                    >
                    <span>{{ ucfirst($role->name) }}</span>
                </div>
            @endforeach
        </div>

        <div>
            <label class="text-sm text-gray-600">Status Anggota</label>
            <select name="status" class="w-full border rounded px-3 py-2" required>
                <option value="aktif" selected>Aktif</option>
                <option value="cuti">Cuti</option>
                <option value="tugas_belajar">Tugas Belajar</option>
                <option value="tidak_aktif">Tidak Aktif</option>
            </select>
        </div>

        <div class="flex gap-2 pt-4">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Simpan
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 bg-gray-200 rounded">
                Batal
            </a>
        </div>
    </form>

</div>
@endsection
