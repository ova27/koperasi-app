<?php

namespace App\Console\Commands;

use App\Services\LegacySeptemberPositionImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class ImportLegacySeptemberPosition extends Command
{
    protected $signature = 'koperasi:import-posisi-september
        {--folder=storage/app/private/import/september-2026 : Folder empat file sumber}
        {--commit : Ganti data uji coba setelah konfirmasi}';

    protected $description = 'Import aman posisi koperasi per September 2026 dari empat file sumber';

    public function handle(LegacySeptemberPositionImportService $service): int
    {
        try {
            $this->assertSafeEnvironment();
            $folder = base_path((string) $this->option('folder'));
            $data = $service->prepare(
                $folder . DIRECTORY_SEPARATOR . '0. LAPORAN KOPERASI SIMPATIK SEPTEMBER 2026.xlsx',
                $folder . DIRECTORY_SEPARATOR . '1. RINCIAN POTONGAN SEPTEMBER 2026.xlsx',
                $folder . DIRECTORY_SEPARATOR . '2. POTONGAN BRI-BSI SEPTEMBER 2026.xlsx',
                $folder . DIRECTORY_SEPARATOR . 'ARUS KAS & WAITING LIST SIMPATIK 2026.xlsx',
            );

            $this->showPlan($data);
            if (! $this->option('commit')) {
                $this->warn('SIMULASI SAJA. Database belum diubah. Tambahkan --commit setelah memeriksa ringkasan.');

                return self::SUCCESS;
            }

            $this->assertBackupExists();
            if (! $this->confirm('Backup valid ditemukan. Ganti seluruh data uji coba koperasi sekarang?', false)) {
                $this->info('Import dibatalkan. Database tidak diubah.');

                return self::SUCCESS;
            }

            $result = $service->import($data);
            $this->newLine();
            $this->info('IMPORT BERHASIL DAN SUDAH DIVERIFIKASI');
            $this->table(['Hasil', 'Nilai'], [
                ['Anggota aktif', $result['anggota_aktif']],
                ['Anggota tidak aktif', $result['anggota_tidak_aktif']],
                ['Saldo simpanan', $this->rupiah($result['simpanan'])],
                ['Sisa pinjaman', $this->rupiah($result['pinjaman'])],
                ['Potongan September', $this->rupiah($result['potongan'])],
                ['Kas koperasi', $this->rupiah($result['kas_koperasi'])],
                ['Kas operasional', $this->rupiah($result['kas_operasional'])],
                ['Pengajuan aktif', $result['pengajuan_aktif']],
            ]);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function assertSafeEnvironment(): void
    {
        if (! app()->environment('local')) {
            throw new RuntimeException('Import hanya boleh dijalankan saat APP_ENV=local.');
        }
        if ((string) DB::connection()->getDatabaseName() !== 'koperasi') {
            throw new RuntimeException('Import dibatalkan karena database aktif bukan koperasi.');
        }
        if (! Schema::hasColumn('potongan_bulanan_details', 'metode_pembayaran')) {
            throw new RuntimeException('Jalankan php artisan migrate terlebih dahulu.');
        }
    }

    private function assertBackupExists(): void
    {
        $files = glob(database_path('backups/koperasi_before_import_*.sql')) ?: [];
        rsort($files);
        $path = $files[0] ?? null;
        if (! $path || filesize($path) < 1_000) {
            throw new RuntimeException('Backup SQL yang valid tidak ditemukan di database/backups.');
        }
        $handle = fopen($path, 'rb');
        $sample = $handle ? fread($handle, 200_000) : false;
        if (is_resource($handle)) {
            fclose($handle);
        }
        if (! is_string($sample) || ! str_contains($sample, 'CREATE TABLE')) {
            throw new RuntimeException('File backup tidak lolos pemeriksaan CREATE TABLE.');
        }
        $this->info('Backup: ' . basename($path));
    }

    private function showPlan(array $data): void
    {
        $this->newLine();
        $this->info('RENCANA IMPORT POSISI SEPTEMBER 2026');
        $this->table(['Data', 'Jumlah/Total'], [
            ['Anggota aktif', count($data['anggota'])],
            ['Anggota tidak aktif dengan pinjaman', count($data['anggota_tidak_aktif'])],
            ['Saldo simpanan', $this->rupiah(array_sum(array_column($data['simpanan'], 'total')))],
            ['Pinjaman aktif', count($data['pinjaman'])],
            ['Sisa pinjaman', $this->rupiah(array_sum(array_column($data['pinjaman'], 'sisa_pinjaman')))],
            ['Potongan September', $this->rupiah(array_sum(array_column($data['potongan'], 'total')))],
            ['Kas koperasi', $this->rupiah($data['kas']['koperasi'])],
            ['Kas operasional', $this->rupiah($data['kas']['operasional'])],
            ['Pengajuan waiting list aktif', count($data['pengajuan'])],
        ]);
        $this->line('Akun login, role, permission, dan rekening koperasi dipertahankan.');
    }

    private function rupiah(int $value): string
    {
        return 'Rp' . number_format($value, 0, ',', '.');
    }
}
