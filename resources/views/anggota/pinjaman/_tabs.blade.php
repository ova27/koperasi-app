<div class="finance-section-tabs flex gap-6 border-b border-slate-200">
    @can('view pinjaman saya')
        <a href="{{ route('anggota.pinjaman.index') }}"
            class="inline-flex items-center gap-2 border-b-2 px-1 pb-3 pt-1 text-sm font-medium transition-colors {{ request()->routeIs('anggota.pinjaman.index') ? 'border-slate-800 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Pinjaman Saya
        </a>
    @endcan

    @can('create pinjaman')
        <a href="{{ route('anggota.pinjaman.ajukan') }}"
            class="inline-flex items-center gap-2 border-b-2 px-1 pb-3 pt-1 text-sm font-medium transition-colors {{ request()->routeIs('anggota.pinjaman.ajukan') ? 'border-slate-800 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Form Pengajuan
        </a>
    @endcan
</div>
