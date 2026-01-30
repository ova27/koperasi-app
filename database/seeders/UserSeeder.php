<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Anggota;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'role' => 'admin',
                'email' => 'admin@koperasi.test',
                'nama' => 'Admin',
            ],
            [
                'role' => 'ketua',
                'email' => 'ketua@koperasi.test',
                'nama' => 'Ketua Koperasi',
            ],
            [
                'role' => 'bendahara',
                'email' => 'bendahara@koperasi.test',
                'nama' => 'Bendahara',
            ],
            [
                'role' => 'anggota',
                'email' => 'anggota1@koperasi.test',
                'nama' => 'Anggota Satu',
            ],
        ];

        foreach ($data as $row) {
            $user = User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['nama'],
                    'password' => Hash::make('password'),
                ]
            );

            $user->assignRole($row['role']);

            // hanya anggota yang masuk tabel anggotas
            if ($row['role'] === 'anggota') {
                Anggota::firstOrCreate([
                    'user_id' => $user->id,
                ], [
                    'nomor_anggota' => 'AG-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'nama' => $row['nama'],
                    'status' => 'aktif',
                    'tanggal_masuk' => now(),
                ]);
            }
        }
    }
}
