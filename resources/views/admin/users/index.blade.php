@extends('layouts.main')

@section('title', 'Master Data')
@section('page-title', 'Master Data')
    
@section('content')
<div class="space-y-6">
    @include('admin.master-data._tabs')

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
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
            <button
                onclick="openCreateUserModal()"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 whitespace-nowrap">
                + Tambah Akun
            </button>
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
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="border-b-2 border-blue-300 bg-gradient-to-r from-blue-50 to-blue-100">
                <tr class="text-blue-900">
                    <th class="w-12 px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest">No</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest">Nama</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest">Email</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-widest">Role</th>
                    @can('manage users')
                    <th class="w-36 px-4 py-2 text-center text-xs font-semibold uppercase tracking-widest">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $user)
                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-blue-50' }} group transition-all duration-300 hover:bg-blue-100">
                        <td class="px-4 py-1.5 text-gray-500">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-4 py-1.5">
                            <div class="font-medium text-gray-800">
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="px-4 py-1.5 text-gray-700">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 py-1.5 text-center">
                            <div class="flex flex-wrap justify-center gap-2">
                                @foreach($user->roles as $role)
                                    @if($role->name === 'admin')
                                        <span class="inline-flex items-center rounded-full border border-red-200 bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @elseif($role->name === 'ketua')
                                        <span class="inline-flex items-center rounded-full border border-violet-200 bg-violet-100 px-2.5 py-1 text-xs font-medium text-violet-700">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @elseif($role->name === 'bendahara')
                                        <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-blue-200 bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        @can('manage users')
                        <td class="px-4 py-1.5 text-center">
                            <div class="flex justify-center gap-2 text-sm">
                                <button
                                    type="button"
                                    title="Edit"
                                    class="btn-edit inline-flex items-center rounded-md border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 transition-colors duration-150 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-300"
                                    data-url="{{ route('admin.users.edit', $user) }}">
                                    Ubah
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
                                        class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 transition-colors duration-150 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-300">
                                        Hapus
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

    {{-- MODAL CREATE USER --}}
    <div id="createUserModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="create-user-modal-title">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCreateUserModal()"></div>

        <div id="modalContent" class="relative w-full max-w-5xl max-h-[90vh] overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <div class="flex items-start justify-between border-b border-slate-100 px-6 pb-4 pt-6">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-blue-600 text-base font-bold text-white shadow-md">
                        +
                    </div>
                    <div class="min-w-0">
                        <h2 id="create-user-modal-title" class="truncate text-lg font-bold text-slate-800">Tambah Akun</h2>
                        <p class="truncate text-sm text-slate-500">Data ini akan dipakai untuk membuat akun login dan profil anggota</p>
                    </div>
                </div>
                <button onclick="closeCreateUserModal()" class="ml-4 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-slate-400 transition-colors duration-150 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-0" autocomplete="off">
                @csrf

                <div class="max-h-[70vh] space-y-5 overflow-y-auto px-6 py-5">
                    
                    {{-- ROW 1 --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Lengkap</label>
                            <input type="text" name="name" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                            <input type="email" name="email" value="" autocomplete="new-password" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" placeholder="Masukkan email" required>
                        </div>
                    </div>

                    {{-- ROW 2 --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">NIP</label>
                            <input type="text" name="anggota_nip" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" placeholder="Masukkan NIP" required>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Jabatan</label>
                            <input type="text" name="jabatan" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" placeholder="Masukkan jabatan" required>
                        </div>
                    </div>

                    {{-- ROW 3 --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" required>
                                <option value="">Pilih jenis kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status Anggota</label>
                            <select name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" required>
                                <option value="aktif" selected>Aktif</option>
                                <option value="cuti">Cuti</option>
                                <option value="tugas_belajar">Tugas Belajar</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    {{-- ROW 4 --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" value="{{ now()->toDateString() }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" required>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Password</label>
                            <input type="password" name="password" value="" autocomplete="new-password" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" placeholder="Masukkan password" required>
                        </div>
                    </div>

                    {{-- REKENING --}}
                    <div>
                        <div class="grid grid-cols-1 gap-4 rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Bank</label>
                                <input type="text" name="nama_bank" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" placeholder="Contoh: BANK BRI">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nomor Rekening</label>
                                <input type="text" name="nomor_rekening" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" placeholder="Masukkan nomor rekening">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Pemilik</label>
                                <input type="text" name="nama_pemilik" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" placeholder="Nama pemilik rekening">
                            </div>
                        </div>
                    </div>

                    {{-- ROLE SELECTION --}}
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Role Pengguna</label>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach($roles as $role)
                                <label class="group flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 transition hover:border-sky-300 hover:bg-sky-50/60">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" data-create-role="{{ $role->name }}" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm font-semibold text-slate-700">{{ ucfirst($role->name) }}</span>
                                        <span class="block text-xs text-slate-400">
                                            {{ $role->name === 'admin' ? 'Akses penuh untuk pengelolaan sistem.' : ($role->name === 'ketua' ? 'Fokus pada persetujuan dan kontrol data.' : ($role->name === 'bendahara' ? 'Mengelola transaksi dan keuangan.' : 'Akses area anggota dan profil pribadi.')) }}
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">
                    <button type="button" onclick="closeCreateUserModal()" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-6 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT MODAL CREATE USER --}}
<script>
    function openCreateUserModal() {
        const modal = document.getElementById('createUserModal');
        const modalContent = document.getElementById('modalContent');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        // Trigger animation
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10); // Small delay to ensure display change
        
        const form = document.querySelector('#createUserModal form');
        form?.querySelector('input[name="email"]')?.setAttribute('value', '');
        form?.querySelector('input[name="password"]')?.setAttribute('value', '');
        if (form?.email) form.email.value = '';
        if (form?.password) form.password.value = '';
    }

    function closeCreateUserModal() {
        const modal = document.getElementById('createUserModal');
        const modalContent = document.getElementById('modalContent');
        
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        // Wait for animation to complete
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300); // Match transition duration
    }
</script>

{{-- SCRIPT SEARCH --}}
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
