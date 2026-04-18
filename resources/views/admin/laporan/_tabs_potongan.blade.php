@can('export laporan pinjaman')
<div class="border-b border-gray-200">
    <nav class="flex flex-wrap sm:gap-6" aria-label="Tabs Potongan Bulanan">
            <a href="{{ route('admin.laporan.potongan-bulanan.index') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('admin.laporan.potongan-bulanan.index') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Rincian Potongan Anggota
            </a>

            <a href="{{ route('admin.laporan.potongan-bulanan.bank.index') }}"
                class="inline-flex items-center border-b-2 px-1 py-1 text-sm font-medium transition {{ request()->routeIs('admin.laporan.potongan-bulanan.bank.*') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Administrasi Bank
            </a>
    </nav>
</div>
@endcan
