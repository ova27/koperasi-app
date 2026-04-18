<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanPotonganBulananExport;
use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\PotonganTitipan;
use App\Services\NominalTitipanImportService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPotonganBulananController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view laporan pinjaman');

        $bulanPotongan = $request->get('bulan', now()->addMonthNoOverflow()->format('Y-m'));
        $bulanAcuan = Carbon::createFromFormat('Y-m', $bulanPotongan)
            ->subMonthNoOverflow()
            ->format('Y-m');

        $rows = $this->buildPotonganRows();

        return view('admin.laporan.potongan-bulanan.index', [
            'bulanAcuan' => $bulanAcuan,
            'bulanPotongan' => $bulanPotongan,
            'rows' => $rows,
            'totalWajib' => (int) $rows->sum('wajib'),
            'totalCicilan' => (int) $rows->sum('cicilan'),
            'totalTitipan' => (int) $rows->sum('total_titipan'),
            'totalIuranOperasional' => (int) $rows->sum('iuran_operasional'),
            'totalPotongan' => (int) $rows->sum('total'),
            'ringkasanBank' => $rows->groupBy('bank')->map(fn ($items) => [
                'jumlah_anggota' => $items->count(),
                'total' => (int) $items->sum('total'),
            ])->sortKeys(),
        ]);
    }

    public function indexBank(Request $request)
    {
        $this->authorize('view laporan pinjaman');

        $bulanPotongan = $request->get('bulan', now()->addMonthNoOverflow()->format('Y-m'));
        $namaBank = trim((string) $request->get('nama_bank', ''));
        $allRows = $this->buildPotonganRows();
        $bankOptions = $allRows
            ->pluck('bank')
            ->filter(fn ($bank) => filled($bank))
            ->unique()
            ->sort()
            ->values();

        $rows = $namaBank !== ''
            ? $allRows->where('bank', $namaBank)->values()
            : $allRows;

        return view('admin.laporan.potongan-bulanan.bank', [
            'bulanPotongan' => $bulanPotongan,
            'namaBank' => $namaBank,
            'bankOptions' => $bankOptions,
            'rows' => $rows,
            'totalSetoranBank' => (int) $rows->sum('total'),
            'ringkasanBank' => $rows->groupBy('bank')->map(fn ($items) => [
                'jumlah_anggota' => $items->count(),
                'total' => (int) $items->sum('total'),
            ])->sortKeys(),
        ]);
    }

    public function updateAnggota(Request $request, Anggota $anggota)
    {
        $this->authorize('manage simpanan anggota');

        $validated = $request->validate([
            'iuran_dharma_wanita' => ['required', 'integer', 'min:0'],
            'infaq_pegawai' => ['required', 'integer', 'min:0'],
            'tabungan_qurban' => ['required', 'integer', 'min:0'],
            'bulan' => ['nullable', 'date_format:Y-m'],
        ]);

        PotonganTitipan::updateOrCreate(
            ['anggota_id' => $anggota->id],
            [
                'iuran_dharma_wanita' => (int) $validated['iuran_dharma_wanita'],
                'infaq_pegawai' => (int) $validated['infaq_pegawai'],
                'tabungan_qurban' => (int) $validated['tabungan_qurban'],
            ]
        );

        return redirect()
            ->route('admin.laporan.potongan-bulanan.index', ['bulan' => $validated['bulan'] ?? now()->addMonthNoOverflow()->format('Y-m')])
            ->with('success', 'Nominal titipan ' . $anggota->nama . ' berhasil diperbarui');
    }

    public function export(Request $request)
    {
        $this->authorize('export laporan pinjaman');

        $bulanPotongan = $request->get('bulan', now()->addMonthNoOverflow()->format('Y-m'));

        return Excel::download(
            new LaporanPotonganBulananExport($bulanPotongan),
            'rincian-potongan-' . $bulanPotongan . '.xlsx'
        );
    }

    public function exportBank(Request $request)
    {
        $this->authorize('export laporan pinjaman');

        $bulanPotongan = $request->get('bulan', now()->addMonthNoOverflow()->format('Y-m'));
        $namaBank = trim((string) $request->get('nama_bank', ''));

        return Excel::download(
            new \App\Exports\PotonganBankBulanDepanExport($bulanPotongan, $namaBank !== '' ? $namaBank : null),
            'setoran-bank-potongan-' . $bulanPotongan . '.xlsx'
        );
    }

    public function exportBankWord(Request $request)
    {
        $this->authorize('export laporan pinjaman');

        $validated = $request->validate([
            'bulan' => ['nullable', 'date_format:Y-m'],
            'nama_bank' => ['required', 'string'],
        ]);

        $bulanPotongan = $validated['bulan'] ?? now()->addMonthNoOverflow()->format('Y-m');
        $namaBank = trim((string) $validated['nama_bank']);
        $rows = $this->buildPotonganRowsByBank($namaBank);

        if ($rows->isEmpty()) {
            return back()->with('error', 'Data setoran untuk bank terpilih tidak ditemukan.');
        }

        $totalSetoran = (int) $rows->sum('total');
        $bulanLabel = Carbon::createFromFormat('Y-m', $bulanPotongan)->translatedFormat('F Y');

        $suratKuasaConfig = config('koperasi.surat_kuasa_bank', []);
        $tanggalSurat = Carbon::parse($suratKuasaConfig['tanggal'] ?? now())->translatedFormat('d F Y');
        $logoPath = public_path('images/bps-surat.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath))
            : null;

        $html = view('admin.laporan.potongan-bulanan.word.surat-kuasa-bank', [
            'logoDataUri' => $logoDataUri,
            'bulanPotongan' => $bulanPotongan,
            'bulanLabel' => $bulanLabel,
            'namaBank' => $namaBank,
            'rows' => $rows,
            'totalSetoran' => $totalSetoran,
            'totalSetoranTerbilang' => $this->toTerbilang($totalSetoran),
            'suratKuasa' => [
                'instansi' => (string) ($suratKuasaConfig['instansi'] ?? 'BADAN PUSAT STATISTIK PROVINSI BANTEN'),
                'penandatangan_1' => [
                    'jabatan_pengantar' => (string) ($suratKuasaConfig['penandatangan_1']['jabatan_pengantar'] ?? 'Kepala Bagian Umum BPS Provinsi Banten'),
                    'nama' => (string) ($suratKuasaConfig['penandatangan_1']['nama'] ?? 'Ridwan Hidayat'),
                    'nip' => (string) ($suratKuasaConfig['penandatangan_1']['nip'] ?? '19720306 199512 1 001'),
                    'jabatan' => (string) ($suratKuasaConfig['penandatangan_1']['jabatan'] ?? 'Kepala Bagian Umum'),
                ],
                'penandatangan_2' => [
                    'jabatan_pengantar' => (string) ($suratKuasaConfig['penandatangan_2']['jabatan_pengantar'] ?? 'Bendahara Pengeluaran'),
                    'nama' => (string) ($suratKuasaConfig['penandatangan_2']['nama'] ?? 'Intan Putri Firdaus, A.Md'),
                    'nip' => (string) ($suratKuasaConfig['penandatangan_2']['nip'] ?? '19910508 201403 2 003'),
                    'jabatan' => (string) ($suratKuasaConfig['penandatangan_2']['jabatan'] ?? 'Bendahara Pengeluaran BPS Provinsi Banten'),
                ],
                'bank_tujuan' => [
                    'nama' => (string) ($suratKuasaConfig['bank_tujuan']['nama'] ?? 'Bank Syariah Indonesia (BSI) Cabang Serang'),
                    'nomor_rekening' => (string) ($suratKuasaConfig['bank_tujuan']['nomor_rekening'] ?? '7235147593'),
                    'atas_nama' => (string) ($suratKuasaConfig['bank_tujuan']['atas_nama'] ?? 'DANDI ISWANDI'),
                    'keterangan' => (string) ($suratKuasaConfig['bank_tujuan']['keterangan'] ?? 'Pengurus Koperasi Pegawai BPS Provinsi Banten'),
                ],
                'kota' => (string) ($suratKuasaConfig['kota'] ?? 'Serang'),
                'tanggal' => $tanggalSurat,
            ],
        ])->render();

        $filename = 'surat-kuasa-pendebetan-' . Str::slug($namaBank) . '-' . $bulanPotongan . '.doc';

        return response($html, Response::HTTP_OK)
            ->header('Content-Type', 'application/msword; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function uploadPreview(Request $request)
    {
        $this->authorize('manage simpanan anggota');

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048'],
        ]);

        try {
            $service = new NominalTitipanImportService();
            $result = $service->parseExcel($validated['file']);

            return back()
                ->with('upload_preview', $result)
                ->with('upload_success', $result['success'])
                ->with('upload_total', $result['total_rows']);
        } catch (\Exception $e) {
            return back()
                ->with('upload_error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    public function uploadConfirm(Request $request)
    {
        $this->authorize('manage simpanan anggota');

        $validated = $request->validate([
            'data' => ['required', 'json'],
        ]);

        try {
            $data = json_decode($validated['data'], true);
            if (!is_array($data)) {
                throw new \Exception('Data tidak valid');
            }

            $service = new NominalTitipanImportService();
            $count = $service->batchSave($data);

            return redirect()
                ->route('admin.laporan.potongan-bulanan.index')
                ->with('success', "Berhasil update nominal $count anggota dari file Excel");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $this->authorize('manage simpanan anggota');

        $anggotaList = Anggota::where('status', 'aktif')
            ->orderBy('nama')
            ->get(['nama', 'id']);

        return Excel::download(
            new class($anggotaList) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function __construct(private $anggotaList) {}

                public function array(): array
                {
                    return $this->anggotaList->map(fn($a) => [
                        $a->nama,
                        0,
                        0,
                        0,
                    ])->toArray();
                }

                public function headings(): array
                {
                    return ['Nama Anggota', 'Iuran Dharma Wanita', 'Infaq Pegawai', 'Tabungan Qurban'];
                }
            },
            'template-nominal-titipan.xlsx'
        );
    }

    private function buildPotonganRows()
    {
        $wajibDefault = (int) config('koperasi.simpanan_wajib', 0);
        $iuranDharmaWanita = (int) config('koperasi.iuran_dharma_wanita', 0);
        $infaqPegawai = (int) config('koperasi.infaq_pegawai', 0);
        $tabunganQurban = (int) config('koperasi.tabungan_qurban', 0);
        $iuranOperasional = (int) config('koperasi.iuran_operasional', 5000);

        return Anggota::query()
            ->with(['rekeningAktif', 'pinjamanAktif', 'potonganTitipan'])
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get()
            ->map(function ($anggota) use ($wajibDefault, $iuranOperasional, $iuranDharmaWanita, $infaqPegawai, $tabunganQurban) {
                $pinjamanAktif = $anggota->pinjamanAktif;
                $cicilan = 0;
                $titipan = $anggota->potonganTitipan;

                if ($pinjamanAktif) {
                    $cicilan = (int) min(
                        (int) ($pinjamanAktif->cicilan_per_bulan ?? 0),
                        (int) ($pinjamanAktif->sisa_pinjaman ?? 0)
                    );
                }

                $dharma = $titipan ? (int) $titipan->iuran_dharma_wanita : $iuranDharmaWanita;
                $infaq = $titipan ? (int) $titipan->infaq_pegawai : $infaqPegawai;
                $qurban = $titipan ? (int) $titipan->tabungan_qurban : $tabunganQurban;
                $totalTitipan = $dharma + $infaq + $qurban;

                return [
                    'anggota' => $anggota,
                    'nama' => $anggota->nama,
                    'bank' => $anggota->rekeningAktif->nama_bank ?? '-',
                    'nomor_rekening' => $anggota->rekeningAktif->nomor_rekening ?? '-',
                    'wajib' => $wajibDefault,
                    'cicilan' => $cicilan,
                    'iuran_dharma_wanita' => $dharma,
                    'infaq_pegawai' => $infaq,
                    'tabungan_qurban' => $qurban,
                    'total_titipan' => $totalTitipan,
                    'iuran_operasional' => $iuranOperasional,
                    'total' => $wajibDefault + $cicilan + $totalTitipan + $iuranOperasional,
                ];
            })
            ->values();
    }

    private function buildPotonganRowsByBank(string $namaBank)
    {
        return $this->buildPotonganRows()
            ->where('bank', $namaBank)
            ->values();
    }

    private function toTerbilang(int $value): string
    {
        if ($value === 0) {
            return 'nol';
        }

        return trim($this->toPenyebut($value));
    }

    private function toPenyebut(int $value): string
    {
        $value = abs($value);
        $angka = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($value < 12) {
            return ' ' . $angka[$value];
        }

        if ($value < 20) {
            return $this->toPenyebut($value - 10) . ' belas';
        }

        if ($value < 100) {
            return $this->toPenyebut((int) floor($value / 10)) . ' puluh' . $this->toPenyebut($value % 10);
        }

        if ($value < 200) {
            return ' seratus' . $this->toPenyebut($value - 100);
        }

        if ($value < 1000) {
            return $this->toPenyebut((int) floor($value / 100)) . ' ratus' . $this->toPenyebut($value % 100);
        }

        if ($value < 2000) {
            return ' seribu' . $this->toPenyebut($value - 1000);
        }

        if ($value < 1000000) {
            return $this->toPenyebut((int) floor($value / 1000)) . ' ribu' . $this->toPenyebut($value % 1000);
        }

        if ($value < 1000000000) {
            return $this->toPenyebut((int) floor($value / 1000000)) . ' juta' . $this->toPenyebut($value % 1000000);
        }

        if ($value < 1000000000000) {
            return $this->toPenyebut((int) floor($value / 1000000000)) . ' miliar' . $this->toPenyebut($value % 1000000000);
        }

        return $this->toPenyebut((int) floor($value / 1000000000000)) . ' triliun' . $this->toPenyebut($value % 1000000000000);
    }
}
