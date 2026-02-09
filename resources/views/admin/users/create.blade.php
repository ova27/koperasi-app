@extends('layouts.main')
@section('title', 'Tambah Pengguna')
@section('content')
<div class="max-w-xl mx-auto">

    <h1 class="text-xl font-semibold mb-6">
        Tambah Pengguna
    </h1>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4" autocomplete="off">
        @csrf

        {{-- NAMA --}}
        <div>
            <label class="text-sm text-gray-600">Nama</label>
            <input
                type="text"
                name="name"
                autocomplete="off"
                value=""
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        {{-- EMAIL --}}
        <div>
            <label class="text-sm text-gray-600">Email</label>
            <input
                type="email"
                name="email"
                autocomplete="new-password"
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        {{-- PASSWORD --}}
        <div>
            <label class="text-sm text-gray-600">Password</label>
            <input
                type="password"
                name="password"
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        {{-- ROLE --}}
        <div>
            <label class="text-sm text-gray-600">Role</label>
            @foreach($roles as $role)
                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        name="roles[]"
                        value="{{ $role->name }}"
                    >
                    <span>{{ ucfirst($role->name) }}</span>
                </div>
            @endforeach
        </div>

        <div class="flex gap-2 pt-4">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Simpan
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 bg-gray-200 rounded">
                Batal
            </a>
        </div>
    </form>

</div>
@endsection
