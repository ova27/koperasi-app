<div class="space-y-4 text-sm">

    {{-- HEADER --}}
    <div class="border-b pb-2">
        <h4 class="font-semibold text-gray-800">
            Detail Pinjaman Aktif
        </h4>
        <p class="text-xs text-gray-500">
            Informasi pinjaman yang sedang berjalan
        </p>
    </div>

    {{-- INFO --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

        <div class="bg-white border rounded p-3">
            <div class="text-xs text-gray-500">Anggota</div>
            <div class="font-semibold">
                {{ optional($pinjaman->anggota)->nama ?? '-' }}
            </div>
        </div>

        <div class="bg-white border rounded p-3">
            <div class="text-xs text-gray-500">Status</div>
            <div class="font-semibold capitalize">
                {{ $pinjaman->status ?? '-' }}
            </div>
        </div>

        <div class="bg-white border rounded p-3">
            <div class="text-xs text-gray-500">Tanggal Cair</div>
            <div class="font-semibold">
                {{ $pinjaman->tanggal_cair 
                    ? \Carbon\Carbon::parse($pinjaman->tanggal_cair)->format('d/m/Y') 
                    : '-' }}
            </div>
        </div>

    </div>

    {{-- RINGKASAN --}}
    <div class="bg-gray-50 rounded-lg p-3 space-y-2 text-xs">

        <div class="flex justify-between">
            <span>Total Pinjaman</span>
            <span class="font-semibold">
                Rp {{ number_format($pinjaman->jumlah_pinjaman ?? $pinjaman->jumlah_disetujui ?? 0, 0, ',', '.') }}
            </span>
        </div>

        <div class="flex justify-between">
            <span>Tenor</span>
            <span class="font-semibold">
                {{ $pengajuan->tenor ?? '-' }} Bulan
            </span>
        </div>

        <div class="flex justify-between">
            <span>Sudah Dibayar</span>
            <span class="font-semibold text-green-600">
                Rp {{ number_format($pinjaman->total_dibayar ?? 0, 0, ',', '.') }}
            </span>
        </div>

        <div class="flex justify-between">
            <span>Sisa Pinjaman</span>
            <span class="font-semibold text-red-600">
                Rp {{ number_format($pinjaman->sisa_pinjaman ?? 0, 0, ',', '.') }}
            </span>
        </div>

    </div>

    {{-- PROGRESS --}}
    @php
        $total = $pinjaman->jumlah_pinjaman ?? $pinjaman->jumlah_disetujui ?? 0;
        $dibayar = $pinjaman->total_dibayar ?? 0;
        $progress = $total > 0 ? min(100, ($dibayar / $total) * 100) : 0;
    @endphp

    <div>
        <div class="flex justify-between text-xs mb-1">
            <span>Progress Pembayaran</span>
            <span>{{ number_format($progress, 0) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
        </div>
    </div>

</div>