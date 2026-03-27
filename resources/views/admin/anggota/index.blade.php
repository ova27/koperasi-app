@extends('layouts.main')

@section('title', 'Daftar Anggota')
@section('page-title', 'Daftar Anggota')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-">
        <form id="form-search-anggota" method="GET" action="{{ route('admin.anggota.index') }}" class="w-full sm:w-80">
            <label for="search" class="sr-only">Cari anggota</label>

            <input type="hidden" name="sort" value="{{ request('sort', 'nama') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">

            <div class="relative">
                <input
                    id="search"
                    name="search"
                    type="text"
                    value="{{ old('search', $search ?? '') }}"
                    placeholder="Cari nama, jabatan, atau status..."
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500"
                >
                <button
                    id="clear-search"
                    type="button"
                    class="hidden absolute right-2 top-1/2 -translate-y-1/2 rounded-md bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-200"
                    aria-label="Hapus pencarian"
                >
                    ×
                </button>
            </div>
        </form>
    </div>

    {{-- TABEL --}}
    <div class="overflow-x-auto bg-white border rounded-lg">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="px-4 py-3 text-left w-12">No</th>

                    @php
                        $isNama = ($sort ?? 'nama') === 'nama';
                        $isJabatan = ($sort ?? '') === 'jabatan';
                        $isStatus = ($sort ?? '') === 'status';
                        $isTanggalMasuk = ($sort ?? '') === 'tanggal_masuk';
                        $d = $direction ?? 'asc';
                    @endphp

                    <th class="px-4 py-3 text-left">
                        <a href="{{ route('admin.anggota.index', array_merge(request()->except('page'), ['sort' => 'nama', 'direction' => $isNama && $d === 'asc' ? 'desc' : 'asc'])) }}"
                           class="inline-flex items-center gap-1 hover:text-blue-600">
                            Nama
                            @if ($isNama)
                                <span>{{ $d === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>

                    <th class="px-4 py-3 text-center">
                        <a href="{{ route('admin.anggota.index', array_merge(request()->except('page'), ['sort' => 'jabatan', 'direction' => $isJabatan && $d === 'asc' ? 'desc' : 'asc'])) }}"
                           class="inline-flex items-center gap-1 hover:text-blue-600">
                            Jabatan
                            @if ($isJabatan)
                                <span>{{ $d === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>

                    <th class="px-4 py-3 text-center">
                        <a href="{{ route('admin.anggota.index', array_merge(request()->except('page'), ['sort' => 'status', 'direction' => $isStatus && $d === 'asc' ? 'desc' : 'asc'])) }}"
                           class="inline-flex items-center gap-1 hover:text-blue-600">
                            Status
                            @if ($isStatus)
                                <span>{{ $d === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>

                    @can('edit anggota')
                    <th class="px-4 py-3 text-center w-40">Aksi</th>
                    @endcan
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($anggotas as $anggota)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- NO --}}
                        <td class="px-4 py-3 text-gray-500">
                            {{ $anggotas->firstItem() + $loop->index }}
                        </td>

                        {{-- NAMA --}}
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">
                                {{ $anggota->nama }}
                            </div>
                        </td>

                        {{-- NOMOR --}}
                        <td class="px-4 py-3 text-center text-gray-700">
                            {{ $anggota->jabatan ?? '-' }}
                        </td>

                        {{-- STATUS --}}
                        <td class="px-4 py-3 text-center">
                            {{-- versi component --}}
                            <x-status-anggota :status="$anggota->status" />
                        </td>

                        {{-- AKSI --}}
                        @can('edit anggota')
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-3 text-sm">

                                {{-- DETAIL --}}
                                @can('view anggota list')
                                    <button
                                        type="button"
                                        title="Detail"
                                        class="btn-detail inline-flex items-center justify-center rounded-md border border-blue-200 bg-blue-50 p-1 text-xs text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                        data-url="{{ route('admin.anggota.show', $anggota) }}">
                                        <span aria-hidden="true">👁</span>
                                    </button>
                                @endcan


                                {{-- EDIT --}}
                                @can('edit anggota')
                                    <button
                                        type="button"
                                        title="Edit"
                                        class="btn-edit inline-flex items-center justify-center rounded-md border border-amber-200 bg-amber-50 p-1 text-xs text-amber-700 transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-400"
                                        data-url="{{ route('admin.anggota.edit', $anggota) }}">
                                        <span aria-hidden="true">✏️</span>
                                    </button>
                                @endcan
                            </div>
                        </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can('edit anggota') ? '5' : '4' }}"
                            class="px-4 py-10 text-center text-gray-500">
                            <div class="font-medium">
                                Belum ada data anggota
                            </div>
                            <div class="text-xs mt-1">
                                Data anggota akan muncul di sini
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-2 py-3">
        <p class="text-sm text-gray-600">
            Menampilkan
            <span class="font-semibold text-gray-900">{{ $anggotas->firstItem() ?? 0 }}</span>
            sampai
            <span class="font-semibold text-gray-900">{{ $anggotas->lastItem() ?? 0 }}</span>
            dari
            <span class="font-semibold text-gray-900">{{ $anggotas->total() }}</span>
            anggota
        </p>

        <div>
            {{ $anggotas->links() }}
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('search');
        const form = document.getElementById('form-search-anggota');
        const clearBtn = document.getElementById('clear-search');

        if (!input || !form || !clearBtn) return;

        const updateClearButton = () => {
            if (input.value.trim().length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        };

        updateClearButton();

        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            updateClearButton();

            timer = setTimeout(function () {
                form.submit();
            }, 300); // 300ms debounce
        });

        clearBtn.addEventListener('click', function () {
            input.value = '';
            updateClearButton();
            form.submit();
        });
    });
</script>
@endsection
