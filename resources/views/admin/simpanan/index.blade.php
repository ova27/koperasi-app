@extends('layouts.main')

@section('title', 'Transaksi Simpanan Anggota')
@section('page-title', 'Transaksi Simpanan Anggota')

@section('content')
<div class="space-y-6">

@php(
    $canManageSimpanan = auth()->user()->hasRole('bendahara')
)

<div x-data="{ tab: 'wajib' }" class="space-y-4">

    {{-- ================= TAB NAVIGATION ================= --}}
    <div class="border-b flex gap-6 text-sm font-medium">
        <button @click="tab = 'wajib'"
            :class="tab === 'wajib' ? 'border-b-2 border-black text-black font-medium' : 'text-gray-400'"
            class="pb-2">
            Wajib
        </button>

        <button @click="tab = 'manual'"
            :class="tab === 'manual' ? 'border-b-2 border-black text-black font-medium' : 'text-gray-400'"
            class="pb-2">
            Manual
        </button>

        <button @click="tab = 'ambil'"
            :class="tab === 'ambil' ? 'border-b-2 border-black text-black font-medium' : 'text-gray-400'"
            class="pb-2">
            Pengambilan
        </button>

        <button @click="tab = 'keluar'"
            :class="tab === 'keluar' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-400'"
            class="pb-2">
            Pensiun / Mutasi
        </button>
    </div>

    
    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div id="flash-message" class="px-4 py-4 mt-6 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="closeFlashMessage()" class="ml-4 hover:text-green-900">×</button>
        </div>
    @endif

    @unless($canManageSimpanan)
        <div class="px-2 py-2 mt-6 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg text-sm">
            Hanya Bendahara yang dapat melakukan transaksi simpanan
        </div>
    @endunless

    @if($errors->any())
        <div id="flash-message" class="px-4 py-4 mt-6 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex justify-between items-center">
            <span>{{ $errors->first() }}</span>
            <button onclick="closeFlashMessage()" class="ml-4 hover:text-red-900">×</button>
        </div>
    @endif


    {{-- ================= --}}
    {{-- A. SIMPANAN WAJIB --}}
    {{-- ================= --}}
    <div x-show="tab === 'wajib'" 
        x-transition.opacity.duration.200ms
        class="bg-white border rounded-lg p-4 space-y-4">
                    
        <h2 class="font-semibold text-gray-800 text-sm">
            Input Simpanan Wajib (Bulanan)
        </h2>
        
        @if ($sudahGenerateWajib)
            <div class="text-sm italic">
                Simpanan wajib bulan ini sudah digenerate.
            </div>
        @else
            <div class="text-sm italic">
                Simpanan wajib bulan ini belum digenerate.
            </div>
        @endif
        <span class="text-xs text-gray-500 mb-4 block italic">
            Bulan: <strong>{{ \Carbon\Carbon::parse($bulan)->format('F Y') }}</strong>
        </span>

        <form action="{{ route('admin.simpanan.generate-wajib') }}" method="POST">
            @csrf
            <fieldset @disabled(!$canManageSimpanan)>
                <input type="hidden" name="bulan" value="{{ $bulan }}">

                @if ($sudahGenerateWajib)
                <button
                    type="button"
                    disabled
                    class="px-4 py-2 text-sm font-semibold text-white bg-gray-400 rounded-lg cursor-not-allowed shadow-sm flex items-center gap-2"
                >
                    Sudah Digenerate
                </button>
            @else
                <button
                    type="submit"
                    @disabled(!$canManageSimpanan)
                    class="px-4 py-2 text-sm font-semibold text-white rounded-lg transition shadow-sm {{ $canManageSimpanan ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed' }}"
                >
                    Generate Simpanan Wajib
                </button>
            @endif
            </fieldset>
        </form>
        
    </div>


    {{-- ======================== --}}
    {{-- B. INPUT SIMPANAN MANUAL --}}
    {{-- ======================== --}}
    <div x-show="tab === 'manual'" 
        x-transition.opacity.duration.200ms
        class="bg-white border rounded-lg p-4 space-y-4">

        <h2 class="font-semibold text-gray-800 text-sm">
            Input Simpanan (Manual)
        </h2>

        <p class="text-sm text-gray-500 mb-4 leading-relaxed">
            Digunakan untuk:
            <br>• Simpanan sukarela
            <br>• Simpanan wajib anggota baru / terlewat
            <br>• Simpanan pokok (1x di awal keanggotaan)
        </p>

        <form method="POST" action="{{ route('admin.simpanan.store-manual') }}" class="space-y-4">
            @csrf
            <fieldset @disabled(!$canManageSimpanan)>

            {{-- ANGGOTA --}}
            <div>
                <label class="text-xs text-gray-500">Anggota</label>
                <select name="anggota_id" 
                        id="anggotaSelectSimpan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none" required>
                    <option value="" disabled selected>Pilih Anggota</option>
                    @foreach ($anggotas as $anggota)
                        <option value="{{ $anggota->id }}">
                            {{ $anggota->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="saldoBoxSimpan" class="hidden mt-3 p-3 bg-gray-50 border rounded-lg text-sm">
                <div class="flex justify-between">
                    <span>Pokok</span>
                    <span id="saldoPokokSimpan">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span>Wajib</span>
                    <span id="saldoWajibSimpan">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span>Sukarela</span>
                    <span id="saldoSukarelaSimpan">Rp 0</span>
                </div>
                <div class="flex justify-between border-t mt-2 pt-2 font-bold">
                    <span>Total</span>
                    <span id="saldoTotalSimpan">Rp 0</span>
                </div>
            </div>

            {{-- JENIS --}}
            <div>
                <label class="text-xs text-gray-500">Jenis Simpanan</label>
                <select name="jenis_simpanan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    <option value="" disabled selected>Pilih Jenis</option>
                    <option value="pokok">Pokok</option>
                    <option value="wajib">Wajib</option>
                    <option value="sukarela">Sukarela</option>
                </select>
            </div>

            {{-- JUMLAH --}}
            <div>
                <label class="text-xs text-gray-500">Jumlah</label>
                <input
                    type="number"
                    placeholder="Masukkan Jumlah Simpanan"
                    name="jumlah"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"
                    required>
            </div>

            {{-- KETERANGAN --}}
            <div>
                <label class="text-xs text-gray-500">Keterangan (opsional)</label>
                <input
                    type="text"
                    placeholder="Keterangan"
                    name="keterangan"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"
                >
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    @disabled(!$canManageSimpanan)
                    class="px-4 py-2 text-sm font-semibold text-white rounded-lg transition shadow-sm {{ $canManageSimpanan ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed' }}"
                >
                    Simpan
                </button>
            </div>
            </fieldset>
        </form>
        
    </div>

    {{-- ======================== --}}
    {{-- C. PENGAMBILAN SIMPANAN  --}}
    {{-- ======================== --}}
    <div x-show="tab === 'ambil'" 
        x-transition.opacity.duration.200ms
        class="bg-white border rounded-lg p-4 space-y-4">

        <h2 class="font-semibold text-gray-800 text-sm">
            Pengambilan Simpanan Sukarela Anggota
        </h2>

        <form method="POST" action="{{ route('admin.simpanan.ambil') }}" class="space-y-4">
            @csrf
            <fieldset @disabled(!$canManageSimpanan)>

            {{-- ANGGOTA --}}
            <div>
                <label class="text-xs text-gray-500">Anggota</label>
                <select name="anggota_id" 
                        id="anggotaSelectAmbil"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 outline-none" required>
                    <option value="" disabled selected>Pilih Anggota</option>
                    @foreach ($anggotas as $anggota)
                        <option value="{{ $anggota->id }}">
                            {{ $anggota->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="saldoBoxAmbil" class="hidden mt-3 p-3 bg-gray-50 border rounded-lg text-sm">
                <div class="flex justify-between">
                    <span>Pokok</span>
                    <span id="saldoPokokAmbil">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span>Wajib</span>
                    <span id="saldoWajibAmbil">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span>Sukarela</span>
                    <span id="saldoSukarelaAmbil" class="font-semibold">Rp 0</span>
                </div>
                <div class="flex justify-between border-t mt-2 pt-2 font-bold">
                    <span>Total</span>
                    <span id="saldoTotalAmbil">Rp 0</span>
                </div>
            </div>

            {{-- JUMLAH --}}
            <div>
                <label class="text-xs text-gray-500">Jumlah</label>
                <input
                    type="number"
                    name="jumlah"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 outline-none"
                    required
                >
            </div>

            {{-- KETERANGAN --}}
            <div>
                <label class="text-xs text-gray-500">Keterangan</label>
                <input
                    type="text"
                    name="keterangan"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 outline-none"
                    required
                >
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    @disabled(!$canManageSimpanan)
                    class="px-4 py-2 text-sm font-semibold text-white rounded-lg transition shadow-sm {{ $canManageSimpanan ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-400 cursor-not-allowed' }}"
                >
                    Ambil Simpanan
                </button>
            </div>
            </fieldset>
        </form>
    </div>

    {{-- ========================== --}}
    {{-- D. PROSES PENSIUN / MUTASI --}}
    {{-- ========================== --}}
        <div x-show="tab === 'keluar'" 
            x-transition.opacity.duration.200ms
            class="bg-white border rounded-lg p-4 space-y-4">

            <h2 class="font-semibold text-red-600 text-sm">
                Proses Keluar Anggota Karena Pensiun / Mutasi
            </h2>

            {{-- CONTENT --}}
            <p class="text-sm text-gray-500">
                Proses ini akan:
                <br>• Menonaktifkan anggota
                <br>• Mengembalikan seluruh simpanan
            </p>

            <fieldset @disabled(!$canManageSimpanan)>
                {{-- PILIH ANGGOTA --}}
                <div>
                    <label class="text-xs text-gray-500">Anggota</label>
                    <select id="anggotaSelectKeluar"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="" disabled selected>Pilih Anggota</option>
                        @foreach ($anggotas as $anggota)
                            <option value="{{ $anggota->id }}">
                                {{ $anggota->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

            {{-- SALDO --}}
            <div id="saldoBoxKeluar" class="hidden mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm space-y-1">
                <div class="flex justify-between">
                    <span>Pokok</span>
                    <span id="saldoPokokKeluar">Rp 0</span>
                </div>

                <div class="flex justify-between">
                    <span>Wajib</span>
                    <span id="saldoWajibKeluar">Rp 0</span>
                </div>

                <div class="flex justify-between">
                    <span>Sukarela</span>
                    <span id="saldoSukarelaKeluar">Rp 0</span>
                </div>

                <div class="flex justify-between border-t pt-2 mt-2 font-bold text-red-600">
                    <span>Total Dikembalikan</span>
                    <span id="saldoTotalKeluar">Rp 0</span>
                </div>
            </div>

            {{-- ALASAN --}}
            <div class="space-y-3">

                {{-- PILIH JENIS --}}
                <div>
                    <label class="text-xs text-gray-500">Jenis Keluar</label>
                    <select id="jenisKeluarSelect"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 outline-none">
                        <option value="" disabled selected>Pilih Jenis</option>
                        <option value="pensiun">Pensiun</option>
                        <option value="mutasi">Mutasi</option>
                    </select>
                </div>

                {{-- INPUT KETERANGAN TAMBAHAN --}}
                <div>
                    <label class="text-xs text-gray-500">Keterangan (opsional)</label>
                    <input type="text"
                        id="keteranganKeluar"
                        placeholder="Contoh: pindah instansi"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 outline-none">
                </div>

                {{-- INFO TOTAL --}}
                <div id="totalKeluarBox" class="hidden text-sm bg-gray-50 border p-3 rounded-lg">
                    Total akan dikembalikan:
                    <span id="totalKeluar" class="font-bold">Rp 0</span>
                </div>

                {{-- BUTTON --}}
                <button type="button"
                    id="btnKeluar"
                    @disabled(!$canManageSimpanan)
                    class="px-4 py-2 text-sm font-semibold text-white rounded-lg transition shadow-sm cursor-not-allowed {{ $canManageSimpanan ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-400' }}"
                >
                    Proses Keluar
                </button>
            </div>
            </fieldset>
        </div>
        <form id="formKeluar" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="alasan" id="alasanKeluar">
            <input type="hidden" name="keterangan" id="keteranganHidden">
        </form>
</div>

{{-- SCRIPT --}}
<script>
function toggleSection(name) {
    const content = document.getElementById('content-' + name);
    const icon = document.getElementById('icon-' + name);

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.innerText = '▲';
    } else {
        content.classList.add('hidden');
        icon.innerText = '▼';
    }
}
</script>

<script>
    function closeFlashMessage() {
        const el = document.getElementById('flash-message');
        if (el) {
            el.style.transition = 'opacity 0.3s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        }
    }

    // auto hide
    const flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 300);
        }, 5000);
    }
</script>

<script>
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    function attachSaldoListener(selectId, target) {
        const select = document.getElementById(selectId);

        if (!select) return;

        select.addEventListener('change', function () {
            const anggotaId = this.value;

            if (!anggotaId) return;

            fetch(`{{ url('admin/simpanan/saldo') }}/${anggotaId}`)
                .then(res => {
                    if (!res.ok) throw new Error('HTTP error ' + res.status);
                    return res.json();
                })
                .then(data => {

                    document.getElementById(`saldoPokok${target}`).innerText = formatRupiah(data.pokok);
                    document.getElementById(`saldoWajib${target}`).innerText = formatRupiah(data.wajib);
                    document.getElementById(`saldoSukarela${target}`).innerText = formatRupiah(data.sukarela);
                    document.getElementById(`saldoTotal${target}`).innerText = formatRupiah(data.total);

                    document.getElementById(`saldoBox${target}`).classList.remove('hidden');
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal mengambil saldo');
                });
        });
    }

    attachSaldoListener('anggotaSelectSimpan', 'Simpan');
    attachSaldoListener('anggotaSelectAmbil', 'Ambil');
</script>

{{-- SCRIPT PENSIUN/MUTASI --}}
<script>
    const canManageSimpanan = @json($canManageSimpanan);
    const selectKeluar = document.getElementById('anggotaSelectKeluar');
    const btnKeluar = document.getElementById('btnKeluar');

    if (selectKeluar) {
        selectKeluar.addEventListener('change', function () {
            const id = this.value;
            const VALID_ALASAN = ['pensiun', 'mutasi'];
            if (!id) return;

            // ambil saldo
            fetch(`{{ url('admin/simpanan/saldo') }}/${id}`)
                .then(res => res.json())
                .then(data => {

                    // tampilkan saldo
                    document.getElementById('saldoPokokKeluar').innerText = formatRupiah(data.pokok);
                    document.getElementById('saldoWajibKeluar').innerText = formatRupiah(data.wajib);
                    document.getElementById('saldoSukarelaKeluar').innerText = formatRupiah(data.sukarela);
                    document.getElementById('saldoTotalKeluar').innerText = formatRupiah(data.total);

                    document.getElementById('saldoBoxKeluar').classList.remove('hidden');

                    if (!canManageSimpanan) {
                        return;
                    }

                    // aktifkan tombol
                    btnKeluar.disabled = false;
                    btnKeluar.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    btnKeluar.classList.add('bg-red-600', 'hover:bg-red-700');

                    // klik handler
                    btnKeluar.onclick = function () {
                        const jenis = document.getElementById('jenisKeluarSelect').value;
                        const keterangan = document.getElementById('keteranganKeluar').value;

                        if (!jenis) {
                            alert("Pilih jenis keluar dulu!");
                            return;
                        }

                        if (confirm(
                            `Jenis: ${jenis}\nTotal dikembalikan: ${formatRupiah(data.total)}\n\nLanjutkan?`
                        )) {
                            const form = document.getElementById('formKeluar');

                            form.action = `{{ url('admin/anggota') }}/${id}/keluar`;
                            document.getElementById('alasanKeluar').value = jenis;
                            document.getElementById('keteranganHidden').value = keterangan;

                            form.submit();
                        }
                    };
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal mengambil saldo');
                });
        });
    }
</script>
@endsection
