<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\ArusKas;
use App\Models\Pinjaman;
use App\Models\PotonganBulananDetail;
use App\Models\PotonganBulananSetting;
use App\Models\PotonganTitipan;
use App\Models\RekeningAnggota;
use App\Models\RekeningKoperasi;
use App\Models\Simpanan;
use App\Models\TransaksiPinjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

class LegacySeptemberPositionImportService
{
    private const SNAPSHOT_DATE = '2026-09-30';

    private const EXPECTED = [
        'anggota_aktif' => 84,
        'simpanan' => 315_848_076,
        'pinjaman' => 313_025_000,
        'potongan' => 51_105_000,
        'kas_koperasi' => 2_823_076,
        'kas_operasional' => 1_108_517,
    ];

    /** @var array<string, array{balance:int, tenor:int, cicilan:int, tanggal:string, pencairan:int}> */
    private const SEPTEMBER_RESTRUCTURES = [
        'pramitagayatri' => ['balance' => 20_000_000, 'tenor' => 10, 'cicilan' => 2_000_000, 'tanggal' => '2026-09-04', 'pencairan' => 20_000_000],
        'viskaekayani' => ['balance' => 9_000_000, 'tenor' => 3, 'cicilan' => 3_000_000, 'tanggal' => '2026-09-08', 'pencairan' => 3_000_000],
        'maulani' => ['balance' => 16_000_000, 'tenor' => 16, 'cicilan' => 1_000_000, 'tanggal' => '2026-09-04', 'pencairan' => 10_000_000],
        'rikamustikaasjaya' => ['balance' => 15_000_000, 'tenor' => 15, 'cicilan' => 1_000_000, 'tanggal' => '2026-09-04', 'pencairan' => 8_500_000],
    ];

    public function prepare(string $masterPath, string $rincianPath, string $bankPath, string $cashPath): array
    {
        foreach ([$masterPath, $rincianPath, $bankPath, $cashPath] as $path) {
            if (! is_file($path)) {
                throw new RuntimeException("File tidak ditemukan: {$path}");
            }
        }

        $master = IOFactory::load($masterPath);
        $rincian = IOFactory::load($rincianPath);
        $bank = IOFactory::load($bankPath);
        $cash = IOFactory::load($cashPath);

        $anggotaSheet = $master->getSheetByName('Anggota');
        $simpananSheet = $master->getSheetByName('Simpanan');
        $pinjamanSheet = $master->getSheetByName('Pinjaman');
        $neracaSheet = $master->getSheetByName('Neraca');
        $operasionalSheet = $master->getSheetByName('Operasional');
        $rincianSheet = $rincian->getSheetByName('September');
        $briSheet = $bank->getSheetByName('BRI');
        $bsiSheet = $bank->getSheetByName('BSI');
        $cashSheet = $cash->getSheetByName('2026');

        if (! $anggotaSheet || ! $simpananSheet || ! $pinjamanSheet || ! $neracaSheet || ! $operasionalSheet || ! $rincianSheet || ! $briSheet || ! $bsiSheet || ! $cashSheet) {
            throw new RuntimeException('Ada sheet sumber wajib yang tidak ditemukan.');
        }

        $data = [
            'anggota' => $this->readAnggota($anggotaSheet),
            'simpanan' => $this->readSimpanan($simpananSheet),
            'pinjaman' => $this->readPinjaman($pinjamanSheet, $rincianSheet),
            'potongan' => $this->readPotongan($rincianSheet, $briSheet, $bsiSheet),
            'kas' => [
                'koperasi' => $this->integerValue($neracaSheet, 'F7'),
                'operasional' => $this->integerValue($operasionalSheet, 'C18'),
            ],
        ];

        $this->validateCashSource($cashSheet);
        $this->validatePreparedData($data);

        return $data;
    }

