@extends('layouts.main')

@section('title', 'Data Anggota')
@section('page-title', 'Data Anggota')

@section('content')
<div class="space-y-6">
    @php
        $canSeeAksiColumn = auth()->user()->can('view anggota list');
    @endphp

    @can('manage users')
        @include('admin.master-data._tabs')
    @endcan

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
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

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div id="flash-message" class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" 
                    onclick="closeFlashMessage()"
                    class="text-green-700 hover:text-green-900 ml-4">
                ×
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="flash-message" class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" 
                    onclick="closeFlashMessage()"
                    class="text-red-700 hover:text-red-900 ml-4">
                ×
            </button>
        </div>
    @endif

    {{-- TABEL --}}
    <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-gradient-to-r from-blue-50 to-blue-100 border-b-2 border-blue-300">
                <tr class="text-blue-900">
                    <th class="px-4 py-2 text-left w-12 font-semibold text-xs uppercase tracking-widest">No</th>

                    @php
                        $isNama = ($sort ?? 'nama') === 'nama';
                        $isJabatan = ($sort ?? '') === 'jabatan';
                        $isStatus = ($sort ?? '') === 'status';
                        $isTanggalMasuk = ($sort ?? '') === 'tanggal_masuk';
                        $d = $direction ?? 'asc';
                    @endphp

                    <th class="px-4 py-2 text-left font-semibold text-xs uppercase tracking-widest">
                        <a href="{{ route('admin.anggota.index', array_merge(request()->except('page'), ['sort' => 'nama', 'direction' => $isNama && $d === 'asc' ? 'desc' : 'asc'])) }}"
                        class="inline-flex items-center gap-1 hover:text-blue-600 transition-colors">
                            Nama
                            @if ($isNama)
                                <span>{{ $d === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>

                    <th class="px-4 py-2 text-center font-semibold text-xs uppercase tracking-widest">
                        <a href="{{ route('admin.anggota.index', array_merge(request()->except('page'), ['sort' => 'jabatan', 'direction' => $isJabatan && $d === 'asc' ? 'desc' : 'asc'])) }}"
                        class="inline-flex items-center gap-1 hover:text-orange-600 transition-colors">
                            Jabatan
                            @if ($isJabatan)
                                <span>{{ $d === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>

                    <th class="px-4 py-2 text-center font-semibold text-xs uppercase tracking-widest">
                        <a href="{{ route('admin.anggota.index', array_merge(request()->except('page'), ['sort' => 'status', 'direction' => $isStatus && $d === 'asc' ? 'desc' : 'asc'])) }}"
                        class="inline-flex items-center gap-1 hover:text-orange-600 transition-colors">
                            Status
                            @if ($isStatus)
                                <span>{{ $d === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>

                    @if($canSeeAksiColumn)
                    <th class="px-4 py-2 text-center w-28 font-semibold text-xs uppercase tracking-widest">Aksi</th>
                    @endif
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($anggotas as $anggota)
                    <tr class="@if($loop->odd) bg-white @else bg-blue-50 @endif hover:bg-blue-100 transition-all duration-300 group">
                        {{-- NO --}}
                        <td class="px-4 py-1.5 text-gray-500">
                            {{ $anggotas->firstItem() + $loop->index }}
                        </td>

                        {{-- NAMA --}}
                        <td class="px-4 py-1.5">
                            <div class="font-medium text-gray-800">
                                {{ $anggota->nama }}
                            </div>
                        </td>

                        {{-- NOMOR --}}
                        <td class="px-4 py-1.5 text-center text-gray-700">
                            {{ $anggota->jabatan ?? '-' }}
                        </td>

                        {{-- STATUS --}}
                        <td class="px-4 py-1.5 text-center">
                        <x-status-anggota 
                            :status="$anggota->status"
                            :alasan="$alasanKeluarMap[$anggota->id] ?? null"/>
                        </td>

                        {{-- AKSI --}}
                        @if($canSeeAksiColumn)
                        <td class="px-4 py-1.5 text-center">
                            <a
                                href="{{ route('admin.anggota.show', $anggota) }}"
                                title="Detail Lengkap"
                                class="inline-flex items-center rounded-md border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 transition-colors duration-150 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-300"
                            >
                                Detail
                            </a>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canSeeAksiColumn ? '5' : '4' }}"
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
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-2">
        <p class="text-sm text-gray-600">
            Menampilkan
            <span class="font-semibold text-gray-900">{{ $anggotas->firstItem() ?? 0 }}</span>
            sampai
            <span class="font-semibold text-gray-900">{{ $anggotas->lastItem() ?? 0 }}</span>
            dari
            <span class="font-semibold text-gray-900">{{ $anggotas->total() }}</span>
            data
        </p>

        <div class="flex justify-center sm:justify-end w-full sm:w-auto">
            {{ $anggotas->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('search');
        const form = document.getElementById('form-search-anggota');
        const clearBtn = document.getElementById('clear-search');
        const flashMessage = document.getElementById('flash-message');
        
        if (flashMessage) {
            // Auto close setelah 5 detik (5000 ms)
            setTimeout(function() {
                flashMessage.style.transition = 'opacity 0.3s ease-out';
                flashMessage.style.opacity = '0';
                setTimeout(function() {
                    flashMessage.remove();
                }, 300);
            }, 5000);
        }

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
            }, 10); // 10ms debounce
        });

        clearBtn.addEventListener('click', function () {
            input.value = '';
            updateClearButton();
            form.submit();
        });
    });
    function closeFlashMessage() {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            flashMessage.style.transition = 'opacity 0.3s ease-out';
            flashMessage.style.opacity = '0';
            setTimeout(function() {
                flashMessage.remove();
            }, 300);
        }
    }
</script>
@endsection
