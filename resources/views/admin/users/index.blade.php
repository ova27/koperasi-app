@extends('layouts.main')
@section('title', 'Manajemen Pengguna')
@section('content')
<div class="max-w-6xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">
            Manajemen Pengguna
        </h1>

        @can('manage users')
            <a href="{{ route('admin.users.create') }}"
               class="px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                + Tambah Pengguna
            </a>
        @endcan
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-2 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABEL USERS --}}
    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">No</th>
                    <th class="px-3 py-2 text-left">Nama</th>
                    <th class="px-3 py-2 text-left">Email</th>
                    <th class="px-3 py-2">Role</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-t">
                        <td class="px-3 py-2">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $user->name }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $user->email }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @foreach($user->roles as $role)
                                <span class="inline-block px-2 py-1 text-xs bg-gray-200 rounded">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('manage users')
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="text-blue-600 hover:underline">
                                        Edit
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            class="px-3 py-4 text-center text-gray-500">
                            Belum ada pengguna
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
