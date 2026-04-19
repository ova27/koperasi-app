@extends('layouts.main')

@section('title', 'Daftar Pencairan Pinjaman')
@section('page-title', 'Daftar Pencairan Pinjaman')
@section('content')
<div class="space-y-4">

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div id="flash-message" class="px-4 py-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="closeFlashMessage()" class="ml-4 hover:text-green-900">×</button>
        </div>
    @endif

    @if(session('error'))
        <div id="flash-message" class="px-4 py-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="closeFlashMessage()" class="ml-4 hover:text-red-900">×</button>
        </div>
    @endif

    {{-- DAFTAR PINJAMAN YANG SIAP DICAIRKAN --}}
    <div class="mb-6">
        <h2 class="section-title">
            Pengajuan Pinjaman Siap Dicairkan (Sudah Disetujui)
        </h2>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto mb-4">
            @php
                function sortLinkP($col, $label, $sort, $dir) {
                    $newDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                    $arrow = $sort === $col ? ($dir === 'asc' ? '▲' : '▼') : '';

                    return '<a href="'.request()->fullUrlWithQuery([
                        'p_sort'=>$col,
                        'p_direction'=>$newDir
                    ]).'" class="hover:underline">'.$label.' '.$arrow.'</a>';
                }

                function sortLinkR($col, $label, $sort, $dir) {
                    $newDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                    $arrow = $sort === $col ? ($dir === 'asc' ? '▲' : '▼') : '';

                    return '<a href="'.request()->fullUrlWithQuery([
                        'r_sort'=>$col,
                        'r_direction'=>$newDir
                    ]).'" class="hover:underline">'.$label.' '.$arrow.'</a>';
                }
            @endphp
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-50 to-blue-100 text-blue-800">
                        <th class="px-3 py-2 text-center font-semibold tracking-wide">No</th>
                        <th class="px-3 py-2 text-center font-semibold tracking-wide">{!! sortLinkP('tanggal_pengajuan','Tanggal Pengajuan',$p_sort,$p_direction) !!}</th>
                        <th class="px-3 py-2 text-center font-semibold tracking-wide">Anggota</th>
                        <th class="px-3 py-2 text-right font-semibold tracking-wide">{!! sortLinkP('jumlah_diajukan','Jumlah',$p_sort,$p_direction) !!}</th>
                        <th class="px-3 py-2 text-center font-semibold tracking-wide">{!! sortLinkP('bulan_pinjam','Bulan Pinjam',$p_sort,$p_direction) !!}</th>
                        <th class="px-3 py-2 text-center font-semibold tracking-wide">Tenor</th>
                        <th class="px-3 py-2 text-center font-semibold tracking-wide">{!! sortLinkP('tanggal_persetujuan','Tanggal Persetujuan',$p_sort,$p_direction) !!}</th>
                        <th class="px-3 py-2 text-center font-semibold tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pencairanSiapCair as $p)
                        <tr class="transition-all duration-200 hover:scale-[1.01] hover:shadow-lg bg-white even:bg-blue-50 rounded-xl">
                            <td class="px-3 py-2 rounded-l-xl text-center text-gray-500 align-middle">
                                {{ $pencairanSiapCair->firstItem() + $loop->index }}
                            </td>
                            <td class="px-3 py-2 text-center text-gray-500 align-middle">
                                {{ optional($p->tanggal_pengajuan)->format('d F Y') ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-center align-middle">
                                {{ $p->anggota->nama }}
                            </td>
                            <td class="px-3 py-2 text-right font-bold text-blue-700 align-middle">
                                    Rp {{ number_format($p->jumlah_diajukan, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2 text-center text-gray-500 align-middle">
                                {{ \Carbon\Carbon::parse($p->bulan_pinjam)->format('F Y') }}
                            </td>
                            <td class="px-3 py-2 text-center text-gray-700 align-middle">
                                {{ $p->tenor }} Bulan
                            </td>
                            <td class="px-3 py-2 text-center text-gray-500 align-middle">
                                {{ optional($p->tanggal_persetujuan)->format('d F Y') ?? '-' }}
                            </td>
                            <td class="px-3 py-2 rounded-r-xl text-center align-middle">
                                @can('pencairan pinjaman')
                                    <form method="POST"
                                        action="{{ route('admin.pinjaman.pencairan.process', $p) }}"
                                        onsubmit="return confirmCairkan(event, this)"
                                        data-anggota="{{ $p->anggota->nama }}"
                                        data-jumlah="Rp {{ number_format($p->jumlah_diajukan, 0, ',', '.') }}"
                                        data-tenor="{{ $p->tenor }} Bulan"
                                        data-bulan="{{ \Carbon\Carbon::parse($p->bulan_pinjam)->format('F Y') }}"
                                        data-tanggal-pengajuan="{{ optional($p->tanggal_pengajuan)->format('d F Y') ?? '-' }}"
                                        data-tanggal-persetujuan="{{ optional($p->tanggal_persetujuan)->format('d F Y') ?? '-' }}"
                                        data-tujuan="{{ $p->tujuan ?: '-' }}">
                                        @csrf
                                        <input type="hidden" name="jumlah_diajukan" value="{{ $p->jumlah_diajukan }}">
                                        <input type="hidden" name="tenor" value="{{ $p->tenor }}">
                                        <input type="hidden" name="bulan_pinjam" value="{{ \Carbon\Carbon::parse($p->bulan_pinjam)->format('Y-m') }}">
                                        <input type="hidden" name="tujuan" value="{{ $p->tujuan }}">
                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition shadow-md">
                                            Cairkan
                                        </button>
                                    </form>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="px-3 py-1.5 text-xs font-semibold text-white bg-gray-300 rounded-lg cursor-not-allowed">
                                        Cairkan
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-2 py-3 text-center text-gray-500 bg-white rounded-xl">
                                Tidak ada pengajuan pinjaman yang siap dicairkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($pencairanSiapCair->hasPages())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-2">
                <p class="text-sm text-gray-600">
                    Menampilkan
                    <span class="font-semibold text-gray-900">{{ $pencairanSiapCair->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-semibold text-gray-900">{{ $pencairanSiapCair->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold text-gray-900">{{ $pencairanSiapCair->total() }}</span>
                    data
                </p>

                <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                    {{ $pencairanSiapCair->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>

    {{-- RIWAYAT PENCAIRAN --}}
    <div x-data="{ open: false }" class="pt-6 border-t border-gray-200">

        <button
            @click="open = !open"
            class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 transition group">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500 group-hover:text-gray-700 transition"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7h18M3 12h18M3 17h18" />
                </svg>

                <h2 class="font-semibold text-gray-800 text-sm">
                    Riwayat Pencairan Pinjaman Aktif (Belum Lunas)
                </h2>
            </div>

            <svg
                :class="{ 'rotate-180': open }"
                class="w-4 h-4 text-gray-500 transform transition duration-200 group-hover:text-gray-700"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div
            x-show="open"
            x-transition
            x-cloak
            class="mt-2 space-y-3"
        >
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto mb-2">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="px-3 py-2 text-left font-semibold tracking-wide">Tanggal Pengajuan</th>
                            <th class="px-3 py-2 text-left font-semibold tracking-wide">Anggota</th>
                            <th class="px-3 py-2 text-right font-semibold tracking-wide">Jumlah</th>
                            <th class="px-3 py-2 text-center font-semibold tracking-wide">Tenor</th>
                            <th class="px-3 py-2 text-center font-semibold tracking-wide">Tanggal Cair</th>
                            <th class="px-3 py-2 text-center font-semibold tracking-wide">Status</th>
                            <th class="px-3 py-2 text-center font-semibold tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatPencairan as $r)
                            <tr class="transition-all duration-200 hover:scale-[1.01] hover:shadow-lg bg-white even:bg-gray-50 rounded-xl">
                                <td class="px-3 py-1 rounded-l-xl align-middle text-gray-500">
                                    {{ $r->tanggal_pengajuan->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-1 align-middle">
                                    {{ $r->anggota->nama }}
                                </td>
                                <td class="px-3 py-1 text-right font-semibold text-gray-700 align-middle">
                                    Rp {{ number_format($r->jumlah_diajukan, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-1 text-center text-gray-700 align-middle">
                                    {{ $r->tenor }} Bulan
                                </td>
                                <td class="px-3 py-1 text-center text-gray-500 align-middle">
                                    {{ $r->updated_at->timezone('Asia/Jakarta')->format('d/m/y H:i') }}
                                </td>
                                <td class="px-3 py-1 text-center align-middle">
                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase">
                                        Dicairkan
                                    </span>
                                </td>
                                <td class="px-3 py-1 rounded-r-xl text-center align-middle">
                                    @can('pencairan pinjaman')
                                        @php
                                            $pinjamanAcuan = $r->pinjaman ?: $r->anggota?->pinjamanAktif;
                                            $cicilanSudahBerjalan = $pinjamanAcuan
                                                && $pinjamanAcuan->transaksi->where('jenis', 'cicilan')->isNotEmpty()
                                                && $pinjamanAcuan->transaksi->where('jenis', 'pelunasan')->isEmpty();
                                        @endphp

                                        @if($cicilanSudahBerjalan)

                                            <span class="text-gray-400 text-xs italic">Tidak bisa dibatalkan</span>

                                        @else
                                            <form method="POST"
                                                action="{{ route('admin.pinjaman.pencairan.batal', $r) }}"
                                                onsubmit="return confirmBatal()">
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-md border border-red-200 bg-red-50 px-2 py-1 text-xs text-red-700 hover:bg-red-100 transition">
                                                    Batal
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <button
                                            type="button"
                                            disabled
                                            class="inline-flex items-center justify-center rounded-md border border-gray-200 bg-gray-100 px-2 py-1 text-xs text-gray-400 cursor-not-allowed">
                                            Batal
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-2 py-3 text-center text-gray-500 bg-white rounded-xl">
                                    Belum ada riwayat pencairan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($riwayatPencairan->hasPages())
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-1">
                    <p class="text-sm text-gray-600">
                        Menampilkan
                        <span class="font-semibold text-gray-900">{{ $riwayatPencairan->firstItem() ?? 0 }}</span>
                        sampai
                        <span class="font-semibold text-gray-900">{{ $riwayatPencairan->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-semibold text-gray-900">{{ $riwayatPencairan->total() }}</span>
                        data
                    </p>

                    <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                        {{ $riwayatPencairan->links('vendor.pagination.custom') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL PREVIEW PENCAIRAN --}}
<div id="modalPreviewPencairan" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="preview-pencairan-title">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closePreviewPencairan()"></div>

    <div id="previewPencairanPanel" class="relative w-full max-w-lg overflow-hidden rounded-lg bg-white shadow-2xl ring-1 ring-black/5 transform scale-95 opacity-0 transition-all duration-200 ease-out">
        <div class="flex items-start justify-between gap-4 border-b border-gray-100 bg-gray-50 px-5 py-4">
            <div>
                <h3 id="preview-pencairan-title" class="mt-1 text-lg font-bold text-gray-900">Konfirmasi Pencairan Pinjaman</h3>
            </div>
            <button type="button" onclick="closePreviewPencairan()" class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Tutup modal">
                &times;
            </button>
        </div>

        <form id="formPreviewPencairan" class="space-y-4 px-6 py-6" onsubmit="submitPreviewPencairan(event)">
            <div class="space-y-1">
                <label class="block text-xs text-gray-500">Nama</label>
                <div id="previewAnggota" class="font-semibold text-gray-900 text-base bg-gray-50 rounded px-3 py-2 border border-gray-100">-</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs text-gray-500">Tanggal Pengajuan</label>
                    <div id="previewTanggalPengajuan" class="font-medium text-gray-800 bg-gray-50 rounded px-3 py-2 border border-gray-100">-</div>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs text-gray-500">Tanggal Persetujuan</label>
                    <div id="previewTanggalPersetujuan" class="font-medium text-gray-800 bg-gray-50 rounded px-3 py-2 border border-gray-100">-</div>
                </div>
            </div>
            <div class="space-y-1">
                <label class="block text-xs text-gray-500" for="input_jumlah_format">Jumlah yang Disetujui</label>
                <input type="text" id="input_jumlah_format" class="w-full border border-gray-200 rounded px-3 py-2 text-gray-900 font-semibold bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-base" placeholder="Rp 0" oninput="formatRupiah(this)" value="{{ old('jumlah_diajukan') ? 'Rp ' . number_format(old('jumlah_diajukan'), 0, ',', '.') : '' }}" required>
                <input type="hidden" name="jumlah_diajukan" id="jumlah_asli" value="{{ old('jumlah_diajukan') }}">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs text-gray-500" for="editTenor">Tenor yang Disetujui</label>
                    <input id="editTenor" name="tenor" type="number" min="1" class="w-full border border-gray-200 rounded px-3 py-2 text-gray-900 font-semibold bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-base" value="" required />
                </div>
                <div class="space-y-1">
                    <label class="block text-xs text-gray-500" for="editBulan">Bulan Pinjam yang Disetujui</label>
                    <input id="editBulan" name="bulan_pinjam" type="month" class="w-full border border-gray-200 rounded px-3 py-2 text-gray-900 font-semibold bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-base" value="" required />
                </div>
            </div>
            <div class="space-y-1">
                <label class="block text-xs text-gray-500">Tujuan</label>
                <div id="previewTujuan" class="text-sm font-medium text-gray-800 bg-gray-50 rounded px-3 py-2 border border-gray-100">-</div>
            </div>
            <div class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded px-3 py-2 mt-2">
                Setelah dikonfirmasi, data pinjaman akan masuk sebagai pinjaman aktif dan arus kas keluar akan dicatat.
            </div>
            <div class="flex flex-col-reverse gap-2 px-0 py-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closePreviewPencairan()" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
                    Batal
                </button>
                <button type="submit" id="confirmPreviewPencairanButton" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700">
                    Ya, Cairkan Pinjaman
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT --}}
<script>
    let pencairanFormAktif = null;


    function setPreviewText(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value || '-';
        }
    }

    function confirmCairkan(event, form) {
        event.preventDefault();
        pencairanFormAktif = form;

        setPreviewText('previewAnggota', form.dataset.anggota);
        setPreviewText('previewTanggalPengajuan', form.dataset.tanggalPengajuan);
        setPreviewText('previewTanggalPersetujuan', form.dataset.tanggalPersetujuan);

        // Set editable fields
        // Jumlah
        let jumlah = (form.elements['jumlah_diajukan']?.value || '').replace(/[^\d]/g, '');
        const jumlahInput = document.getElementById('input_jumlah_format');
        const jumlahHidden = document.getElementById('jumlah_asli');
        if (jumlahInput && jumlahHidden) {
            jumlahInput.value = jumlah ? formatRupiahValue(jumlah) : '';
            jumlahHidden.value = jumlah;
        }
        // Tenor
        let tenor = (form.elements['tenor']?.value || '').replace(/[^\d]/g, '');
        document.getElementById('editTenor').value = tenor;
        // Bulan Pinjam
        let bulan = form.elements['bulan_pinjam']?.value || '';
        document.getElementById('editBulan').value = bulan;
        // Tujuan (readonly)
        setPreviewText('previewTujuan', form.elements['tujuan']?.value || '-');

        // Tampilkan preview bulan dalam format teks
        let bulanText = '';
        if (bulan) {
            const [y, m] = bulan.split('-');
            const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            bulanText = monthNames[parseInt(m,10)-1] + ' ' + y;
        }
        setPreviewText('previewBulan', bulanText);

        const modal = document.getElementById('modalPreviewPencairan');
        const panel = document.getElementById('previewPencairanPanel');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        requestAnimationFrame(() => {
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });

        return false;
    }

    function closePreviewPencairan() {
        const modal = document.getElementById('modalPreviewPencairan');
        const panel = document.getElementById('previewPencairanPanel');

        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pencairanFormAktif = null;
        }, 200);
    }


    function submitPreviewPencairan(e) {
        if (e) e.preventDefault();
        if (!pencairanFormAktif) return;

        // Update hidden fields in the original form
        const jumlahInput = document.getElementById('editJumlah');
        const tenorInput = document.getElementById('editTenor');
        const bulanInput = document.getElementById('editBulan');
        const tujuanInput = document.getElementById('editTujuan');
        if (jumlahInput && pencairanFormAktif.elements['jumlah_diajukan']) {
            pencairanFormAktif.elements['jumlah_diajukan'].value = jumlahInput.value.replace(/[^\d]/g, '');
        }
            // Format input jumlah ke rupiah saat user mengetik
            document.addEventListener('DOMContentLoaded', function() {
                var jumlahInput = document.getElementById('editJumlah');
                if (jumlahInput) {
                    jumlahInput.addEventListener('input', function(e) {
                        var value = this.value.replace(/[^\d]/g, '');
                        this.value = value ? formatRupiah(value) : '';
                        setPreviewText('previewJumlah', this.value);
                    });
                    // Format value awal jika sudah ada
                    if (jumlahInput.value) {
                        jumlahInput.value = formatRupiah(jumlahInput.value.replace(/[^\d]/g, ''));
                        setPreviewText('previewJumlah', jumlahInput.value);
                    } else {
                        setPreviewText('previewJumlah', '');
                    }
                }
            });
        if (tenorInput && pencairanFormAktif.elements['tenor']) {
            pencairanFormAktif.elements['tenor'].value = tenorInput.value;
        }
        if (bulanInput && pencairanFormAktif.elements['bulan_pinjam']) {
            pencairanFormAktif.elements['bulan_pinjam'].value = bulanInput.value;
        }
        // Tidak perlu update tujuan, hanya readonly

        const button = document.getElementById('confirmPreviewPencairanButton');
        button.disabled = true;
        button.textContent = 'Memproses...';

        pencairanFormAktif.submit();
    }

    function closeFlashMessage() {
        const el = document.getElementById('flash-message');
        if (el) {
            el.style.transition = 'opacity 0.3s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        }
    }

    // auto hide flash
    const flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 300);
        }, 5000);
    }

    function confirmBatal() {
        return confirm('Yakin ingin membatalkan pencairan ini?');
    }

    // Format angka ke format rupiah (Rp 1.234.567)
    function formatRupiahValue(angka) {
        if (!angka) return '';
        let number_string = angka.toString().replace(/[^\d]/g, ''),
            sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/g);
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah ? 'Rp ' + rupiah : '';
    }

    // Event handler for input field
    document.addEventListener('DOMContentLoaded', function() {
        var jumlahInput = document.getElementById('input_jumlah_format');
        var jumlahHidden = document.getElementById('jumlah_asli');
        if (jumlahInput && jumlahHidden) {
            function handleInput() {
                let value = jumlahInput.value.replace(/[^\d]/g, '');
                jumlahInput.value = value ? formatRupiahValue(value) : '';
                jumlahHidden.value = value;
            }
            jumlahInput.addEventListener('input', handleInput);
            // Format value awal jika sudah ada
            if (jumlahInput.value) {
                let value = jumlahInput.value.replace(/[^\d]/g, '');
                jumlahInput.value = value ? formatRupiahValue(value) : '';
                jumlahHidden.value = value;
            }
        }
    });
</script>
@endsection
