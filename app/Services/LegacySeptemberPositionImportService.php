<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\ArusKas;
use App\Models\Pinjaman;
use App\Models\PengajuanPinjaman;
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

    private const DEFAULT_PENDING_TENOR = 20;

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
        $undurDiriSheet = $master->getSheetByName('Undur Diri');
        $rincianSheet = $rincian->getSheetByName('September');
        $briSheet = $bank->getSheetByName('BRI');
        $bsiSheet = $bank->getSheetByName('BSI');
        $cashSheet = $cash->getSheetByName('2026');
        $waitingListSheet = $cash->getSheetByName('WAITING LIST');

        if (! $anggotaSheet || ! $simpananSheet || ! $pinjamanSheet || ! $neracaSheet || ! $operasionalSheet || ! $undurDiriSheet || ! $rincianSheet || ! $briSheet || ! $bsiSheet || ! $cashSheet || ! $waitingListSheet) {
            throw new RuntimeException('Ada sheet sumber wajib yang tidak ditemukan.');
        }

        $anggota = $this->readAnggota($anggotaSheet);
        $potongan = $this->readPotongan($rincianSheet, $briSheet, $bsiSheet);
        $data = [
            'anggota' => $anggota,
            'anggota_tidak_aktif' => $this->readInactiveMembers($anggota, $potongan, $undurDiriSheet),
            'simpanan' => $this->readSimpanan($simpananSheet),
            'pinjaman' => $this->readPinjaman($pinjamanSheet, $rincianSheet, $cashSheet),
            'pengajuan' => $this->readPendingApplications($waitingListSheet, $anggota),
            'potongan' => $potongan,
            'kas' => [
                'koperasi' => $this->integerValue($neracaSheet, 'F7'),
                'operasional' => $this->integerValue($operasionalSheet, 'C18'),
            ],
            'controls' => [
                'simpanan' => $this->integerValue($neracaSheet, 'C5') + $this->integerValue($neracaSheet, 'C6'),
                'pinjaman' => $this->integerValue($neracaSheet, 'F5'),
            ],
        ];

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

            foreach ($data['anggota_tidak_aktif'] as $index => $row) {
                $anggota = Anggota::create([
                    'user_id' => null,
                    'nomor_anggota' => sprintf('LEGACY-%04d', $index + 1),
                    'nip' => null,
                    'nama' => $row['nama'],
                    'jenis_kelamin' => null,
                    'jabatan' => null,
                    'status' => 'tidak_aktif',
                    'tanggal_masuk' => $row['tanggal_masuk'],
                    'tanggal_keluar' => $row['tanggal_keluar'],
                ]);
                $anggotaIds[$this->normalizeName($row['nama'])] = $anggota->id;
            }

            foreach ($data['potongan'] as $row) {
                $key = $this->normalizeName($row['nama']);
                if ($row['bank']) {
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

            foreach ($data['pengajuan'] as $row) {
                $anggotaId = $anggotaIds[$row['anggota_key']];
                $anggota = Anggota::findOrFail($anggotaId);
                PengajuanPinjaman::create([
                    'anggota_id' => $anggotaId,
                    'jumlah_diajukan' => $row['jumlah_diajukan'],
                    'tenor' => $row['tenor'],
                    'bulan_pinjam' => $row['bulan_pinjam'],
                    'tujuan' => 'Migrasi antrian waiting list manual',
                    'status' => 'diajukan',
                    'diajukan_oleh' => $anggota->user_id ?: $creator->id,
                    'disetujui_oleh' => null,
                    'dicairkan_oleh' => null,
                    'tanggal_pengajuan' => $row['tanggal_pengajuan'],
                    'tanggal_persetujuan' => null,
                    'tanggal_pencairan' => null,
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
                'pengajuan_aktif' => PengajuanPinjaman::where('status', 'diajukan')->count(),
            ];

            if ($result['anggota_aktif'] !== count($data['anggota'])
                || $result['anggota_tidak_aktif'] !== count($data['anggota_tidak_aktif'])
                || $result['simpanan'] !== array_sum(array_column($data['simpanan'], 'total'))
                || $result['pinjaman'] !== array_sum(array_column($data['pinjaman'], 'sisa_pinjaman'))
                || $result['potongan'] !== array_sum(array_column($data['potongan'], 'total'))
                || $result['kas_koperasi'] !== $data['kas']['koperasi']
                || $result['kas_operasional'] !== $data['kas']['operasional']
                || $result['pengajuan_aktif'] !== count($data['pengajuan'])) {
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
            // Mengikuti komponen formula simpanan sukarela pada Neraca sumber.
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

    private function readPinjaman(Worksheet $sheet, Worksheet $rincianSheet, Worksheet $cashSheet): array
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

        $cashAdjustments = $this->readSeptemberLoanAdjustments($cashSheet);
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

            $detail = $details[$key] ?? null;
            $detailBalance = $detail ? max(0, $this->findDetailBalance($rincianSheet, $key)) : 0;
            if ($sisa !== $detailBalance) {
                $special = $this->matchCashAdjustment($cashAdjustments, $key);
                if (! $special || $special['tenor'] <= 0 || $sisa % $special['tenor'] !== 0) {
                    throw new RuntimeException("Jadwal pinjaman baru/top-up September tidak lengkap untuk {$nama}.");
                }
                $rows[] = [
                    'nama' => $nama,
                    'tanggal_pinjam' => $special['tanggal'],
                    'jumlah_pinjaman' => $sisa,
                    'sisa_pinjaman' => $sisa,
                    'tenor' => $special['tenor'],
                    'cicilan_per_bulan' => (int) ($sisa / $special['tenor']),
                    'keterangan' => 'Pinjaman baru/top-up/restruktur September 2026; cicilan baru mulai Oktober 2026',
                ];
                continue;
            }

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

    private function readSeptemberLoanAdjustments(Worksheet $sheet): array
    {
        $rows = [];
        for ($row = 1; $row <= $sheet->getHighestDataRow(); $row++) {
            $date = $sheet->getCell("B{$row}")->getCalculatedValue();
            $description = trim((string) $sheet->getCell("C{$row}")->getValue());
            $dateValue = $this->dateValue($date);
            if (! $dateValue || ! str_starts_with($dateValue, '2026-09-') || ! str_contains(mb_strtolower($description), 'pinjaman')) {
                continue;
            }
            $key = $this->normalizeName(preg_replace('/^.*?\ban\.?\s*/iu', '', $description) ?? $description);
            $note = trim((string) $sheet->getCell("F{$row}")->getValue());
            if (! preg_match('/(\d+)\s*x/iu', $note, $match)
                && ! preg_match('/(\d+)\s*bulan/iu', $note, $match)) {
                continue;
            }
            $rows[] = [
                'name_key' => $key,
                'tanggal' => $dateValue,
                'pencairan' => $this->integerValue($sheet, "E{$row}"),
                'tenor' => (int) $match[1],
            ];
        }

        return $rows;
    }

    private function validatePreparedData(array $data): void
    {
        if (array_sum(array_column($data['simpanan'], 'total')) !== $data['controls']['simpanan']) {
            throw new RuntimeException('Rincian simpanan tidak sama dengan kontrol Neraca.');
        }
        if (array_sum(array_column($data['pinjaman'], 'sisa_pinjaman')) !== $data['controls']['pinjaman']) {
            throw new RuntimeException('Rincian pinjaman tidak sama dengan kontrol Neraca.');
        }
        if ($data['kas']['koperasi'] !== $data['controls']['simpanan'] - $data['controls']['pinjaman']) {
            throw new RuntimeException('Saldo kas koperasi tidak sama dengan simpanan dikurangi piutang anggota.');
        }

        $known = [];
        foreach ($data['anggota'] as $row) {
            $known[$this->normalizeName($row['nama'])] = true;
        }
        foreach ($data['anggota_tidak_aktif'] as $row) {
            $known[$this->normalizeName($row['nama'])] = true;
        }
        foreach (['simpanan', 'pinjaman', 'potongan'] as $section) {
            foreach ($data[$section] as $row) {
                if (! isset($known[$this->normalizeName($row['nama'])])) {
                    throw new RuntimeException("Nama {$row['nama']} pada {$section} tidak ditemukan di daftar anggota.");
                }
            }
        }
    }

    private function readPendingApplications(Worksheet $sheet, array $activeMembers): array
    {
        $memberKeys = [];
        foreach ($activeMembers as $member) {
            $memberKeys[$this->normalizeName($member['nama'])] = $member['nama'];
        }

        $rows = [];
        for ($row = 3; $row <= $sheet->getHighestDataRow(); $row++) {
            if (strtoupper(trim((string) $sheet->getCell("F{$row}")->getValue())) !== 'BELUM') {
                continue;
            }
            $sourceName = trim((string) $sheet->getCell("C{$row}")->getValue());
            $anggotaKey = $this->resolveMemberKey($sourceName, array_keys($memberKeys));
            if (! $anggotaKey) {
                throw new RuntimeException("Nama waiting list tidak cocok dengan anggota aktif: {$sourceName}.");
            }
            $jumlah = $this->integerValue($sheet, "D{$row}");
            $note = trim((string) $sheet->getCell("G{$row}")->getValue());
            preg_match('/(\d+)\s*x/iu', $note, $tenorMatch);
            $tenor = isset($tenorMatch[1]) ? (int) $tenorMatch[1] : self::DEFAULT_PENDING_TENOR;
            $tanggal = $this->dateValue($sheet->getCell("B{$row}")->getValue());
            $bulan = $this->monthValue($sheet->getCell("E{$row}")->getValue());
            if ($jumlah <= 0 || ! $tanggal || ! $bulan || $tenor <= 0) {
                throw new RuntimeException("Data waiting list pada baris {$row} belum lengkap.");
            }
            $dedupeKey = implode('|', [$anggotaKey, $jumlah, $bulan]);
            $rows[$dedupeKey] = [
                'anggota_key' => $anggotaKey,
                'jumlah_diajukan' => $jumlah,
                'tenor' => $tenor,
                'bulan_pinjam' => $bulan,
                'tanggal_pengajuan' => $tanggal,
            ];
        }

        return array_values($rows);
    }

    private function readInactiveMembers(array $activeMembers, array $potongan, Worksheet $undurDiriSheet): array
    {
        $active = [];
        foreach ($activeMembers as $row) {
            $active[$this->normalizeName($row['nama'])] = true;
        }
        $exitDates = [];
        for ($row = 6; $row <= $undurDiriSheet->getHighestDataRow(); $row++) {
            $name = trim((string) $undurDiriSheet->getCell("C{$row}")->getValue());
            if ($name !== '') {
                $exitDates[$this->normalizeName($name)] = $this->dateValue($undurDiriSheet->getCell("B{$row}")->getValue());
            }
        }

        $result = [];
        foreach ($potongan as $row) {
            $key = $this->normalizeName($row['nama']);
            if (isset($active[$key])) {
                continue;
            }
            $result[$key] = [
                'nama' => $row['nama'],
                'tanggal_masuk' => '2011-01-01',
                'tanggal_keluar' => $this->matchDateByName($exitDates, $key) ?? self::SNAPSHOT_DATE,
            ];
        }

        return array_values($result);
    }

    private function findDetailBalance(Worksheet $sheet, string $nameKey): int
    {
        for ($row = 7; $row <= $sheet->getHighestDataRow(); $row++) {
            if ($this->normalizeName((string) $sheet->getCell("B{$row}")->getValue()) === $nameKey) {
                return $this->integerValue($sheet, "H{$row}");
            }
        }

        return 0;
    }

    private function matchCashAdjustment(array $rows, string $nameKey): ?array
    {
        foreach ($rows as $row) {
            if (str_contains($row['name_key'], $nameKey) || str_contains($nameKey, $row['name_key'])) {
                return $row;
            }
        }

        return null;
    }

    private function matchDateByName(array $dates, string $nameKey): ?string
    {
        foreach ($dates as $key => $date) {
            if (str_contains($key, $nameKey) || str_contains($nameKey, $key)) {
                return $date;
            }
        }

        return null;
    }

    private function dateValue(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->toDateString();
        }
        if (is_numeric($value)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value))->toDateString();
        }
        $text = mb_strtolower(trim((string) $value));
        foreach ($this->indonesianMonths() as $name => $month) {
            if (preg_match('/\b(\d{1,2})\s+' . $name . '\s+(\d{4})\b/u', $text, $match)) {
                return Carbon::create((int) $match[2], $month, (int) $match[1])->toDateString();
            }
        }
        try {
            return $text !== '' ? Carbon::parse((string) $value)->toDateString() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function monthValue(mixed $value): ?string
    {
        $date = $this->dateValue($value);
        if ($date) {
            return Carbon::parse($date)->startOfMonth()->toDateString();
        }
        $text = mb_strtolower(trim((string) $value));
        foreach ($this->indonesianMonths() as $name => $month) {
            if (preg_match('/\b' . $name . '\s+(\d{4})\b/u', $text, $match)) {
                return Carbon::create((int) $match[1], $month, 1)->toDateString();
            }
        }

        return null;
    }

    private function indonesianMonths(): array
    {
        return [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];
    }

    private function resolveMemberKey(string $name, array $memberKeys): ?string
    {
        $source = $this->normalizeName($name);
        if (in_array($source, $memberKeys, true)) {
            return $source;
        }
        $best = null;
        $distance = PHP_INT_MAX;
        foreach ($memberKeys as $key) {
            $current = levenshtein($source, $key);
            if ($current < $distance) {
                $distance = $current;
                $best = $key;
            }
        }

        return $distance <= 2 ? $best : null;
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
