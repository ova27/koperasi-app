@extends('layouts.main')

@section('title', 'Input Cicilan')

@section('content')
<h1 class="text-xl font-bold mb-4">
    Cicilan Pinjaman
</h1>

<div class="mb-4">
    <p><strong>Nama:</strong> {{ $pinjaman->anggota->nama }}</p>
    <p><strong>Sisa Pinjaman:</strong> Rp {{ number_format($pinjaman->sisa_pinjaman) }}</p>
</div>

<form method="POST" action="{{ route('admin.pinjaman.cicil.store', $pinjaman) }}">
    @csrf

    <div class="mb-4">
        <label class="block mb-1 font-semibold">
            Jumlah Cicilan
        </label>
        <input
            type="number"
            name="jumlah"
            class="w-full border rounded px-3 py-2"
            required
        >
    </div>

    <div class="mb-4">
        <label class="block mb-1 font-semibold">
            Keterangan
        </label>
        <input
            type="text"
            name="keterangan"
            class="w-full border rounded px-3 py-2"
            placeholder="Opsional"
        >
    </div>

    <button class="px-4 py-2 bg-blue-600 text-white rounded">
        Simpan Cicilan
    </button>
</form>
@endsection