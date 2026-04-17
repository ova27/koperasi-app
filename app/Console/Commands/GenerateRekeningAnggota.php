<?php

namespace App\Console\Commands;

use App\Models\Anggota;
use App\Models\RekeningAnggota;
use Illuminate\Console\Command;

class GenerateRekeningAnggota extends Command
{
    protected $signature = 'koperasi:generate-rekening-anggota
                            {--bank=BANK BPS : Nama bank default}
                            {--prefix=8801 : Prefix nomor rekening default}
                            {--dry-run : Simulasi tanpa menyimpan data}';

    protected $description = 'Membuat rekening anggota default untuk semua anggota yang belum punya rekening';

    public function handle(): int
    {
        $bank = (string) $this->option('bank');
        $prefix = preg_replace('/\D/', '', (string) $this->option('prefix')) ?: '8801';
        $dryRun = (bool) $this->option('dry-run');

        $anggotas = Anggota::query()->with('rekening')->get();

        if ($anggotas->isEmpty()) {
            $this->warn('Tidak ada data anggota.');
            return self::SUCCESS;
        }

        $created = 0;
        $skipped = 0;

        foreach ($anggotas as $anggota) {
            if ($anggota->rekening->isNotEmpty()) {
                $skipped++;
                continue;
            }

            $nomorRekening = $this->generateNomorRekening($prefix, (int) $anggota->id);

            if ($dryRun) {
                $this->line("[DRY-RUN] {$anggota->nama} => {$nomorRekening}");
                $created++;
                continue;
            }

            RekeningAnggota::create([
                'anggota_id' => $anggota->id,
                'nama_bank' => $bank,
                'nomor_rekening' => $nomorRekening,
                'nama_pemilik' => $anggota->nama,
                'aktif' => true,
            ]);

            $created++;
        }

        $this->info('Generate rekening anggota selesai.');
        $this->info("Dibuat   : {$created}");
        $this->info("Dilewati : {$skipped}");

        return self::SUCCESS;
    }

    private function generateNomorRekening(string $prefix, int $anggotaId): string
    {
        $base = $prefix . str_pad((string) $anggotaId, 8, '0', STR_PAD_LEFT);
        $candidate = $base;
        $suffix = 1;

        while (RekeningAnggota::query()->where('nomor_rekening', $candidate)->exists()) {
            $candidate = $base . str_pad((string) $suffix, 2, '0', STR_PAD_LEFT);
            $suffix++;
        }

        return $candidate;
    }
}
