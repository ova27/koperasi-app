<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Anggota;
use App\Services\SimpananService;
use Exception;

class GenerateSimpananWajib extends Command
{
    protected $signature = 'koperasi:generate-simpanan-wajib {bulan?}';
    protected $description = 'Generate simpanan wajib bulanan untuk anggota aktif';

    public function handle(SimpananService $simpananService)
    {
        $this->info('Mulai generate simpanan wajib...');
        
        $bulan = $this->argument('bulan')
            ?? now()->format('Y-m');

        $anggotas = Anggota::where('status', 'aktif')
            ->whereDoesntHave('simpanans', function ($q) use ($bulan) {
                $q->where('jenis_simpanan', 'wajib')
                ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan]);
            })
            ->get();

        foreach ($anggotas as $anggota) {
            try {
                $simpananService->tambah(
                    $anggota->id,
                    'wajib',
                    config('koperasi.simpanan_wajib'),
                    'otomatis',
                    'otomatis',
                    'Simpanan wajib bulanan'
                );

                $this->info(
                    "✔ Simpanan wajib ditambahkan: {$anggota->nama}"
                );

            } catch (Exception $e) {
                // biasanya karena sudah pernah input bulan ini
                $this->warn(
                    "⚠ {$anggota->nama}: {$e->getMessage()}"
                );
            }
        }

        $this->info('Selesai.');
        return Command::SUCCESS;
    }
}
