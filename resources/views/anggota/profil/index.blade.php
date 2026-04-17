@extends('layouts.main')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
@php
    $showPengaturan =
    $errors->any() ||
    in_array(session('status'), ['profile-updated', 'verification-link-sent']);
@endphp

<div class="space-y-6" x-data="{ showPengaturan: {{ $showPengaturan ? 'true' : 'false' }} }">
    @if($user->anggota)
        @php
            $anggota = $user->anggota;
            $rekeningList = $anggota->rekening;
            $genderLabel = match($anggota->jenis_kelamin) {
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
                default => $anggota->jenis_kelamin ?? '-',
            };
        @endphp

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 sm:p-6">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold text-slate-800">Biodata</h3>
                <button
                    type="button"
                    @click="showPengaturan = true"
                    class="inline-flex items-center justify-center rounded-lg border border-yellow-200 bg-yellow-50 px-3 py-1.5 text-xs font-semibold text-yellow-700 transition hover:bg-yellow-100"
                >
                    Ubah
                </button>
            </div>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Nama</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $anggota->nama ?? '-' }}</dd>
                </div>

                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Nomor Anggota</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $anggota->nomor_anggota ?? '-' }}</dd>
                </div>

                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Jenis Kelamin</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $genderLabel }}</dd>
                </div>

                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Status</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ ucfirst($anggota->status ?? '-') }}</dd>
                </div>

                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">NIP</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $anggota->nip ?? '-' }}</dd>
                </div>

                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Jabatan</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $anggota->jabatan ?? '-' }}</dd>
                </div>

                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Tanggal Masuk</dt>
                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $anggota->tanggal_masuk ? \Carbon\Carbon::parse($anggota->tanggal_masuk)->translatedFormat('d F Y') : '-' }}
                    </dd>
                </div>

                <div class="border-b border-slate-100 pb-3">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Email Akun</dt>
                    <dd class="mt-1 font-semibold text-slate-900 break-all">{{ $user->email ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="px-4 py-4 sm:px-6 bg-slate-50 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-800">Rekening Anggota</h3>
            </div>

            @if($rekeningList->isNotEmpty())
                <div class="overflow-x-auto pb-3">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-xs uppercase tracking-wider">Nama Bank</th>
                                <th class="px-5 py-3 text-left font-semibold text-xs uppercase tracking-wider">Nomor Rekening</th>
                                <th class="px-5 py-3 text-left font-semibold text-xs uppercase tracking-wider">Nama Pemilik</th>
                                <th class="px-5 py-3 text-center font-semibold text-xs uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rekeningList as $rekening)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-3.5 text-slate-800 font-medium">{{ $rekening->nama_bank ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-slate-700">{{ $rekening->nomor_rekening ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-slate-700">{{ $rekening->nama_pemilik ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if($rekening->aktif)
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">Aktif</span>
                                        @else
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-10 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h.01M11 15h2m8-5v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2h14a2 2 0 012 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-600">Belum ada data rekening anggota.</p>
                </div>
            @endif
        </div>
    @else
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-4 text-sm text-yellow-800">
            Data anggota belum tersedia untuk akun ini.
        </div>
    @endif

    {{-- MODAL UBAH PROFIL --}}
    <div
        x-show="showPengaturan"
        x-transition
        @keydown.escape.window="showPengaturan = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div class="absolute inset-0 bg-black/40" @click="showPengaturan = false"></div>

        <div class="relative w-full max-w-4xl max-h-[88vh] overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-xl">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-800">Ubah Profil</h3>
                <button
                    type="button"
                    @click="showPengaturan = false"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                    aria-label="Tutup modal"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-4 sm:p-4">
                <div class="rounded-xl border border-slate-200 p-3">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
