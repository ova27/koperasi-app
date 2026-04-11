<div class="border-b border-gray-200">
    <nav class="-mb-px flex flex-wrap gap-2 sm:gap-4" aria-label="Tabs Master Data">
        @can('manage users')
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Data Pengguna
            </a>
        @endcan

        @can('view anggota list')
            <a href="{{ route('admin.anggota.index') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('admin.anggota.*') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Data Anggota
            </a>
        @endcan
    </nav>
</div>
