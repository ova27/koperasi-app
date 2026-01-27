<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggota;
use Illuminate\Support\Str;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {

            $nomor = 'AG-' . str_pad($i, 4, '0', STR_PAD_LEFT);

            Anggota::firstOrCreate(
                ['nomor_anggota' => $nomor],
                [
                    'nip'           => '1980' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'nama'          => 'Anggota ' . $i,
                    'email'         => "anggota{$i}@koperasi.test",
                    'status'        => 'aktif',
                    'tanggal_masuk' => now()->subMonths(rand(1, 24)),
                ]
            );
        }

        // contoh anggota tidak aktif
        Anggota::firstOrCreate(
            ['nomor_anggota' => 'AG-9999'],
            [
                'nip'            => '999999999',
                'nama'           => 'Anggota Pensiun',
                'email'          => 'pensiun@koperasi.test',
                'status'         => 'tidak_aktif',
                'tanggal_masuk'  => now()->subYears(10),
                'tanggal_keluar' => now()->subMonths(3),
            ]
        );

    }
}
