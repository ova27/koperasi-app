<div class="max-w-full mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="p-4 max-h-[70vh] overflow-y-auto mb-4">
        
        {{-- Info Ringkas --}}
        <div class="rounded-lg p-2 mb-4 border-b border-gray-200">
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800">{{ $pengajuan->anggota->nama }}</h4>
                        <p class="text-xs text-gray-500">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="text-right text-xs hidden sm:block">
                    <span class="text-gray-400 font-bold block">Tujuan</span>
                    <p class="text-gray-600 italic line-clamp-2">{{ $pengajuan->tujuan ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- INFORMASI PINJAMAN BERJALAN --}}
        @if($pinjaman)
            @include('admin.pinjaman.pengajuan._preview', ['pinjaman' => $pinjaman])
        @else
            <div class="text-xs text-gray-400 italic mb-4 px-2">
                Tidak ada pinjaman aktif
            </div>
        @endif

        {{-- Flash Error --}}
        @if (!empty($validationErrors))
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($validationErrors as $fieldErrors)
                        @foreach ($fieldErrors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Persetujuan --}}
        <form action="{{ route('admin.pinjaman.pengajuan.setujui', $pengajuan->id) }}" method="POST" id="formApproval" class="mt-2 px-4 py-3 bg-green-50 border border-green-200 rounded">
            @csrf
            <div class="space-y-3">
                {{-- Input Jumlah Realisasi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 items-center">
                        <svg class="w-4 h-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                        Jumlah Realisasi
                    </label>
                    <input type="text"
                        id="approval_jumlah_format"
                        oninput="formatRupiahKetua(this)"
                        value="Rp {{ number_format($pengajuan->jumlah_diajukan, 0, ',', '.') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-bold text-blue-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    <input type="hidden" name="jumlah_diajukan" id="approval_jumlah_asli" value="{{ $pengajuan->jumlah_diajukan }}">
                </div>

                {{-- Grid untuk Tenor dan Bulan Pinjam --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Tenor
                        </label>
                        <input type="number" name="tenor" value="{{ $pinjaman ? $pinjaman->tenor : $pengajuan->tenor }}"
                            class="w-full border border-gray-300 rounded px-2 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                            placeholder="12" min="1" max="120">
                        @if($pinjaman)
                            <div class="text-[11px] text-blue-500 mt-1">Mengikuti Pinjaman Aktif (jika ada), ubah jika diperlukan.</div>
                        @endif
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Mulai
                        </label>
                        <input type="month" name="bulan_pinjam"
                            value="{{ \Carbon\Carbon::parse($pengajuan->bulan_pinjam)->format('Y-m') }}"
                            min="{{ now()->format('Y-m') }}"
                            class="w-full border border-gray-300 rounded px-2 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2 mt-5 mb-2">
                <button type="submit"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded font-bold text-xs transition shadow-sm flex items-center justify-center"
                    id="btnApprove">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    SETUJUI
                </button>

                <button type="button"
                    class="toggle-reject-btn flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded font-bold text-xs transition shadow-sm flex items-center justify-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    TOLAK
                </button>
            </div>
        </form>

        {{-- Form Tolak (Hidden by default) --}}
        <form method="POST" action="{{ route('admin.pinjaman.pengajuan.tolak', $pengajuan->id) }}" id="rejectForm" class="hidden mt-4 bg-red-50 border border-red-200 rounded p-3">
            @csrf
            <h3 class="text-sm font-semibold text-red-800 mb-2">
                Alasan Penolakan
            </h3>
            <textarea name="alasan_tolak" required
                class="w-full border border-red-300 rounded p-2 text-xs focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none mb-2"
                placeholder="Jelaskan alasan penolakan..." rows="3"></textarea>
            <div class="flex gap-2">
                <button type="submit"
                    onclick="return confirm('Yakin ingin menolak?')"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-2 rounded font-bold text-xs transition">
                    Konfirmasi
                </button>
                <button type="button"
                    class="toggle-reject-btn flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-2 rounded font-bold text-xs transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>