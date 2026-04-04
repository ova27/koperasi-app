@extends('layouts.main')

@section('title', 'Data Pengguna')
@section('page-title', 'Data Pengguna')
    
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form id="form-search-user" method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:w-80">
            <label for="search" class="sr-only">Cari pengguna</label>

            <div class="relative">
                <input
                    id="search"
                    name="search"
                    type="text"
                    value="{{ old('search', $search ?? '') }}"
                    placeholder="Cari nama atau email..."
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

        @can('manage users')
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                + Tambah Pengguna
            </a>
        @endcan
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

    {{-- TABEL USERS --}}
    <div class="overflow-x-auto bg-white border rounded-lg">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="px-4 py-3 text-left w-12">No</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-center">Role</th>
                    @can('manage users')
                    <th class="px-4 py-3 text-center w-20">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-wrap justify-center gap-2">
                                @foreach($user->roles as $role)
                                    @if($role->name === 'admin')
                                        <span class="inline-block px-2.5 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @elseif($role->name === 'ketua')
                                        <span class="inline-block px-2.5 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @elseif($role->name === 'bendahara')
                                        <span class="inline-block px-2.5 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-full">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        @can('manage users')
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-3 text-sm">
                                <button
                                    type="button"
                                    title="Edit"
                                    class="btn-edit inline-flex items-center justify-center rounded-md border border-amber-200 bg-amber-50 p-1 text-xs text-amber-700 transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-400"
                                    data-url="{{ route('admin.users.edit', $user) }}">
                                    <span aria-hidden="true">✏️</span>
                                </button>

                                {{-- DELETE BUTTON --}}
                                <form action="{{ route('admin.users.destroy', $user) }}" 
                                    method="POST" 
                                    style="display: inline;"
                                    onsubmit="return confirm('Yakin ingin menghapus pengguna {{ $user->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        title="Hapus"
                                        class="inline-flex items-center justify-center rounded-md border border-red-200 bg-red-50 p-1 text-xs text-red-700 transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-400">
                                        <span aria-hidden="true">🗑️</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can('manage users') ? '5' : '4' }}"
                            class="px-4 py-10 text-center text-gray-500">
                            <div class="font-medium">
                                Belum ada pengguna
                            </div>
                            <div class="text-xs mt-1">
                                Data pengguna akan muncul di sini
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
            <span class="font-semibold text-gray-900">{{ $users->firstItem() ?? 0 }}</span>
            sampai
            <span class="font-semibold text-gray-900">{{ $users->lastItem() ?? 0 }}</span>
            dari
            <span class="font-semibold text-gray-900">{{ $users->total() }}</span>
            data
        </p>

        <div class="flex justify-center sm:justify-end w-full sm:w-auto">
            {{ $users->links('vendor.pagination.custom') }}
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const input = document.getElementById('search');
        const form = document.getElementById('form-search-user');
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
        
        if (input && form && clearBtn) {
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
        }
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