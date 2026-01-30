<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        foreach (['admin','ketua','bendahara','anggota'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
