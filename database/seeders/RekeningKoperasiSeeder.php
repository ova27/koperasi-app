<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RekeningKoperasi;

class RekeningKoperasiSeeder extends Seeder
{
    public function run(): void
    {
        RekeningKoperasi::firstOrCreate(
            [
                'nama' => 'Bank BRI',
            ],
            [
                'jenis' => 'bank',
                'aktif' => true,
            ]
        );
    }
}
