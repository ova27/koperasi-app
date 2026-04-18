<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanPotonganBulananExport;
use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\PotonganTitipan;
use App\Models\RekeningKoperasi;
use App\Services\NominalTitipanImportService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPotonganBulananController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view laporan pinjaman');

        $bulanPotongan = $request->get('bulan', now()->addMonthNoOverflow()->format('Y-m'));
        $search = trim((string) $request->get('search', ''));
        $bulanAcuan = Carbon::createFromFormat('Y-m', $bulanPotongan)
            ->subMonthNoOverflow()
            ->format('Y-m');

        $allRows = $this->buildPotonganRows($bulanPotongan);
        $filteredRows = $allRows;

        if ($search !== '') {
            $needle = Str::lower($search);
            $filteredRows = $allRows
                ->filter(function ($row) use ($needle) {
                    return Str::contains(Str::lower((string) ($row['nama'] ?? '')), $needle)
                        || Str::contains(Str::lower((string) ($row['bank'] ?? '')), $needle)
                        || Str::contains(Str::lower((string) ($row['nomor_rekening'] ?? '')), $needle);
                })
                ->values();
        }

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $rows = new LengthAwarePaginator(
            $filteredRows->forPage($currentPage, $perPage)->values(),
            $filteredRows->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('admin.laporan.potongan-bulanan.index', [
            'bulanAcuan' => $bulanAcuan,
            'bulanPotongan' => $bulanPotongan,
            'rows' => $rows,
            'totalWajib' => (int) $filteredRows->sum('wajib'),
            'totalCicilan' => (int) $filteredRows->sum('cicilan'),
            'totalTitipan' => (int) $filteredRows->sum('total_titipan'),
            'totalIuranOperasional' => (int) $filteredRows->sum('iuran_operasional'),
            'totalDharma' => (int) $filteredRows->sum('iuran_dharma_wanita'),
            'totalInfaq' => (int) $filteredRows->sum('infaq_pegawai'),
            'totalQurban' => (int) $filteredRows->sum('tabungan_qurban'),
            'totalPotongan' => (int) $filteredRows->sum('total'),
            'ringkasanBank' => $filteredRows->groupBy('bank')->map(fn ($items) => [
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
        $allRows = $this->buildPotonganRows($bulanPotongan);

        $bankOptions = $allRows
            ->pluck('bank')
            ->filter(fn ($bank) => filled($bank))
            ->unique()
            ->sort()
            ->values();

        $rows = $namaBank !== ''
            ? $allRows->where('bank', $namaBank)->values()
            : $allRows;

        $rekeningKoperasiList = RekeningKoperasi::query()
            ->where('jenis', 'bank')
            ->where('aktif', true)
            ->get(['id', 'nama'])
            ->sortBy('nama')
            ->values();

        $rekeningKoperasiMap = $this->buildRekeningKoperasiAliasMap($rekeningKoperasiList);
        $selectedRekeningKoperasiId = $this->resolveRekeningKoperasiIdFromBankName($namaBank, $rekeningKoperasiMap);

        return view('admin.laporan.potongan-bulanan.bank', [
            'bulanPotongan' => $bulanPotongan,
            'namaBank' => $namaBank,
            'bankOptions' => $bankOptions,
            'rekeningKoperasiMap' => $rekeningKoperasiMap,
            'selectedRekeningKoperasiId' => $selectedRekeningKoperasiId,
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
        $this->authorize('view laporan pinjaman');

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
            'rekening_koperasi_id' => ['required', 'integer', 'exists:rekening_koperasis,id'],
        ]);

        $bulanPotongan = $validated['bulan'] ?? now()->addMonthNoOverflow()->format('Y-m');
        $namaBank = trim((string) $validated['nama_bank']);
        $rekeningKoperasiId = (int) $validated['rekening_koperasi_id'];

        $rekeningKoperasiList = RekeningKoperasi::query()
            ->where('jenis', 'bank')
            ->where('aktif', true)
            ->get(['id', 'nama'])
            ->values();
        $rekeningKoperasiMap = $this->buildRekeningKoperasiAliasMap($rekeningKoperasiList);
        $mappedRekeningKoperasiId = $this->resolveRekeningKoperasiIdFromBankName($namaBank, $rekeningKoperasiMap);

        if ($mappedRekeningKoperasiId === null || $mappedRekeningKoperasiId !== $rekeningKoperasiId) {
            return back()->with('error', 'Mapping bank rekening anggota ke rekening koperasi tidak sesuai. Pastikan bank terpilih sudah mengarah ke rekening koperasi yang benar.');
        }

        $rekeningKoperasi = RekeningKoperasi::query()
            ->whereKey($rekeningKoperasiId)
            ->where('jenis', 'bank')
            ->where('aktif', true)
            ->first();

        if (! $rekeningKoperasi) {
            return back()->with('error', 'Mapping rekening koperasi untuk export Word tidak valid atau tidak aktif.');
        }

        $rows = $this->buildPotonganRowsByBank($namaBank, $bulanPotongan);

        if ($rows->isEmpty()) {
            return back()->with('error', 'Data setoran untuk bank terpilih tidak ditemukan.');
        }

        Carbon::setLocale('id');

        $totalSetoran = (int) $rows->sum('total');
        $bulanLabel = Carbon::createFromFormat('Y-m', $bulanPotongan)
            ->locale('id')
            ->translatedFormat('F Y');

        $suratKuasaConfig = config('koperasi.surat_kuasa_bank', []);
        $bankTujuan = $this->resolveBankTujuanSuratKuasa($suratKuasaConfig, $rekeningKoperasi);
        $tanggalSurat = Carbon::parse($suratKuasaConfig['tanggal'] ?? now())
            ->locale('id')
            ->translatedFormat('d F Y');
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
                'bank_tujuan' => $bankTujuan,
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

    private function buildPotonganRows(?string $bulanPotongan = null)
    {
        $bulanPotongan = $bulanPotongan ?: now()->addMonthNoOverflow()->format('Y-m');
        $batasRiwayatCicilan = Carbon::createFromFormat('Y-m', $bulanPotongan)
            ->subMonthNoOverflow()
            ->endOfMonth();

        $wajibDefault = (int) config('koperasi.simpanan_wajib', 0);
        $iuranDharmaWanita = (int) config('koperasi.iuran_dharma_wanita', 0);
        $infaqPegawai = (int) config('koperasi.infaq_pegawai', 0);
        $tabunganQurban = (int) config('koperasi.tabungan_qurban', 0);
        $iuranOperasional = (int) config('koperasi.iuran_operasional', 5000);

        return Anggota::query()
            ->with([
                'rekeningAktif',
                'pinjamans.transaksi' => function ($query) {
                    $query->orderBy('tanggal')->orderBy('id');
                },
                'potonganTitipan',
            ])
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get()
            ->map(function ($anggota) use ($wajibDefault, $iuranOperasional, $iuranDharmaWanita, $infaqPegawai, $tabunganQurban, $batasRiwayatCicilan) {
                $pinjamanAcuan = $this->pinjamanPadaAkhirBulan($anggota->pinjamans, $batasRiwayatCicilan);
                $cicilan = 0;
                $titipan = $anggota->potonganTitipan;

                if ($pinjamanAcuan) {
                    $sisaPinjamanLalu = $this->sisaPinjamanPerAkhirBulan($pinjamanAcuan, $batasRiwayatCicilan);
                    if ($sisaPinjamanLalu > 0) {
                        $cicilan = (int) min(
                            (int) ($pinjamanAcuan->cicilan_per_bulan ?? 0),
                            $sisaPinjamanLalu
                        );
                    }
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

    private function buildPotonganRowsByBank(string $namaBank, ?string $bulanPotongan = null)
    {
        $rekeningKoperasiList = RekeningKoperasi::query()
            ->where('jenis', 'bank')
            ->where('aktif', true)
            ->get(['id', 'nama'])
            ->values();
        $rekeningKoperasiMap = $this->buildRekeningKoperasiAliasMap($rekeningKoperasiList);
        $selectedRekeningKoperasiId = $this->resolveRekeningKoperasiIdFromBankName($namaBank, $rekeningKoperasiMap);

        return $this->buildPotonganRows($bulanPotongan)
            ->filter(function ($row) use ($selectedRekeningKoperasiId, $namaBank, $rekeningKoperasiMap) {
                if ($selectedRekeningKoperasiId === null) {
                    return Str::lower(trim((string) ($row['bank'] ?? ''))) === Str::lower(trim($namaBank));
                }

                return $this->resolveRekeningKoperasiIdFromBankName((string) ($row['bank'] ?? ''), $rekeningKoperasiMap) === $selectedRekeningKoperasiId;
            })
            ->values();
    }

    private function buildRekeningKoperasiAliasMap($rekeningKoperasiList)
    {
        return $rekeningKoperasiList
            ->reduce(function ($carry, $rekening) {
                foreach ($this->bankNameCandidates((string) $rekening->nama) as $candidate) {
                    if ($candidate !== '' && ! array_key_exists($candidate, $carry)) {
                        $carry[$candidate] = (int) $rekening->id;
                    }
                }

                return $carry;
            }, []);
    }

    private function resolveRekeningKoperasiIdFromBankName(string $bankName, array $rekeningKoperasiMap): ?int
    {
        foreach ($this->bankNameCandidates($bankName) as $candidate) {
            if (array_key_exists($candidate, $rekeningKoperasiMap)) {
                return (int) $rekeningKoperasiMap[$candidate];
            }
        }

        return null;
    }

    private function bankNameCandidates(string $bankName): array
    {
        $normalized = $this->normalizeBankName($bankName);
        if ($normalized === '') {
            return [];
        }

        $candidates = [$normalized];
        $stopWords = ['bank', 'cabang', 'kantor', 'kc', 'kcp', 'pt', 'tbk'];

        if (preg_match_all('/\(([^)]+)\)/', $bankName, $matches)) {
            foreach ($matches[1] as $value) {
                $abbr = $this->normalizeBankName((string) $value);
                if ($abbr !== '') {
                    $candidates[] = $abbr;
                }
            }
        }

        $parts = preg_split('/\s+/', $normalized) ?: [];

        foreach ($parts as $part) {
            if ($part === '' || in_array($part, $stopWords, true)) {
                continue;
            }

            if (Str::length($part) >= 3) {
                $candidates[] = $part;
            }
        }

        $acronym = collect($parts)
            ->filter(fn ($part) => $part !== '')
            ->map(fn ($part) => Str::substr($part, 0, 1))
            ->implode('');

        if (Str::length($acronym) >= 2) {
            $candidates[] = $acronym;
        }

        return collect($candidates)
            ->filter(fn ($item) => $item !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeBankName(string $bankName): string
    {
        $normalized = Str::of($bankName)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->value();

        return trim($normalized);
    }

    private function resolveBankTujuanSuratKuasa(array $suratKuasaConfig, RekeningKoperasi $rekeningKoperasi): array
    {
        return [
            'nama' => (string) $rekeningKoperasi->nama,
            'nomor_rekening' => (string) ($rekeningKoperasi->nomor_rekening ?? '-'),
            'atas_nama' => (string) ($rekeningKoperasi->nama_pemilik ?? $suratKuasaConfig['bank_tujuan']['atas_nama'] ?? '-'),
            'keterangan' => (string) ($suratKuasaConfig['bank_tujuan']['keterangan'] ?? 'Pengurus Koperasi Pegawai BPS Provinsi Banten'),
        ];
    }

    private function pinjamanPadaAkhirBulan($pinjamans, Carbon $batasRiwayatCicilan)
    {
        $kandidat = $pinjamans
            ->filter(function ($pinjaman) use ($batasRiwayatCicilan) {
                return $pinjaman->tanggal_pinjam && $pinjaman->tanggal_pinjam->lte($batasRiwayatCicilan);
            })
            ->sortBy(function ($pinjaman) {
                $tanggal = $pinjaman->tanggal_pinjam ? $pinjaman->tanggal_pinjam->format('Ymd') : '00000000';
                return $tanggal . '-' . str_pad((string) $pinjaman->id, 10, '0', STR_PAD_LEFT);
            })
            ->values();

        for ($i = $kandidat->count() - 1; $i >= 0; $i--) {
            $pinjaman = $kandidat->get($i);
            if ($this->sisaPinjamanPerAkhirBulan($pinjaman, $batasRiwayatCicilan) > 0) {
                return $pinjaman;
            }
        }

        return null;
    }

    private function sisaPinjamanPerAkhirBulan($pinjaman, Carbon $batasRiwayatCicilan): int
    {
        if (! $pinjaman) {
            return 0;
        }

        if (! $pinjaman->tanggal_pinjam || $pinjaman->tanggal_pinjam->gt($batasRiwayatCicilan)) {
            return 0;
        }

        $transaksiSampaiBatas = $pinjaman->transaksi
            ->filter(function ($transaksi) use ($batasRiwayatCicilan) {
                return $transaksi->tanggal && $transaksi->tanggal->lte($batasRiwayatCicilan);
            })
            ->values();

        if ($transaksiSampaiBatas->isNotEmpty()) {
            return max(0, (int) ($transaksiSampaiBatas->last()->sisa_setelah ?? 0));
        }

        return max(0, (int) ($pinjaman->jumlah_pinjaman ?? 0));
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
