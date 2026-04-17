@extends('layouts.main')

@section('title', 'Form Pengajuan Pinjaman')
@section('page-title', 'Form Pengajuan Pinjaman')

@section('content')
<div class="space-y-7 -mt-1">
    @include('anggota.pinjaman._tabs')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ========================= --}}
        {{-- KOLOM KIRI : FORM --}}
        {{-- ========================= --}}
        <div class="lg:col-span-2">

            @if ($bolehAjukan)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

                    {{-- HEADER --}}
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800">Form Pengajuan Pinjaman</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Isi data berikut untuk mengajukan pinjaman baru</p>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- ERROR MESSAGE --}}
                        @if ($errors->any())
                            <div class="mb-5 p-4 rounded-lg bg-red-50 border border-red-200 flex gap-3">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <ul class="text-sm text-red-700 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('anggota.pinjaman.store') }}">
                            @csrf

                            {{-- JUMLAH PINJAMAN --}}
                            <div class="mb-5">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                    Jumlah Pinjaman
                                </label>
                                <input
                                    type="text"
                                    id="input_jumlah_format"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-gray-900 font-medium bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                                    placeholder="Rp 0"
                                    oninput="formatRupiah(this)"
                                    value="{{ old('jumlah_diajukan') ? 'Rp ' . number_format(old('jumlah_diajukan'), 0, ',', '.') : '' }}"
                                    required>
                                <input type="hidden" name="jumlah_diajukan" id="jumlah_asli" value="{{ old('jumlah_diajukan') }}">
                            </div>

                            {{-- TENOR & BULAN --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                        Tenor
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="number"
                                            name="tenor"
                                            min="1"
                                            max="20"
                                            value="{{ old('tenor', 1) }}"
                                            class="w-full border border-gray-200 rounded-lg px-4 py-3 pr-14 text-gray-900 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                                            required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">bulan</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                        Rencana Bulan Pinjam
                                    </label>
                                    <input
                                        type="month"
                                        name="bulan_pinjam"
                                        min="{{ now()->format('Y-m') }}"
                                        class="w-full border border-gray-200 rounded-lg px-4 py-3 text-gray-900 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                                        required>
                                </div>
                            </div>

                            {{-- KETERANGAN --}}
                            <div class="mb-6">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                    Keterangan <span class="normal-case font-normal text-gray-400">(opsional)</span>
                                </label>
                                <textarea
                                    name="tujuan"
                                    rows="3"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-gray-900 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none text-sm"
                                    placeholder="Jelaskan tujuan pinjaman Anda...">{{ old('tujuan') }}</textarea>
                            </div>

                            {{-- SUBMIT --}}
                            <button
                                type="submit"
                                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 hover:shadow-lg text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Ajukan Pinjaman
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- TIDAK BOLEH AJUKAN --}}
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-yellow-200 rounded-full flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-700" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-yellow-900 mb-2">
                                Pengisian Form Pengajuan Tidak Dapat Dilakukan
                            </h3>

                            @if ($pengajuanAktif && $pengajuanAktif->status === 'diajukan')
                                <p class="text-sm text-yellow-800">
                                    Pinjaman Anda sedang diproses. Mohon menunggu keputusan dari pengurus koperasi atau lakukan edit pengajuan jika diperlukan.
                                </p>
                            @elseif ($pengajuanAktif && $pengajuanAktif->status === 'disetujui')
                                <p class="text-sm text-yellow-800">
                                    Pengajuan pinjaman Anda sudah disetujui. Mohon menunggu untuk pencairan.
                                </p>
                            @else
                                <p class="text-sm text-yellow-800">
                                    Anda belum memenuhi syarat untuk mengajukan pinjaman baru. Sisa pinjaman aktif harus ≤ Rp 5.000.000 dan tidak ada pengajuan yang sedang diproses.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- ========================= --}}
        {{-- KOLOM KANAN : INFO --}}
        {{-- ========================= --}}
        <div class="space-y-4">

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 bg-blue-200 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="font-semibold text-blue-900">Informasi Pinjaman Anda</div>
                </div>

                <div class="space-y-3">
                    <div class="bg-white/60 rounded-lg p-3">
                        <div class="text-xs text-blue-700 mb-0.5">Pinjaman Aktif</div>
                        <div class="text-lg font-bold text-blue-900">{{ $ringkasan['aktif'] }}</div>
                    </div>
                    <div class="bg-white/60 rounded-lg p-3">
                        <div class="text-xs text-blue-700 mb-0.5">Sisa Pinjaman</div>
                        <div class="text-lg font-bold text-blue-900">Rp {{ number_format($ringkasan['sisa'], 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-blue-200 text-xs text-blue-800 leading-relaxed space-y-1">
                    <p class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> Top-up hanya boleh jika sisa pinjaman ≤ Rp 5.000.000</p>
                    <p class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> Total pengajuan maksimal Rp 20.000.000</p>
                    <p class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> Tenor maksimal 20 bulan</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ========================= --}}
    {{-- RIWAYAT PENGAJUAN --}}
    {{-- ========================= --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">

        <h2 class="text-base font-semibold text-gray-700 mb-6">
            Riwayat Pengajuan Pinjaman Saya
        </h2>

        @if ($riwayatPengajuan->isEmpty())
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="w-16 h-16 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-gray-400">Belum ada riwayat pengajuan pinjaman.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-blue-50 to-blue-100 border-b-2 border-blue-300">
                            <tr>
                                <th class="px-5 py-2.5 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">Tanggal</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-xs text-blue-900 uppercase tracking-widest">Jumlah</th>
                                <th class="px-5 py-2.5 text-center font-semibold text-xs text-blue-900 uppercase tracking-widest">Tenor</th>
                                <th class="px-5 py-2.5 text-center font-semibold text-xs text-blue-900 uppercase tracking-widest">Bulan Pinjam</th>
                                <th class="px-5 py-2.5 text-center font-semibold text-xs text-blue-900 uppercase tracking-widest">Status</th>
                                <th class="px-5 py-2.5 text-center font-semibold text-xs text-blue-900 uppercase tracking-widest">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($riwayatPengajuan as $item)
                                @php
                                    $badge = match($item->status) {
                                        'diajukan' => 'bg-yellow-100 text-yellow-700 border border-yellow-300 shadow-sm',
                                        'disetujui' => 'bg-blue-100 text-blue-700 border border-blue-300 shadow-sm',
                                        'ditolak'  => 'bg-red-100 text-red-700 border border-red-300 shadow-sm',
                                        'dicairkan' => 'bg-green-100 text-green-700 border border-green-300 shadow-sm',
                                        default    => 'bg-gray-100 text-gray-700 border border-gray-300 shadow-sm',
                                    };
                                @endphp

                                <tr class="@if($loop->odd) bg-white @else bg-blue-50 @endif hover:bg-blue-100 transition-all duration-300 group">
                                    <td class="px-5 py-2.5 text-gray-800 font-medium text-xs">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $item->tanggal_pengajuan->format('d M Y') }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-2.5 text-right font-bold text-gray-900 text-sm">
                                        <span class="text-blue-600">Rp {{ number_format($item->jumlah_diajukan, 0, ',', '.') }}</span>
                                    </td>

                                    <td class="px-5 py-2.5 text-center text-xs">
                                        {{ $item->tenor }} bln
                                    </td>

                                    <td class="px-5 py-2.5 text-center text-xs">
                                        {{ \Carbon\Carbon::parse($item->bulan_pinjam)->translatedFormat('F Y') }}
                                    </td>

                                    <td class="px-5 py-2.5 text-center">
                                        @if ($item->status === 'ditolak')
                                            <button 
                                                type="button" 
                                                onclick="showAlasanTolak('{{ addslashes($item->alasan_tolak ?? 'Tidak ada alasan') }}')"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badge }} hover:opacity-90 transition">
                                                {{ ucfirst($item->status) }}
                                            </button>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-2.5 text-center">
                                        @if ($item->status === 'diajukan')
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- EDIT --}}
                                                <button
                                                    type="button" onclick="openEditModal(
                                                    {{ $item->id }}, 
                                                    {{ $item->jumlah_diajukan }}, 
                                                    {{ $item->tenor }}, 
                                                    '{{ \Carbon\Carbon::parse($item->bulan_pinjam)->format('Y-m') }}', 
                                                    '{{ addslashes($item->tujuan) }}')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-all duration-200 hover:shadow-md">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </button>

                                                {{-- BATAL --}}
                                                <button
                                                    type="button"
                                                    onclick="handleDelete({{ $item->id }})"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-all duration-200 hover:shadow-md">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Batal
                                                </button>
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
            </div>
            
            {{-- PAGINATION --}}
            @if($riwayatPengajuan->hasPages())
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-4 pt-4 border-t border-gray-200">
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
<div id="modalEdit" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">

    <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6">

        <div class="flex items-center gap-3 mb-6">
            <div class="p-2.5 bg-blue-100 rounded-full">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Edit Pengajuan</h3>
        </div>

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
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah</label>
                <input type="text" id="edit_jumlah_format"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    oninput="formatRupiahEdit(this)">
                <input type="hidden" name="jumlah_diajukan" id="edit_jumlah_asli">
            </div>

            {{-- TENOR --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tenor (Bulan)</label>
                    <input type="number" name="tenor" id="edit_tenor"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                {{-- BULAN --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bulan Pinjam</label>
                    <input type="month" name="bulan_pinjam" id="edit_bulan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            </div>

            {{-- KETERANGAN --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                <textarea name="tujuan" id="edit_tujuan"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none" rows="3"></textarea>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Batal
                </button>

                <button type="submit"
                    class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold rounded-lg hover:shadow-md transition-all duration-200">
                    Simpan Perubahan
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
