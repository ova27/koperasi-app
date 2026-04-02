@extends('layouts.main')

@section('title', 'Data Pinjaman Anggota')
@section('page-title', 'Data Pinjaman Anggota')
@section('content')
<div class="space-y-6">

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div id="flash-message" class="px-4 py-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" 
                    onclick="closeFlashMessage()"
                    class="text-green-700 hover:text-green-900 ml-4">
                ×
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="flash-message" class="px-4 py-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" 
                    onclick="closeFlashMessage()"
                    class="text-red-700 hover:text-red-900 ml-4">
                ×
            </button>
        </div>
    @endif

    {{-- TABLE PINJAMAN AKTIF --}}
    <div>
        <h2 class="section-title">Pinjaman Aktif Anggota</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                        <th class="p-3 border-b text-left">Anggota</th>
                        <th class="p-3 border-b text-left">Tanggal Pinjam</th>
                        <th class="p-3 border-b text-right">Jumlah Pinjaman</th>
                        <th class="p-3 border-b text-right">Sisa Pinjaman</th>
                        <th class="p-3 border-b text-center">Tenor</th>
                        <th class="p-3 border-b text-center">Status</th>
                        <th class="p-3 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($pinjamansAktif as $pinjaman)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 border-b">
                                <div class="font-medium text-gray-800">
                                    {{ $pinjaman->anggota->nama ?? '-' }}
                                </div>
                            </td>
                            <td class="p-3 border-b text-gray-500">
                                {{ $pinjaman->tanggal_pinjam->format('d/m/Y') }}
                            </td>
                            <td class="p-3 border-b text-right font-semibold text-gray-800">
                                Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                            </td>
                            <td class="p-3 border-b text-right font-semibold {{ $pinjaman->sisa_pinjaman > 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                            </td>
                            <td class="p-3 border-b text-center text-gray-700">
                                {{ $pinjaman->tenor ?? '-' }} bulan
                            </td>
                            <td class="p-3 border-b text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $pinjaman->status === 'aktif' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($pinjaman->status) }}
                                </span>
                            </td>
                            <td class="p-3 border-b text-center space-x-0">
                                {{-- EDIT (Ketua & Bendahara) - hanya untuk pinjaman aktif --}}
                                @can('edit pinjaman')
                                    @if($pinjaman->status === 'aktif')
                                        <button 
                                            onclick="window.location.href='{{ route('admin.pinjaman.data-anggota.edit', $pinjaman) }}'"
                                            title="Edit"
                                            class="px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                                            Edit
                                        </button>
                                    @endif
                                @endcan

                                {{-- CICIL & PELUNASAN CEPAT (Bendahara Only) --}}
                                @can('manage cicilan pinjaman')
                                    @if($pinjaman->status === 'aktif')
                                        <button 
                                            onclick="window.location.href='{{ route('admin.pinjaman.cicil.create', $pinjaman) }}'"
                                            title="Cicil"
                                            class="px-3 py-1.5 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition shadow-sm">
                                            Cicil
                                        </button>

                                        <button
                                            onclick="confirmPelunasan({{ $pinjaman->id }}, '{{ addslashes($pinjaman->anggota->nama) }}', {{ $pinjaman->sisa_pinjaman }})"
                                            title="Pelunasan Cepat"
                                            class="px-3 py-1.5 text-xs font-semibold text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition shadow-sm">
                                            Pelunasan
                                        </button>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">
                                Tidak ada pinjaman aktif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION PINJAMAN AKTIF --}}
        @if($pinjamansAktif->hasPages())
            <div class="flex items-center justify-center gap-2 mt-4">
                {{ $pinjamansAktif->appends(request()->except('aktif_page'))->links() }}
            </div>
        @endif
    </div>

    {{-- TABLE PINJAMAN LUNAS --}}
    <div class="pt-6 border-t border-gray-200">
        <h2 class="section-title">Pinjaman Lunas Anggota</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
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
                            <td class="p-3 border-b">
                                <div class="font-medium text-gray-800">
                                    {{ $pinjaman->anggota->nama ?? '-' }}
                                </div>
                            </td>
                            <td class="p-3 border-b text-gray-500">
                                {{ $pinjaman->tanggal_pinjam->format('d M Y') }}
                            </td>

                            <td class="p-3 border-b text-gray-500">
                                @php
                                    $tanggalLunas = $pinjaman->transaksi->where('jenis', 'cicilan')->sortByDesc('tanggal')->first()?->tanggal
                                        ?: $pinjaman->transaksi->where('jenis', 'pelunasan')->sortByDesc('tanggal')->first()?->tanggal;
                                @endphp
                                {{ $tanggalLunas ? \Carbon\Carbon::parse($tanggalLunas)->format('d M Y') : '-' }}
                            </td>

                            <td class="p-3 border-b text-right font-semibold text-gray-800">
                                Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                            </td>

                            <td class="p-3 border-b text-center text-gray-700">
                                {{ $pinjaman->tenor ?? '-' }} bulan
                            </td>

                            <td class="p-3 border-b text-center">
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
                                <td colspan="6" class="px-6 py-4 bg-gray-50 border-l-4 border-gray-400 rounded-r-lg shadow-inner">

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
            <div class="flex items-center justify-center gap-2 mt-4">
                {{ $pinjamansLunas->appends(request()->except('lunas_page'))->links() }}
            </div>
        @endif
    </div>

</div>

{{-- MODAL PELUNASAN CEPAT --}}
<div id="pelunasamModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Pelunasan Cepat</h3>
        <p class="text-gray-600 mb-2">
            Anggota: <span id="pelunsaanAnggota" class="font-semibold text-gray-900"></span>
        </p>
        <p class="text-gray-600 mb-6">
            Jumlah Sisa: <span id="pelunasanJumlah" class="font-semibold text-red-600"></span>
        </p>

        <form id="pelunsaanForm" method="POST" class="space-y-6">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah Pelunasan (diharapkan nilai penuh)
                </label>
                <input
                    type="number"
                    name="jumlah"
                    id="pelunsaanJumlahInput"
                    min="1"
                    readonly
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    placeholder="Rp 0">
                <input type="hidden" id="pelunsaanJumlahSisa" value="0">
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

<script>
// Close flash message
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

// Auto close flash message on page load
document.addEventListener('DOMContentLoaded', function() {
    const flashMessage = document.getElementById('flash-message');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.display = 'none';
        }, 5000);
    }
});

function confirmPelunasan(pinjamanId, anggotaNama, sisaPinjaman) {
    document.getElementById('pelunsaanAnggota').textContent = anggotaNama;
    document.getElementById('pelunasanJumlah').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(sisaPinjaman);
    const jumlahInput = document.getElementById('pelunsaanJumlahInput');

    jumlahInput.value = sisaPinjaman;
    jumlahInput.min = 1;
    jumlahInput.max = sisaPinjaman;

    const form = document.getElementById('pelunsaanForm');
    form.action = `/admin/pinjaman/${pinjamanId}/cicil`;
    form.method = 'POST';

    document.getElementById('pelunsaanJumlahSisa').value = sisaPinjaman;
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
</script>
@endsection
