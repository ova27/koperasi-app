<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

class LegacySeptemberPreviewService
{
    public function preview(string $masterPath, string $rincianPath, string $bankPath): array
    {
        foreach ([$masterPath, $rincianPath, $bankPath] as $path) {
            if (! is_file($path)) {
                throw new RuntimeException("File tidak ditemukan: {$path}");
            }
        }

        $master = IOFactory::load($masterPath);
        $rincian = IOFactory::load($rincianPath);
        $bank = IOFactory::load($bankPath);

        $anggotaSheet = $master->getSheetByName('Anggota');
        $rincianSheet = $rincian->getSheetByName('September');
        $briSheet = $bank->getSheetByName('BRI');
        $bsiSheet = $bank->getSheetByName('BSI');

        if (! $anggotaSheet || ! $rincianSheet || ! $briSheet || ! $bsiSheet) {
            throw new RuntimeException('Sheet wajib tidak ditemukan. Dibutuhkan: Anggota, September, BRI, dan BSI.');
        }

        $anggota = $this->readAnggota($anggotaSheet);
        $rincianRows = $this->readRincian($rincianSheet);
        $bankRows = array_merge(
            $this->readBank($briSheet, 'BRI'),
            $this->readBank($bsiSheet, 'BSI')
        );

        $anggotaMap = $this->keyByName($anggota);
        $rincianMap = $this->keyByName($rincianRows);
        $bankMap = $this->keyByName($bankRows);

        $manualKeys = array_diff(array_keys($rincianMap), array_keys($bankMap));
        $manualRows = array_values(array_intersect_key($rincianMap, array_flip($manualKeys)));

        $totalRincian = array_sum(array_column($rincianRows, 'total'));
        $totalBri = array_sum(array_column(array_filter($bankRows, fn ($row) => $row['bank'] === 'BRI'), 'total'));
        $totalBsi = array_sum(array_column(array_filter($bankRows, fn ($row) => $row['bank'] === 'BSI'), 'total'));
        $totalManual = array_sum(array_column($manualRows, 'total'));

        $totalMismatches = [];
        $accountMismatches = [];
        foreach (array_intersect(array_keys($rincianMap), array_keys($bankMap)) as $key) {
            if ($rincianMap[$key]['total'] !== $bankMap[$key]['total']) {
                $totalMismatches[] = [
                    'nama' => $rincianMap[$key]['nama'],
                    'rincian' => $rincianMap[$key]['total'],
                    'bank' => $bankMap[$key]['total'],
                ];
            }

            if ($this->normalizeAccount($rincianMap[$key]['rekening']) !== $this->normalizeAccount($bankMap[$key]['rekening'])) {
                $accountMismatches[] = [
                    'nama' => $rincianMap[$key]['nama'],
                    'rincian' => $rincianMap[$key]['rekening'],
                    'bank' => $bankMap[$key]['rekening'],
                ];
            }
        }

        return [
            'counts' => [
                'anggota_aktif' => count($anggota),
                'rincian' => count($rincianRows),
                'bri' => count(array_filter($bankRows, fn ($row) => $row['bank'] === 'BRI')),
                'bsi' => count(array_filter($bankRows, fn ($row) => $row['bank'] === 'BSI')),
                'transfer_manual' => count($manualRows),
            ],
            'totals' => [
                'rincian' => $totalRincian,
                'bri' => $totalBri,
                'bsi' => $totalBsi,
                'transfer_manual' => $totalManual,
                'rekonsiliasi' => $totalBri + $totalBsi + $totalManual,
            ],
            'manual_rows' => $manualRows,
            'anggota_tanpa_rincian' => $this->namesForKeys($anggotaMap, array_diff(array_keys($anggotaMap), array_keys($rincianMap))),
            'rincian_bukan_anggota_aktif' => $this->namesForKeys($rincianMap, array_diff(array_keys($rincianMap), array_keys($anggotaMap))),
            'bank_tanpa_rincian' => $this->namesForKeys($bankMap, array_diff(array_keys($bankMap), array_keys($rincianMap))),
            'total_mismatches' => $totalMismatches,
            'account_mismatches' => $accountMismatches,
            'is_reconciled' => $totalRincian === ($totalBri + $totalBsi + $totalManual)
                && $totalMismatches === []
                && $accountMismatches === [],
        ];
    }

    private function readAnggota(Worksheet $sheet): array
    {
        $rows = [];
        for ($row = 6; $row <= $sheet->getHighestDataRow(); $row++) {
            if (! is_numeric($sheet->getCell("A{$row}")->getValue())) {
                continue;
            }

            $rows[] = [
                'nama' => trim((string) $sheet->getCell("B{$row}")->getValue()),
                'jenis_kelamin' => trim((string) $sheet->getCell("C{$row}")->getValue()),
                'nip' => trim((string) $sheet->getCell("D{$row}")->getFormattedValue()),
                'email' => trim((string) $sheet->getCell("E{$row}")->getValue()),
            ];
        }

        return $rows;
    }

    private function readRincian(Worksheet $sheet): array
    {
        $rows = [];
        for ($row = 7; $row <= $sheet->getHighestDataRow(); $row++) {
            if (! is_numeric($sheet->getCell("A{$row}")->getValue())) {
                continue;
            }

            $rows[] = [
                'nama' => trim((string) $sheet->getCell("B{$row}")->getValue()),
                'rekening' => $this->accountValue($sheet->getCell("C{$row}")->getValue()),
                'sisa_pinjaman_lalu' => $this->integerValue($sheet, "D{$row}"),
                'tenor' => $this->integerValue($sheet, "E{$row}"),
                'cicilan_ke' => $this->integerValue($sheet, "F{$row}"),
                'cicilan' => $this->integerValue($sheet, "G{$row}"),
                'sisa_pinjaman_sekarang' => $this->integerValue($sheet, "H{$row}"),
                'simpanan_dan_iuran' => $this->integerValue($sheet, "I{$row}"),
                'iuran_dharma_wanita' => $this->integerValue($sheet, "J{$row}"),
                'iuran_pegawai' => $this->integerValue($sheet, "K{$row}"),
                'total' => $this->integerValue($sheet, "L{$row}"),
            ];
        }

        return $rows;
    }

    private function readBank(Worksheet $sheet, string $bank): array
    {
        $rows = [];
        for ($row = 1; $row <= $sheet->getHighestDataRow(); $row++) {
            if (! is_numeric($sheet->getCell("A{$row}")->getValue())) {
                continue;
            }

            $rows[] = [
                'nama' => trim((string) $sheet->getCell("B{$row}")->getValue()),
                'bank' => $bank,
                'rekening' => $this->accountValue($sheet->getCell("D{$row}")->getValue()),
                'total' => $this->integerValue($sheet, "E{$row}"),
            ];
        }

        return $rows;
    }

    private function integerValue(Worksheet $sheet, string $coordinate): int
    {
        $value = $sheet->getCell($coordinate)->getCalculatedValue();

        return is_numeric($value) ? (int) round((float) $value) : 0;
    }

    private function accountValue(mixed $value): string
    {
        if (is_float($value) || is_int($value)) {
            return number_format((float) $value, 0, '', '');
        }

        return trim((string) $value);
    }

    private function normalizeName(string $value): string
    {
        return preg_replace('/[^\pL\pN]+/u', '', mb_strtolower(trim($value))) ?? '';
    }

    private function normalizeAccount(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    private function keyByName(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[$this->normalizeName($row['nama'])] = $row;
        }

        return $result;
    }

    private function namesForKeys(array $map, array $keys): array
    {
        return array_values(array_map(fn ($key) => $map[$key]['nama'], $keys));
    }
}
