<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanPotonganBulananExport;
use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\PotonganBulananDetail;
use App\Models\PotonganBulananSetting;
use App\Models\PotonganTitipan;
use App\Models\RekeningKoperasi;
use App\Services\NominalTitipanImportService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPotonganBulananController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view laporan pinjaman');

        $bulanPotongan = $this->validatedBulanPotongan($request);
        $batasBulanPotongan = $this->batasBulanPotongan();
        $isFixed = $this->isBulanPotonganFixed($bulanPotongan);
        $canManagePotongan = $request->user()?->can('manage simpanan anggota') ?? false;
        $search = trim((string) $request->get('search', ''));
        $bulanAcuan = Carbon::createFromFormat('Y-m', $bulanPotongan)
            ->subMonthNoOverflow()
            ->format('Y-m');

        $allRows = $isFixed
            ? $this->buildFixedPotonganRows($bulanPotongan)
            : ($canManagePotongan ? $this->buildPotonganRows($bulanPotongan) : collect());
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
            'batasBulanPotongan' => $batasBulanPotongan,
            'isFixed' => $isFixed,
            'canManagePotongan' => $canManagePotongan,
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

        $bulanPotongan = $this->validatedBulanPotongan($request);
        $batasBulanPotongan = $this->batasBulanPotongan();
        $isFixed = $this->isBulanPotonganFixed($bulanPotongan);
        $canManagePotongan = $request->user()?->can('manage simpanan anggota') ?? false;
        $namaBank = trim((string) $request->get('nama_bank', ''));
        $allRows = $isFixed
            ? $this->buildFixedPotonganRows($bulanPotongan)
            : ($canManagePotongan ? $this->buildPotonganRows($bulanPotongan) : collect());

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
            'batasBulanPotongan' => $batasBulanPotongan,
            'isFixed' => $isFixed,
            'canManagePotongan' => $canManagePotongan,
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
            'cicilan' => ['required', 'integer', 'min:0'],
            'iuran_operasional' => ['required', 'integer', 'min:0'],
            'bulan' => $this->bulanPotonganRules(),
        ]);

        $bulanPotongan = $validated['bulan'] ?? now()->addMonthNoOverflow()->format('Y-m');
        if ($this->isBulanPotonganFixed($bulanPotongan)) {
            return back()->with('error', 'Potongan bulan ini sudah difix, nominal tidak bisa diubah.');
        }


        // Update PotonganTitipan
        PotonganTitipan::updateOrCreate(
            ['anggota_id' => $anggota->id],
            [
                'iuran_dharma_wanita' => (int) $validated['iuran_dharma_wanita'],
                'infaq_pegawai' => (int) $validated['infaq_pegawai'],
                'tabungan_qurban' => (int) $validated['tabungan_qurban'],
            ]
        );

        // Update PotonganBulananDetail untuk bulan berjalan jika ada (jika belum difix)
        PotonganBulananDetail::where('bulan_potongan', $bulanPotongan)
            ->where('anggota_id', $anggota->id)
            ->update([
                'cicilan' => (int) $validated['cicilan'],
                'iuran_operasional' => (int) $validated['iuran_operasional'],
                'iuran_dharma_wanita' => (int) $validated['iuran_dharma_wanita'],
                'infaq_pegawai' => (int) $validated['infaq_pegawai'],
                'tabungan_qurban' => (int) $validated['tabungan_qurban'],
            ]);

        return redirect()
            ->route('admin.laporan.potongan-bulanan.index', ['bulan' => $bulanPotongan])
            ->with('success', 'Nominal titipan ' . $anggota->nama . ' berhasil diperbarui');
    }

    public function fix(Request $request)
    {
        $this->authorize('manage simpanan anggota');

        $bulanPotongan = $this->validatedBulanPotongan($request);

        if ($this->isBulanPotonganFixed($bulanPotongan)) {
            return redirect()
                ->route('admin.laporan.potongan-bulanan.index', ['bulan' => $bulanPotongan])
                ->with('info', 'Potongan bulan ini sudah difix.');
        }

        $rows = $this->buildPotonganRows($bulanPotongan);

        DB::transaction(function () use ($bulanPotongan, $rows, $request) {
            $setting = PotonganBulananSetting::updateOrCreate(
                ['bulan_potongan' => $bulanPotongan],
                [
                    'is_fixed' => true,
                    'fixed_at' => now(),
                    'fixed_by' => $request->user()?->id,
                ]
            );

            PotonganBulananDetail::where('bulan_potongan', $setting->bulan_potongan)->delete();

            foreach ($rows as $row) {
                PotonganBulananDetail::create([
                    'bulan_potongan' => $setting->bulan_potongan,
                    'anggota_id' => $row['anggota']->id ?? null,
                    'nama' => (string) ($row['nama'] ?? '-'),
                    'bank' => (string) ($row['bank'] ?? '-'),
                    'nomor_rekening' => (string) ($row['nomor_rekening'] ?? '-'),
                    'simpanan_wajib' => (int) ($row['wajib'] ?? 0),
                    'cicilan' => (int) ($row['cicilan'] ?? 0),
                    'iuran_dharma_wanita' => (int) ($row['iuran_dharma_wanita'] ?? 0),
                    'infaq_pegawai' => (int) ($row['infaq_pegawai'] ?? 0),
                    'tabungan_qurban' => (int) ($row['tabungan_qurban'] ?? 0),
                    'total_titipan' => (int) ($row['total_titipan'] ?? 0),
                    'iuran_operasional' => (int) ($row['iuran_operasional'] ?? 0),
                    'total' => (int) ($row['total'] ?? 0),
                    'sisa_pinjaman_lalu' => (int) ($row['sisa_pinjaman_lalu'] ?? 0),
                    'sisa_pinjaman_sekarang' => (int) ($row['sisa_pinjaman_sekarang'] ?? 0),
                    'tenor' => (string) ($row['tenor'] ?? '-'),
                    'cicilan_ke' => (string) ($row['cicilan_ke'] ?? '-'),
                ]);
            }
        });

        return redirect()
            ->route('admin.laporan.potongan-bulanan.index', ['bulan' => $bulanPotongan])
            ->with('success', 'Potongan bulan ' . $bulanPotongan . ' berhasil difix.');
    }

    public function cancelFix(Request $request)
    {
        $this->authorize('manage simpanan anggota');

        $bulanPotongan = $this->validatedBulanPotongan($request);

        if (! $this->isBulanPotonganFixed($bulanPotongan)) {
            return redirect()
                ->route('admin.laporan.potongan-bulanan.index', ['bulan' => $bulanPotongan])
                ->with('info', 'Potongan bulan ini belum difix.');
        }

        DB::transaction(function () use ($bulanPotongan) {
            PotonganBulananSetting::where('bulan_potongan', $bulanPotongan)
                ->update([
                    'is_fixed' => false,
                    'fixed_at' => null,
                    'fixed_by' => null,
                ]);

            PotonganBulananDetail::where('bulan_potongan', $bulanPotongan)->delete();
        });

        return redirect()
            ->route('admin.laporan.potongan-bulanan.index', ['bulan' => $bulanPotongan])
            ->with('success', 'Fix potongan bulan ' . $bulanPotongan . ' berhasil dibatalkan. Data sudah bisa diperbaiki lagi.');
    }

    public function export(Request $request)
    {
        $this->authorize('view laporan pinjaman');

        $bulanPotongan = $this->validatedBulanPotongan($request);
        if (! $this->isBulanPotonganFixed($bulanPotongan)) {
            return back()->with('error', 'Rincian potongan bulan ini belum difix oleh Bendahara.');
        }

        return Excel::download(
            new LaporanPotonganBulananExport($bulanPotongan),
            'rincian-potongan-' . $bulanPotongan . '.xlsx'
        );
    }

    public function exportBank(Request $request)
    {
        $this->authorize('export laporan pinjaman');

        $bulanPotongan = $this->validatedBulanPotongan($request);
        if (! $this->isBulanPotonganFixed($bulanPotongan)) {
            return back()->with('error', 'Setoran bank bulan ini belum difix oleh Bendahara.');
        }
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
            'bulan' => $this->bulanPotonganRules(),
            'nama_bank' => ['required', 'string'],
            'rekening_koperasi_id' => ['required', 'integer', 'exists:rekening_koperasis,id'],
        ]);

        $bulanPotongan = $validated['bulan'] ?? now()->addMonthNoOverflow()->format('Y-m');
        if (! $this->isBulanPotonganFixed($bulanPotongan)) {
            return back()->with('error', 'Setoran bank bulan ini belum difix oleh Bendahara.');
        }
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

        $bulanPotongan = now()->addMonthNoOverflow()->format('Y-m');
        $rows = $this->buildPotonganRows($bulanPotongan);

        return Excel::download(
            new class($rows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function __construct(private $rows) {}

                public function array(): array
                {
                    return $this->rows->map(function($row) {
                        return [
                            $row['nama'],
                            $row['cicilan'] ?? 0,
                            $row['iuran_operasional'] ?? 0,
                            $row['iuran_dharma_wanita'] ?? 0,
                            $row['infaq_pegawai'] ?? 0,
                            $row['tabungan_qurban'] ?? 0,
                        ];
                    })->toArray();
                }

                public function headings(): array
                {
                    return ['Nama Anggota', 'Cicilan Pinjaman', 'Iuran Operasional', 'Iuran Dharma Wanita', 'Infaq Pegawai', 'Tabungan Qurban'];
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
                $sisaPinjamanLalu = 0;
                $sisaPinjamanSekarang = 0;
                $tenor = '-';
                $cicilanKe = '-';
                $titipan = $anggota->potonganTitipan;

                if ($pinjamanAcuan) {
                    $sisaPinjamanLalu = $this->sisaPinjamanPerAkhirBulan($pinjamanAcuan, $batasRiwayatCicilan);
                    if ($sisaPinjamanLalu > 0) {
                        $tenor = (int) ($pinjamanAcuan->tenor ?? 0) > 0
                            ? (string) ((int) $pinjamanAcuan->tenor)
                            : '-';
                        $cicilan = (int) min(
                            (int) ($pinjamanAcuan->cicilan_per_bulan ?? 0),
                            $sisaPinjamanLalu
                        );
                        $sisaPinjamanSekarang = max(0, $sisaPinjamanLalu - $cicilan);
                        $cicilanKe = (string) ($this->jumlahCicilanSampaiBulan($pinjamanAcuan->transaksi, $batasRiwayatCicilan) + 1);
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
                    'sisa_pinjaman_lalu' => $sisaPinjamanLalu,
                    'sisa_pinjaman_sekarang' => $sisaPinjamanSekarang,
                    'tenor' => $tenor,
                    'cicilan_ke' => $cicilanKe,
                ];
            })
            ->values();
    }

    private function validatedBulanPotongan(Request $request): string
    {
        $validated = $request->validate([
            'bulan' => $this->bulanPotonganRules(),
        ]);

        return $validated['bulan'] ?? now()->addMonthNoOverflow()->format('Y-m');
    }

    private function bulanPotonganRules(): array
    {
        return [
            'bail',
            'nullable',
            'date_format:Y-m',
            function (string $attribute, mixed $value, \Closure $fail) {
                if ($value === null || $value === '') {
                    return;
                }

                $bulan = Carbon::createFromFormat('Y-m', (string) $value)->startOfMonth();
                $batas = Carbon::createFromFormat('Y-m', $this->batasBulanPotongan())->startOfMonth();

                if ($bulan->gt($batas)) {
                    $fail('Bulan potongan maksimal hanya sampai bulan depan.');
                }
            },
        ];
    }

    private function batasBulanPotongan(): string
    {
        return now()->addMonthNoOverflow()->format('Y-m');
    }

    private function isBulanPotonganFixed(string $bulanPotongan): bool
    {
        return PotonganBulananSetting::where('bulan_potongan', $bulanPotongan)
            ->where('is_fixed', true)
            ->exists();
    }

    private function buildFixedPotonganRows(string $bulanPotongan)
    {
        return PotonganBulananDetail::query()
            ->with('anggota')
            ->where('bulan_potongan', $bulanPotongan)
            ->orderBy('nama')
            ->get()
            ->map(fn (PotonganBulananDetail $detail) => [
                'anggota' => $detail->anggota,
                'nama' => $detail->nama,
                'bank' => $detail->bank ?? '-',
                'nomor_rekening' => $detail->nomor_rekening ?? '-',
                'wajib' => (int) $detail->simpanan_wajib,
                'cicilan' => (int) $detail->cicilan,
                'iuran_dharma_wanita' => (int) $detail->iuran_dharma_wanita,
                'infaq_pegawai' => (int) $detail->infaq_pegawai,
                'tabungan_qurban' => (int) $detail->tabungan_qurban,
                'total_titipan' => (int) $detail->total_titipan,
                'iuran_operasional' => (int) $detail->iuran_operasional,
                'total' => (int) $detail->total,
                'sisa_pinjaman_lalu' => (int) $detail->sisa_pinjaman_lalu,
                'sisa_pinjaman_sekarang' => (int) $detail->sisa_pinjaman_sekarang,
                'tenor' => $detail->tenor ?? '-',
                'cicilan_ke' => $detail->cicilan_ke ?? '-',
            ])
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

        $rows = $this->isBulanPotonganFixed($bulanPotongan)
            ? $this->buildFixedPotonganRows($bulanPotongan)
            : $this->buildPotonganRows($bulanPotongan);

        return $rows
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

    private function jumlahCicilanSampaiBulan($transaksi, Carbon $batasRiwayatCicilan): int
    {
        return $transaksi
            ->where('jenis', 'cicilan')
            ->filter(function ($item) use ($batasRiwayatCicilan) {
                return $item->tanggal && $item->tanggal->lte($batasRiwayatCicilan);
            })
            ->count();
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
