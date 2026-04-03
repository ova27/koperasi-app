<div class="text-xs text-gray-500 px-3 py-2 border rounded mb-3">

    {{-- HEADER --}}
    <div class="mb-1 text-[11px] text-gray-400">
        Pinjaman Aktif Anggota
    </div>

    {{-- DATA RINGKAS --}}
    <div class="flex flex-wrap gap-x-4 gap-y-1 text-gray-600">

        <span>
            <span class="text-gray-400">Total:</span>
            Rp {{ number_format($pinjaman->jumlah_pinjaman ?? $pinjaman->jumlah_disetujui ?? 0, 0, ',', '.') }}
        </span>

        <span>
            <span class="text-gray-400">Sisa:</span>
            <span class="text-red-500">
                Rp {{ number_format($pinjaman->sisa_pinjaman ?? 0, 0, ',', '.') }}
            </span>
        </span>

        <span>
            <span class="text-gray-400">Tenor:</span>
            {{ $pinjaman->tenor ?? '-' }} bln
        </span>

        <span>
            <span class="text-gray-400">Status:</span>
            {{ $pinjaman->status ?? '-' }}
        </span>

    </div>

    {{-- PROGRESS MINI --}}
    @php
        $total = $pinjaman->jumlah_pinjaman ?? $pinjaman->jumlah_disetujui ?? 0;
        $dibayar = $pinjaman->total_dibayar ?? 0;
        $progress = $total > 0 ? min(100, ($dibayar / $total) * 100) : 0;
    @endphp

    <div class="mt-2">
        <div class="w-full bg-gray-200 rounded-full h-1">
            <div class="bg-gray-400 h-1 rounded-full" style="width: {{ $progress }}%"></div>
        </div>
    </div>

</div>