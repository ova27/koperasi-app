@extends('layouts.main')

@section('title', 'Edit Pinjaman')
@section('page-title', 'Edit Pinjaman - ' . $pinjaman->anggota->nama)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- BACK LINK --}}
    <a href="{{ route('admin.pinjaman.data-anggota.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>

    {{-- FORM CARD --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">

        <h2 class="text-xl font-bold text-gray-900">Edit Pinjaman</h2>

        {{-- DETAIL PINJAMAN --}}
        <ul class="text-sm text-gray-700 space-y-1 bg-gray-50 p-4 rounded-lg">
            <li><strong>Anggota:</strong> {{ $pinjaman->anggota->nama }}</li>
            <li><strong>Nomor Anggota:</strong> {{ $pinjaman->anggota->nomor_anggota }}</li>
            <li><strong>Tanggal Pinjam:</strong> {{ $pinjaman->tanggal_pinjam->format('d/m/Y') }}</li>
            <li><strong>Total Pinjaman:</strong> Rp {{ number_format($pinjaman->jumlah_pinjaman,0,',','.') }}</li>
            <li><strong>Sisa Pinjaman:</strong> <span class="{{ $pinjaman->sisa_pinjaman>0?'text-red-600':'text-green-600' }}">Rp {{ number_format($pinjaman->sisa_pinjaman,0,',','.') }}</span></li>
            <li><strong>Tenor:</strong> {{ $pinjaman->tenor ?? '-' }} bulan</li>
            <li><strong>Status:</strong> 
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pinjaman->status==='aktif'?'bg-blue-100 text-blue-800':'bg-green-100 text-green-800' }}">
                    {{ ucfirst($pinjaman->status) }}
                </span>
            </li>
        </ul>

        {{-- FORM EDIT --}}
        <form method="POST" action="{{ route('admin.pinjaman.data-anggota.update', $pinjaman) }}" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- TENOR --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tenor (bulan)</label>
                <input type="number" name="tenor" min="1" max="20" placeholder="Masukkan Perubahan Ternor"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('tenor')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ESTIMASI CICILAN --}}
            <div id="estimasi-container" class="text-sm text-gray-700 hidden">
                <div>Estimasi Cicilan Per Bulan:</div>
                <div class="text-lg font-bold text-blue-600">
                    Rp <span id="estimasi-cicilan">0</span>
                </div>
            </div>

            {{-- KETERANGAN --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Tambahkan keterangan (opsional)">{{ old('keterangan', $pinjaman->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BUTTONS --}}
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.pinjaman.data-anggota.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
<script>
    const tenorInput = document.querySelector('input[name="tenor"]');
    const estimasiContainer = document.getElementById('estimasi-container');
    const estimasiCicilanSpan = document.getElementById('estimasi-cicilan');
    const sisaPinjaman = {{ $pinjaman->sisa_pinjaman }};

    function updateEstimasiCicilan() {
        const tenor = parseInt(tenorInput.value) || 0;

        if (tenor > 0) {
            const estimasi = Math.ceil(sisaPinjaman / tenor);
            estimasiCicilanSpan.textContent = new Intl.NumberFormat('id-ID').format(estimasi);
            estimasiContainer.classList.remove('hidden');
        } else {
            estimasiContainer.classList.add('hidden');
            estimasiCicilanSpan.textContent = '0';
        }
    }

    tenorInput.addEventListener('input', updateEstimasiCicilan);
    tenorInput.addEventListener('change', updateEstimasiCicilan);
</script>
@endsection