<div class="space-y-4">

<style>
    @keyframes slideInToast {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animate-slide-in {
        animation: slideInToast 0.4s ease-out forwards;
    }
</style>

<div class="bg-white border rounded-xl p-4 shadow-sm hover:shadow-md transition">
    <h2 class="font-semibold text-gray-800 mb-4 text-sm">
        Pengajuan Pinjaman Menunggu Approval
    </h2>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto mb-4">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="p-3 border-b text-left">Tanggal Pengajuan</th>
                    <th class="p-3 border-b text-left">Anggota</th>
                    <th class="p-3 border-b text-left">Rencana Pinjam</th>
                    <th class="p-3 border-b text-right">Jumlah</th>
                    <th class="p-3 border-b text-center">Tenor</th>
                    <th class="p-3 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($pengajuans as $p)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 border-b text-gray-500">
                            {{ $p->tanggal_pengajuan->format('d/m/Y') }}
                        </td>
                        <td class="p-3 border-b">{{ $p->anggota->nama }}</td>
                        <td class="p-3 border-b">
                            {{ \Carbon\Carbon::parse($p->bulan_pinjam)->translatedFormat('F Y') }}
                        </td>
                        <td class="p-3 border-b text-right font-semibold text-gray-800">
                            Rp {{ number_format($p->jumlah_diajukan, 0, ',', '.') }}
                        </td>
                        <td class="p-3 border-b text-center">{{ $p->tenor }} Bulan</td>
                        <td class="p-3 border-b text-center space-x-0">
                            {{-- Tombol Approval --}}
                            <button 
                                onclick="openModalDetail({{ $p->id }})"
                                title="Approval"
                                class="px-3 py-1.5 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition shadow-sm">
                                Approval
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                Tidak ada pengajuan pending.
                            </td>
                        </tr>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengajuans->hasPages())
        {{-- PAGINATION --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-2 mb-1">
            <p class="text-sm text-gray-600">
                Menampilkan
                <span class="font-semibold text-gray-900">{{ $pengajuans->firstItem() ?? 0 }}</span>
                sampai
                <span class="font-semibold text-gray-900">{{ $pengajuans->lastItem() ?? 0 }}</span>
                dari
                <span class="font-semibold text-gray-900">{{ $pengajuans->total() }}</span>
                data
            </p>

            <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                {{ $pengajuans->links('vendor.pagination.custom') }}
            </div>
        </div>
    @endif
    
</div>

{{-- Tampilan Riwayat Approval --}}
<div x-data="{ open: false }"
    class="pt-4 border-t border-gray-200 bg-lime-50 border rounded-xl p-4 shadow-sm hover:shadow-md transition">
    
    {{-- HEADER (CLICKABLE) --}}
    <button 
        @click="open = !open"
        class="w-full flex items-center justify-between px-3 py-2 rounded-lg 
            bg-gray-50 hover:bg-gray-100 
            border border-gray-200 
            transition group"
    >
        <div class="flex items-center gap-2">
            {{-- ICON --}}
            <svg class="w-4 h-4 text-gray-500 group-hover:text-gray-700 transition"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7h18M3 12h18M3 17h18" />
            </svg>

            <h2 class="font-semibold text-gray-800 text-sm">
                Riwayat Approval
            </h2>
        </div>

        {{-- ICON PANAH --}}
        <svg 
            :class="{ 'rotate-180': open }"
            class="w-4 h-4 text-gray-500 transform transition duration-200 group-hover:text-gray-700"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- CONTENT --}}
    <div 
        x-show="open"
        x-transition
        x-cloak
        class="mt-4 space-y-3"
    >
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto mb-2">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                        <th class="px-4 py-3 text-left">Tanggal Pengajuan</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-right">Jumlah</th>
                        <th class="px-4 py-3 text-center">Tenor</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Waktu Proses</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($riwayatApproval as $r)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $r->tanggal_pengajuan->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $r->anggota->nama }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($r->jumlah_diajukan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->tenor }} Bulan</td>
                            <td class="px-4 py-3 text-center">
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
                            <td class="px-4 py-3 text-center">
                                {{-- Jam real-time Indonesia --}}
                                {{ $r->updated_at->timezone('Asia/Jakarta')->format('d/m/y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-center space-x-0">
                                @if($r->status !== 'dicairkan')
                                    <button onclick="openModalDetail({{ $r->id }})" 
                                        class="inline-flex items-center justify-center rounded-md border border-amber-200 bg-amber-50 p-1 text-xs text-amber-700 transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-400"
                                        title="Ubah Persetujuan">
                                        <span aria-hidden="true">✏️</span>
                                    </button>
                                @else
                                    <span class="text-gray-500 text-[10px] italic">Sudah Dicairkan</span>
                                @endif

                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-10 text-center">Belum ada riwayat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-2">
            <p class="text-sm text-gray-600">
                Menampilkan
                <span class="font-semibold text-gray-900">{{ $riwayatApproval->firstItem() ?? 0 }}</span>
                sampai
                <span class="font-semibold text-gray-900">{{ $riwayatApproval->lastItem() ?? 0 }}</span>
                dari
                <span class="font-semibold text-gray-900">{{ $riwayatApproval->total() }}</span>
                data
            </p>

            <div class="flex justify-center sm:justify-end w-full sm:w-auto">
                {{ $riwayatApproval->links('vendor.pagination.custom') }}
            </div>
        </div>
        
    </div>
</div>

{{-- MODAL APPROVAL --}}
<div id="modalDetailPengajuan" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-lg shadow-2xl overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
        <div class="px-4 py-3 border-b flex justify-between items-center bg-gray-50 flex-shrink-0">
            <h3 class="font-bold text-gray-800 text-sm">Detail Pengajuan Pinjaman</h3>
            <button onclick="closeModalDetail()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <div id="modalBody" class="flex-1 overflow-y-auto">
            {{-- Konten dari AJAX akan muncul di sini --}}
            <div class="flex justify-center italic text-gray-500 p-4">Memuat data...</div>
        </div>
    </div>
</div>
{{-- MODAL PREVIEW PINJAMAN --}}
<div id="modalPreviewPinjaman" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-lg shadow-2xl overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
        <div class="px-4 py-3 border-b flex justify-between items-center bg-gray-50 flex-shrink-0">
            <h3 class="font-bold text-gray-800 text-sm">Pinjaman Aktif</h3>
            <button onclick="closeModalPreview()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <div id="modalPreviewBody" class="flex-1 overflow-y-auto">
            <div class="flex justify-center italic text-gray-500 p-4">Memuat data...</div>
        </div>
    </div>
</div>

<script>
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

    document.querySelectorAll('.toast-item').forEach(toast => {
            setTimeout(() => {
                toast.style.transition = "all 0.5s ease";
                toast.style.opacity = "0";
                toast.style.transform = "translateX(100%)";
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        });
        
    function openModalDetail(id) {
        const modal = document.getElementById('modalDetailPengajuan');
        const body = document.getElementById('modalBody');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        body.innerHTML = '<div class="flex justify-center py-4">Loading...</div>';

        // Ambil konten via AJAX
        fetch(`/admin/pinjaman/pengajuan/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => { 
            body.innerHTML = html;
            
            // Attach event listeners after content is loaded
            const modal = document.getElementById('modalDetailPengajuan');
            modal.addEventListener('click', function(e) {
                if (e.target.classList.contains('toggle-reject-btn') || e.target.closest('.toggle-reject-btn')) {
                    const form = document.getElementById('rejectForm');
                    if (form) form.classList.toggle('hidden');
                }
            });
            
            const formApproval = document.getElementById('formApproval');
            if (formApproval) {
                formApproval.addEventListener('submit', function() {
                    const btn = document.getElementById('btnApprove');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';
                    }
                });
            }
        })
        .catch(() => { body.innerHTML = '<span class="text-red-500">Gagal mengambil data.</span>'; });
    }

    function closeModalDetail() {
        document.getElementById('modalDetailPengajuan').classList.add('hidden');
        document.getElementById('modalDetailPengajuan').classList.remove('flex');
    }

    function openModalPreview(id) {
        const modal = document.getElementById('modalPreviewPinjaman');
        const body = document.getElementById('modalPreviewBody');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        body.innerHTML = '<div class="flex justify-center py-4">Loading...</div>';

        fetch(`/admin/pinjaman/${id}/preview`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => { body.innerHTML = html; })
        .catch(() => { body.innerHTML = '<span class="text-red-500">Gagal mengambil data.</span>'; });
    }

    function closeModalPreview() {
        document.getElementById('modalPreviewPinjaman').classList.add('hidden');
        document.getElementById('modalPreviewPinjaman').classList.remove('flex');
    }

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

    function handleTolak() {
        const textarea = document.getElementById('alasanTolak');

        if (!textarea) {
            alert('Textarea tidak ditemukan!');
            return false;
        }

        const val = textarea.value.trim();

        if (!val) {
            alert('Alasan penolakan wajib diisi!');
            return false;
        }

        if (!confirm('Yakin ingin menolak pengajuan ini?')) {
            return false;
        }

        document.getElementById('inputAlasanTolak').value = val;

        return true; // lanjut submit
    }

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

@if (session('open_modal_pengajuan'))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        openModalDetail({{ session('pengajuan_id') }});
    });
    </script>
@endif

<style>
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .animate-slide-in { animation: slideIn 0.4s ease-out; }
</style>