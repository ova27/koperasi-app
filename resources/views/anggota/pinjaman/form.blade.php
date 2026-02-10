@extends('layouts.main')

@section('title', 'Pengajuan Pinjaman')
@section('page-title', 'Pengajuan Pinjaman')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Pengajuan Pinjaman
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Ajukan pinjaman baru sesuai ketentuan koperasi
            </p>
        </div>

        <a href="{{ route('anggota.pinjaman.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2
                bg-white border border-gray-300 text-gray-700
                text-sm font-medium rounded-lg
                hover:bg-gray-50 transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ========================= --}}
        {{-- KOLOM KIRI : FORM --}}
        {{-- ========================= --}}
        <div class="lg:col-span-2">

            @if ($bolehAjukan)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="section-title">Form Pengajuan Pinjaman</h2>

                    <form method="POST" action="{{ route('anggota.pinjaman.store') }}">
                        @csrf

                        {{-- JUMLAH --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jumlah Pinjaman
                            </label>

                            <input
                                type="text"
                                id="input_jumlah_format"
                                class="w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500"
                                placeholder="Rp 0"
                                oninput="formatRupiah(this)"
                                value="{{ old('jumlah_diajukan') ? 'Rp ' . number_format(old('jumlah_diajukan'), 0, ',', '.') : '' }}"
                                required>

                            <input
                                type="hidden"
                                name="jumlah_diajukan"
                                id="jumlah_asli"
                                value="{{ old('jumlah_diajukan') }}">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tenor (Bulan)
                                </label>
                                <input
                                    type="number"
                                    name="tenor"
                                    min="1"
                                    max="24"
                                    value="{{ old('tenor', 1) }}"
                                    class="w-full border border-gray-300 rounded-md p-2"
                                    required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Rencana Bulan Pinjam
                                </label>
                                <input
                                    type="month"
                                    name="bulan_pinjam"
                                    min="{{ now()->format('Y-m') }}"
                                    class="w-full border border-gray-300 rounded-md p-2"
                                    required>
                            </div>
                        </div>

                        {{-- KETERANGAN --}}
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Keterangan
                            </label>
                            <textarea
                                name="keterangan"
                                rows="3"
                                class="w-full border border-gray-300 rounded-md p-2"
                                placeholder="Tujuan atau catatan pinjaman">{{ old('keterangan') }}</textarea>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition shadow-sm">
                                Ajukan Pinjaman
                            </button>

                            <a href="{{ route('anggota.pinjaman.index') }}"
                               class="text-sm text-gray-500 hover:underline">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            @else
                {{-- TIDAK BOLEH AJUKAN --}}
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl p-4">
                    <div class="font-medium mb-1">
                        Pengajuan Tidak Dapat Dilakukan
                    </div>

                    @if ($pengajuanAktif && $pengajuanAktif->status === 'diajukan')
                        Pengajuan pinjaman Anda sedang diproses.
                    @elseif ($pengajuanAktif && $pengajuanAktif->status === 'disetujui')
                        Pengajuan pinjaman Anda sudah disetujui dan menunggu pencairan.
                    @else
                        Anda belum memenuhi syarat untuk mengajukan pinjaman baru.
                    @endif
                </div>
            @endif

        </div>

        {{-- ========================= --}}
        {{-- KOLOM KANAN : INFO --}}
        {{-- ========================= --}}
        <div class="space-y-4">

            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm">
                <div class="font-semibold text-gray-800 mb-2">
                    Informasi Pinjaman Anda
                </div>

                <ul class="space-y-1 text-gray-700">
                    <li>
                        Pinjaman aktif:
                        <strong>{{ $ringkasan['aktif'] }}</strong>
                    </li>
                    <li>
                        Sisa pinjaman:
                        <strong>
                            Rp {{ number_format($ringkasan['sisa'], 0, ',', '.') }}
                        </strong>
                    </li>
                </ul>

                <div class="mt-3 text-xs text-gray-500 leading-relaxed">
                    • Top-up hanya boleh jika sisa pinjaman ≤ Rp 5.000.000<br>
                    • Total pengajuan maksimal Rp 20.000.000
                </div>
            </div>

        </div>
    </div>

    {{-- ========================= --}}
    {{-- RIWAYAT PENGAJUAN --}}
    {{-- ========================= --}}
    <div class="pt-6 border-t border-gray-200">

        <h2 class="section-title">
            Riwayat Pengajuan Pinjaman Saya
        </h2>

        @if ($riwayatPengajuan->isEmpty())
            <div class="text-sm text-gray-500 mt-2">
                Belum ada riwayat pengajuan pinjaman.
            </div>
        @else
            <div class="mt-4 bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left w-[18%]">Tanggal</th>
                            <th class="px-4 py-3 text-right w-[22%]">Jumlah</th>
                            <th class="px-4 py-3 text-center w-[12%]">Tenor</th>
                            <th class="px-4 py-3 text-center w-[18%]">Bulan Pinjam</th>
                            <th class="px-4 py-3 text-center w-[15%]">Status</th>
                            <th class="px-4 py-3 text-center w-[15%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayatPengajuan as $item)
                            @php
                                $badge = match($item->status) {
                                    'diajukan' => 'bg-yellow-100 text-yellow-700',
                                    'disetujui' => 'bg-blue-100 text-blue-700',
                                    'ditolak'  => 'bg-red-100 text-red-700',
                                    default    => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    {{ $item->tanggal_pengajuan->format('d M Y') }}
                                </td>

                                <td class="px-4 py-2 text-right font-medium">
                                    Rp {{ number_format($item->jumlah_diajukan, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-2 text-center">
                                    {{ $item->tenor }} bln
                                </td>

                                <td class="px-4 py-2 text-center">
                                    {{ \Carbon\Carbon::parse($item->bulan_pinjam)->translatedFormat('F Y') }}
                                </td>

                                <td class="px-4 py-2 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-4 py-2 text-center">
                                    @if ($item->status === 'diajukan')
                                        <div class="inline-flex items-center gap-4 text-sm">

                                            {{-- EDIT --}}
                                            <button
                                                type="button"
                                                onclick="openEditModal(
                                                    {{ $item->id }},
                                                    {{ $item->jumlah_diajukan }},
                                                    {{ $item->tenor }},
                                                    '{{ \Carbon\Carbon::parse($item->bulan_pinjam)->format('Y-m') }}',
                                                    '{{ addslashes($item->keterangan) }}'
                                                )"
                                                class="text-blue-600 hover:underline whitespace-nowrap">
                                                Edit
                                            </button>

                                            {{-- BATAL --}}
                                            <form
                                                action="{{ route('anggota.pinjaman.destroy', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Batalkan pengajuan ini?')"
                                                class="inline-flex"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="text-red-600 hover:underline whitespace-nowrap">
                                                    Batal
                                                </button>
                                            </form>

                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">
                                            Tidak tersedia
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>


</div>
@endsection

<script>
function formatRupiah(el) {
    let value = el.value.replace(/[^,\d]/g, '');
    let sisa = value.length % 3;
    let rupiah = value.substr(0, sisa);
    let ribuan = value.substr(sisa).match(/\d{3}/g);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    el.value = value ? 'Rp ' + rupiah : '';
    document.getElementById('jumlah_asli').value = value;
}
</script>
