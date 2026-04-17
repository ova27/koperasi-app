<div class="rounded-2xl border border-slate-200 bg-gradient-to-r from-slate-50 via-white to-sky-50/60 p-2 shadow-sm">
    <nav class="flex flex-wrap gap-2" aria-label="Tabs Master Data">
        @can('manage users')
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-blue-600 to-sky-500 text-white shadow-md ring-1 ring-blue-300' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-slate-800 hover:ring-slate-300' }}">
                Data Pengguna
            </a>
        @endcan

        @can('view anggota list')
            <a href="{{ route('admin.anggota.index') }}"
                class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.anggota.*') ? 'bg-gradient-to-r from-blue-600 to-sky-500 text-white shadow-md ring-1 ring-blue-300' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-slate-800 hover:ring-slate-300' }}">
                Data Anggota
            </a>
        @endcan
    </nav>
</div>
