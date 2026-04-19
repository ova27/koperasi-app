<div class="border-b border-gray-200">
    <nav class="-mb-px flex flex-wrap gap-2 sm:gap-4" aria-label="Tabs Keuangan">
        
        @can('view arus koperasi')
            <a href="{{ route('admin.keuangan.arus.koperasi') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('admin.keuangan.arus.koperasi') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Transaksi Koperasi
            </a>
        @endcan

        @can('view arus operasional')
            <a href="{{ route('admin.keuangan.arus.operasional') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('admin.keuangan.arus.operasional') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Transaksi Operasional
            </a>
        @endcan

        @can('view saldo')
            <a href="{{ route('admin.keuangan.saldo') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('admin.keuangan.saldo') ? 'border-indigo-700 text-indigo-700' : 'border-transparent text-indigo-700 hover:border-indigo-500 hover:text-indigo-700' }}">
                Neraca Saldo
            </a>
        @endcan
    </nav>
</div>
