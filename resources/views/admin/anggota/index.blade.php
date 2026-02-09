@extends('layouts.main')
@section('title', 'Daftar Anggota')
@section('content')
<div class="max-w-6xl mx-auto space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800">
            Daftar Anggota
        </h1>
    </div>

    {{-- TABEL --}}
    <div class="overflow-x-auto bg-white border rounded-lg">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="px-4 py-3 text-left w-12">No</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-center">Jabatan</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center w-40">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($anggotas as $anggota)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- NO --}}
                        <td class="px-4 py-3 text-gray-500">
                            {{ $loop->iteration }}
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

                            {{-- kalau belum pakai component, ganti dengan ini:
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @class([
                                    'bg-green-100 text-green-700' => $anggota->status === 'aktif',
                                    'bg-yellow-100 text-yellow-700' => $anggota->status === 'cuti',
                                    'bg-blue-100 text-blue-700' => $anggota->status === 'tugas_belajar',
                                    'bg-red-100 text-red-700' => $anggota->status === 'tidak_aktif',
                                ])">
                                {{ ucfirst(str_replace('_',' ', $anggota->status)) }}
                            </span>
                            --}}
                        </td>

                        {{-- AKSI --}}
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-3 text-sm">

                                {{-- DETAIL --}}
                                @can('view anggota list')
                                    <button
                                        type="button"
                                        class="btn-detail text-blue-600 hover:underline"
                                        data-url="{{ route('admin.anggota.show', $anggota) }}">
                                        Detail
                                    </button>
                                @endcan


                                {{-- EDIT --}}
                                @can('edit anggota')
                                    <button
                                        type="button"
                                        class="btn-edit text-yellow-600 hover:underline"
                                        data-url="{{ route('admin.anggota.edit', $anggota) }}">
                                        Edit
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
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

</div>
@endsection
