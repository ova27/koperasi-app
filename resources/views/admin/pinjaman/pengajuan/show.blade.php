@extends('layouts.main')

@section('title', 'Detail Pengajuan Pinjaman')

@section('content')
<h1 class="text-xl font-bold mb-4">Detail Pengajuan Pinjaman</h1>

<div class="mb-4">
    <p><strong>Nama Anggota:</strong> {{ $pengajuan->anggota->nama }}</p>
    <p><strong>Jumlah:</strong> Rp {{ number_format($pengajuan->jumlah_diajukan) }}</p>
    <p><strong>Tujuan:</strong> {{ $pengajuan->tujuan ?? '-' }}</p>
</div>

<form method="POST"
      action="{{ route('admin.pinjaman.pengajuan.setujui', $pengajuan) }}"
      class="inline">
    @csrf
    <button class="px-4 py-2 bg-green-600 text-white rounded">
        Setujui
    </button>
</form>

<button
    onclick="document.getElementById('form-tolak').classList.toggle('hidden')"
    class="px-4 py-2 bg-red-600 text-white rounded ml-2">
    Tolak
</button>

<form method="POST"
      action="{{ route('admin.pinjaman.pengajuan.tolak', $pengajuan) }}"
      id="form-tolak"
      class="mt-4 hidden">
    @csrf

    <textarea name="alasan"
              class="w-full border rounded p-2"
              placeholder="Alasan penolakan"
              required></textarea>

    <button class="mt-2 px-4 py-2 bg-red-700 text-white rounded">
        Konfirmasi Tolak
    </button>
</form>
@endsection