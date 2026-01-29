<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@koperasi.test',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Anggota Dummy',
            'email' => 'dummy@koperasi.test',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Anggota 2',
            'email' => 'anggota2@koperasi.test',
            'password' => Hash::make('password'),
        ]);
    }
}
