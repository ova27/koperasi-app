{{-- MODAL HEADER --}}
<div class="flex items-start justify-between px-6 pt-6 pb-4 border-b border-slate-100">
    <div class="flex items-center gap-4 min-w-0">
        {{-- AVATAR --}}
        <div class="flex-shrink-0 inline-flex h-12 w-12 items-center justify-center rounded-full
                    bg-gradient-to-br from-sky-500 to-blue-600 text-base font-bold text-white shadow-md">
            {{ strtoupper(substr($anggota->nama, 0, 1)) }}
        </div>
        <div class="min-w-0">
            <h2 id="modal-title" class="text-lg font-bold text-slate-800 truncate">
                {{ $anggota->nama }}
            </h2>
            <div class="mt-1 flex items-center gap-2 flex-wrap">
                <x-status-anggota
                    :status="$anggota->status"
                    :alasan="$alasanKeluarMap[$anggota->id] ?? null"/>
                @if($anggota->jabatan)
                    <span class="text-xs text-slate-500">{{ $anggota->jabatan }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- TOMBOL TUTUP --}}
    <button type="button"
            onclick="closeModal()"
            class="flex-shrink-0 ml-4 inline-flex items-center justify-center h-8 w-8 rounded-full
                   text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors duration-150
                   focus:outline-none focus:ring-2 focus:ring-slate-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

{{-- MODAL BODY --}}
<div class="px-6 py-5 space-y-4 max-h-[65vh] overflow-y-auto">

    {{-- PROFIL SINGKAT --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
            <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400 mb-0.5">Email</p>
            <p class="text-sm font-semibold text-slate-700 break-all">{{ $anggota->user->email ?? '-' }}</p>
        </div>
        <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
            <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400 mb-0.5">NIP</p>
            <p class="text-sm font-semibold text-slate-700">{{ $anggota->nip ?? '-' }}</p>
        </div>
        <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
            @if($anggota->status !== 'aktif' && $anggota->tanggal_keluar)
                <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400 mb-0.5">Tanggal Keluar</p>
                <p class="text-sm font-semibold text-slate-700">
                    {{ \Carbon\Carbon::parse($anggota->tanggal_keluar)->translatedFormat('d M Y') }}
                </p>
            @else
                <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400 mb-0.5">Tanggal Masuk</p>
                <p class="text-sm font-semibold text-slate-700">
                    {{ \Carbon\Carbon::parse($anggota->tanggal_masuk)->translatedFormat('d M Y') }}
                </p>
            @endif
        </div>
    </div>

    @if($canViewFullDetails)
    {{-- RINGKASAN SIMPANAN --}}
    <div class="rounded-xl border border-slate-100 overflow-hidden">
        <div class="flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-slate-100">
            <div class="flex-shrink-0 h-7 w-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-slate-700">Ringkasan Simpanan</h3>
        </div>
        <div class="px-4 py-3 divide-y divide-slate-50 bg-white">
            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-slate-500">Pokok</span>
                <span class="text-sm font-medium text-slate-700">Rp {{ number_format($pokok, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-slate-500">Wajib</span>
                <span class="text-sm font-medium text-slate-700">Rp {{ number_format($wajib, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-slate-500">Sukarela</span>
                <span class="text-sm font-medium text-slate-700">Rp {{ number_format($sukarela, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-2.5 bg-emerald-50 -mx-4 px-4 mt-1 rounded-b-xl">
                <span class="text-sm font-bold text-emerald-700">Total</span>
                <span class="text-sm font-bold text-emerald-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- RINGKASAN PINJAMAN --}}
    <div class="rounded-xl border border-slate-100 overflow-hidden">
        <div class="flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-slate-100">
            <div class="flex-shrink-0 h-7 w-7 rounded-lg bg-orange-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-slate-700">Ringkasan Pinjaman</h3>
        </div>
        <div class="px-4 py-3 divide-y divide-slate-50 bg-white">
            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-slate-500">Pinjaman Aktif</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                             {{ $ringkasanPinjaman['aktif'] > 0 ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $ringkasanPinjaman['aktif'] }}
                </span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-slate-500">Pinjaman Lunas</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                    {{ $ringkasanPinjaman['lunas'] }}
                </span>
            </div>
            <div class="flex justify-between items-center py-2.5 bg-orange-50 -mx-4 px-4 mt-1 rounded-b-xl">
                <span class="text-sm font-bold text-orange-700">Sisa Pinjaman</span>
                <span class="text-sm font-bold text-orange-700">Rp {{ number_format($ringkasanPinjaman['sisa'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- MODAL FOOTER --}}
<div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl">
    <a href="{{ route('admin.anggota.show', $anggota) }}"
       class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        Lihat Detail Lengkap
    </a>
    <button type="button"
            onclick="closeModal()"
            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2
                   text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors
                   focus:outline-none focus:ring-2 focus:ring-slate-400">
        Tutup
    </button>
</div>
