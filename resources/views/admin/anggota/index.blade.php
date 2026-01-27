@extends('layouts.main')

@section('title', 'Data Anggota')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Data Anggota</h1>

    <div class="bg-white rounded border">
        <table class="w-full text-sm">
            <thead class="border-b bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($anggotas as $anggota)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.anggota.show', $anggota) }}"
                            class="text-blue-600 hover:underline">
                                {{ $anggota->nama }}
                            </a>
                        </td>
                        <td class="px-4 py-2">{{ $anggota->email }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs bg-gray-200">
                                {{ $anggota->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            Belum ada data anggota
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
