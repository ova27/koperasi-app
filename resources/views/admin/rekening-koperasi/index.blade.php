@extends('layouts.main')

@section('title', 'Master Rekening Koperasi')
@section('page-title', 'Master Data')

@section('content')
<div class="space-y-3">
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-4">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Master Rekening Koperasi</h2>
                <p class="text-sm text-gray-500">Kelola daftar rekening bank koperasi.</p>
            </div>
            <a href="{{ route('admin.master.rekening-koperasi.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                Tambah Rekening
            </a>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-4">
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">Cari Rekening</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Contoh: BRI / 1234567890 / Nama Pemilik"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                    <option value="">Semua</option>
                    <option value="1" @selected($status === '1')>Aktif</option>
                    <option value="0" @selected($status === '0')>Nonaktif</option>
                </select>
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium bg-slate-700 text-white hover:bg-slate-800 transition-colors duration-150">
                    Tampilkan
                </button>
                <a href="{{ route('admin.master.rekening-koperasi.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors duration-150">
                    Reset
                </a>
            </div>
        </form>

        <div class="hidden md:block rounded-lg border border-gray-100 overflow-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gradient-to-r from-blue-50 to-blue-100 border-b-2 border-blue-300">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">Nama Bank</th>
                        <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">No Rekening</th>
                        <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">Nama Pemilik</th>
                        <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">Status</th>
                        <th class="px-3 py-2 text-right font-semibold text-xs text-blue-900 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rekeningKoperasis as $rekening)
                        <tr class="even:bg-blue-50 hover:bg-blue-100 transition-all duration-200">
                            <td class="px-3 py-2 text-xs font-medium text-gray-800">{{ $rekening->nama }}</td>
                            <td class="px-3 py-2 text-xs text-gray-700">{{ $rekening->nomor_rekening }}</td>
                            <td class="px-3 py-2 text-xs text-gray-700">{{ $rekening->nama_pemilik ?? '-' }}</td>
                            <td class="px-3 py-2 text-xs">
                                <span class="inline-flex px-2 py-1 rounded-full {{ $rekening->aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $rekening->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.master.rekening-koperasi.edit', $rekening) }}" class="px-3 py-1 rounded-md text-xs font-medium bg-blue-600 text-white hover:bg-blue-700">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.master.rekening-koperasi.destroy', $rekening) }}" onsubmit="return confirm('Hapus rekening ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 rounded-md text-xs font-medium bg-red-600 text-white hover:bg-red-700">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-gray-500">Belum ada data rekening koperasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-2.5">
            @forelse($rekeningKoperasis as $rekening)
                <div class="bg-gradient-to-r from-blue-50 to-white border border-blue-100 rounded-lg p-3.5 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs font-semibold text-gray-800">{{ $rekening->nama }}</div>
                            <div class="text-xs text-gray-600">{{ $rekening->nomor_rekening }}</div>
                            <div class="text-xs text-gray-500">{{ $rekening->nama_pemilik ?? '-' }}</div>
                        </div>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs {{ $rekening->aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $rekening->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="mt-2 flex gap-2">
                        <a href="{{ route('admin.master.rekening-koperasi.edit', $rekening) }}" class="flex-1 px-3 py-1.5 rounded-md text-xs font-medium bg-blue-600 text-white text-center hover:bg-blue-700">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.master.rekening-koperasi.destroy', $rekening) }}" class="flex-1" onsubmit="return confirm('Hapus rekening ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-1.5 rounded-md text-xs font-medium bg-red-600 text-white hover:bg-red-700">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-400 text-sm">
                    Belum ada data rekening koperasi.
                </div>
            @endforelse
        </div>

        @if($rekeningKoperasis->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-200">
                {{ $rekeningKoperasis->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>
@endsection
