<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggota;
use App\Models\User;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'dummy@koperasi.test')->first();
        $user2 = User::where('email', 'anggota2@koperasi.test')->first();

        Anggota::create([
            'user_id' => $user1->id,
            'nomor_anggota' => 'AG-0001',
            'nip' => '1980000001',
            'nama' => 'Anggota Dummy',
            'email' => 'dummy@koperasi.test',
            'status' => 'aktif',
            'tanggal_masuk' => now()->subYear(),
        ]);

        Anggota::create([
            'user_id' => $user2->id,
            'nomor_anggota' => 'AG-0002',
            'nip' => '1980000002',
            'nama' => 'Anggota 2',
            'email' => 'anggota2@koperasi.test',
            'status' => 'aktif',
            'tanggal_masuk' => now()->subMonths(6),
        ]);
    }
}

