<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RekeningKoperasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rekening_koperasis')->insert([
            [
                'nama' => 'Kas Tunai',
                'jenis' => 'kas',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Bank BRI',
                'jenis' => 'bank',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
