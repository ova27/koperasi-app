@extends('layouts.main')

@section('title', 'Transaksi Pinjaman Anggota')
@section('page-title', 'Transaksi Pinjaman Anggota')
@section('content')
<div class="space-y-6">
<div x-data="{ tab: 'aktif' }">
    {{-- ================= TAB NAVIGATION ================= --}}
    <div class="border-b flex gap-6 text-sm font-medium mb-4">

        <button @click="tab = 'aktif'"
            :class="tab === 'aktif' 
                ? 'border-b-2 border-black text-black font-medium' 
                : 'text-gray-400'"
            class="pb-2">
            Pinjaman Aktif
        </button>

        <button @click="tab = 'lunas'"
            :class="tab === 'lunas' 
                ? 'border-b-2 border-black text-black font-medium' 
                : 'text-gray-400'"
            class="pb-2">
            Pinjaman Lunas
        </button>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div id="flash-message" class="px-4 py-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center justify-between mb-4">
            <span>{{ session('success') }}</span>
            <button type="button" 
                    onclick="closeFlashMessage()"
                    class="text-green-700 hover:text-green-900 ml-4">
                ×
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="flash-message" class="px-4 py-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center justify-between mb-4">
            <span>{{ session('error') }}</span>
            <button type="button" 
                    onclick="closeFlashMessage()"
                    class="text-red-700 hover:text-red-900 ml-4">
                ×
            </button>
        </div>
    @endif   

    {{-- TABLE PINJAMAN AKTIF --}}
    <div x-show="tab === 'aktif'" 
        x-transition.opacity.duration.200ms>
    
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-50 to-blue-100 text-blue-800">
                        <th class="p-3 text-left font-semibold tracking-wide bg-orange-50 text-orange-700">No</th>
                        <th class="p-3 text-left font-semibold tracking-wide bg-orange-50 text-orange-700">Anggota</th>
                        <th class="p-3 text-left font-semibold tracking-wide bg-orange-50 text-orange-700">Tanggal Pinjam</th>
                        <th class="p-3 text-right font-semibold tracking-wide bg-orange-50 text-orange-700">Jumlah Pinjaman</th>
                        <th class="p-3 text-right font-semibold tracking-wide bg-orange-50 text-orange-700">Sisa Pinjaman</th>
                        <th class="p-3 text-center font-semibold tracking-wide bg-orange-50 text-orange-700">Tenor</th>
                        <th class="p-3 text-center font-semibold tracking-wide bg-orange-50 text-orange-700">Status</th>
                        <th class="p-3 text-center font-semibold tracking-wide bg-orange-50 text-orange-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pinjamansAktif as $pinjaman)
                        <tr class="transition-all duration-200 hover:scale-[1.01] hover:shadow-lg bg-white even:bg-orange-50 rounded-xl">
                            <td class="p-3 rounded-l-xl align-middle">{{ $loop->iteration }}</td>
                            <td class="p-3 align-middle">
                                <div class="font-medium text-gray-900">
                                    {{ $pinjaman->anggota->nama ?? '-' }}
                                </div>
                            </td>
                            <td class="p-3 text-gray-500 align-middle">
                                {{ $pinjaman->tanggal_pinjam->format('d/m/Y') }}
                            </td>
                            <td class="p-3 text-right font-bold text-blue-700 align-middle">
                                Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-right font-bold align-middle {{ $pinjaman->sisa_pinjaman > 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-center text-gray-700 align-middle">
                                {{ $pinjaman->tenor ?? '-' }} bulan
                            </td>
                            <td class="p-3 text-center align-middle">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $pinjaman->status === 'aktif' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-800' }} shadow-sm">
                                    {{ ucfirst($pinjaman->status) }}
                                </span>
                            </td>
                            <td class="p-3 rounded-r-xl text-center space-x-1 align-middle">
                                {{-- EDIT (Ketua & Bendahara) - hanya untuk pinjaman aktif --}}
                                @can('edit pinjaman')
                                    @if($pinjaman->status === 'aktif')
                                        <button 
                                            type="button"
                                            onclick='openEditModal(@json($pinjaman))'
                                            title="Edit"
                                            class="px-3 py-1.5 text-xs font-semibold text-white bg-gray-600 rounded-lg hover:bg-gray-700 transition shadow-md">
                                            Ubah
                                        </button>
                                    @endif
                                @endcan

                                {{-- CICIL & PELUNASAN CEPAT (Bendahara Only) --}}
                                @can('manage cicilan pinjaman')
                                    @if($pinjaman->status === 'aktif')
                                        <button onclick='openCicilanModal(@json($pinjaman))'
                                            title="Input Cicilan"
                                            class="px-3 py-1.5 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition shadow-md"
                                            >
                                            Cicil
                                        </button>

                                        <button
                                            onclick="confirmPelunasan({{ $pinjaman->id }}, '{{ addslashes($pinjaman->anggota->nama) }}', {{ $pinjaman->sisa_pinjaman }})"
                                            title="Pelunasan Cepat"
                                            class="px-3 py-1.5 text-xs font-semibold text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 transition shadow-md">
                                            Pelunasan
                                        </button>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-400 bg-white rounded-xl">
                                Tidak ada pinjaman aktif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION PINJAMAN AKTIF --}}
        @if($pinjamansAktif->hasPages())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-2 mt-2">
                <p class="text-sm text-gray-600">
                    Menampilkan
                    <span class="font-semibold text-gray-900">{{ $pinjamansAktif->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-semibold text-gray-900">{{ $pinjamansAktif->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold text-gray-900">{{ $pinjamansAktif->total() }}</span>
                    data
                </p>
                <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                    {{ $pinjamansAktif->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>

    {{-- TABLE PINJAMAN LUNAS --}}
    <div x-show="tab === 'lunas'" 
        x-transition.opacity.duration.200ms>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto mb-2">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                        <th class="p-3 border-b text-center">No</th>
                        <th class="p-3 border-b text-left">Anggota</th>
                        <th class="p-3 border-b text-left">Tanggal Pinjam</th>
                        <th class="p-3 border-b text-left">Tanggal Lunas</th>
                        <th class="p-3 border-b text-right">Jumlah Pinjaman</th>
                        <th class="p-3 border-b text-center">Tenor</th>
                        <th class="p-3 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($pinjamansLunas as $pinjaman)
                        {{-- ========================= --}}
                        {{-- BARIS PINJAMAN LUNAS --}}
                        {{-- ========================= --}}
                        <tr class="border-t hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-3 py-1 border-b text-center">{{ $loop->iteration }}</td>
                            <td class="px-3 py-1 border-b">
                                <div class="font-medium text-gray-800">
                                    {{ $pinjaman->anggota->nama ?? '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-1 border-b text-gray-500">
                                {{ $pinjaman->tanggal_pinjam->format('d M Y') }}
                            </td>

                            <td class="px-3 py-1 border-b text-gray-500">
                                {{ $pinjaman->updated_at->translatedFormat('d M Y H:i') }}
                            </td>

                            <td class="px-3 py-1 border-b text-right font-semibold text-gray-800">
                                Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                            </td>

                            <td class="px-3 py-1 border-b text-center text-gray-700">
                                {{ $pinjaman->tenor ?? '-' }} bulan
                            </td>

                            <td class="px-3 py-1 border-b text-center">
                                @if($pinjaman->transaksi->isNotEmpty())
                                    <button
                                        onclick="toggleCicilan({{ $pinjaman->id }})"
                                        id="btn-lihat-{{ $pinjaman->id }}"
                                        class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium px-3 py-1.5 rounded-md transition-all duration-200 inline-flex items-center gap-1 transform hover:scale-105">
                                        <svg class="w-3 h-3 transition-transform duration-200" id="icon-lihat-{{ $pinjaman->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        Lihat
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 italic">-</span>
                                @endif
                            </td>
                        </tr>

                        {{-- ========================= --}}
                        {{-- RIWAYAT TRANSAKSI LENGKAP --}}
                        {{-- ========================= --}}
                        @if($pinjaman->transaksi->isNotEmpty())
                            <tr id="cicilan-{{ $pinjaman->id }}" class="hidden">
                                <td colspan="7" class="px-6 py-4 bg-gray-50 border-l-4 border-gray-400 rounded-r-lg shadow-inner">

                                    <div class="text-sm font-semibold mb-3 text-gray-700">
                                        Riwayat Transaksi Lengkap
                                    </div>

                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-100 text-gray-600">
                                                <tr>
                                                    <th class="px-4 py-2 text-left">Tanggal</th>
                                                    <th class="px-4 py-2 text-center">Jenis</th>
                                                    <th class="px-4 py-2 text-right">Jumlah</th>
                                                    <th class="px-4 py-2 text-right">Sisa Pinjaman</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    // Sort transactions: pencairan first, then others by date ascending
                                                    $pencairanTransactions = $pinjaman->transaksi->where('jenis', 'pencairan')->sortBy('tanggal');
                                                    $otherTransactions = $pinjaman->transaksi->where('jenis', '!=', 'pencairan')->sortBy('tanggal');
                                                @endphp

                                                {{-- Pencairan transactions first --}}
                                                @php
                                                    $previousSisa = 0; // Start with 0 for pencairan
                                                @endphp
                                                @foreach($pencairanTransactions as $t)
                                                    @php
                                                        $selisih = ($t->sisa_setelah ?? 0) - $previousSisa;
                                                        $previousSisa = $t->sisa_setelah ?? 0;
                                                    @endphp
                                                    <tr class="border-t">
                                                        <td class="px-4 py-2">
                                                            {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                                                        </td>

                                                        <td class="px-4 py-2 text-center capitalize">
                                                            {{ ucfirst($t->jenis) }}
                                                        </td>

                                                        <td class="px-4 py-2 text-right">
                                                            Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                                        </td>

                                                        <td class="px-4 py-2 text-right font-medium">
                                                            Rp {{ number_format($t->sisa_setelah ?? 0, 0, ',', '.') }}
                                                            @if($selisih != 0)
                                                                <span class="text-xs {{ $selisih < 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                    ({{ $selisih < 0 ? '-' : '+' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }})
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                {{-- Other transactions in chronological order --}}
                                                @php
                                                    // Continue from last pencairan sisa, or original loan amount if no pencairan
                                                    $previousSisa = $previousSisa ?: $pinjaman->jumlah_pinjaman;
                                                    $counters = []; // Initialize counters for each jenis
                                                @endphp
                                                @foreach($otherTransactions as $t)
                                                    @php
                                                        $selisih = ($t->sisa_setelah ?? 0) - $previousSisa;
                                                        $previousSisa = $t->sisa_setelah ?? 0;

                                                        // Initialize counter for this jenis if not exists
                                                        if(!isset($counters[$t->jenis])) {
                                                            $counters[$t->jenis] = 1;
                                                        }
                                                    @endphp
                                                    <tr class="border-t">
                                                        <td class="px-4 py-2">
                                                            {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                                                        </td>

                                                        <td class="px-4 py-2 text-center capitalize">
                                                            @if(in_array($t->jenis, ['pencairan', 'topup','pelunasan']))
                                                                {{ ucfirst($t->jenis) }}
                                                            @else
                                                                {{ ucfirst($t->jenis) }} ke-{{ $counters[$t->jenis] }}
                                                            @endif
                                                        </td>

                                                        <td class="px-4 py-2 text-right">
                                                            Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                                        </td>

                                                        <td class="px-4 py-2 text-right font-medium">
                                                            Rp {{ number_format($t->sisa_setelah ?? 0, 0, ',', '.') }}
                                                            @if($selisih != 0)
                                                                <span class="text-xs {{ $selisih < 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                    ({{ $selisih < 0 ? '-' : '+' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }})
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $counters[$t->jenis]++;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </td>
                            </tr>
                        @endif

                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400">
                                Tidak ada pinjaman lunas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION PINJAMAN LUNAS --}}
        @if($pinjamansLunas->hasPages())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-2">
                <p class="text-sm text-gray-600">
                    Menampilkan
                    <span class="font-semibold text-gray-900">{{ $pinjamansLunas->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-semibold text-gray-900">{{ $pinjamansLunas->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold text-gray-900">{{ $pinjamansLunas->total() }}</span>
                    data
                </p>
                <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                    {{ $pinjamansLunas->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>

</div>
</div>

{{-- MODAL EDIT PINJAMAN --}}
<div id="editPinjamanModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-lg w-full mx-6 relative">
        <button type="button" onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
        <h3 class="text-xl font-semibold text-gray-800 mb-2 border-b pb-2">Edit Pinjaman</h3>
        <div id="editPinjamanDetail" class="mb-4">
            <!-- Detail pinjaman will be filled by JS -->
        </div>
        <form id="editPinjamanForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tenor Perubahan (bulan)</label>
                <input type="number" name="tenor" id="editTenorInput" min="1" max="20" placeholder="Masukkan Perubahan Tenor"
                    class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition shadow-sm">
                <p id="editTenorError" class="text-red-600 text-sm mt-1 hidden"></p>
            </div>
            <div id="editEstimasiContainer" class="text-sm text-gray-700 hidden">
                <div>Estimasi Perubahan Cicilan (mulai bulan berikutnya):</div>
                <div class="text-lg font-bold text-blue-600">
                    Rp <span id="editEstimasiCicilan">0</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" id="editKeteranganInput" rows="3" class="w-full border border-blue-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition shadow-sm"
                    placeholder="Tambahkan keterangan (opsional)"></textarea>
                <p id="editKeteranganError" class="text-red-600 text-sm mt-1 hidden"></p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition shadow">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL CICILAN --}}
<div id="cicilanModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full mx-6">

        {{-- TITLE --}}
        <h3 class="text-xl font-semibold text-gray-800 mb-2 border-b pb-2">
            Input Cicilan
        </h3>

        <div id="infoPerubahanTenor" class="text-left hidden mt-3 px-1 mb-2 text-xs text-gray-600 space-y-2"></div>
        {{-- INFO PINJAMAN --}}
        <div class="bg-gray-50 rounded-xl p-4 space-y-1 mb-4 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Nama</span>
                <span id="cicilanAnggota" class="font-semibold text-gray-800"></span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Jumlah Pinjaman</span>
                <span id="cicilanJumlah"></span>
            </div>
            
            <div class="flex justify-between">
                <span class="text-gray-500">Tenor</span>
                <span class="font-semibold text-gray-800">
                    <span id="cicilanTenor"></span> bulan
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Sisa Pinjaman</span>
                <span id="cicilanSisa" class="font-semibold text-red-500"></span>
            </div>

            <div class="flex justify-between border-t pt-2">
                <span class="text-gray-500">Cicilan / bulan</span>
                <span id="cicilanRekomendasi" class="font-semibold text-green-600"></span>
            </div>

        </div>

        {{-- RIWAYAT CICILAN --}}
        <div class="mb-5">
            <button
                type="button"
                onclick="toggleRiwayatCicilan()"
                class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 mb-2">

                <span>Riwayat Cicilan</span>

                <svg id="iconRiwayat" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="riwayatWrapper" class="hidden bg-gray-50 rounded-xl border border-gray-200 max-h-40 overflow-y-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-100 text-gray-500 sticky top-0">
                        <tr>
                            <th class="px-3 py-2 text-left">Tanggal</th>
                            <th class="px-3 py-2 text-center">Jenis</th>
                            <th class="px-3 py-2 text-center">Jumlah</th>
                            <th class="px-3 py-2 text-center">Info</th>
                        </tr>
                    </thead>
                    <tbody id="riwayatCicilanBody"></tbody>
                </table>
            </div>
        </div>

        {{-- FORM --}}
        <form id="cicilanForm" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">
                    Jumlah Cicilan
                </label>
                <input
                    type="text"
                    id="inputCicilanJumlah"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none"
                    placeholder="Rp 0"
                    required
                >
                <input type="hidden" name="jumlah" id="jumlahHidden">
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">
                    Keterangan
                </label>
                <input
                    type="text"
                    name="keterangan"
                    id="cicilanKeterangan"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none"
                    placeholder="Opsional"
                >
            </div>

            {{-- ACTION --}}
            <div class="flex gap-3 justify-end pt-1">
                <button
                    type="button"
                    onclick="closeCicilanModal()"
                    class="px-3 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                    Batal
                </button>

                <button
                    type="submit"
                    class="px-3 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition shadow-sm">
                    Simpan
                </button>
            </div>
        </form>

    </div>
</div>

{{-- MODAL PELUNASAN CEPAT --}}
<div id="pelunasamModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Pelunasan Cepat</h3>
        <p class="text-gray-600 mb-2">
            Anggota: <span id="pelunasanAnggota" class="font-semibold text-gray-900"></span>
        </p>
        <p class="text-gray-600 mb-6">
            Jumlah Sisa: <span id="pelunasanJumlah" class="font-semibold text-red-600"></span>
        </p>

        <form id="pelunsaanForm" method="POST" class="space-y-6">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah Pelunasan
                </label>
                <input
                    type="text"
                    name="jumlah_display"
                    id="pelunasanJumlahInput"
                    readonly
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    placeholder="Rp 0">
                <input type="hidden" id="pelunasanJumlahSisa" name="jumlah" value="0">
            </div>

            <div class="flex gap-3 justify-end">
                <button
                    type="button"
                    onclick="closePelunasamModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-400 transition">
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition">
                    Proses
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT FLASH MESSAGE --}}
<script>
    function closeFlashMessage() {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            flashMessage.style.display = 'none';
        }
        // Auto close after 5 seconds
        setTimeout(() => {
            if (flashMessage) {
                flashMessage.style.display = 'none';
            }
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.display = 'none';
            }, 5000);
        }
    });
</script>

{{-- SCRIPT UBAH PINJAMAN --}}
<script>
    // Modal Edit Pinjaman
    let editPinjamanSisa = 0;
    function openEditModal(pinjaman) {
        // Fill detail
        let html = `<ul class='text-sm text-gray-700 space-y-1 bg-blue-50 p-4 rounded-xl border border-blue-100 mb-2'>
            <li><strong>Anggota:</strong> ${pinjaman.anggota?.nama ?? '-'}</li>
            <li><strong>Nomor Anggota:</strong> ${pinjaman.anggota?.nomor_anggota ?? '-'}</li>
            <li><strong>Tanggal Pinjam:</strong> ${formatTanggal(pinjaman.tanggal_pinjam)}</li>
            <li><strong>Total Pinjaman:</strong> Rp ${formatRupiah(pinjaman.jumlah_pinjaman)}</li>
            <li><strong>Sisa Pinjaman:</strong> <span class='${pinjaman.sisa_pinjaman>0?'text-red-600':'text-green-600'}'>Rp ${formatRupiah(pinjaman.sisa_pinjaman)}</span></li>
            <li><strong>Tenor:</strong> ${pinjaman.tenor ?? '-'} bulan</li>
            <li><strong>Cicilan saat ini:</strong> <span class="text-blue-700 font-semibold">Rp ${formatRupiah(pinjaman.cicilan_per_bulan ?? 0)}</span></li>
            <li><strong>Status:</strong> <span class='inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${pinjaman.status==='aktif'?'bg-blue-100 text-blue-800':'bg-green-100 text-green-800'} shadow-sm'>${capitalize(pinjaman.status)}</span></li>
        </ul>`;
        document.getElementById('editPinjamanDetail').innerHTML = html;
        // Prefill form
        document.getElementById('editTenorInput').value = '';
        document.getElementById('editKeteranganInput').value = pinjaman.keterangan ?? '';
        // Save sisa for estimasi
        editPinjamanSisa = pinjaman.sisa_pinjaman ?? 0;
        // Reset error
        document.getElementById('editTenorError').classList.add('hidden');
        document.getElementById('editKeteranganError').classList.add('hidden');
        // Set form action
        document.getElementById('editPinjamanForm').action = `/admin/pinjaman/data-anggota/${pinjaman.id}`;
        // Show modal
        document.getElementById('editPinjamanModal').classList.remove('hidden');
        // Sembunyikan estimasi di awal
        document.getElementById('editEstimasiContainer').classList.add('hidden');
        // Estimasi update saat input berubah
        editTenorTouched = false;
    }
    function closeEditModal() {
        document.getElementById('editPinjamanModal').classList.add('hidden');
    }
    function updateEditEstimasiCicilan() {
        const tenor = parseInt(document.getElementById('editTenorInput').value) || 0;
        const estimasiContainer = document.getElementById('editEstimasiContainer');
        const estimasiCicilanSpan = document.getElementById('editEstimasiCicilan');
        if (tenor > 0) {
            const estimasi = Math.ceil(editPinjamanSisa / tenor);
            estimasiCicilanSpan.textContent = new Intl.NumberFormat('id-ID').format(estimasi);
            estimasiContainer.classList.remove('hidden');
        } else {
            estimasiContainer.classList.add('hidden');
            estimasiCicilanSpan.textContent = '0';
        }
    }
    let editTenorTouched = false;
    document.getElementById('editTenorInput').addEventListener('input', function() {
        updateEditEstimasiCicilan();
    });
    document.getElementById('editTenorInput').addEventListener('change', updateEditEstimasiCicilan);
</script>

{{-- SCRIPT RIWAYAT CICILAN, PELUNASAN --}}
<script>
    let tenorAwal = 0;
    let jumlahPinjamanGlobal = 0;
    let sisaPinjamanGlobal = 0;

    function openCicilanModal(pinjaman) {

        const anggotaNama = pinjaman.anggota?.nama ?? '-';
        const jumlahPinjaman = pinjaman.jumlah_pinjaman ?? 0;
        const sisaPinjaman = pinjaman.sisa_pinjaman ?? 0;
        const tenor = pinjaman.tenor ?? 0;
        const pinjamanId = pinjaman.id;

        // simpan global (untuk detect perubahan tenor)
        tenorAwal = tenor;
        jumlahPinjamanGlobal = jumlahPinjaman;
        sisaPinjamanGlobal = sisaPinjaman;

        // =========================
        // INFO PINJAMAN
        // =========================
        document.getElementById('cicilanAnggota').textContent = anggotaNama;
        document.getElementById('cicilanJumlah').textContent = formatRupiah(jumlahPinjaman);
        document.getElementById('cicilanSisa').textContent = formatRupiah(sisaPinjaman);
        
        // =========================
        // AMBIL STRUKTUR TERAKHIR
        // =========================
        let tenorAcuan = tenor;
        const infoBox = document.getElementById('infoPerubahanTenor');
        let html = '';

        if (pinjaman.updated_at) {

            html = `
                <div class="text-[11px] text-gray-400">
                    Update terakhir: ${formatTanggal(pinjaman.updated_at)}
                </div>
            `;

            infoBox.innerHTML = html;
            infoBox.classList.remove('hidden');

        } else {
            infoBox.classList.add('hidden');
        }

        // =========================
        // HITUNG CICILAN REAL
        // =========================
        let cicilanTetap = pinjaman.cicilan_per_bulan ?? 0;
        document.getElementById('cicilanTenor').textContent = tenorAcuan || 'N/A';
        document.getElementById('cicilanRekomendasi').textContent = formatRupiah(cicilanTetap);

        setDefaultCicilan(cicilanTetap);

        // =========================
        // FORM
        // =========================
        const form = document.getElementById('cicilanForm');
        form.action = `/admin/pinjaman/${pinjamanId}/cicil`;

        // =========================
        // RIWAYAT CICILAN (FULL LOGIC)
        // =========================
        const tbody = document.getElementById('riwayatCicilanBody');
        tbody.innerHTML = '';

        if (!pinjaman.transaksi || pinjaman.transaksi.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center py-3 text-gray-400 italic">
                        Belum ada transaksi
                    </td>
                </tr>
            `;
        } else {

            // 🔥 SORT SEPERTI DI BLADE
            const pencairan = pinjaman.transaksi
                .filter(t => t.jenis === 'pencairan')
                .sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal));

            const lainnya = pinjaman.transaksi
                .filter(t => t.jenis !== 'pencairan')
                .sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal));

            let previousSisa = 0;

            // =========================
            // 1. PENCAIRAN DULU
            // =========================
            pencairan.forEach(t => {

                const row = `
                    <tr class="border-t">
                        <td class="px-3 py-2">
                            ${formatTanggal(t.updated_at)}
                        </td>
                        <td class="px-3 py-2 text-center text-blue-600 font-medium">
                            Pencairan
                        </td>
                        <td class="px-3 py-2 text-right font-medium">
                            ${formatRupiah(t.jumlah)}
                        </td>
                    </tr>
                `;

                previousSisa = t.sisa_setelah ?? 0;

                tbody.insertAdjacentHTML('beforeend', row);
            });

            // =========================
            // 2. TRANSAKSI LAIN (CICILAN, TOPUP, DLL)
            // =========================
            let cicilanKe = 0;
            lainnya.forEach(t => {
            const jenis = t.jenis;

            // =========================
            // RESET CICILAN
            // =========================
            if (jenis === 'topup' || jenis === 'ubah_tenor') {
                cicilanKe = 0;
            }

            let label;

            if (jenis === 'cicilan') {
                cicilanKe++;
                label = `Cicilan ke-${cicilanKe}`;
            } 
            else if (jenis === 'topup') {
                label = 'Topup';
            } 
            else if (jenis === 'pelunasan') {
                label = 'Pelunasan';
            } 
            else {
                label = capitalize(jenis);
            }

            const warna = {
                cicilan: 'text-green-600',
                topup: 'text-purple-600',
                pelunasan: 'text-gray-600',
                ubah_tenor: 'text-blue-600'
            };

            const row = `
                <tr class="border-t">
                    <td class="px-3 py-2">
                        ${formatTanggal(t.updated_at)}
                    </td>
                    <td class="px-3 py-2 text-center font-medium ${warna[jenis] || ''}">
                        ${label}
                    </td>
                    <td class="px-3 py-2 text-right font-medium">
                        ${formatRupiah(t.jumlah)}
                    </td>
                    <td class="px-3 py-2">
                        ${keterangan = t.keterangan ? `<span class="text-xs text-gray-500 italic">${t.keterangan}</span>` : '' }
                    </td>
                </tr>
            `;

            tbody.insertAdjacentHTML('beforeend', row);
        });
        }

        document.getElementById('cicilanModal').classList.remove('hidden');
    }

    function capitalize(text) {
        return text.charAt(0).toUpperCase() + text.slice(1);
    }

    function closeCicilanModal() {
        document.getElementById('cicilanModal').classList.add('hidden');
    }

    function formatTanggal(tgl) {
        const date = new Date(tgl);

        const tanggal = date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });

        const waktu = date.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        return `${tanggal} ${waktu}`;
    }

    function confirmPelunasan(pinjamanId, anggotaNama, sisaPinjaman) {
        document.getElementById('pelunasanAnggota').textContent = anggotaNama;
        document.getElementById('pelunasanJumlah').textContent = formatRupiah(sisaPinjaman);
        
        const jumlahInput = document.getElementById('pelunasanJumlahInput');
        jumlahInput.value = formatRupiah(sisaPinjaman);
        jumlahInput.min = 1;
        jumlahInput.max = sisaPinjaman;

        const form = document.getElementById('pelunsaanForm');
        form.action = `/admin/pinjaman/${pinjamanId}/cicil`;
        form.method = 'POST';

        document.getElementById('pelunasanJumlahSisa').value = sisaPinjaman;
        document.getElementById('pelunasamModal').classList.remove('hidden');
    }

    function closePelunasamModal() {
        document.getElementById('pelunasamModal').classList.add('hidden');
    }

    // Submit form validation
    document.getElementById('pelunsaanForm')?.addEventListener('submit', function(e) {
        const jumlah = parseInt(document.getElementById('pelunsaanJumlahInput').value);
        const sisa = parseInt(document.getElementById('pelunsaanJumlahSisa').value);

        if (!jumlah || jumlah <= 0) {
            e.preventDefault();
            alert('Jumlah pelunasan harus lebih dari 0');
            return;
        }

        if (jumlah !== sisa) {
            e.preventDefault();
            alert('Untuk pelunasan cepat, jumlah harus sama dengan sisa pinjaman.');
        }
    });

    // Toggle expandable transaction history
    function toggleCicilan(pinjamanId) {
        const cicilanRow = document.getElementById(`cicilan-${pinjamanId}`);
        const btnLihat = document.getElementById(`btn-lihat-${pinjamanId}`);
        const iconLihat = document.getElementById(`icon-lihat-${pinjamanId}`);

        if (cicilanRow.classList.contains('hidden')) {
            // Show the row
            cicilanRow.classList.remove('hidden');
            btnLihat.classList.remove('bg-gray-500', 'hover:bg-gray-600');
            btnLihat.classList.add('bg-gray-700', 'hover:bg-gray-800');
            btnLihat.innerHTML = `
                <svg class="w-3 h-3 transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
                Tutup
            `;
        } else {
            // Hide the row
            cicilanRow.classList.add('hidden');
            btnLihat.classList.remove('bg-gray-700', 'hover:bg-gray-800');
            btnLihat.classList.add('bg-gray-500', 'hover:bg-gray-600');
            btnLihat.innerHTML = `
                <svg class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                Lihat
            `;
        }
    }

    function toggleRiwayatCicilan() {
        const wrapper = document.getElementById('riwayatWrapper');
        const icon = document.getElementById('iconRiwayat');

        wrapper.classList.toggle('hidden');

        // rotate icon
        icon.classList.toggle('rotate-180');
    }

    function setDefaultCicilan(nominal) {
        const input = document.getElementById('inputCicilanJumlah');
        const hidden = document.getElementById('jumlahHidden');

        hidden.value = nominal;
        input.value = formatRupiah(nominal.toString());
    }

    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka || 0);
    }

    const input = document.getElementById('inputCicilanJumlah');
    const hidden = document.getElementById('jumlahHidden');

    input.addEventListener('input', function(e) {
        let value = e.target.value;

        // simpan angka asli (tanpa Rp & titik)
        let numeric = value.replace(/[^0-9]/g, '');

        hidden.value = numeric;

        // tampilkan versi rupiah
        e.target.value = formatRupiah(numeric);
    });
</script>
@endsection
