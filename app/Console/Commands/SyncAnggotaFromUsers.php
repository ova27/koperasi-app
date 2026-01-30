<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Anggota;
use Illuminate\Support\Str;

class SyncAnggotaFromUsers extends Command
{
    protected $signature = 'koperasi:sync-anggota';
    protected $description = 'Sinkronisasi data anggota dari user berdasarkan email';

    public function handle()
    {
        $users = User::role('anggota')->get();

        if ($users->isEmpty()) {
            $this->warn('Tidak ada user dengan role anggota');
            return Command::SUCCESS;
        }

        $created = 0;
        $skipped = 0;

        foreach ($users as $user) {

            // Cek sudah ada anggota?
            if (Anggota::where('user_id', $user->id)->exists()) {
                $skipped++;
                continue;
            }

            Anggota::create([
                'user_id'        => $user->id,
                'nama'           => $user->name,
                'email'          => $user->email,
                'nomor_anggota'  => 'AG-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
                'status'         => 'aktif',
                'tanggal_masuk'  => now(),
            ]);

            $created++;
        }

        $this->info("Sinkronisasi selesai");
        $this->info("✔ Dibuat : {$created}");
        $this->info("➖ Dilewati : {$skipped}");

        return Command::SUCCESS;
    }
}
