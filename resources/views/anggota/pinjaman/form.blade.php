@extends('layouts.main')

@section('content')

<div class="container">

    {{-- ====== --}}
    {{-- HEADER --}}
    {{-- ====== --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">
            Pinjaman Saya > Pengajuan Pinjaman
        </h3>

        <a href="{{ route('anggota.pinjaman.index') }}" 
        class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    {{-- Toast Notification Container --}}
    <div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-3">
        
        {{-- Toast Sukses --}}
        @if (session('success'))
            <div class="toast-item flex items-center p-4 w-full max-w-xs text-gray-500 bg-white rounded-lg shadow-lg border-l-4 border-green-500 animate-slide-in">
                <div class="inline-flex flex-shrink-0 justify-center items-center w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                </div>
                <div class="ml-3 text-sm font-normal">{{ session('success') }}</div>
            </div>
        @endif

        {{-- Toast Error (Dari Service / Rule) --}}
        @if ($errors->any())
            <div class="toast-item flex items-center p-4 w-full max-w-xs text-gray-500 bg-white rounded-lg shadow-lg border-l-4 border-red-500 animate-slide-in">
                <div class="inline-flex flex-shrink-0 justify-center items-center w-8 h-8 text-red-500 bg-red-100 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                </div>
                <div class="ml-3 text-sm font-normal">
                    {{-- Menampilkan error pertama saja agar toast tidak terlalu panjang --}}
                    {{ $errors->first() }}
                </div>
            </div>
        @endif

    </div>

    <style>
    @keyframes slideInToast {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animate-slide-in {
        animation: slideInToast 0.4s ease-out forwards;
    }
    </style>

    {{-- ========================== --}}
    {{-- RINGKASAN PINJAMAN ANGGOTA --}}
    {{-- ========================== --}}
    <div class="mb-4 p-3 bg-gray-50 border rounded text-sm">
        <div class="font-semibold mb-1">
            Informasi Pinjaman Anda
        </div>

        <ul class="space-y-1 text-gray-700">
            <li>
                Pinjaman aktif:
                <strong>{{ $ringkasan['aktif'] }}</strong>
            </li>

            <li>
                Sisa pinjaman:
                <strong>
                    Rp {{ number_format($ringkasan['sisa'], 0, ',', '.') }}
                </strong>
            </li>
            <hr>
            <li class="text-xs text-gray-500">
                Top-up hanya boleh jika sisa pinjaman ≤ Rp 5.000.000
            </li>

            <li class="text-xs text-gray-500">
                Total pengajuan maksimal Rp 20.000.000
            </li>
        </ul>
    </div>

    {{-- ==================== --}}
    {{-- FORM AJUKAN PINJAMAN --}}
    {{-- ==================== --}}
    @if ($bolehAjukan)
        <div class="bg-blue-50 border border-blue-200 rounded-lg overflow-hidden shadow-sm">

            {{-- HEADER COLLAPSE --}}
            <div
                class="flex items-center justify-between px-4 py-3 cursor-pointer
                    bg-gradient-to-r from-blue-100 to-blue-50
                    hover:from-blue-200 hover:to-blue-100
                    transition"
                onclick="toggleAjukan()"
            >
                <div class="flex items-center gap-2">
                    {{-- ICON --}}
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-blue-600"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v16m8-8H4" />
                    </svg>

                    <h4 class="font-semibold text-blue-800">
                        Form Pengajuan Pinjaman
                    </h4>
                </div>

                {{-- CHEVRON --}}
                <svg id="iconAjukan"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-blue-600 transform transition-transform duration-300"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- BODY FORM (COLLAPSIBLE) --}}
            <div
                id="formAjukan"
                class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out"
            >
                <div class="p-4 bg-white border-t border-blue-200">

                    <form method="POST" action="{{ route('anggota.pinjaman.store') }}">
                        @csrf

                        {{-- JUMLAH --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-blue-900 mb-1">
                                Jumlah Pinjaman
                            </label>
                            
                            {{-- Input Tampilan (Masking) --}}
                            <input
                                type="text"
                                id="input_jumlah_format"
                                class="w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500"
                                placeholder="Rp 0"
                                oninput="formatRupiah(this)"
                                value="{{ old('jumlah_diajukan') ? 'Rp ' . number_format(old('jumlah_diajukan'), 0, ',', '.') : '' }}"
                                required>
                            
                            {{-- Input Asli (Hidden) - Ini yang dikirim ke Controller --}}
                            <input 
                                type="hidden" 
                                name="jumlah_diajukan" 
                                id="jumlah_asli" 
                                value="{{ old('jumlah_diajukan') }}"
                            >
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-blue-900 mb-1">Tenor (Bulan)</label>
                                <input type="number" 
                                    name="tenor" 
                                    id="tenor_utama"
                                    min="1" 
                                    max="24" 
                                    value="{{ old('tenor', 1) }}" {{-- Default ke 1 jika kosong --}}
                                    class="w-full border border-gray-300 rounded-md p-2" 
                                    placeholder="Contoh: 10" 
                                    required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-semibold mb-1">Rencana Bulan Pinjam</label>
                                <input type="month" 
                                    name="bulan_pinjam" 
                                    class="w-full border border-gray-300 rounded-md p-2" 
                                    min="{{ now()->format('Y-m') }}" {{-- Batasi mulai bulan ini --}}
                                    required>
                            </div>
                        </div>

                        {{-- KETERANGAN --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-blue-900 mb-1">
                                Keterangan
                            </label>
                            <textarea
                                name="keterangan"
                                rows="3"
                                class="w-full border border-gray-300 rounded-md p-2"
                                placeholder="Isi keterangan"
                            >{{ old('keterangan') }}</textarea>
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center
                                px-6 py-2 bg-blue-600 text-white font-semibold
                                rounded-md hover:bg-blue-700 transition
                                shadow-sm"
                        >
                            Ajukan Pinjaman
                        </button>
                    </form>

                </div>
            </div>
        </div>
    @else
        {{-- INFO JIKA TIDAK BOLEH AJUKAN --}}
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg">
            @if ($pengajuanAktif && $pengajuanAktif->status === 'diajukan')
                Pengajuan pinjaman Anda sedang diproses.
            @elseif ($pengajuanAktif && $pengajuanAktif->status === 'disetujui')
                Pengajuan pinjaman Anda sudah disetujui dan menunggu pencairan.
            @else
                Anda belum bisa mengajukan pinjaman baru.
                <div class="text-xs mt-1 text-yellow-600">
                    Top-up hanya boleh jika sisa pinjaman ≤ Rp 5.000.000
                </div>
            @endif
        </div>
    @endif


    {{-- ================= --}}
    {{-- RIWAYAT PENGAJUAN --}}
    {{-- ================= --}}
    <hr class="my-6">

    <h4 class="text-md font-semibold mb-3">
        Riwayat Pengajuan Pinjaman Saya
    </h4>

    @if ($riwayatPengajuan->isEmpty())
        <p class="text-sm text-gray-500">
            Belum ada riwayat pengajuan pinjaman.
        </p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase">
                        <th class="p-3 border-b text-left">Tanggal</th>
                        <th class="p-3 border-b text-right">Jumlah</th>
                        <th class="p-3 border-b text-center">Tenor</th>
                        <th class="p-3 border-b text-center">Bulan Pinjam</th>
                        <th class="p-3 border-b text-center">Status</th>
                        <th class="p-3 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse ($riwayatPengajuan as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 border-b">{{ $item->tanggal_pengajuan->format('d/m/Y') }}</td>
                            <td class="p-3 border-b text-right font-medium">
                                Rp {{ number_format($item->jumlah_diajukan, 0, ',', '.') }}
                            </td>
                            <td class="p-3 border-b text-center">{{ $item->tenor }} Bulan</td>
                            <td class="p-3 border-b text-center">
                                {{-- Format bulan dari YYYY-MM ke Bulan YYYY --}}
                                {{ \Carbon\Carbon::parse($item->bulan_pinjam)->translatedFormat('F Y') }}
                            </td>
                            <td class="p-3 border-b text-center">
                                @php
                                    $statusColor = [
                                        'diajukan' => 'bg-yellow-100 text-yellow-700',
                                        'disetujui' => 'bg-blue-100 text-blue-700',
                                        'ditolak' => 'bg-red-100 text-red-700',
                                    ][$item->status] ?? 'bg-green-100 text-green-700';
                                @endphp
                                <span class="{{ $statusColor }} px-2 py-1 rounded-full text-[10px] font-bold uppercase">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="border px-3 py-2 text-center">
                                @if ($item->status === 'diajukan')
                                    {{-- Kontainer Flex dengan items-center untuk memastikan rata tengah secara vertikal --}}
                                    <div class="flex items-center justify-center gap-2">
                                        
                                        {{-- Tombol Edit --}}
                                        <button type="button" 
                                            onclick="openEditModal({{ $item->id }}, 
                                            {{ $item->jumlah_diajukan }}, 
                                            {{ $item->tenor }},
                                            '{{ \Carbon\Carbon::parse($item->bulan_pinjam)->format('Y-m') }}',
                                            '{{ addslashes($item->keterangan) }}')"
                                            class="text-blue-600 hover:underline leading-none">
                                            Edit
                                        </button>

                                        {{-- Pemisah --}}
                                        <span class="text-gray-300">|</span>

                                        {{-- Form Batal - Ditambahkan class 'flex' atau 'inline-block' dan 'm-0' --}}
                                        <form action="{{ route('anggota.pinjaman.destroy', $item->id) }}" 
                                            method="POST" 
                                            onsubmit="return confirm('Batalkan?')" 
                                            class="flex items-center m-0"> {{-- Kunci perbaikan di sini --}}
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline leading-none">
                                                Batal
                                            </button>
                                        </form>

                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">Sudah diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        {{-- Empty state tetap seperti sebelumnya --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

{{-- ==================== --}}
{{-- MODAL EDIT PENGAJUAN --}}
{{-- ==================== --}}
<div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded shadow p-5">
        <h4 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">Edit Pengajuan</h4>

        <form method="POST" action="" id="formEditPinjaman">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                {{-- Input Jumlah --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase">Jumlah Pinjaman</label>
                    
                    {{-- Input Tampilan (Masking) --}}
                    <input type="text" 
                        id="edit_jumlah_format" 
                        class="w-full border rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500 outline-none"
                        oninput="formatRupiahModal(this)"
                        placeholder="Rp 0">

                    {{-- Input Asli (Hidden) - ID tetap edit_jumlah agar tidak merusak JS openEditModal --}}
                    <input type="hidden" name="jumlah_diajukan" id="edit_jumlah">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Input Tenor --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Tenor (Bulan)</label>
                        <input type="number" name="tenor" id="edit_tenor" 
                               class="w-full border rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    {{-- Input Bulan --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Mulai Pinjam</label>
                        <input type="month" 
                            name="bulan_pinjam" 
                            id="edit_bulan" 
                            class="w-full border rounded px-3 py-2 mt-1"
                            min="{{ now()->format('Y-m') }}" {{-- Batasi mulai bulan ini --}}
                            required>
                    </div>
                </div>

                {{-- Input Keterangan --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase">Tujuan/Keterangan</label>
                    <textarea name="keterangan" id="edit_keterangan" rows="3" 
                              class="w-full border rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-500 font-medium hover:text-gray-700">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-md hover:bg-blue-700 transition">
                    Update Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // 1. FUNGSI MASKING RUPIAH (Untuk Form Utama)
    function formatRupiah(element) {
        let value = element.value.replace(/[^,\d]/g, "").toString();
        let split = value.split(",");
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }
        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        element.value = value ? "Rp " + rupiah : "";

        // Kirim angka bersih ke hidden input form utama
        const murni = value.replace(/\./g, "");
        const target = document.getElementById('jumlah_asli');
        if(target) target.value = murni;
    }

    // 2. FUNGSI MASKING RUPIAH (Untuk Modal Edit)
    function formatRupiahModal(element) {
        let value = element.value.replace(/[^,\d]/g, "").toString();
        let split = value.split(",");
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }
        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        element.value = value ? "Rp " + rupiah : "";

        // Kirim angka bersih ke hidden input modal edit
        const murni = value.replace(/\./g, "");
        const target = document.getElementById('edit_jumlah');
        if(target) target.value = murni;
    }

    // 3. FUNGSI HELPER (Angka ke Format Rp saat buka modal)
    function formatAngkaKeRupiah(angka) {
        if (!angka) return '';
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // 4. MODAL CONTROL
    function openEditModal(id, jumlah, tenor, bulan, keterangan) {
        const form = document.getElementById('formEditPinjaman');
        form.action = '/anggota/pinjaman/' + id; 

        document.getElementById('edit_jumlah').value = jumlah;
        
        // Menggunakan helper agar langsung muncul format Rp
        const displayElement = document.getElementById('edit_jumlah_format');
        if(displayElement) displayElement.value = formatAngkaKeRupiah(jumlah);

        document.getElementById('edit_tenor').value = tenor;
        document.getElementById('edit_bulan').value = bulan;
        document.getElementById('edit_keterangan').value = keterangan;
        
        const modal = document.getElementById('modalEdit');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('modalEdit');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // 5. EVENT LISTENERS & UI
    function toggleAjukan() {
        const body = document.getElementById('formAjukan');
        const icon = document.getElementById('iconAjukan');
        if (body.style.maxHeight && body.style.maxHeight !== '0px') {
            body.style.maxHeight = '0px';
            icon.classList.remove('rotate-180');
        } else {
            body.style.maxHeight = body.scrollHeight + 'px';
            icon.classList.add('rotate-180');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Auto-format jika ada 'old value' dari Laravel
        const inputFormat = document.getElementById('input_jumlah_format');
        if (inputFormat && inputFormat.value) {
            formatRupiah(inputFormat);
        }

        // Auto-open form jika ada error
        @if ($errors->any())
            const body = document.getElementById('formAjukan');
            const icon = document.getElementById('iconAjukan');
            if(body) {
                body.style.maxHeight = body.scrollHeight + 'px';
                icon.classList.add('rotate-180');
            }
        @endif

        // Toast auto-hide
        document.querySelectorAll('.toast-item').forEach(toast => {
            setTimeout(() => {
                toast.style.transition = "all 0.5s ease";
                toast.style.opacity = "0";
                toast.style.transform = "translateX(100%)";
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        });
    });
</script>
