@extends('layouts.main')

@section('title', 'Transaksi Pinjaman Anggota')
@section('page-title', 'Transaksi Pinjaman Anggota')
@section('content')
<div class="space-y-4">

    {{-- ================= TAB NAVIGATION ================= --}}
    <div class="border-b flex gap-6 text-sm font-medium">
        <a href="{{ route('admin.pinjaman.data-anggota.index', ['tab' => 'pengajuan']) }}"
            class="pb-2 text-gray-400 hover:text-gray-700">
            Pengajuan Pinjaman
        </a>

        <a href="{{ route('admin.pinjaman.pencairan.index') }}"
            class="pb-2 border-b-2 border-black text-black font-medium">
            Pencairan Pinjaman
        </a>

        <a href="{{ route('admin.pinjaman.data-anggota.index', ['tab' => 'aktif']) }}"
            class="pb-2 text-gray-400 hover:text-gray-700">
            Pinjaman Aktif
        </a>

        <a href="{{ route('admin.pinjaman.data-anggota.index', ['tab' => 'lunas']) }}"
            class="pb-2 text-gray-400 hover:text-gray-700">
            Pinjaman Lunas
        </a>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div id="flash-message" class="px-4 py-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="closeFlashMessage()" class="ml-4 hover:text-green-900">×</button>
        </div>
    @endif

    @if(session('error'))
        <div id="flash-message" class="px-4 py-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="closeFlashMessage()" class="ml-4 hover:text-red-900">×</button>
        </div>
    @endif

    {{-- DAFTAR PINJAMAN YANG SIAP DICAIRKAN --}}
    <div class="bg-white border rounded-xl p-4 shadow-sm hover:shadow-md transition">
        <h2 class="font-semibold text-gray-800 mb-4 text-sm">
            Pinjaman Siap Dicairkan
        </h2>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto mb-4">
            @php
                function sortLinkP($col, $label, $sort, $dir) {
                    $newDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                    $arrow = $sort === $col ? ($dir === 'asc' ? '▲' : '▼') : '';

                    return '<a href="'.request()->fullUrlWithQuery([
                        'p_sort'=>$col,
                        'p_direction'=>$newDir
                    ]).'" class="hover:underline">'.$label.' '.$arrow.'</a>';
                }

                function sortLinkR($col, $label, $sort, $dir) {
                    $newDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                    $arrow = $sort === $col ? ($dir === 'asc' ? '▲' : '▼') : '';

                    return '<a href="'.request()->fullUrlWithQuery([
                        'r_sort'=>$col,
                        'r_direction'=>$newDir
                    ]).'" class="hover:underline">'.$label.' '.$arrow.'</a>';
                }
            @endphp
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                        <th class="p-3 text-center">No</th>
                        <th class="p-3 text-center">
                            {!! sortLinkP('tanggal_pengajuan','Tanggal Pengajuan',$p_sort,$p_direction) !!}
                        </th>
                        <th class="p-3 text-center">Anggota</th>
                        <th class="p-3 text-right">
                            {!! sortLinkP('jumlah_diajukan','Jumlah',$p_sort,$p_direction) !!}
                        </th>
                        <th class="p-3 text-center">
                            {!! sortLinkP('bulan_pinjam','Bulan Pinjam',$p_sort,$p_direction) !!}
                        </th>
                        <th class="p-3 text-center">Tenor</th>
                        <th class="p-3 text-center">
                            {!! sortLinkP('tanggal_persetujuan','Tanggal Persetujuan',$p_sort,$p_direction) !!}
                        </th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($pengajuans as $p)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 text-center text-gray-500">
                                {{ $pengajuans->firstItem() + $loop->index }}
                            </td>
                            <td class="p-3 text-center text-gray-500">
                                {{ optional($p->tanggal_pengajuan)->format('d F Y') ?? '-' }}
                            </td>
                            <td class="p-3 text-center">
                                {{ $p->anggota->nama }}
                            </td>
                            <td class="p-3 text-right font-semibold text-gray-800">
                                Rp {{ number_format($p->jumlah_diajukan, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-center text-gray-500">
                                {{ \Carbon\Carbon::parse($p->bulan_pinjam)->format('F Y') }}
                            </td>
                            <td class="p-3 text-center">
                                {{ $p->tenor }} Bulan
                            </td>
                            <td class="p-3 text-center text-gray-500">
                                {{ optional($p->tanggal_persetujuan)->format('d F Y') ?? '-' }}
                            </td>
                            <td class="p-3 text-center">
                                @can('pencairan pinjaman')
                                    <form method="POST"
                                        action="{{ route('admin.pinjaman.pencairan.process', $p) }}"
                                        onsubmit="return confirmCairkan()">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition shadow-sm">
                                            Cairkan
                                        </button>
                                    </form>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="px-3 py-1.5 text-xs font-semibold text-white bg-gray-300 rounded-lg cursor-not-allowed">
                                        Cairkan
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($pengajuans->hasPages())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-2">
                <p class="text-sm text-gray-600">
                    Menampilkan
                    <span class="font-semibold text-gray-900">{{ $pengajuans->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-semibold text-gray-900">{{ $pengajuans->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold text-gray-900">{{ $pengajuans->total() }}</span>
                    data
                </p>

                <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                    {{ $pengajuans->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>

    {{-- RIWAYAT PENCAIRAN --}}
    <div x-data="{ open: false }"
        class="bg-green-50 border rounded-xl p-4 shadow-sm hover:shadow-md transition">

        <button
            @click="open = !open"
            class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 transition group"
        >
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500 group-hover:text-gray-700 transition"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7h18M3 12h18M3 17h18" />
                </svg>

                <h2 class="font-semibold text-gray-800 text-sm">
                    Riwayat Pencairan Pinjaman Aktif (Belum Lunas)
                </h2>
            </div>

            <svg
                :class="{ 'rotate-180': open }"
                class="w-4 h-4 text-gray-500 transform transition duration-200 group-hover:text-gray-700"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div
            x-show="open"
            x-transition
            x-cloak
            class="mt-4 space-y-3"
        >
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto mb-4">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                        <th class="px-4 py-3 text-left">Tanggal Pengajuan</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-right">Jumlah</th>
                        <th class="px-4 py-3 text-center">Tenor</th>
                        <th class="px-4 py-3 text-center">Tanggal Cair</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($riwayatPencairan as $r)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-500">
                                {{ $r->tanggal_pengajuan->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $r->anggota->nama }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">
                                Rp {{ number_format($r->jumlah_diajukan, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $r->tenor }} Bulan
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">
                                {{ $r->updated_at->timezone('Asia/Jakarta')->format('d/m/y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase">
                                    Dicairkan
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @can('pencairan pinjaman')
                                    @if($r->pinjaman
                                        && $r->pinjaman->transaksi->where('jenis','cicilan')->count() > 0
                                        && !$r->pinjaman->transaksi->where('jenis','pelunasan')->count())

                                        <span class="text-gray-400 text-xs italic">Tidak bisa dibatalkan</span>

                                    @else
                                        <form method="POST"
                                            action="{{ route('admin.pinjaman.pencairan.batal', $r) }}"
                                            onsubmit="return confirmBatal()">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-md border border-red-200 bg-red-50 px-2 py-1 text-xs text-red-700 hover:bg-red-100 transition">
                                                Batal
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="inline-flex items-center justify-center rounded-md border border-gray-200 bg-gray-100 px-2 py-1 text-xs text-gray-400 cursor-not-allowed">
                                        Batal
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                Belum ada riwayat pencairan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>

            {{-- PAGINATION --}}
            @if($riwayatPencairan->hasPages())
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-2">
                    <p class="text-sm text-gray-600">
                        Menampilkan
                        <span class="font-semibold text-gray-900">{{ $riwayatPencairan->firstItem() ?? 0 }}</span>
                        sampai
                        <span class="font-semibold text-gray-900">{{ $riwayatPencairan->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-semibold text-gray-900">{{ $riwayatPencairan->total() }}</span>
                        data
                    </p>

                    <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                        {{ $riwayatPencairan->links('vendor.pagination.custom') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
    function confirmCairkan() {
        return confirm('Yakin ingin mencairkan pinjaman ini?');
    }

    function closeFlashMessage() {
        const el = document.getElementById('flash-message');
        if (el) {
            el.style.transition = 'opacity 0.3s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        }
    }

    // auto hide flash
    const flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 300);
        }, 5000);
    }

    function confirmBatal() {
        return confirm('Yakin ingin membatalkan pencairan ini?');
    }
</script>
@endsection
