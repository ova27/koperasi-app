<div class="p-2">
    {{-- Info Ringkas --}}
    <div class="flex justify-between items-end mb-4 pb-4 border-b border-gray-100">
        <div>
            <h4 class="text-sm font-bold text-gray-800">{{ $pengajuan->anggota->nama }}</h4>
            <p class="text-xs text-gray-500">Diajukan: {{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</p>
        </div>
        <div class="text-right">
            <span class="text-[10px] text-gray-400 uppercase font-bold block">Alasan</span>
            <p class="text-xs text-gray-600 italic">"{{ $pengajuan->tujuan ?? '-' }}"</p>
        </div>
    </div>

    {{-- Form Utama --}}
    <form action="{{ route('admin.pinjaman.pengajuan.setujui', $pengajuan->id) }}" method="POST" id="formApproval">
        @csrf
        <div class="space-y-4">
            {{-- Input Realisasi --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah Realisasi</label>
                <input type="text" 
                    id="approval_jumlah_format"
                    oninput="formatRupiahKetua(this)" 
                    value="Rp {{ number_format($pengajuan->jumlah_diajukan, 0, ',', '.') }}"
                    class="w-full border border-gray-300 rounded-lg p-2.5 font-bold text-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                
                <input type="hidden" name="jumlah_diajukan" id="approval_jumlah_asli" value="{{ $pengajuan->jumlah_diajukan }}">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tenor (Bulan)</label>
                    <input type="number" name="tenor" value="{{ $pengajuan->tenor }}" 
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Mulai Pinjam</label>
                    <input type="month" name="bulan_pinjam" 
                        value="{{ \Carbon\Carbon::parse($pengajuan->bulan_pinjam)->format('Y-m') }}" 
                        min="{{ now()->format('Y-m') }}"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            {{-- Alasan/Catatan Ketua (Penting jika menolak) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan/Alasan (ditolak)</label>
                <textarea name="alasan" rows="2" placeholder="Alasan jika ditolak..."
                    class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="grid grid-cols-2 gap-2 mt-6">
            <button type="submit" 
                class="bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg font-bold text-sm transition shadow-sm">
                SETUJUI
            </button>
            
            {{-- Tombol Tolak memicu form yang berbeda atau method berbeda --}}
            <button type="button" 
                onclick="submitTolak()"
                class="bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 py-2.5 rounded-lg font-bold text-sm transition">
                TOLAK
            </button>
        </div>

        <button type="button" onclick="closeModalDetail()" 
            class="w-full mt-3 py-2 text-xs text-gray-400 font-medium hover:text-gray-600 transition">
            BATAL & TUTUP
        </button>
    </form>
</div>

{{-- Hidden Form khusus Tolak --}}
<form id="formTolak" action="{{ route('admin.pinjaman.pengajuan.tolak', $pengajuan->id) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="alasan_tolak" id="hidden_alasan_tolak">
</form>

<script>
    function formatRupiahKetua(element) {
        // Logic yang sama persis dengan form anggota
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

        // Update hidden input dengan angka murni
        const murni = value.replace(/\./g, "");
        document.getElementById('approval_jumlah_asli').value = murni;
    }

    function submitTolak() {
        if (confirm('Yakin ingin menolak pengajuan ini?')) {
            // Ambil catatan dari textarea form utama untuk dijadikan alasan tolak
            const catatan = document.querySelector('textarea[name="catatan_ketua"]').value;
            document.getElementById('hidden_alasan_tolak').value = catatan;
            document.getElementById('formTolak').submit();
        }
    }
</script>