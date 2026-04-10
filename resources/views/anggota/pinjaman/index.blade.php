@extends('layouts.main')

@section('title', 'Pinjaman Saya')
@section('page-title', 'Pinjaman Saya')

@section('content')
<div class="space-y-10">

    {{-- ========================= --}}
    {{-- RINGKASAN PINJAMAN --}}
    {{-- ========================= --}}
    <div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            {{-- TOTAL PINJAMAN --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                <div class="text-sm text-gray-600 mb-1">
                    Total Pinjaman
                </div>
                <div class="text-xl font-semibold text-gray-900">
                    Rp {{ number_format($totalPinjamanSaya ?? 0, 0, ',', '.') }}
                </div>
            </div>

            {{-- SISA PINJAMAN --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                <div class="text-sm text-gray-600 mb-1">
                    Sisa Pinjaman
                </div>
                <div class="text-xl font-semibold text-gray-900">
                    Rp {{ number_format($sisaPinjamanSaya ?? 0, 0, ',', '.') }}
                </div>
            </div>

            {{-- STATUS --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                <div class="text-sm text-gray-600 mb-1">
                    Status Pinjaman
                </div>

                @if($pinjamanAktifSaya)
                    <span class="inline-block px-3 py-1 rounded-full text-sm
                        bg-green-100 text-green-800">
                        Aktif
                    </span>
                @else
                    <span class="inline-block px-3 py-1 rounded-full text-sm
                        bg-gray-100 text-gray-700">
                        Tidak Ada Pinjaman Aktif
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- TABEL PINJAMAN AKTIF--}}
    {{-- ========================= --}}
    @if ($pinjamanAktif->isNotEmpty())
        <div>
            <h2 class="section-title">
                Pinjaman Aktif
            </h2>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left w-[15%]">
                                Tanggal
                            </th>
                            <th class="px-4 py-3 text-center w-[15%]">
                                Status
                            </th>
                            <th class="px-4 py-3 text-right w-[20%]">
                                Jumlah
                            </th>
                            <th class="px-4 py-3 text-right w-[15%]">
                                Tenor
                            </th>
                            <th class="px-4 py-3 text-right w-[15%]">
                                Cicilan/Bulan
                            <th class="px-4 py-3 text-right w-[20%]">
                                Sisa
                            </th>
                            <th class="px-4 py-3 text-center w-[25%]">
                                Detail
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($pinjamanAktif as $p)

                            {{-- ========================= --}}
                            {{-- BARIS PINJAMAN UTAMA --}}
                            {{-- ========================= --}}
                            <tr class="border-t hover:bg-gray-50 transition-colors duration-200 border-l-4 border-transparent" id="pinjaman-row-{{ $p->id }}">
                                <td class="px-4 py-2">
                                    {{ $p->tanggal_pinjam->format('d M Y') }}
                                </td>

                                <td class="px-4 py-2 text-center">
                                    @php
                                        $badge = match($p->status) {
                                            'aktif' => 'bg-green-100 text-green-800',
                                            'pengajuan' => 'bg-yellow-100 text-yellow-800',
                                            'disetujui' => 'bg-blue-100 text-blue-800',
                                            'ditolak' => 'bg-red-100 text-red-800',
                                            'lunas' => 'bg-gray-100 text-gray-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp

                                    <span class="px-3 py-1 rounded-full text-xs {{ $badge }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-2 text-right font-medium">
                                    Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-2 text-right">
                                    {{ $p->tenor }} bulan
                                </td>
                                <td class="px-4 py-2 text-right">
                                    Rp {{ number_format($p->cicilan_per_bulan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-2 text-right font-medium">
                                    Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-2 text-center">
                                    @if($p->transaksi->isNotEmpty())
                                        <button
                                            onclick="toggleCicilan({{ $p->id }})"
                                            id="btn-lihat-{{ $p->id }}"
                                            class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium px-3 py-1.5 rounded-md transition-all duration-200 inline-flex items-center gap-1 transform hover:scale-105">
                                            <svg class="w-3 h-3 transition-transform duration-200" id="icon-lihat-{{ $p->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            {{-- RIWAYAT CICILAN --}}
                            {{-- ========================= --}}
                            @if($p->transaksi->isNotEmpty())
                                <tr id="cicilan-{{ $p->id }}" class="hidden">
                                    <td colspan="8" class="px-6 py-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg shadow-inner">

                                        <div class="text-sm font-semibold mb-3 text-gray-700">
                                            Riwayat Transaksi
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
                                                        // Sort transactions: pencairan first (by date asc for progression), then others by date asc
                                                        $pencairanTransactions = $p->transaksi->where('jenis', 'pencairan')->sortBy('tanggal');
                                                        $otherTransactions = $p->transaksi->where('jenis', '!=', 'pencairan')->sortBy('tanggal');
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
                                                        $previousSisa = $previousSisa ?: $p->jumlah_pinjaman;
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    

    {{-- ========================= --}}
    {{-- RIWAYAT PINJAMAN LUNAS --}}
    {{-- ========================= --}}
    <div>
        <h2 class="section-title">
            Riwayat Pinjaman Lunas
        </h2>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left w-[25%]">
                            Tanggal Pinjam
                        </th>
                        <th class="px-4 py-3 text-left w-[25%]">
                            Tanggal Lunas
                        </th>
                        <th class="px-4 py-3 text-right w-[20%]">
                            Jumlah Pinjaman
                        </th>
                        <th class="px-4 py-3 text-center w-[40%]">
                            Detail
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pinjamanLunas as $p)

                        {{-- ========================= --}}
                        {{-- BARIS PINJAMAN LUNAS --}}
                        {{-- ========================= --}}
                        <tr class="border-t hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-4 py-2">
                                {{ $p->tanggal_pinjam->format('d M Y') }}
                            </td>

                            <td class="px-4 py-2">
                                @php
                                    $tanggalLunas = $p->transaksi->where('jenis', 'cicilan')->sortByDesc('tanggal')->first()?->tanggal;
                                @endphp
                                {{ $tanggalLunas ? \Carbon\Carbon::parse($tanggalLunas)->format('d M Y') : '-' }}
                            </td>

                            <td class="px-4 py-2 text-right font-medium">
                                Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-2 text-center">
                                @if($p->transaksi->isNotEmpty())
                                    <button
                                        onclick="toggleCicilan({{ $p->id }})"
                                        id="btn-lihat-{{ $p->id }}"
                                        class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium px-3 py-1.5 rounded-md transition-all duration-200 inline-flex items-center gap-1 transform hover:scale-105">
                                        <svg class="w-3 h-3 transition-transform duration-200" id="icon-lihat-{{ $p->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        {{-- RIWAYAT CICILAN --}}
                        {{-- ========================= --}}
                        @if($p->transaksi->isNotEmpty())
                            <tr id="cicilan-{{ $p->id }}" class="hidden">
                                <td colspan="5" class="px-6 py-4 bg-gray-50 border-l-4 border-gray-400 rounded-r-lg shadow-inner">

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
                                                    // Sort transactions: pencairan first, then others by date descending
                                                    $pencairanTransactions = $p->transaksi->where('jenis', 'pencairan')->sortBy('tanggal');
                                                    $otherTransactions = $p->transaksi->where('jenis', '!=', 'pencairan')->sortBy('tanggal');
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
                                                    $previousSisa = $previousSisa ?: $p->jumlah_pinjaman;
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
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                Belum ada riwayat pinjaman lunas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($pinjamanLunas->hasPages())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-2 px-2">
                <p class="text-sm text-gray-600">
                    Menampilkan
                    <span class="font-semibold text-gray-900">{{ $pinjamanLunas->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-semibold text-gray-900">{{ $pinjamanLunas->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold text-gray-900">{{ $pinjamanLunas->total() }}</span>
                    data
                </p>

                <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                    {{ $pinjamanLunas->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

<script>
function toggleCicilan(id) {
    const row = document.getElementById('cicilan-' + id);
    const mainRow = document.getElementById('pinjaman-row-' + id);
    const button = document.getElementById('btn-lihat-' + id);
    const icon = document.getElementById('icon-lihat-' + id);

    // Toggle visibility
    row.classList.toggle('hidden');

    // Change main row background when detail is open
    if (!row.classList.contains('hidden')) {
        mainRow.classList.add('bg-blue-100', 'border-blue-300');
        mainRow.classList.remove('hover:bg-gray-50', 'border-transparent');
        mainRow.classList.add('border-blue-400');
        button.innerHTML = `
            <svg class="w-3 h-3 transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
            </svg>
            Tutup
        `;
        button.classList.remove('bg-blue-500', 'hover:bg-blue-600');
        button.classList.add('bg-red-500', 'hover:bg-red-600');
    } else {
        mainRow.classList.remove('bg-blue-100', 'border-blue-300', 'border-blue-400');
        mainRow.classList.add('hover:bg-gray-50', 'border-transparent');
        button.innerHTML = `
            <svg class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
            Lihat
        `;
        button.classList.remove('bg-red-500', 'hover:bg-red-600');
        button.classList.add('bg-blue-500', 'hover:bg-blue-600');
    }
}
</script>
