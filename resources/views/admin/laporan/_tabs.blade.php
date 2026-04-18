<div class="border-b border-gray-200">
    <nav class="flex flex-wrap sm:gap-6" aria-label="Tabs Laporan Bulanan">
        @can('view laporan arus kas')
            <a href="{{ route('admin.keuangan.arus-kas') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('admin.keuangan.arus-kas') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Rekap Arus Kas
            </a>
        @endcan

        @can('view laporan simpanan bulanan')
                <a href="{{ route('admin.laporan.simpanan-bulanan') }}"
                    class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('admin.laporan.simpanan-bulanan') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Laporan Simpanan Bulanan
            </a>
        @endcan

        @can('view laporan pinjaman')
            <a href="{{ route('admin.laporan.pinjaman.index') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('admin.laporan.pinjaman.index') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Laporan Pinjaman
            </a>
        @endcan
    </nav>
</div>
