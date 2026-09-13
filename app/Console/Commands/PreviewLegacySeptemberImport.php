<?php

namespace App\Console\Commands;

use App\Services\LegacySeptemberPreviewService;
use Illuminate\Console\Command;
use Throwable;

class PreviewLegacySeptemberImport extends Command
{
    protected $signature = 'koperasi:preview-import-september
        {--folder=storage/app/private/import/september-2026 : Folder yang berisi tiga file Excel September 2026}';

    protected $description = 'Memvalidasi dan merekonsiliasi sumber import September 2026 tanpa menulis database';

    public function handle(LegacySeptemberPreviewService $service): int
    {
        $folder = base_path((string) $this->option('folder'));

        try {
            $result = $service->preview(
                $folder . DIRECTORY_SEPARATOR . '0. LAPORAN KOPERASI SIMPATIK SEPTEMBER 2026.xlsx',
                $folder . DIRECTORY_SEPARATOR . '1. RINCIAN POTONGAN SEPTEMBER 2026.xlsx',
                $folder . DIRECTORY_SEPARATOR . '2. POTONGAN BRI-BSI SEPTEMBER 2026.xlsx',
            );
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('PREVIEW IMPORT SEPTEMBER 2026 — DATABASE TIDAK DIUBAH');
        $this->table(['Data', 'Jumlah'], [
            ['Anggota aktif', $result['counts']['anggota_aktif']],
            ['Rincian potongan', $result['counts']['rincian']],
            ['Pendebetan BRI', $result['counts']['bri']],
            ['Pendebetan BSI', $result['counts']['bsi']],
            ['Transfer manual', $result['counts']['transfer_manual']],
        ]);

        $this->table(['Nilai', 'Total'], [
            ['Rincian potongan', $this->rupiah($result['totals']['rincian'])],
            ['Pendebetan BRI', $this->rupiah($result['totals']['bri'])],
            ['Pendebetan BSI', $this->rupiah($result['totals']['bsi'])],
            ['Transfer manual', $this->rupiah($result['totals']['transfer_manual'])],
            ['Rekonsiliasi', $this->rupiah($result['totals']['rekonsiliasi'])],
        ]);

        if ($result['manual_rows'] !== []) {
            $this->warn('Tagihan transfer manual:');
            $this->table(
                ['Nama', 'Cicilan', 'Iuran/Simpanan', 'Total'],
                array_map(fn ($row) => [
                    $row['nama'],
                    $this->rupiah($row['cicilan']),
                    $this->rupiah($row['simpanan_dan_iuran']),
                    $this->rupiah($row['total']),
                ], $result['manual_rows'])
            );
        }

        $this->showNames('Anggota aktif tanpa rincian', $result['anggota_tanpa_rincian']);
        $this->showNames('Rincian bukan anggota aktif', $result['rincian_bukan_anggota_aktif']);
        $this->showNames('Data bank tanpa rincian', $result['bank_tanpa_rincian']);

        if ($result['total_mismatches'] !== []) {
            $this->error('Ada total anggota yang berbeda antara rincian dan bank.');
        }
        if ($result['account_mismatches'] !== []) {
            $this->error('Ada nomor rekening yang berbeda antara rincian dan bank.');
        }

        if (! $result['is_reconciled']) {
            $this->error('HASIL BELUM REKONSILIASI. Import tidak boleh dilanjutkan.');

            return self::FAILURE;
        }

        $this->info('REKONSILIASI SESUAI. Aman melanjutkan ke penyusunan import database.');

        return self::SUCCESS;
    }

    private function showNames(string $label, array $names): void
    {
        $message = $names === [] ? '-' : implode(', ', $names);
        $this->line("{$label}: {$message}");
    }

    private function rupiah(int $value): string
    {
        return 'Rp' . number_format($value, 0, ',', '.');
    }
}
