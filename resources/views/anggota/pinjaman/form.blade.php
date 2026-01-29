@extends('layouts.main')

@section('content')
<div class="max-w-lg mx-auto">

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SUCCESS --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-xl font-bold mb-4">
        {{ $mode === 'edit' ? 'Edit Pengajuan Pinjaman' : 'Ajukan Pinjaman' }}
    </h2>

    <form method="POST" action="{{ route('anggota.pinjaman.store') }}">
        @csrf

        {{-- 🔑 MODE EDIT --}}
        @if ($mode === 'edit')
            <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">
        @endif

        <div class="mb-4">
            <label class="block mb-1 font-semibold">
                Jumlah Pinjaman
            </label>
            <input
                type="number"
                name="jumlah_diajukan"
                required
                class="w-full border rounded px-3 py-2"
                value="{{ old('jumlah_diajukan', $pengajuan->jumlah_diajukan ?? '') }}"
            >
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">
                Tujuan
            </label>
            <textarea
                name="tujuan"
                class="w-full border rounded px-3 py-2"
                rows="3"
            >{{ old('tujuan', $pengajuan->tujuan ?? '') }}</textarea>
        </div>

        <button
            type="submit"
            class="px-4 py-2 rounded text-white
                {{ $mode === 'edit' ? 'bg-yellow-500' : 'bg-blue-600' }}">
            {{ $mode === 'edit' ? 'Update Pengajuan' : 'Ajukan Pinjaman' }}
        </button>
    </form>

</div>
@endsection
