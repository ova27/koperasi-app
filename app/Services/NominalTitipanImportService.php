<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\PotonganTitipan;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class NominalTitipanImportService
{
    /**
     * Parse Excel file and return validation result with preview
     */
    public function parseExcel(UploadedFile $file): array
    {
        $filePath = $file->getRealPath();

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            unset($rows[0]); // Remove header row
            $rows = array_values($rows); // Reindex

            $data = [];
            $errors = [];
            $anggotaMap = Anggota::where('status', 'aktif')
                ->pluck('id', 'nama')
                ->toArray();


            foreach ($rows as $index => $row) {
                $lineNum = $index + 2; // +1 for 0-based, +1 for header

                if (empty(array_filter($row))) {
                    continue; // Skip empty rows
                }

                $nama = trim($row[0] ?? '');
                $cicilan = $row[1] ?? 0;
                $operasional = $row[2] ?? 0;
                $dharma = $row[3] ?? 0;
                $infaq = $row[4] ?? 0;
                $qurban = $row[5] ?? 0;

                if (empty($nama)) {
                    $errors[] = "Baris $lineNum: Nama anggota kosong";
                    continue;
                }

                if (!isset($anggotaMap[$nama])) {
                    $errors[] = "Baris $lineNum: Anggota '$nama' tidak ditemukan atau tidak aktif";
                    continue;
                }

                if (!is_numeric($cicilan) || !is_numeric($operasional) || !is_numeric($dharma) || !is_numeric($infaq) || !is_numeric($qurban)) {
                    $errors[] = "Baris $lineNum: Nominal harus angka (Cicilan: $cicilan, Operasional: $operasional, Dharma: $dharma, Infaq: $infaq, Qurban: $qurban)";
                    continue;
                }

                $cicilan = (int) $cicilan;
                $operasional = (int) $operasional;
                $dharma = (int) $dharma;
                $infaq = (int) $infaq;
                $qurban = (int) $qurban;

                if ($cicilan < 0 || $operasional < 0 || $dharma < 0 || $infaq < 0 || $qurban < 0) {
                    $errors[] = "Baris $lineNum: Nominal tidak boleh negatif";
                    continue;
                }

                $data[] = [
                    'nama' => $nama,
                    'anggota_id' => $anggotaMap[$nama],
                    'cicilan' => $cicilan,
                    'iuran_operasional' => $operasional,
                    'iuran_dharma_wanita' => $dharma,
                    'infaq_pegawai' => $infaq,
                    'tabungan_qurban' => $qurban,
                ];
            }

            @unlink($filePath);

            return [
                'success' => count($errors) === 0,
                'data' => $data,
                'errors' => $errors,
                'total_rows' => count($data),
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Batch save nominal titipan
     */
    public function batchSave(array $data): int
    {
        $count = 0;
        foreach ($data as $item) {
            PotonganTitipan::updateOrCreate(
                ['anggota_id' => $item['anggota_id']],
                [
                    'iuran_dharma_wanita' => $item['iuran_dharma_wanita'],
                    'infaq_pegawai' => $item['infaq_pegawai'],
                    'tabungan_qurban' => $item['tabungan_qurban'],
                ]
            );

            // Update PotonganBulananDetail jika ada untuk bulan berjalan
            $bulanPotongan = now()->addMonthNoOverflow()->format('Y-m');
            \App\Models\PotonganBulananDetail::where('bulan_potongan', $bulanPotongan)
                ->where('anggota_id', $item['anggota_id'])
                ->update([
                    'cicilan' => $item['cicilan'] ?? 0,
                    'iuran_operasional' => $item['iuran_operasional'] ?? 0,
                    'iuran_dharma_wanita' => $item['iuran_dharma_wanita'] ?? 0,
                    'infaq_pegawai' => $item['infaq_pegawai'] ?? 0,
                    'tabungan_qurban' => $item['tabungan_qurban'] ?? 0,
                ]);

            $count++;
        }
        return $count;
    }
}
