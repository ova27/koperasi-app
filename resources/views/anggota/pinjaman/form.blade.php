@extends('layouts.main')

@section('title', 'Pengajuan Pinjaman Saya')
@section('page-title', 'Pengajuan Pinjaman Saya')

@section('content')
<div class="space-y-10">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ========================= --}}
        {{-- KOLOM KIRI : FORM --}}
        {{-- ========================= --}}
        <div class="lg:col-span-2">

            @if ($bolehAjukan)
                <div class="bg-white border border-gray-200 border-l-4 border-l-blue-500 rounded-xl shadow-sm p-4">
                    <h2 class="section-title font-black">Form Pengajuan Pinjaman</h2>
                    <hr class="my-2 mb-6 border-gray-200">
                    
                    {{-- ERROR MESSAGE --}}
                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
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
                                    max="20"
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
                                name="tujuan"
                                rows="2"
                                class="w-full border border-gray-300 rounded-md p-2"
                                placeholder="Keterangan">{{ old('tujuan') }}</textarea>
                        </div>

                        <div class="mt-6">
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition shadow-sm">
                                <span class="font-sm"> Ajukan Pinjaman </span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                {{-- TIDAK BOLEH AJUKAN --}}
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl p-4">
                    <div class="font-medium mb-2">
                        Pengisian Form Pengajuan Tidak Dapat Dilakukan
                    </div>

                    @if ($pengajuanAktif && $pengajuanAktif->status === 'diajukan')
                        <span
                         class="block text-sm">
                            Pinjaman Anda sedang diproses. Mohon menunggu keputusan dari pengurus koperasi/lakukan edit pengajuan jika diperlukan.
                        </span>
                    @elseif ($pengajuanAktif && $pengajuanAktif->status === 'disetujui')
                        <span
                         class="block text-sm">
                            Pengajuan pinjaman Anda sudah disetujui. Mohon menunggu untuk pencairan.
                        </span>
                    @else
                        <span
                         class="block text-sm">
                            Anda belum memenuhi syarat untuk mengajukan pinjaman baru. Sisa pinjaman aktif harus ≤ Rp 5.000.000 dan tidak ada pengajuan yang sedang diproses.
                        </span>
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
                    • Total pengajuan maksimal Rp 20.000.000<br>
                    • Tenor maksimal 20 bulan
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
                            <th class="px-4 py-3 text-right w-[18%]">Jumlah</th>
                            <th class="px-4 py-3 text-center w-[15%]">Tenor</th>
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
                                    'dicairkan' => 'bg-green-100 text-green-700',
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

                                <td class="px-4 py-3 text-center align-middle">
                                    {{ $item->tenor }} bln
                                </td>

                                <td class="px-4 py-3 text-center align-middle">
                                    {{ \Carbon\Carbon::parse($item->bulan_pinjam)->translatedFormat('F Y') }}
                                </td>

                                <td class="px-4 py-3 text-center align-middle">
                                    @if ($item->status === 'ditolak')
                                        <button 
                                            type="button" 
                                            onclick="showAlasanTolak('{{ addslashes($item->alasan_tolak ?? 'Tidak ada alasan') }}')"
                                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }} hover:opacity-80 transition">
                                            {{ ucfirst($item->status) }}
                                        </button>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center align-middle">
                                    @if ($item->status === 'diajukan')
                                        <div class="flex items-center justify-center gap-3">
                                            {{-- EDIT --}}
                                            <div class="relative group">
                                                <button
                                                    type="button" onclick="openEditModal(
                                                    {{ $item->id }}, 
                                                    {{ $item->jumlah_diajukan }}, 
                                                    {{ $item->tenor }}, 
                                                    '{{ \Carbon\Carbon::parse($item->bulan_pinjam)->format('Y-m') }}', 
                                                    '{{ addslashes($item->tujuan) }}')"
                                                    class="w-9 h-9 flex items-center justify-center
                                                        rounded-lg bg-blue-50 text-blue-600
                                                        hover:bg-blue-100 transition
                                                        text-base leading-none">
                                                    ✏️
                                                </button>

                                                <span class="absolute -top-8 left-1/2 -translate-x-1/2
                                                    bg-gray-800 text-white text-[10px] px-2 py-1 rounded
                                                    opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                                                    Edit
                                                </span>
                                            </div>

                                            {{-- BATAL --}}
                                            <div class="relative group">
                                                <button
                                                    type="submit"
                                                    onclick="handleDelete({{ $item->id }})"
                                                    class="w-9 h-9 flex items-center justify-center
                                                        rounded-lg bg-red-50 text-red-600
                                                        hover:bg-red-100 transition
                                                        text-base leading-none">
                                                    🗑️
                                                </button>

                                                <span class="absolute -top-8 left-1/2 -translate-x-1/2
                                                    bg-gray-800 text-white text-[10px] px-2 py-1 rounded
                                                    opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                                                    Batal
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            @if($riwayatPengajuan->hasPages())
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-2 px-2">
                    <p class="text-sm text-gray-600">
                        Menampilkan
                        <span class="font-semibold text-gray-900">{{ $riwayatPengajuan->firstItem() ?? 0 }}</span>
                        sampai
                        <span class="font-semibold text-gray-900">{{ $riwayatPengajuan->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-semibold text-gray-900">{{ $riwayatPengajuan->total() }}</span>
                        data
                    </p>

                    <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                        {{ $riwayatPengajuan->links('vendor.pagination.custom') }}
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<form id="formDelete" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- MODAL EDIT -->
<div id="modalEdit" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">

        <h3 class="text-lg font-bold mb-4">Edit Pengajuan</h3>

        {{-- ERROR MESSAGE --}}
        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')

            {{-- JUMLAH --}}
            <div class="mb-3">
                <label class="text-sm">Jumlah</label>
                <input type="text" id="edit_jumlah_format"
                    class="w-full border rounded p-2"
                    oninput="formatRupiahEdit(this)">
                <input type="hidden" name="jumlah_diajukan" id="edit_jumlah_asli">
            </div>

            {{-- TENOR --}}
            <div class="mb-3">
                <label class="text-sm">Tenor</label>
                <input type="number" name="tenor" id="edit_tenor"
                    class="w-full border rounded p-2">
            </div>

            {{-- BULAN --}}
            <div class="mb-3">
                <label class="text-sm">Bulan Pinjam</label>
                <input type="month" name="bulan_pinjam" id="edit_bulan"
                    class="w-full border rounded p-2">
            </div>

            {{-- KETERANGAN --}}
            <div class="mb-3">
                <label class="text-sm">Catatan</label>
                <textarea name="tujuan" id="edit_tujuan"
                    class="w-full border rounded p-2"></textarea>
            </div>

            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 text-sm text-gray-500">
                    Batal
                </button>

                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

<script>
function handleDelete(id) {
    if (!confirm('Batalkan pengajuan ini?')) return;

    const form = document.getElementById('formDelete');
    form.action = `/anggota/pinjaman/${id}`;
    form.submit();
}

function formatRupiah(el) { 
    let value = el.value.replace(/[^,\d]/g, '');
    let sisa = value.length % 3; let rupiah = value.substr(0, sisa); 
    let ribuan = value.substr(sisa).match(/\d{3}/g); 
    if (ribuan) { 
        let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.');
    } el.value = value ? 'Rp ' + rupiah : ''; 
    document.getElementById('jumlah_asli').value = value; 
}
function openEditModal(id, jumlah_diajukan, tenor, bulan_pinjam, tujuan) {

    // set action form
    document.getElementById('formEdit').action = `/anggota/pinjaman/${id}`;

    // isi field
    document.getElementById('edit_jumlah_format').value = 'Rp ' + jumlah_diajukan.toLocaleString('id-ID');
    document.getElementById('edit_jumlah_asli').value = jumlah_diajukan;

    document.getElementById('edit_tenor').value = tenor;
    document.getElementById('edit_bulan').value = bulan_pinjam;
    document.getElementById('edit_tujuan').value = tujuan;

    // tampilkan modal
    document.getElementById('modalEdit').classList.remove('hidden');
    document.getElementById('modalEdit').classList.add('flex');
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function formatRupiahEdit(el) {
    let value = el.value.replace(/[^,\d]/g, '');
    let sisa = value.length % 3;
    let rupiah = value.substr(0, sisa);
    let ribuan = value.substr(sisa).match(/\d{3}/g);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    el.value = value ? 'Rp ' + rupiah : '';
    document.getElementById('edit_jumlah_asli').value = value;
}
</script>

@if (session('open_edit_modal'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        openEditModal(
            @json(session('edit_id')),
            @json(old('jumlah_diajukan', 0)),
            @json(old('tenor', 1)),
            @json(old('bulan_pinjam')),
            @json(old('tujuan'))
        );
    });
</script>
@endif

<script>
    function showAlasanTolak(alasan) {
        alert('Alasan Ditolak:\n' + alasan);
    }
</script>