<div class="flex gap-2">
    @can('view pinjaman saya')
        <a href="{{ route('anggota.pinjaman.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('anggota.pinjaman.index') ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Pinjaman Saya
        </a>
    @endcan

    @can('create pinjaman')
        <a href="{{ route('anggota.pinjaman.ajukan') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('anggota.pinjaman.ajukan') ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-gray-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Form Pengajuan
        </a>
    @endcan
</div>
