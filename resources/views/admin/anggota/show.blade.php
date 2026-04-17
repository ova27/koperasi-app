@extends('layouts.main')

@section('title', 'Detail Anggota')

@section('content')
@php
    $profileOnlyAccess = auth()->user()->hasRole('anggota')
        && !auth()->user()->hasAnyRole(['admin', 'ketua', 'bendahara']);

    $activeTab = request('tab');

    if ($profileOnlyAccess) {
        $activeTab = 'profil';
    } elseif (!$activeTab) {
        if (request()->has('simpanan_page') || request()->has('jenis')) {
            $activeTab = 'simpanan';
        } elseif (request()->has('pinjaman_page') || request()->has('status_pinjaman')) {
            $activeTab = 'pinjaman';
        } else {
            $activeTab = 'profil';
        }
    }
@endphp

<div
    x-data="{ tab: '{{ $activeTab }}', showEditModal: {{ request()->boolean('open_edit') ? 'true' : 'false' }} }"
    @keydown.escape.window="showEditModal = false"
    class="mx-auto max-w-6xl space-y-6"
>
    <div class="overflow-hidden rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50 via-white to-blue-50 shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-4 min-w-0">
                <div class="inline-flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-blue-600 text-lg font-bold text-white shadow-md">
                    {{ strtoupper(substr($anggota->nama, 0, 1)) }}
                </div>

                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-600">Detail Anggota</p>
                    <div class="mt-1 flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold text-slate-800 sm:text-3xl">
                            {{ $anggota->nama }}
                        </h1>
                        <x-status-anggota
                            :status="$anggota->status"
                            :alasan="$alasanKeluarMap[$anggota->id] ?? null"/>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                        <span>{{ $anggota->jabatan ?: 'Jabatan belum diisi' }}</span>
                        <span class="hidden h-1 w-1 rounded-full bg-slate-300 sm:inline-block"></span>
                        <span>NIP {{ $anggota->nip ?: '-' }}</span>
                    </div>

                    @if ($anggota->status !== 'aktif')
                        <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700">
                            <span class="inline-flex h-2 w-2 rounded-full bg-red-500"></span>
                            Seluruh transaksi dibekukan.
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 justify-end">
                @can('edit anggota')
                    <button
                        type="button"
                        @click="showEditModal = true"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-100 hover:shadow-sm"
                    >
                        Edit Profil
                    </button>
                @endcan

                <a href="{{ route('admin.anggota.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-sky-200 hover:text-sky-700 hover:shadow-sm">
                    ← Kembali ke Daftar Anggota
                </a>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
            <div class="flex flex-wrap gap-2">
                <button
                    @click="tab = 'profil'"
                    :class="tab === 'profil' ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'"
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition"
                >
                    Profil
                </button>

                @if(!$profileOnlyAccess)
                <button
                    @click="tab = 'simpanan'"
                    :class="tab === 'simpanan' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'"
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition"
                >
                    Simpanan
                </button>

                <button
                    @click="tab = 'pinjaman'"
                    :class="tab === 'pinjaman' ? 'bg-orange-500 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'"
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition"
                >
                    Pinjaman
                </button>
                @endif
            </div>
        </div>

        <div x-show="tab === 'profil'" x-transition.opacity.duration.200ms class="space-y-4">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-500">Informasi Pribadi</h3>

                    <dl class="mt-4 divide-y divide-slate-100">
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">Nama</dt>
                            <dd class="text-sm font-semibold text-slate-800 text-right">{{ $anggota->nama }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">NIP</dt>
                            <dd class="text-sm font-semibold text-slate-800 text-right">{{ $anggota->nip ?: '-' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">Email</dt>
                            <dd class="text-sm font-semibold text-slate-800 text-right break-all">{{ $anggota->user->email ?? '-' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">Jenis Kelamin</dt>
                            <dd class="text-sm font-semibold text-slate-800 text-right">{{ $anggota->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">Jabatan</dt>
                            <dd class="text-sm font-semibold text-slate-800 text-right">{{ $anggota->jabatan ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-500">Informasi Keanggotaan</h3>

                    <dl class="mt-4 divide-y divide-slate-100">
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">Bank</dt>
                            <dd class="text-sm font-semibold text-slate-800 text-right">{{ $anggota->rekeningAktif->nama_bank ?? '-' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">No Rekening</dt>
                            <dd class="text-sm font-semibold text-slate-800 text-right">{{ $anggota->rekeningAktif->nomor_rekening ?? '-' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500">Tanggal Masuk</dt>
                            <dd class="text-sm font-semibold text-slate-800 text-right">
                                {{ \Carbon\Carbon::parse($anggota->tanggal_masuk)->translatedFormat('d M Y') }}
                            </dd>
                        </div>

                        @if($anggota->status === 'tidak_aktif')
                            <div class="flex items-start justify-between gap-4 py-3">
                                <dt class="text-sm text-slate-500">Tanggal Keluar</dt>
                                <dd class="text-sm font-semibold text-slate-800 text-right">
                                    {{ $anggota->tanggal_keluar ? \Carbon\Carbon::parse($anggota->tanggal_keluar)->translatedFormat('d M Y') : '-' }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        @if(!$profileOnlyAccess)
        <div x-show="tab === 'simpanan'" x-transition.opacity.duration.200ms class="space-y-4">
            @if($canViewFullDetails)
                @php
                    $pokok = $saldoSimpanan['pokok'] ?? 0;
                    $wajib = $saldoSimpanan['wajib'] ?? 0;
                    $sukarela = $saldoSimpanan['sukarela'] ?? 0;
                    $total = $pokok + $wajib + $sukarela;
                @endphp

                <div
                    x-data="{open: {{ (request()->has('simpanan_page') || request()->has('jenis')) ? 'true' : 'false' }}}"
                    class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm"
                >
                    <div class="flex flex-col gap-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-teal-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Ringkasan Simpanan</h2>
                            <p class="mt-1 text-sm text-slate-500">Informasi simpanan anggota dan riwayat transaksinya.</p>
                        </div>

                        <button
                            @click="open = !open"
                            class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50"
                        >
                            <span x-text="open ? 'Sembunyikan' : 'Riwayat Transaksi Simpanan'"></span>
                            <svg
                                :class="open ? 'rotate-180' : ''"
                                class="h-4 w-4 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-5 text-sm lg:grid-cols-4">
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-600">Pokok</p>
                            <p class="mt-2 text-lg font-bold text-emerald-800">Rp {{ number_format($pokok, 0, ',', '.') }}</p>
                        </div>

                        <div class="rounded-2xl border border-teal-100 bg-teal-50 px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-teal-600">Wajib</p>
                            <p class="mt-2 text-lg font-bold text-teal-800">Rp {{ number_format($wajib, 0, ',', '.') }}</p>
                        </div>

                        <div class="rounded-2xl border border-cyan-100 bg-cyan-50 px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-600">Sukarela</p>
                            <p class="mt-2 text-lg font-bold text-cyan-800">Rp {{ number_format($sukarela, 0, ',', '.') }}</p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Total</p>
                            <p class="mt-2 text-lg font-bold text-slate-800">Rp {{ number_format($total, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div x-show="open" x-transition class="border-t border-slate-100 bg-slate-50/70 p-4 sm:p-5">
                        @php
                            $currentJenis = request('jenis');
                            $labelJenis = match($currentJenis) {
                                'pokok' => 'Pokok',
                                'wajib' => 'Wajib',
                                'sukarela' => 'Sukarela',
                                default => 'Semua'
                            };
                        @endphp

                        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h4 class="text-sm font-semibold text-slate-700">Riwayat Transaksi Simpanan</h4>

                            <div x-data="{ openFilter: false }" class="relative inline-block">
                                <button
                                    @click="openFilter = !openFilter"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-50"
                                >
                                    <span>Jenis: <span class="font-semibold text-slate-800">{{ $labelJenis }}</span></span>
                                    <svg
                                        :class="openFilter ? 'rotate-180' : ''"
                                        class="h-4 w-4 transition-transform"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="openFilter"
                                    @click.outside="openFilter = false"
                                    x-transition
                                    class="absolute left-0 z-10 mt-2 w-40 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
                                >
                                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'simpanan', 'jenis' => null, 'simpanan_page' => null, 'page' => null]) }}"
                                       class="block px-3 py-2 text-xs text-slate-600 transition hover:bg-slate-50 {{ !$currentJenis ? 'bg-slate-50 font-semibold text-slate-800' : '' }}">
                                        Semua
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'simpanan', 'jenis' => 'pokok', 'simpanan_page' => 1, 'page' => null]) }}"
                                       class="block px-3 py-2 text-xs text-slate-600 transition hover:bg-slate-50 {{ $currentJenis === 'pokok' ? 'bg-slate-50 font-semibold text-slate-800' : '' }}">
                                        Pokok
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'simpanan', 'jenis' => 'wajib', 'simpanan_page' => 1, 'page' => null]) }}"
                                       class="block px-3 py-2 text-xs text-slate-600 transition hover:bg-slate-50 {{ $currentJenis === 'wajib' ? 'bg-slate-50 font-semibold text-slate-800' : '' }}">
                                        Wajib
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'simpanan', 'jenis' => 'sukarela', 'simpanan_page' => 1, 'page' => null]) }}"
                                       class="block px-3 py-2 text-xs text-slate-600 transition hover:bg-slate-50 {{ $currentJenis === 'sukarela' ? 'bg-slate-50 font-semibold text-slate-800' : '' }}">
                                        Sukarela
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gradient-to-r from-emerald-50 to-teal-50 text-xs uppercase tracking-widest text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Tanggal</th>
                                        <th class="px-4 py-3 text-center">Jenis</th>
                                        <th class="px-4 py-3 text-right">Jumlah</th>
                                        <th class="px-4 py-3 text-left">Sumber</th>
                                        <th class="px-4 py-3 text-center">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($simpanans as $simpanan)
                                        <tr id="simpanan-row-{{ $simpanan->id }}" class="transition hover:bg-emerald-50/40">
                                            <td class="px-4 py-3 text-left text-slate-700">
                                                {{ \Carbon\Carbon::parse($simpanan->tanggal)->format('d-m-Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-center capitalize text-slate-600">
                                                {{ $simpanan->jenis_simpanan }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-semibold">
                                                @if ($simpanan->jumlah < 0)
                                                    <span class="text-red-600">- Rp {{ number_format(abs($simpanan->jumlah), 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-emerald-600">+ Rp {{ number_format($simpanan->jumlah, 0, ',', '.') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-left text-slate-700">
                                                {{ $simpanan->sumber }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-slate-500">
                                                {{ $simpanan->keterangan ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                                Belum ada transaksi simpanan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($simpanans->hasPages())
                            <div class="mt-4 flex flex-col gap-2 px-1 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-sm text-slate-600">
                                    Menampilkan
                                    <span class="font-semibold text-slate-900">{{ $simpanans->firstItem() ?? 0 }}</span>
                                    sampai
                                    <span class="font-semibold text-slate-900">{{ $simpanans->lastItem() ?? 0 }}</span>
                                    dari
                                    <span class="font-semibold text-slate-900">{{ $simpanans->total() }}</span>
                                    data
                                </p>

                                <div class="flex justify-center sm:justify-end">
                                    {{ $simpanans->appends(array_merge(request()->query(), ['tab' => 'simpanan']))->links('vendor.pagination.custom') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        @endif

        @if(!$profileOnlyAccess)
        <div x-show="tab === 'pinjaman'" x-transition.opacity.duration.200ms class="space-y-4">
            @if($canViewFullDetails)
                @php
                    $aktif = $ringkasanPinjaman['aktif'] ?? 0;
                    $lunas = $ringkasanPinjaman['lunas'] ?? 0;
                    $sisa = $ringkasanPinjaman['sisa'] ?? 0;
                @endphp

                <div
                    x-data="{open: {{ (request()->has('pinjaman_page') || request()->has('status_pinjaman')) ? 'true' : 'false' }}}"
                    class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm"
                >
                    <div class="flex flex-col gap-4 border-b border-slate-100 bg-gradient-to-r from-orange-50 to-amber-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Ringkasan Pinjaman</h2>
                            <p class="mt-1 text-sm text-slate-500">Status pinjaman aktif, lunas, dan detail cicilan anggota.</p>
                        </div>

                        <button
                            @click="open = !open"
                            class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-3 py-1.5 text-xs font-semibold text-orange-700 transition hover:bg-orange-50"
                        >
                            <span x-text="open ? 'Sembunyikan' : 'Riwayat Pinjaman'"></span>
                            <svg
                                :class="open ? 'rotate-180' : ''"
                                class="h-4 w-4 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-5 text-sm lg:grid-cols-3">
                        <div class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-600">Pinjaman Aktif</p>
                            <p class="mt-2 text-lg font-bold text-amber-800">{{ $aktif }}</p>
                        </div>

                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-600">Pinjaman Lunas</p>
                            <p class="mt-2 text-lg font-bold text-emerald-800">{{ $lunas }}</p>
                        </div>

                        <div class="rounded-2xl border border-orange-100 bg-orange-50 px-4 py-4 col-span-2 lg:col-span-1">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-orange-600">Sisa Pinjaman</p>
                            <p class="mt-2 text-lg font-bold text-orange-800">Rp {{ number_format($sisa, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div x-show="open" x-transition class="border-t border-slate-100 bg-slate-50/70 p-4 sm:p-5">
                        @php
                            $statusPinjaman = request('status_pinjaman');
                            $labelStatus = match($statusPinjaman) {
                                'aktif' => 'Aktif',
                                'lunas' => 'Lunas',
                                default => 'Semua'
                            };
                        @endphp

                        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h4 class="text-sm font-semibold text-slate-700">Riwayat Pinjaman</h4>

                            <div x-data="{ openFilter: false }" class="relative inline-block">
                                <button
                                    @click="openFilter = !openFilter"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-50"
                                >
                                    <span>Status: <span class="font-semibold text-slate-800">{{ $labelStatus }}</span></span>
                                    <svg
                                        :class="openFilter ? 'rotate-180' : ''"
                                        class="h-4 w-4 transition-transform"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="openFilter"
                                    @click.outside="openFilter = false"
                                    x-transition
                                    class="absolute right-0 z-10 mt-2 w-40 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
                                >
                                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'pinjaman', 'status_pinjaman' => null, 'pinjaman_page' => 1, 'page' => null]) }}"
                                       class="block px-3 py-2 text-xs text-slate-600 transition hover:bg-slate-50 {{ !$statusPinjaman ? 'bg-slate-50 font-semibold text-slate-800' : '' }}">
                                        Semua
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'pinjaman', 'status_pinjaman' => 'aktif', 'pinjaman_page' => 1, 'page' => null]) }}"
                                       class="block px-3 py-2 text-xs text-slate-600 transition hover:bg-slate-50 {{ $statusPinjaman === 'aktif' ? 'bg-slate-50 font-semibold text-slate-800' : '' }}">
                                        Aktif
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'pinjaman', 'status_pinjaman' => 'lunas', 'pinjaman_page' => 1, 'page' => null]) }}"
                                       class="block px-3 py-2 text-xs text-slate-600 transition hover:bg-slate-50 {{ $statusPinjaman === 'lunas' ? 'bg-slate-50 font-semibold text-slate-800' : '' }}">
                                        Lunas
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gradient-to-r from-orange-50 to-amber-50 text-xs uppercase tracking-widest text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Tanggal</th>
                                        <th class="px-4 py-3 text-right">Jumlah Pinjaman</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-right">Sisa</th>
                                        <th class="px-4 py-3 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($pinjamans as $pinjaman)
                                        <tr id="pinjaman-row-{{ $pinjaman->id }}" class="border-transparent transition hover:bg-sky-50/50">
                                            <td class="px-4 py-3 text-left text-slate-700">
                                                {{ \Carbon\Carbon::parse($pinjaman->tanggal_pinjam)->format('d-m-Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-semibold text-slate-800">
                                                Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @php
                                                    $statusClass = match($pinjaman->status) {
                                                        'aktif' => 'bg-yellow-100 text-yellow-800',
                                                        'lunas' => 'bg-green-100 text-green-800',
                                                        'dibatalkan' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-600'
                                                    };
                                                @endphp

                                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                    {{ ucfirst($pinjaman->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-semibold text-red-600">
                                                Rp {{ number_format($pinjaman->sisa_pinjaman ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($pinjaman->transaksi->isNotEmpty())
                                                    <button
                                                        onclick="toggleCicilan({{ $pinjaman->id }})"
                                                        id="btn-lihat-{{ $pinjaman->id }}"
                                                        class="inline-flex items-center gap-1 rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white transition-all duration-200 hover:bg-blue-600 hover:shadow-sm"
                                                    >
                                                        <svg class="h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                        Lihat
                                                    </button>
                                                @else
                                                    <span class="text-xs italic text-slate-400">-</span>
                                                @endif
                                            </td>
                                        </tr>

                                        @if($pinjaman->transaksi->isNotEmpty())
                                            <tr id="cicilan-{{ $pinjaman->id }}" class="hidden">
                                                <td colspan="8" class="bg-sky-50 px-6 py-4 border-l-4 border-sky-400">
                                                    <div class="mb-3 text-sm font-semibold text-slate-700">
                                                        Riwayat Transaksi
                                                    </div>

                                                    <div class="overflow-hidden rounded-2xl border border-sky-100 bg-white">
                                                        <table class="w-full text-sm">
                                                            <thead class="bg-slate-50 text-slate-500">
                                                                <tr>
                                                                    <th class="px-4 py-2.5 text-left">Tanggal</th>
                                                                    <th class="px-4 py-2.5 text-center">Jenis</th>
                                                                    <th class="px-4 py-2.5 text-right">Jumlah</th>
                                                                    <th class="px-4 py-2.5 text-right">Sisa Pinjaman</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-slate-100">
                                                                @php
                                                                    $pencairanTransactions = $pinjaman->transaksi->where('jenis', 'pencairan')->sortBy('tanggal');
                                                                    $otherTransactions = $pinjaman->transaksi->where('jenis', '!=', 'pencairan')->sortBy('tanggal');
                                                                @endphp

                                                                @php
                                                                    $previousSisa = 0;
                                                                @endphp
                                                                @foreach($pencairanTransactions as $t)
                                                                    @php
                                                                        $selisih = ($t->sisa_setelah ?? 0) - $previousSisa;
                                                                        $previousSisa = $t->sisa_setelah ?? 0;
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="px-4 py-2.5 text-slate-700">
                                                                            {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                                                                        </td>
                                                                        <td class="px-4 py-2.5 text-center capitalize text-slate-600">
                                                                            {{ ucfirst($t->jenis) }}
                                                                        </td>
                                                                        <td class="px-4 py-2.5 text-right text-slate-700">
                                                                            Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                                                        </td>
                                                                        <td class="px-4 py-2.5 text-right font-semibold text-slate-800">
                                                                            Rp {{ number_format($t->sisa_setelah ?? 0, 0, ',', '.') }}
                                                                            @if($selisih != 0)
                                                                                <span class="text-xs {{ $selisih < 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                                    ({{ $selisih < 0 ? '-' : '+' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }})
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                                @php
                                                                    $previousSisa = $previousSisa ?: $pinjaman->jumlah_pinjaman;
                                                                    $counters = [];
                                                                @endphp
                                                                @foreach($otherTransactions as $t)
                                                                    @php
                                                                        $selisih = ($t->sisa_setelah ?? 0) - $previousSisa;
                                                                        $previousSisa = $t->sisa_setelah ?? 0;

                                                                        if(!isset($counters[$t->jenis])) {
                                                                            $counters[$t->jenis] = 1;
                                                                        }
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="px-4 py-2.5 text-slate-700">
                                                                            {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                                                                        </td>
                                                                        <td class="px-4 py-2.5 text-center capitalize text-slate-600">
                                                                            @if(in_array($t->jenis, ['pencairan', 'topup', 'pelunasan']))
                                                                                {{ ucfirst($t->jenis) }}
                                                                            @else
                                                                                {{ ucfirst($t->jenis) }} ke-{{ $counters[$t->jenis] }}
                                                                            @endif
                                                                        </td>
                                                                        <td class="px-4 py-2.5 text-right text-slate-700">
                                                                            Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                                                        </td>
                                                                        <td class="px-4 py-2.5 text-right font-semibold text-slate-800">
                                                                            Rp {{ number_format($t->sisa_setelah ?? 0, 0, ',', '.') }}
                                                                            @if($selisih != 0)
                                                                                <span class="text-xs {{ $selisih < 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                                    ({{ $selisih < 0 ? '-' : '+' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }})
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @php
                                                                        $counters[$t->jenis]++;
                                                                    @endphp
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                                Tidak ada data
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($pinjamans->hasPages())
                            <div class="mt-4 flex flex-col gap-2 px-1 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-sm text-slate-600">
                                    Menampilkan
                                    <span class="font-semibold text-slate-900">{{ $pinjamans->firstItem() ?? 0 }}</span>
                                    sampai
                                    <span class="font-semibold text-slate-900">{{ $pinjamans->lastItem() ?? 0 }}</span>
                                    dari
                                    <span class="font-semibold text-slate-900">{{ $pinjamans->total() }}</span>
                                    data
                                </p>
                                <div class="flex justify-center sm:justify-end">
                                    {{ $pinjamans->appends(array_merge(request()->query(), ['tab' => 'pinjaman']))->links('vendor.pagination.custom') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        @endif
            @can('edit anggota')
                <div
                    x-show="showEditModal"
                    x-transition.opacity
                    x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    style="display: none;"
                >
                    <div
                        class="absolute inset-0 bg-slate-900/50"
                        @click="showEditModal = false"
                    ></div>

                    <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                        <form id="form-edit-anggota-inline" method="POST" action="{{ route('admin.anggota.update', $anggota) }}" class="space-y-0">
                            @csrf
                            @method('PUT')

                            <div class="flex items-start justify-between border-b border-slate-100 px-6 pb-4 pt-6">
                                <div class="flex min-w-0 items-center gap-4">
                                    <div class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-blue-600 text-base font-bold text-white shadow-md">
                                        {{ strtoupper(substr($anggota->nama, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h2 class="truncate text-lg font-bold text-slate-800">Edit Profil Anggota</h2>
                                        <p class="truncate text-sm text-slate-500">{{ $anggota->nama }}</p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    @click="showEditModal = false"
                                    class="ml-4 inline-flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition-colors duration-150 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="max-h-[65vh] space-y-4 overflow-y-auto px-6 py-5">
                                <div id="form-edit-inline-error" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600"></div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nama</label>
                                        <input type="text" name="nama" value="{{ old('nama', $anggota->nama) }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                        <span class="mt-1 block text-xs text-red-500 error-nama"></span>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                                        <input type="email" name="email" value="{{ old('email', $anggota->user->email ?? '') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                        <span class="mt-1 block text-xs text-red-500 error-email"></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">NIP</label>
                                        <input type="text" name="nip" value="{{ old('nip', $anggota->nip) }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                        <span class="mt-1 block text-xs text-red-500 error-nip"></span>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                            <option value="L" @selected($anggota->jenis_kelamin === 'L')>Laki-laki</option>
                                            <option value="P" @selected($anggota->jenis_kelamin === 'P')>Perempuan</option>
                                        </select>
                                        <span class="mt-1 block text-xs text-red-500 error-jenis_kelamin"></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Jabatan</label>
                                        <input type="text" name="jabatan" value="{{ old('jabatan', $anggota->jabatan) }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                        <span class="mt-1 block text-xs text-red-500 error-jabatan"></span>
                                    </div>

                                    @can('nonaktifkan anggota')
                                        <div>
                                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status Anggota</label>
                                            <select name="status" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                                <option value="aktif" @selected($anggota->status === 'aktif')>Aktif</option>
                                                <option value="cuti" @selected($anggota->status === 'cuti')>Cuti</option>
                                                <option value="tugas_belajar" @selected($anggota->status === 'tugas_belajar')>Tugas Belajar</option>
                                                <option value="tidak_aktif" disabled @selected($anggota->status === 'tidak_aktif')>Pensiun / Mutasi (via Proses Keluar)</option>
                                            </select>
                                            <p class="mt-1 text-xs text-amber-700">
                                                Pensiun/Mutasi harus melalui proses pengembalian simpanan.
                                                <a href="{{ route('admin.simpanan.index') }}" class="font-semibold underline hover:text-amber-900">Buka menu Simpanan</a>
                                            </p>
                                            <span class="mt-1 block text-xs text-red-500 error-status"></span>
                                        </div>
                                    @else
                                        <input type="hidden" name="status" value="{{ $anggota->status }}">
                                    @endcan
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Bank</label>
                                        <input type="text" name="nama_bank" value="{{ old('nama_bank', $anggota->rekeningAktif->nama_bank ?? '') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                        <span class="mt-1 block text-xs text-red-500 error-nama_bank"></span>
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nomor Rekening</label>
                                        <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening', $anggota->rekeningAktif->nomor_rekening ?? '') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                        <span class="mt-1 block text-xs text-red-500 error-nomor_rekening"></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Masuk</label>
                                        <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', \Carbon\Carbon::parse($anggota->tanggal_masuk)->format('Y-m-d')) }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                        <span class="mt-1 block text-xs text-red-500 error-tanggal_masuk"></span>
                                    </div>
                                    
                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Keluar</label>
                                        <input type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar', $anggota->tanggal_keluar ? \Carbon\Carbon::parse($anggota->tanggal_keluar)->format('Y-m-d') : '') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                                        <span class="mt-1 block text-xs text-red-500 error-tanggal_keluar"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                                <button
                                    type="button"
                                    @click="showEditModal = false"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                >
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan
    </div>

    <script>
            if (!window.__formEditAnggotaInlineBound) {
                document.addEventListener('submit', function(e) {
                    const form = e.target;
                    if (form.id !== 'form-edit-anggota-inline') return;

                    e.preventDefault();

                    form.querySelectorAll('[class^="error-"]').forEach(el => el.textContent = '');

                    const formError = document.getElementById('form-edit-inline-error');
                    if (formError) {
                        formError.classList.add('hidden');
                        formError.textContent = '';
                    }

                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(async res => {
                        if (res.status === 422) {
                            const data = await res.json();
                            Object.keys(data.errors).forEach(key => {
                                const el = form.querySelector('.error-' + key);
                                if (el) el.textContent = data.errors[key][0];
                            });
                            return null;
                        }

                        return res.json();
                    })
                    .then(res => {
                        if (res && res.success) {
                            location.reload();
                        }
                    })
                    .catch(() => {
                        if (formError) {
                            formError.textContent = 'Gagal menyimpan perubahan. Silakan coba lagi.';
                            formError.classList.remove('hidden');
                        }
                    });
                });

                window.__formEditAnggotaInlineBound = true;
            }

        function toggleCicilan(id) {
            const row = document.getElementById('cicilan-' + id);
            const mainRow = document.getElementById('pinjaman-row-' + id);
            const button = document.getElementById('btn-lihat-' + id);

            row.classList.toggle('hidden');

            if (!row.classList.contains('hidden')) {
                mainRow.classList.add('bg-sky-100', 'border-sky-300');
                mainRow.classList.remove('hover:bg-sky-50/50', 'border-transparent');
                button.innerHTML = `
                    <svg class="h-3 w-3 rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    Tutup
                `;
                button.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                button.classList.add('bg-red-500', 'hover:bg-red-600');
            } else {
                mainRow.classList.remove('bg-sky-100', 'border-sky-300');
                mainRow.classList.add('hover:bg-sky-50/50', 'border-transparent');
                button.innerHTML = `
                    <svg class="h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    Lihat
                `;
                button.classList.remove('bg-red-500', 'hover:bg-red-600');
                button.classList.add('bg-blue-500', 'hover:bg-blue-600');
            }
        }
    </script>
@endsection
