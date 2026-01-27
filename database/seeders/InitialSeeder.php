<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Anggota;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class InitialSeeder extends Seeder
{
    public function run(): void
    {
        // ROLES
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'ketua']);
        Role::firstOrCreate(['name' => 'bendahara']);
        Role::firstOrCreate(['name' => 'anggota']);

        // USER ADMIN
        $user = User::firstOrCreate(
            ['email' => 'admin@koperasi.test'],
            [
                'name' => 'Admin Koperasi',
                'password' => Hash::make('password'),
            ]
        );

        $user->assignRole($adminRole);

        // ANGGOTA DUMMY
        Anggota::firstOrCreate(
            ['email' => 'dummy@koperasi.test'],
            [
                'nomor_anggota' => 'AG-0001',
                'nip' => '000000000',
                'nama' => 'Anggota Dummy',
                'status' => 'aktif',
                'tanggal_masuk' => now(),
            ]
        );
    }
}
