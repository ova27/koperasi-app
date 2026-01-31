@extends('layouts.main')

@section('title', 'Data Pengajuan Pinjaman')

@section('content')

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

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold">Daftar Pengajuan Pinjaman</h1>
</div>

{{-- 2. TABEL --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-50 text-gray-600 text-sm">
                <th class="p-3 border-b text-left">Anggota</th>
                <th class="p-3 border-b text-right">Jumlah</th>
                <th class="p-3 border-b text-center">Tanggal</th>
                <th class="p-3 border-b text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            @forelse ($pengajuans as $p)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3 border-b">{{ $p->anggota->nama }}</td>
                    <td class="p-3 border-b text-right font-semibold text-gray-800">
                        Rp {{ number_format($p->jumlah_diajukan, 0, ',', '.') }}
                    </td>
                    <td class="p-3 border-b text-center text-gray-500">
                        {{ $p->tanggal_pengajuan->format('d/m/Y') }}
                    </td>
                    <td class="p-3 border-b text-center">
                        {{-- Tombol Detail memanggil fungsi JS --}}
                        <button onclick="openModalDetail({{ $p->id }})" 
                                class="text-blue-600 hover:text-blue-800 font-medium underline">
                            Detail
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-gray-400">Tidak ada pengajuan pending.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Tampilan Riwayat Approval --}}
<div class="mt-12">
    <h2 class="text-lg font-bold mb-4 text-gray-700">Riwayat Approval</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-[11px] uppercase tracking-wider">
                    <th class="p-3 border-b text-left font-bold">Anggota</th>
                    <th class="p-3 border-b text-right font-bold">Jumlah</th>
                    <th class="p-3 border-b text-center font-bold">Tenor</th>
                    <th class="p-3 border-b text-center font-bold">Status</th>
                    <th class="p-3 border-b text-center font-bold">Waktu Proses</th>
                    <th class="p-3 border-b text-center font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse ($riwayatApproval as $r)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="p-3 font-medium text-gray-800">{{ $r->anggota->nama }}</td>
                        <td class="p-3 text-right font-semibold">Rp {{ number_format($r->jumlah_diajukan, 0, ',', '.') }}</td>
                        <td class="p-3 text-center text-gray-600">{{ $r->tenor }} Bulan</td>
                        <td class="p-3 text-center">
                            @php
                                $statusClass = [
                                    'disetujui' => 'bg-blue-100 text-blue-700',
                                    'dicairkan' => 'bg-green-100 text-green-700',
                                    'ditolak'   => 'bg-red-100 text-red-700'
                                ][$r->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="{{ $statusClass }} px-2 py-0.5 rounded-full text-[10px] font-bold uppercase">
                                {{ $r->status }}
                            </span>
                        </td>
                        <td class="p-3 text-center text-gray-500 text-xs">
                            {{-- Jam real-time Indonesia --}}
                            {{ $r->updated_at->timezone('Asia/Jakarta')->format('d/m/y H:i') }}
                        </td>
                        <td class="p-3 text-center">
                            @if($r->status !== 'dicairkan')
                                <button onclick="openModalDetail({{ $r->id }})" 
                                    class="text-blue-600 hover:text-blue-800 font-bold text-xs underline decoration-dotted">
                                    Ubah
                                </button>
                            @else
                                <span class="text-gray-300 text-[10px] italic">Locked</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400 italic text-xs">Belum ada riwayat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 3. MODAL STRUCTURE --}}
<div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-lg rounded-lg shadow-2xl overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Detail Pengajuan</h3>
            <button onclick="closeModalDetail()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div id="modalBody" class="p-6">
            {{-- Konten dari AJAX akan muncul di sini --}}
            <div class="flex justify-center italic text-gray-500">Memuat data...</div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.toast-item').forEach(toast => {
            setTimeout(() => {
                toast.style.transition = "all 0.5s ease";
                toast.style.opacity = "0";
                toast.style.transform = "translateX(100%)";
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        });
        
    function openModalDetail(id) {
        const modal = document.getElementById('modalDetail');
        const body = document.getElementById('modalBody');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        body.innerHTML = '<div class="flex justify-center py-4">Loading...</div>';

        // Ambil konten via AJAX
        fetch(`/admin/pinjaman/pengajuan/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => { body.innerHTML = html; })
        .catch(() => { body.innerHTML = '<span class="text-red-500">Gagal mengambil data.</span>'; });
    }

    function closeModalDetail() {
        document.getElementById('modalDetail').classList.add('hidden');
        document.getElementById('modalDetail').classList.remove('flex');
    }

    // Auto-hide Toast
    setTimeout(() => {
        const toast = document.getElementById('toast-notif');
        if(toast) toast.style.display = 'none';
    }, 4000);

    function formatRupiahKetua(element) {
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

        const murni = value.replace(/\./g, "");
        document.getElementById('approval_jumlah_asli').value = murni;
    }

    function submitTolak() {
        if (confirm('Apakah Anda yakin ingin MENOLAK pengajuan ini?')) {
            const catatan = document.querySelector('textarea[name="catatan_ketua"]').value;
            document.getElementById('hidden_alasan_tolak').value = catatan;
            document.getElementById('formTolak').submit();
        }
    }
</script>

<style>
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .animate-slide-in { animation: slideIn 0.4s ease-out; }
</style>
@endsection