    public function import(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $this->deleteLegacyDomainData();

            $anggotaIds = [];
            $usersByEmail = User::query()->get()->keyBy(fn (User $user) => mb_strtolower(trim($user->email)));

            foreach ($data['anggota'] as $row) {
                $user = $usersByEmail->get(mb_strtolower($row['email']));
                $anggota = Anggota::create([
                    'user_id' => $user?->id,
                    'nomor_anggota' => sprintf('AG-%04d', $row['nomor']),
                    'nip' => $row['nip'] !== '' ? $row['nip'] : null,
                    'nama' => $row['nama'],
                    'jenis_kelamin' => $row['jenis_kelamin'] ?: null,
                    'jabatan' => null,
                    'status' => 'aktif',
                    'tanggal_masuk' => $row['tanggal_masuk'],
                    'tanggal_keluar' => null,
                ]);
                $anggotaIds[$this->normalizeName($row['nama'])] = $anggota->id;
            }

            $faizal = Anggota::create([
                'user_id' => null,
                'nomor_anggota' => 'LEGACY-FAIZAL-AHKAMI',
                'nip' => null,
                'nama' => 'Faizal Ahkami',
                'jenis_kelamin' => 'L',
                'jabatan' => null,
                'status' => 'tidak_aktif',
                'tanggal_masuk' => '2015-01-01',
                'tanggal_keluar' => '2026-05-01',
            ]);
            $anggotaIds[$this->normalizeName($faizal->nama)] = $faizal->id;

            foreach ($data['potongan'] as $row) {
                $key = $this->normalizeName($row['nama']);
                if ($row['bank'] && $key !== 'faizalahkami') {
                    RekeningAnggota::create([
                        'anggota_id' => $anggotaIds[$key],
                        'nama_bank' => $row['bank'],
                        'nomor_rekening' => $row['rekening'],
                        'nama_pemilik' => $row['nama'],
                        'aktif' => true,
                    ]);
                }
            }

            foreach ($data['simpanan'] as $row) {
                $anggotaId = $anggotaIds[$this->normalizeName($row['nama'])];
                foreach (['pokok', 'wajib', 'sukarela'] as $jenis) {
                    if ($row[$jenis] <= 0) {
                        continue;
                    }
                    Simpanan::create([
                        'anggota_id' => $anggotaId,
                        'tanggal' => self::SNAPSHOT_DATE,
                        'jenis_simpanan' => $jenis,
                        'jumlah' => $row[$jenis],
                        'sumber' => 'saldo_awal',
                        'alasan' => 'biasa',
                        'keterangan' => 'Saldo posisi hasil migrasi laporan lama per September 2026',
                    ]);
                }
            }

            foreach ($data['pinjaman'] as $row) {
                $anggotaId = $anggotaIds[$this->normalizeName($row['nama'])];
                $pinjaman = Pinjaman::create([
                    'pengajuan_id' => null,
                    'anggota_id' => $anggotaId,
                    'tanggal_pinjam' => $row['tanggal_pinjam'],
                    'jumlah_pinjaman' => $row['jumlah_pinjaman'],
                    'sisa_pinjaman' => $row['sisa_pinjaman'],
                    'tenor' => $row['tenor'],
                    'cicilan_per_bulan' => $row['cicilan_per_bulan'],
                    'status' => 'aktif',
                    'keterangan' => $row['keterangan'],
                ]);

                $terbayar = max(0, $row['jumlah_pinjaman'] - $row['sisa_pinjaman']);
                if ($terbayar > 0) {
                    TransaksiPinjaman::create([
                        'pinjaman_id' => $pinjaman->id,
                        'tanggal' => self::SNAPSHOT_DATE,
                        'jenis' => 'cicilan',
                        'jumlah' => $terbayar,
                        'sisa_setelah' => $row['sisa_pinjaman'],
                        'keterangan' => 'Akumulasi cicilan sebelum migrasi posisi September 2026',
                    ]);
                }
            }

            $rekeningKoperasi = RekeningKoperasi::where('aktif', true)->first();
            $creator = User::role('admin')->first() ?? User::first();
            if (! $rekeningKoperasi || ! $creator) {
                throw new RuntimeException('Rekening koperasi aktif atau akun admin tidak ditemukan.');
            }
            foreach (['koperasi', 'operasional'] as $jenisArus) {
                ArusKas::create([
                    'tanggal' => self::SNAPSHOT_DATE,
                    'rekening_koperasi_id' => $rekeningKoperasi->id,
                    'jenis_arus' => $jenisArus,
                    'tipe' => 'masuk',
                    'kategori' => 'saldo_awal',
                    'sub_kategori' => 'migrasi',
                    'jumlah' => $data['kas'][$jenisArus],
                    'anggota_id' => null,
                    'created_by' => $creator->id,
                    'keterangan' => 'Saldo posisi hasil migrasi laporan lama per September 2026',
                ]);
            }

            PotonganBulananSetting::create([
                'bulan_potongan' => '2026-09',
                'iuran_dharma_wanita' => 0,
                'infaq_pegawai' => 0,
                'tabungan_qurban' => 0,
                'is_fixed' => true,
                'fixed_at' => self::SNAPSHOT_DATE . ' 23:59:59',
                'fixed_by' => null,
            ]);

            foreach ($data['potongan'] as $row) {
                $anggotaId = $anggotaIds[$this->normalizeName($row['nama'])];
                PotonganBulananDetail::create([
                    'bulan_potongan' => '2026-09',
                    'anggota_id' => $anggotaId,
                    'nama' => $row['nama'],
                    'bank' => $row['bank'] ?: '-',
                    'nomor_rekening' => $row['rekening'] ?: '-',
                    'metode_pembayaran' => $row['metode_pembayaran'],
                    'simpanan_wajib' => $row['simpanan_wajib'],
                    'simpanan_sukarela' => 0,
                    'cicilan' => $row['cicilan'],
                    'iuran_dharma_wanita' => $row['iuran_dharma_wanita'],
                    'infaq_pegawai' => $row['infaq_pegawai'],
                    'tabungan_qurban' => 0,
                    'total_titipan' => $row['iuran_dharma_wanita'] + $row['infaq_pegawai'],
                    'iuran_operasional' => $row['iuran_operasional'],
                    'total' => $row['total'],
                    'sisa_pinjaman_lalu' => $row['sisa_pinjaman_lalu'],
                    'sisa_pinjaman_sekarang' => $row['sisa_pinjaman_sekarang'],
                    'tenor' => (string) $row['tenor'],
                    'cicilan_ke' => (string) $row['cicilan_ke'],
                ]);

                if ($key = $this->normalizeName($row['nama'])) {
                    PotonganTitipan::create([
                        'anggota_id' => $anggotaIds[$key],
                        'iuran_dharma_wanita' => $row['iuran_dharma_wanita'],
                        'infaq_pegawai' => $row['infaq_pegawai'],
                        'tabungan_qurban' => 0,
                    ]);
                }
            }

            $result = [
                'anggota_aktif' => Anggota::where('status', 'aktif')->count(),
                'anggota_tidak_aktif' => Anggota::where('status', 'tidak_aktif')->count(),
                'simpanan' => (int) Simpanan::sum('jumlah'),
                'pinjaman' => (int) Pinjaman::where('status', 'aktif')->sum('sisa_pinjaman'),
                'potongan' => (int) PotonganBulananDetail::where('bulan_potongan', '2026-09')->sum('total'),
                'kas_koperasi' => (int) ArusKas::where('jenis_arus', 'koperasi')->selectRaw("SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE -jumlah END) AS saldo")->value('saldo'),
                'kas_operasional' => (int) ArusKas::where('jenis_arus', 'operasional')->selectRaw("SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE -jumlah END) AS saldo")->value('saldo'),
            ];

            if ($result['anggota_aktif'] !== self::EXPECTED['anggota_aktif']
                || $result['simpanan'] !== self::EXPECTED['simpanan']
                || $result['pinjaman'] !== self::EXPECTED['pinjaman']
                || $result['potongan'] !== self::EXPECTED['potongan']
                || $result['kas_koperasi'] !== self::EXPECTED['kas_koperasi']
                || $result['kas_operasional'] !== self::EXPECTED['kas_operasional']) {
                throw new RuntimeException('Validasi setelah import gagal. Seluruh perubahan dibatalkan otomatis.');
            }

            return $result;
        }, 3);
    }

    private function readAnggota(Worksheet $sheet): array
    {
        $rows = [];
        for ($row = 6; $row <= $sheet->getHighestDataRow(); $row++) {
            if (! is_numeric($sheet->getCell("A{$row}")->getValue())) {
                continue;
            }
            $rows[] = [
                'nomor' => (int) $sheet->getCell("A{$row}")->getValue(),
                'nama' => trim((string) $sheet->getCell("B{$row}")->getValue()),
                'jenis_kelamin' => strtoupper(trim((string) $sheet->getCell("C{$row}")->getValue())),
                'nip' => preg_replace('/\D+/', '', (string) $sheet->getCell("D{$row}")->getFormattedValue()) ?? '',
                'email' => trim((string) $sheet->getCell("E{$row}")->getValue()),
                'tanggal_masuk' => $this->estimateJoinDate($sheet->getParent()->getSheetByName('Simpanan'), $row),
            ];
        }

        return $rows;
    }

    private function estimateJoinDate(?Worksheet $sheet, int $row): string
    {
        if (! $sheet) {
            return '2026-01-01';
        }
        $periods = [
            ['D', '2011-01-01'], ['H', '2011-02-01'], ['L', '2012-01-01'], ['P', '2013-01-01'],
            ['T', '2014-01-01'], ['X', '2015-01-01'], ['AB', '2016-01-01'], ['AF', '2017-01-01'],
            ['AJ', '2018-01-01'], ['AN', '2019-01-01'], ['AR', '2020-01-01'], ['AV', '2021-01-01'],
            ['AZ', '2022-01-01'], ['BD', '2023-01-01'], ['BH', '2024-01-01'], ['BO', '2025-01-01'],
            ['BY', '2026-01-01'],
        ];
        foreach ($periods as [$column, $date]) {
            if ($this->integerValue($sheet, "{$column}{$row}") !== 0) {
                return $date;
            }
        }

        return '2026-01-01';
    }

    private function readSimpanan(Worksheet $sheet): array
    {
        $rows = [];
        for ($row = 6; $row <= $sheet->getHighestDataRow(); $row++) {
            if (! is_numeric($sheet->getCell("A{$row}")->getValue())) {
                continue;
            }
            $total = $this->integerValue($sheet, "CB{$row}");
            $pokok = min($total, max(0, $this->integerValue($sheet, "C{$row}")));
            // Mengikuti angka Neraca: BZ (2026) + BP (2025) + koreksi BI61 = Rp1.500.000.
            $sukarelaSource = $this->integerValue($sheet, "BZ{$row}")
                + $this->integerValue($sheet, "BP{$row}")
                + ($row === 61 ? $this->integerValue($sheet, "BI{$row}") : 0);
            $sukarela = min(max(0, $sukarelaSource), $total - $pokok);
            $rows[] = [
                'nama' => trim((string) $sheet->getCell("B{$row}")->getValue()),
                'pokok' => $pokok,
                'sukarela' => $sukarela,
                'wajib' => $total - $pokok - $sukarela,
                'total' => $total,
            ];
        }

        return $rows;
    }

    private function readPinjaman(Worksheet $sheet, Worksheet $rincianSheet): array
    {
        $details = [];
        for ($row = 7; $row <= $rincianSheet->getHighestDataRow(); $row++) {
            if (! is_numeric($rincianSheet->getCell("A{$row}")->getValue())) {
                continue;
            }
            $details[$this->normalizeName((string) $rincianSheet->getCell("B{$row}")->getValue())] = [
                'tenor' => $this->integerValue($rincianSheet, "E{$row}"),
                'cicilan_ke' => $this->integerValue($rincianSheet, "F{$row}"),
                'cicilan' => $this->integerValue($rincianSheet, "G{$row}"),
            ];
        }

        $rows = [];
        for ($row = 6; $row <= $sheet->getHighestDataRow(); $row++) {
            if (! is_numeric($sheet->getCell("A{$row}")->getValue())) {
                continue;
            }
            $nama = trim((string) $sheet->getCell("B{$row}")->getValue());
            $key = $this->normalizeName($nama);
            $sisa = $this->integerValue($sheet, "AU{$row}");
            if ($sisa <= 0) {
                continue;
            }

            if (isset(self::SEPTEMBER_RESTRUCTURES[$key])) {
                $special = self::SEPTEMBER_RESTRUCTURES[$key];
                $rows[] = [
                    'nama' => $nama,
                    'tanggal_pinjam' => $special['tanggal'],
                    'jumlah_pinjaman' => $special['balance'],
                    'sisa_pinjaman' => $sisa,
                    'tenor' => $special['tenor'],
                    'cicilan_per_bulan' => $special['cicilan'],
                    'keterangan' => 'Pinjaman baru/top-up/restruktur September 2026; cicilan baru mulai Oktober 2026',
                ];
                continue;
            }

            $detail = $details[$key] ?? null;
            if (! $detail || $detail['tenor'] <= 0 || $detail['cicilan'] <= 0) {
                throw new RuntimeException("Jadwal pinjaman aktif tidak ditemukan untuk {$nama}.");
            }
            $jumlah = max($sisa, $detail['cicilan'] * $detail['tenor'], $sisa + ($detail['cicilan'] * $detail['cicilan_ke']));
            $tanggal = Carbon::parse(self::SNAPSHOT_DATE)->startOfMonth()->subMonths(max(0, $detail['cicilan_ke'] - 1));
            $rows[] = [
                'nama' => $nama,
                'tanggal_pinjam' => $tanggal->toDateString(),
                'jumlah_pinjaman' => $jumlah,
                'sisa_pinjaman' => $sisa,
                'tenor' => $detail['tenor'],
                'cicilan_per_bulan' => $detail['cicilan'],
                'keterangan' => 'Posisi pinjaman aktif hasil migrasi laporan lama per September 2026',
            ];
        }

        return $rows;
    }

    private function readPotongan(Worksheet $sheet, Worksheet $briSheet, Worksheet $bsiSheet): array
    {
        $bankRows = [];
        foreach ([[$briSheet, 'BRI'], [$bsiSheet, 'BSI']] as [$bankSheet, $bank]) {
            for ($row = 1; $row <= $bankSheet->getHighestDataRow(); $row++) {
                if (! is_numeric($bankSheet->getCell("A{$row}")->getValue())) {
                    continue;
                }
                $nama = trim((string) $bankSheet->getCell("B{$row}")->getValue());
                $bankRows[$this->normalizeName($nama)] = [
                    'bank' => $bank,
                    'rekening' => $this->accountValue($bankSheet->getCell("D{$row}")->getValue()),
                ];
            }
        }

        $rows = [];
        for ($row = 7; $row <= $sheet->getHighestDataRow(); $row++) {
            if (! is_numeric($sheet->getCell("A{$row}")->getValue())) {
                continue;
            }
            $nama = trim((string) $sheet->getCell("B{$row}")->getValue());
            $key = $this->normalizeName($nama);
            $simpananDanOperasional = $this->integerValue($sheet, "I{$row}");
            $bank = $bankRows[$key] ?? null;
            $rows[] = [
                'nama' => $nama,
                'bank' => $bank['bank'] ?? null,
                'rekening' => $bank['rekening'] ?? $this->accountValue($sheet->getCell("C{$row}")->getValue()),
                'metode_pembayaran' => $bank ? 'potong_bank' : 'transfer_manual',
                'sisa_pinjaman_lalu' => $this->integerValue($sheet, "D{$row}"),
                'tenor' => $this->integerValue($sheet, "E{$row}"),
                'cicilan_ke' => $this->integerValue($sheet, "F{$row}"),
                'cicilan' => $this->integerValue($sheet, "G{$row}"),
                'sisa_pinjaman_sekarang' => $this->integerValue($sheet, "H{$row}"),
                'simpanan_wajib' => max(0, $simpananDanOperasional - 5_000),
                'iuran_operasional' => min(5_000, $simpananDanOperasional),
                'iuran_dharma_wanita' => $this->integerValue($sheet, "J{$row}"),
                'infaq_pegawai' => $this->integerValue($sheet, "K{$row}"),
                'total' => $this->integerValue($sheet, "L{$row}"),
            ];
        }

        return $rows;
    }

    private function validateCashSource(Worksheet $sheet): void
    {
        $found = [];
        for ($row = 1; $row <= $sheet->getHighestDataRow(); $row++) {
            $date = $sheet->getCell("B{$row}")->getValue();
            $description = trim((string) $sheet->getCell("C{$row}")->getValue());
            if (! $date || ! str_contains(mb_strtolower($description), 'pinjaman')) {
                continue;
            }
            $key = $this->normalizeName(preg_replace('/^.*?\ban\.?\s*/iu', '', $description) ?? $description);
            foreach (self::SEPTEMBER_RESTRUCTURES as $nameKey => $expected) {
                if (str_contains($key, $nameKey) || str_contains($nameKey, $key)) {
                    $amount = $this->integerValue($sheet, "E{$row}");
                    if ($amount === $expected['pencairan']) {
                        $found[$nameKey] = true;
                    }
                }
            }
        }
        $missing = array_diff(array_keys(self::SEPTEMBER_RESTRUCTURES), array_keys($found));
        if ($missing !== []) {
            throw new RuntimeException('Pencairan September tidak lengkap di file arus kas: ' . implode(', ', $missing));
        }
    }

    private function validatePreparedData(array $data): void
    {
        $activeKeys = array_column($data['anggota'], null, 'nama');
        if (count($activeKeys) !== self::EXPECTED['anggota_aktif']) {
            throw new RuntimeException('Jumlah anggota aktif sumber tidak sesuai 84.');
        }
        if (array_sum(array_column($data['simpanan'], 'total')) !== self::EXPECTED['simpanan']) {
            throw new RuntimeException('Total simpanan sumber tidak sesuai Rp315.848.076.');
        }
        if (array_sum(array_column($data['pinjaman'], 'sisa_pinjaman')) !== self::EXPECTED['pinjaman']) {
            throw new RuntimeException('Total pinjaman sumber tidak sesuai Rp313.025.000.');
        }
        if (array_sum(array_column($data['potongan'], 'total')) !== self::EXPECTED['potongan']) {
            throw new RuntimeException('Total potongan sumber tidak sesuai Rp51.105.000.');
        }
        if ($data['kas']['koperasi'] !== self::EXPECTED['kas_koperasi']
            || $data['kas']['operasional'] !== self::EXPECTED['kas_operasional']) {
            throw new RuntimeException('Saldo kas koperasi atau operasional tidak sesuai Neraca September 2026.');
        }

        $known = [];
        foreach ($data['anggota'] as $row) {
            $known[$this->normalizeName($row['nama'])] = true;
        }
        $known['faizalahkami'] = true;
        foreach (['simpanan', 'pinjaman', 'potongan'] as $section) {
            foreach ($data[$section] as $row) {
                if (! isset($known[$this->normalizeName($row['nama'])])) {
                    throw new RuntimeException("Nama {$row['nama']} pada {$section} tidak ditemukan di daftar anggota.");
                }
            }
        }
    }

    private function deleteLegacyDomainData(): void
    {
        foreach (['potongan_bulanan_details', 'potongan_bulanan_settings', 'potongan_titipans', 'arus_kas', 'transaksi_pinjaman', 'pinjamans', 'pengajuan_pinjaman', 'simpanans', 'rekening_anggotas', 'closing_bulans', 'anggotas'] as $table) {
            DB::table($table)->delete();
        }
    }

    private function integerValue(Worksheet $sheet, string $coordinate): int
    {
        $value = $sheet->getCell($coordinate)->getCalculatedValue();

        return is_numeric($value) ? (int) round((float) $value) : 0;
    }

    private function accountValue(mixed $value): string
    {
        return is_numeric($value) ? number_format((float) $value, 0, '', '') : trim((string) $value);
    }

    private function normalizeName(string $value): string
    {
        return preg_replace('/[^\pL\pN]+/u', '', mb_strtolower(trim($value))) ?? '';
    }
}
