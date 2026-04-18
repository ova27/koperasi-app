@extends('layouts.main')

@section('title', 'Rincian Potongan Anggota')
@section('page-title', 'Rincian Potongan Anggota')

@section('content')
<div class="space-y-3">
    @include('admin.laporan._tabs_potongan')

    @can('manage simpanan anggota')
        <details class="bg-gray-50 border border-gray-200 rounded-lg" @if($errors->has('file') || session('upload_preview') || session('upload_error')) open @endif>
            <summary class="px-3.5 py-2.5 text-sm font-medium text-gray-700 cursor-pointer select-none">
                Ubah Nominal per Anggota
            </summary>

            <form id="uploadForm" method="POST" action="{{ route('admin.laporan.potongan-bulanan.upload.preview') }}" enctype="multipart/form-data" class="px-3.5 pb-3.5 border-t border-gray-200">
                @csrf
                <div class="pt-2 mb-2">
                    <p class="text-[11px] text-gray-500">Unggah file Excel: Nama Anggota | Iuran Dharma Wanita | Infaq Pegawai | Tabungan Qurban
                        <a href="{{ route('admin.laporan.potongan-bulanan.template.download') }}" class="ml-1.5 text-slate-600 hover:text-slate-700 underline underline-offset-2">
                            Download Template
                        </a>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-2 items-end">
                    <div class="md:col-span-3">
                        <label class="block text-[11px] text-gray-600 mb-1">File Excel (.xlsx, .xls)</label>
                        <input type="file" name="file" accept=".xlsx,.xls" class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-xs bg-white" required>
                    </div>
                    <div>
                        <button type="submit" class="w-full px-3 py-1.5 bg-slate-700 text-white rounded-md hover:bg-slate-800 text-xs transition-colors duration-150">
                            Upload & Preview
                        </button>
                    </div>
                </div>

                @if ($errors->has('file'))
                    <div class="mt-2 px-2 py-1.5 bg-red-50 border border-red-200 text-red-700 text-xs rounded-md">
                        {{ $errors->first('file') }}
                    </div>
                @endif
            </form>
        </details>

        @if (session('upload_error'))
            <div class="mt-2 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                {{ session('upload_error') }}
            </div>
        @endif

        {{-- UPLOAD PREVIEW MODAL --}}
        @if (session('upload_preview'))
            <div id="previewModal" class="bg-white border border-amber-200 rounded-xl p-4 mt-2 shadow-sm">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-gray-700">
                        Preview Data Upload ({{ session('upload_total') }} baris)
                    </h3>
                    <button type="button" onclick="closePreview()" class="text-gray-500 hover:text-gray-700">✕</button>
                </div>

                @if (!session('upload_success'))
                    <div class="mb-3 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                        <strong>Ada kesalahan:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach (session('upload_preview')['errors'] as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="mb-3 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
                        Semua baris valid dan siap disimpan
                    </div>

                    <div class="max-h-96 overflow-y-auto mb-3">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-blue-50">
                                    <th class="border px-2 py-1 text-left">Nama Anggota</th>
                                    <th class="border px-2 py-1 text-right">Dharma</th>
                                    <th class="border px-2 py-1 text-right">Infaq</th>
                                    <th class="border px-2 py-1 text-right">Qurban</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (session('upload_preview')['data'] as $item)
                                    <tr>
                                        <td class="border px-2 py-1">{{ $item['nama'] }}</td>
                                        <td class="border px-2 py-1 text-right">Rp {{ number_format($item['iuran_dharma_wanita'], 0, ',', '.') }}</td>
                                        <td class="border px-2 py-1 text-right">Rp {{ number_format($item['infaq_pegawai'], 0, ',', '.') }}</td>
                                        <td class="border px-2 py-1 text-right">Rp {{ number_format($item['tabungan_qurban'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" onclick="closePreview()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                            Batal
                        </button>
                        <form method="POST" action="{{ route('admin.laporan.potongan-bulanan.upload.confirm') }}" class="inline">
                            @csrf
                            <input type="hidden" name="data" value="{{ json_encode(session('upload_preview')['data']) }}">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                Simpan {{ session('upload_total') }} Anggota
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    @endcan

    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-4">
            <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-2">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Bulan (Potongan)</label>
                    <input type="month" name="bulan" value="{{ $bulanPotongan }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                </div>
                <button class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition-all duration-200">
                    Tampilkan
                </button>
            </form>

            <div class="text-sm text-gray-600">
                Total Potongan:
                <span class="font-bold text-emerald-700">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @forelse($ringkasanBank as $bank => $item)
                <div class="bg-gradient-to-r from-blue-50 to-white border border-blue-100 rounded-lg p-3 shadow-sm">
                    <div class="text-xs uppercase tracking-wide text-gray-500">{{ $bank }}</div>
                    <div class="text-sm text-gray-700">{{ $item['jumlah_anggota'] }} anggota</div>
                    <div class="font-semibold text-blue-700">Rp {{ number_format($item['total'], 0, ',', '.') }}</div>
                </div>
            @empty
                <div class="text-sm text-gray-500">Belum ada data.</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-4">
            @can('view laporan pinjaman')
                <a href="{{ route('admin.laporan.potongan-bulanan.export', ['bulan' => $bulanPotongan]) }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium bg-green-600 text-white shadow-sm hover:bg-green-700 transition-all duration-200">
                    Export Excel
                </a>
            @endcan

            <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-2">
                <input type="hidden" name="bulan" value="{{ $bulanPotongan }}">
                <div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari..."
                        class="border border-gray-300 rounded-lg px-6 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                    >
                </div>
            </form>
        </div>

        <div class="hidden md:block rounded-lg border border-gray-100 overflow-auto max-h-[65vh]">
            <table class="min-w-full text-sm">
            <thead class="bg-gradient-to-r from-blue-50 to-blue-100 border-b-2 border-blue-300 sticky top-0 z-10">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">No</th>
                    <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">Nama Anggota</th>
                    <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">Bank</th>
                    <th class="px-3 py-2 text-left font-semibold text-xs text-blue-900 uppercase tracking-widest">Nomor Rekening</th>
                    <th class="px-3 py-2 text-right font-semibold text-xs text-blue-900 uppercase tracking-widest">Simpanan Wajib</th>
                    <th class="px-3 py-2 text-right font-semibold text-xs text-blue-900 uppercase tracking-widest">Cicilan Pinjaman</th>
                    <th class="px-3 py-2 text-right font-semibold text-xs text-blue-900 uppercase tracking-widest">Iuran Dharma Wanita</th>
                    <th class="px-3 py-2 text-right font-semibold text-xs text-blue-900 uppercase tracking-widest">Infaq Pegawai</th>
                    <th class="px-3 py-2 text-right font-semibold text-xs text-blue-900 uppercase tracking-widest">Tabungan Qurban</th>
                    <th class="px-3 py-2 text-right font-semibold text-xs text-blue-900 uppercase tracking-widest">Iuran Operasional</th>
                    <th class="px-3 py-2 text-right font-semibold text-xs text-blue-900 uppercase tracking-widest">Total Potongan</th>
                    @can('manage simpanan anggota')
                        <th class="px-3 py-2 text-center font-semibold text-xs text-blue-900 uppercase tracking-widest">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rows as $index => $row)
                    <tr class="even:bg-blue-50 hover:bg-blue-100 transition-all duration-200">
                        <td class="px-3 py-2 text-xs text-gray-700">{{ $index + 1 }}</td>
                        <td class="px-3 py-2 text-xs font-medium text-gray-800">{{ $row['nama'] }}</td>
                        <td class="px-3 py-2 text-xs text-gray-700">{{ $row['bank'] }}</td>
                        <td class="px-3 py-2 text-xs text-gray-700">{{ $row['nomor_rekening'] }}</td>
                        <td class="px-3 py-2 text-right text-xs text-gray-800">Rp {{ number_format($row['wajib'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-xs text-gray-800">Rp {{ number_format($row['cicilan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-xs text-gray-800">Rp {{ number_format($row['iuran_dharma_wanita'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-xs text-gray-800">Rp {{ number_format($row['infaq_pegawai'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-xs text-gray-800">Rp {{ number_format($row['tabungan_qurban'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-xs text-gray-800">Rp {{ number_format($row['iuran_operasional'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-xs font-bold text-blue-700">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                        @can('manage simpanan anggota')
                            <td class="px-3 py-2 text-center">
                                <button type="button" onclick="openNominalModal({{ $row['anggota']->id }}, '{{ addslashes($row['nama']) }}', {{ $row['iuran_dharma_wanita'] }}, {{ $row['infaq_pegawai'] }}, {{ $row['tabungan_qurban'] }})" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs">
                                    Edit
                                </button>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-3 py-8 text-center text-gray-500">
                            Tidak ada data anggota aktif
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($rows->count() > 0)
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <th colspan="4" class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Total</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Rp {{ number_format($totalWajib, 0, ',', '.') }}</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Rp {{ number_format($totalCicilan, 0, ',', '.') }}</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Rp {{ number_format($totalDharma, 0, ',', '.') }}</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Rp {{ number_format($totalInfaq, 0, ',', '.') }}</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Rp {{ number_format($totalQurban, 0, ',', '.') }}</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Rp {{ number_format($totalIuranOperasional, 0, ',', '.') }}</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-blue-700">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</th>
                        @can('manage simpanan anggota')
                            <th class="px-3 py-2"></th>
                        @endcan
                    </tr>
                </tfoot>
            @endif
        </table>
        </div>

        <div class="md:hidden space-y-2.5">
            @forelse($rows as $index => $row)
                <div class="bg-gradient-to-r from-blue-50 to-white border-l-4 border-l-blue-600 border border-blue-100 rounded-lg p-3.5 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="text-xs text-gray-500">{{ $index + 1 }}. {{ $row['nama'] }}</div>
                            <div class="text-xs text-gray-600">{{ $row['bank'] }} - {{ $row['nomor_rekening'] }}</div>
                        </div>
                        <div class="text-sm font-bold text-blue-700">Rp {{ number_format($row['total'], 0, ',', '.') }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-x-3 gap-y-1 mt-2 text-xs text-gray-700">
                        <div>Wajib: Rp {{ number_format($row['wajib'], 0, ',', '.') }}</div>
                        <div>Cicilan: Rp {{ number_format($row['cicilan'], 0, ',', '.') }}</div>
                        <div>Dharma: Rp {{ number_format($row['iuran_dharma_wanita'], 0, ',', '.') }}</div>
                        <div>Infaq: Rp {{ number_format($row['infaq_pegawai'], 0, ',', '.') }}</div>
                        <div>Qurban: Rp {{ number_format($row['tabungan_qurban'], 0, ',', '.') }}</div>
                        <div>Operasional: Rp {{ number_format($row['iuran_operasional'], 0, ',', '.') }}</div>
                    </div>
                    @can('manage simpanan anggota')
                        <div class="mt-2">
                            <button type="button" onclick="openNominalModal({{ $row['anggota']->id }}, '{{ addslashes($row['nama']) }}', {{ $row['iuran_dharma_wanita'] }}, {{ $row['infaq_pegawai'] }}, {{ $row['tabungan_qurban'] }})" class="w-full px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs">
                                Edit
                            </button>
                        </div>
                    @endcan
                </div>
            @empty
                <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-400 text-sm">
                    Tidak ada data anggota aktif
                </div>
            @endforelse
        </div>

        @if($rows->hasPages())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Menampilkan
                    <span class="font-semibold text-gray-900">{{ $rows->firstItem() ?? 0 }}</span>
                    -
                    <span class="font-semibold text-gray-900">{{ $rows->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold text-gray-900">{{ $rows->total() }}</span>
                    data
                </p>
                <div class="flex justify-start sm:justify-end w-full sm:w-auto">
                    {{ $rows->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL EDIT NOMINAL PER ANGGOTA --}}
    @can('manage simpanan anggota')
        <div id="nominalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h2 class="text-lg font-semibold mb-1" id="modalTitle"></h2>
                <p class="text-sm text-gray-500 mb-4">Edit nominal titipan per bulan</p>

                <form id="nominalForm" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulanPotongan }}">

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Iuran Dharma Wanita</label>
                        <input type="number" min="0" name="iuran_dharma_wanita" id="dharmaInput" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Infaq Pegawai</label>
                        <input type="number" min="0" name="infaq_pegawai" id="infaqInput" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tabungan Qurban</label>
                        <input type="number" min="0" name="tabungan_qurban" id="qurbanInput" class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="pt-3 flex gap-2 justify-end">
                        <button type="button" onclick="closeNominalModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded hover:bg-slate-800">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openNominalModal(anggotaId, namaAnggota, dharma, infaq, qurban) {
                document.getElementById('modalTitle').innerText = 'Edit Nominal: ' + namaAnggota;
                document.getElementById('dharmaInput').value = dharma;
                document.getElementById('infaqInput').value = infaq;
                document.getElementById('qurbanInput').value = qurban;

                const form = document.getElementById('nominalForm');
                form.action = '{{ route("admin.laporan.potongan-bulanan.anggota.update", ":id") }}'.replace(':id', anggotaId);

                document.getElementById('nominalModal').classList.remove('hidden');
                document.getElementById('nominalModal').classList.add('flex');
            }

            function closeNominalModal() {
                document.getElementById('nominalModal').classList.remove('flex');
                document.getElementById('nominalModal').classList.add('hidden');
            }

            function closePreview() {
                const previewModal = document.getElementById('previewModal');
                if (previewModal) {
                    previewModal.style.display = 'none';
                }
            }
        </script>
    @endcan
</div>
@endsection
