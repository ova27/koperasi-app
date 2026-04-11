<div class="border-b border-gray-200">
    <nav class="-mb-px flex flex-wrap sm:gap-4" aria-label="Tabs Pinjaman Saya">
        @can('view pinjaman saya')
            <a href="{{ route('anggota.pinjaman.index') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('anggota.pinjaman.index') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Pinjaman Saya
            </a>
        @endcan

        @can('create pinjaman')
            <a href="{{ route('anggota.pinjaman.ajukan') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('anggota.pinjaman.ajukan') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Form Pengajuan Pinjaman
            </a>
        @endcan
    </nav>
</div>
