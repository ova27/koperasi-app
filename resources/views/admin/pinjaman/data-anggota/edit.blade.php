@extends('layouts.main')

@section('title', 'Edit Pinjaman')
@section('page-title', 'Edit Pinjaman - ' . $pinjaman->anggota->nama)

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- BACK LINK --}}
    <div class="mb-6">
        <a
            href="{{ route('admin.pinjaman.data-anggota.index') }}"
            class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>
    </div>

    {{-- FORM CARD --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Pinjaman</h2>

        {{-- DETAIL PINJAMAN (READ-ONLY) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg mb-6">
            <div>
                <div class="text-xs text-gray-500 uppercase">Anggota</div>
                <div class="font-semibold text-gray-900">{{ $pinjaman->anggota->nama }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase">Nomor Anggota</div>
                <div class="font-semibold text-gray-900">{{ $pinjaman->anggota->nomor_anggota }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase">Tanggal Pinjam</div>
                <div class="font-semibold text-gray-900">{{ $pinjaman->tanggal_pinjam->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase">Total Pinjaman</div>
                <div class="font-semibold text-gray-900">
                    Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase">Sisa Pinjaman</div>
                <div class="font-semibold {{ $pinjaman->sisa_pinjaman > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase">Tenor</div>
                <div class="font-semibold text-gray-900">{{ $pinjaman->tenor ?? '-' }} bulan</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase">Status</div>
                <div class="font-semibold">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pinjaman->status === 'aktif' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($pinjaman->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- FORM EDIT --}}
        <form method="POST" action="{{ route('admin.pinjaman.data-anggota.update', $pinjaman) }}">
            @csrf
            @method('PUT')

            {{-- TENOR FIELD --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tenor (bulan)
                </label>
                <input
                    type="number"
                    name="tenor"
                    id="tenor"
                    min="1"
                    max="60"
                    value="{{ old('tenor', $pinjaman->tenor) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Masukkan tenor pinjaman">
                @error('tenor')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- CICILAN INFO --}}
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="text-sm text-gray-600 mb-2">Estimasi Cicilan Per Bulan:</div>
                <div class="text-2xl font-bold text-blue-600">
                    Rp <span id="estimasi-cicilan">{{ $pinjaman->tenor > 0 ? number_format((int) ceil($pinjaman->sisa_pinjaman / $pinjaman->tenor), 0, ',', '.') : '0' }}</span>
                </div>
                <div class="text-xs text-gray-500 mt-2">
                    (Sisa Pinjaman: Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }} ÷ Tenor)
                </div>
            </div>

            {{-- KETERANGAN FIELD --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea
                    name="keterangan"
                    rows="4"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Tambahkan keterangan pinjaman (opsional)">{{ old('keterangan', $pinjaman->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BUTTONS --}}
            <div class="flex gap-3 justify-end">
                <a
                    href="{{ route('admin.pinjaman.data-anggota.index') }}"
                    class="px-6 py-2 bg-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-400 transition">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    // Hitung cicilan otomatis ketika tenor berubah
    const tenorInput = document.getElementById('tenor');
    const estimasiCicilanSpan = document.getElementById('estimasi-cicilan');
    const sisaPinjaman = {{ $pinjaman->sisa_pinjaman }};

    tenorInput.addEventListener('change', function() {
        const tenor = parseInt(this.value) || 0;
        
        if (tenor > 0) {
            const estimasiCicilan = Math.ceil(sisaPinjaman / tenor);
            estimasiCicilanSpan.textContent = new Intl.NumberFormat('id-ID').format(estimasiCicilan);
        } else {
            estimasiCicilanSpan.textContent = '0';
        }
    });

    tenorInput.addEventListener('input', function() {
        const tenor = parseInt(this.value) || 0;
        
        if (tenor > 0) {
            const estimasiCicilan = Math.ceil(sisaPinjaman / tenor);
            estimasiCicilanSpan.textContent = new Intl.NumberFormat('id-ID').format(estimasiCicilan);
        } else {
            estimasiCicilanSpan.textContent = '0';
        }
    });
</script>
@endsection
