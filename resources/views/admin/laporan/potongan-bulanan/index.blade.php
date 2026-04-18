@extends('layouts.main')

@section('title', 'Rincian Potongan Anggota')
@section('page-title', 'Rincian Potongan Anggota')

@section('content')
<div class="space-y-6 -mt-1">
    @include('admin.laporan._tabs_potongan')

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">Rincian Potongan Anggota</h1>
            <p class="text-sm text-gray-500">
                Bulan potongan: {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanPotongan)->translatedFormat('F Y') }}
                - Acuan sisa pinjaman: {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanAcuan)->translatedFormat('F Y') }}
            </p>
        </div>

        @can('export laporan pinjaman')
            <a href="{{ route('admin.laporan.potongan-bulanan.export', ['bulan' => $bulanPotongan]) }}"
                class="px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                Export Excel
            </a>
        @endcan
    </div>

    <form method="GET" class="mb-2 flex items-end gap-2">
        <div>
            <label class="text-sm text-gray-600">Bulan (Potongan)</label>
            <input type="month" name="bulan" value="{{ $bulanPotongan }}" class="border rounded px-3 py-1">
        </div>
        <button class="px-4 py-1 bg-blue-600 text-white rounded">Tampilkan</button>
    </form>

    @can('manage simpanan anggota')
        <form id="uploadForm" method="POST" action="{{ route('admin.laporan.potongan-bulanan.upload.preview') }}" enctype="multipart/form-data" class="border rounded bg-blue-50 p-4">
            @csrf
            <div class="mb-3">
                <h2 class="text-sm font-semibold text-gray-700">📤 Upload Nominal per Anggota</h2>
                <p class="text-xs text-gray-500">Unggah file Excel dengan kolom: Nama Anggota | Iuran Dharma Wanita | Infaq Pegawai | Tabungan Qurban
                    <a href="{{ route('admin.laporan.potongan-bulanan.template.download') }}" class="ml-2 text-blue-600 hover:text-blue-700 font-semibold">
                        ⬇️ Download Template
                    </a>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                <div class="md:col-span-2">
                    <label class="text-xs text-gray-600">Pilih File Excel (.xlsx, .xls)</label>
                    <input type="file" name="file" accept=".xlsx,.xls" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Upload & Preview
                    </button>
                </div>
            </div>

            @if ($errors->has('file'))
                <div class="mt-2 p-2 bg-red-100 text-red-700 text-xs rounded">
                    {{ $errors->first('file') }}
                </div>
            @endif
        </form>

        @if (session('upload_error'))
            <div class="mt-2 p-3 bg-red-100 text-red-700 rounded text-sm">
                {{ session('upload_error') }}
            </div>
        @endif

        {{-- UPLOAD PREVIEW MODAL --}}
        @if (session('upload_preview'))
            <div id="previewModal" class="border rounded bg-amber-50 p-4 mt-2">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-gray-700">
                        Preview Data Upload ({{ session('upload_total') }} baris)
                    </h3>
                    <button type="button" onclick="closePreview()" class="text-gray-500 hover:text-gray-700">✕</button>
                </div>

                @if (!session('upload_success'))
                    <div class="mb-3 p-3 bg-red-100 text-red-700 rounded text-sm">
                        <strong>❌ Ada kesalahan:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach (session('upload_preview')['errors'] as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="mb-3 p-3 bg-green-100 text-green-700 rounded text-sm">
                        ✓ Semua baris valid dan siap disimpan
                    </div>

                    <div class="max-h-96 overflow-y-auto mb-3">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-200">
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
                        <button type="button" onclick="closePreview()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 text-sm">
                            Batal
                        </button>
                        <form method="POST" action="{{ route('admin.laporan.potongan-bulanan.upload.confirm') }}" class="inline">
                            @csrf
                            <input type="hidden" name="data" value="{{ json_encode(session('upload_preview')['data']) }}">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                                ✓ Simpan {{ session('upload_total') }} Anggota
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    @endcan

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">Total Simpanan Wajib</div>
            <div class="text-lg font-semibold">Rp {{ number_format($totalWajib, 0, ',', '.') }}</div>
        </div>
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">Total Cicilan Pinjaman</div>
            <div class="text-lg font-semibold">Rp {{ number_format($totalCicilan, 0, ',', '.') }}</div>
        </div>
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">Total Titipan</div>
            <div class="text-lg font-semibold">Rp {{ number_format($totalTitipan, 0, ',', '.') }}</div>
        </div>
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">Total Iuran Operasional</div>
            <div class="text-lg font-semibold">Rp {{ number_format($totalIuranOperasional, 0, ',', '.') }}</div>
        </div>
        <div class="border rounded p-4 bg-white">
            <div class="text-sm text-gray-500">Total Bulan Potongan</div>
            <div class="text-lg font-semibold">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="border rounded bg-white p-4">
        <h2 class="text-sm font-semibold text-gray-700 mb-2">Ringkasan per Bank</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @forelse($ringkasanBank as $bank => $item)
                <div class="rounded border p-3">
                    <div class="text-xs uppercase text-gray-500">{{ $bank }}</div>
                    <div class="text-sm">{{ $item['jumlah_anggota'] }} anggota</div>
                    <div class="font-semibold">Rp {{ number_format($item['total'], 0, ',', '.') }}</div>
                </div>
            @empty
                <div class="text-sm text-gray-500">Belum ada data.</div>
            @endforelse
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border bg-white text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-left">No</th>
                    <th class="border px-3 py-2 text-left">Nama Anggota</th>
                    <th class="border px-3 py-2 text-left">Bank</th>
                    <th class="border px-3 py-2 text-left">Nomor Rekening</th>
                    <th class="border px-3 py-2 text-right">Simpanan Wajib</th>
                    <th class="border px-3 py-2 text-right">Cicilan Pinjaman</th>
                    <th class="border px-3 py-2 text-right">Iuran Dharma Wanita</th>
                    <th class="border px-3 py-2 text-right">Infaq Pegawai</th>
                    <th class="border px-3 py-2 text-right">Tabungan Qurban</th>
                    <th class="border px-3 py-2 text-right">Iuran Operasional</th>
                    <th class="border px-3 py-2 text-right">Total Potongan</th>
                    @can('manage simpanan anggota')
                        <th class="border px-3 py-2 text-center">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $index => $row)
                    <tr>
                        <td class="border px-3 py-2">{{ $index + 1 }}</td>
                        <td class="border px-3 py-2">{{ $row['nama'] }}</td>
                        <td class="border px-3 py-2">{{ $row['bank'] }}</td>
                        <td class="border px-3 py-2">{{ $row['nomor_rekening'] }}</td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($row['wajib'], 0, ',', '.') }}</td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($row['cicilan'], 0, ',', '.') }}</td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($row['iuran_dharma_wanita'], 0, ',', '.') }}</td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($row['infaq_pegawai'], 0, ',', '.') }}</td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($row['tabungan_qurban'], 0, ',', '.') }}</td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($row['iuran_operasional'], 0, ',', '.') }}</td>
                        <td class="border px-3 py-2 text-right font-semibold">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                        @can('manage simpanan anggota')
                            <td class="border px-3 py-2 text-center">
                                <button type="button" onclick="openNominalModal({{ $row['anggota']->id }}, '{{ addslashes($row['nama']) }}', {{ $row['iuran_dharma_wanita'] }}, {{ $row['infaq_pegawai'] }}, {{ $row['tabungan_qurban'] }})" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">
                                    Edit
                                </button>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="border px-3 py-4 text-center text-gray-500">
                            Tidak ada data anggota aktif
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($rows->isNotEmpty())
                <tfoot class="bg-gray-50">
                    <tr>
                        <th colspan="4" class="border px-3 py-2 text-right">Total</th>
                        <th class="border px-3 py-2 text-right">Rp {{ number_format($totalWajib, 0, ',', '.') }}</th>
                        <th class="border px-3 py-2 text-right">Rp {{ number_format($totalCicilan, 0, ',', '.') }}</th>
                        <th class="border px-3 py-2 text-right">Rp {{ number_format($rows->sum('iuran_dharma_wanita'), 0, ',', '.') }}</th>
                        <th class="border px-3 py-2 text-right">Rp {{ number_format($rows->sum('infaq_pegawai'), 0, ',', '.') }}</th>
                        <th class="border px-3 py-2 text-right">Rp {{ number_format($rows->sum('tabungan_qurban'), 0, ',', '.') }}</th>
                        <th class="border px-3 py-2 text-right">Rp {{ number_format($totalIuranOperasional, 0, ',', '.') }}</th>
                        <th class="border px-3 py-2 text-right">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</th>
                        @can('manage simpanan anggota')
                            <th class="border px-3 py-2"></th>
                        @endcan
                    </tr>
                </tfoot>
            @endif
        </table>
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
