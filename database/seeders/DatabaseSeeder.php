<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS (SESUAI EXCEL)
        |--------------------------------------------------------------------------
        */
        $permissions = [

            // UMUM
            'view dashboard',
            'edit profil',

            // DATA
            'view anggota list',
            'create anggota',
            'edit anggota',
            'nonaktifkan anggota',

            // SIMPANAN (PRIBADI)
            'view simpanan saya',

            // PINJAMAN (PRIBADI)
            'view pinjaman saya',
            'create pinjaman',
            'edit pinjaman',
            'delete pinjaman',

            // KEUANGAN (MONITORING)
            'view saldo',
            'view arus koperasi',
            'view arus operasional',
            'view laporan arus kas',
            'export laporan arus kas',

            // TRANSAKSI (OPERASIONAL)
            'manage simpanan anggota',
            'pencairan pinjaman',
            'manage cicilan pinjaman',

            // APPROVAL (KHUSUS KETUA)
            'view pengajuan pinjaman',
            'approve pinjaman',
            'reject pinjaman',
            'cancel pinjaman',

            // LAPORAN (SEMUA ROLE)
            'view laporan simpanan bulanan',
            'export laporan simpanan',
            'lock laporan simpanan',
            'unlock laporan simpanan',
            'view laporan pinjaman',
            'export laporan pinjaman',

            // ADMIN / MASTER
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */
        $anggota   = Role::firstOrCreate(['name' => 'anggota']);
        $ketua     = Role::firstOrCreate(['name' => 'ketua']);
        $bendahara = Role::firstOrCreate(['name' => 'bendahara']);
        $admin     = Role::firstOrCreate(['name' => 'admin']); // opsional jika kamu pakai

        /*
        |--------------------------------------------------------------------------
        | ROLE → PERMISSION MAPPING (FINAL)
        |--------------------------------------------------------------------------
        */

        // ======================
        // ANGGOTA
        // ======================
        $anggota->syncPermissions([
            'view dashboard',

            'view anggota list',

            'view simpanan saya',
            'view pinjaman saya',
            'create pinjaman',
            'edit pinjaman',
            'delete pinjaman',

            'view laporan simpanan bulanan',
            'view laporan pinjaman',

            'edit profil',
        ]);

        // ======================
        // KETUA (APPROVAL)
        // ======================
        $ketua->syncPermissions([
            'view dashboard',

            'view anggota list',
            'create anggota',
            'edit anggota',

            'view simpanan saya',
            'view pinjaman saya',
            'create pinjaman',
            'edit pinjaman',
            'delete pinjaman',

            'view saldo',
            'view arus koperasi',
            'view arus operasional',
            'view laporan arus kas',
            'export laporan arus kas',

            'view pengajuan pinjaman',
            'approve pinjaman',
            'reject pinjaman',
            'cancel pinjaman',

            'view laporan simpanan bulanan',
            'export laporan simpanan',
            'view laporan pinjaman',
            'export laporan pinjaman',

            'edit profil',
        ]);

        // ======================
        // BENDAHARA (PENCAIRAN)
        // ======================
        $bendahara->syncPermissions([
            'view dashboard',

            'view anggota list',
            'create anggota',
            'edit anggota',
            'nonaktifkan anggota',

            'view simpanan saya',
            'view pinjaman saya',
            'create pinjaman',
            'edit pinjaman',
            'delete pinjaman',

            'view saldo',
            'view arus koperasi',
            'view arus operasional',
            'view laporan arus kas',
            'export laporan arus kas',

            'manage simpanan anggota',
            'pencairan pinjaman',
            'manage cicilan pinjaman',

            'view laporan simpanan bulanan',
            'export laporan simpanan',
            'lock laporan simpanan',
            'unlock laporan simpanan',
            'view laporan pinjaman',
            'export laporan pinjaman',

            'edit profil',
            'manage users',
        ]);

        // ======================
        // ADMIN (JIKA DIPAKAI)
        // ======================
        $admin->syncPermissions([
            'view dashboard',
            'manage users',
        ]);

        $this->call(RekeningKoperasiSeeder::class);
    }
}
