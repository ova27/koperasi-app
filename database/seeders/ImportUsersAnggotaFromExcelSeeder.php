<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\Anggota;

class ImportUsersAnggotaFromExcelSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/import/Map Role Anggota.xlsx');

        if (! file_exists($path)) {
            $this->command->error('❌ File Excel tidak ditemukan: ' . $path);
            return;
        }

        $rows = Excel::toArray([], $path)[0];

        // hapus header
        unset($rows[0]);

        foreach ($rows as $index => $row) {

            // ====== AMBIL DATA EXCEL ======
            $email        = trim($row[0] ?? '');
            $nama         = trim($row[1] ?? '');
            $jenisKelamin = trim($row[2] ?? null); // L / P
            $jabatan      = trim($row[3] ?? null);

            $isAnggota    = trim($row[4] ?? '') === '✔';
            $isKetua      = trim($row[5] ?? '') === '✔';
            $isBendahara  = trim($row[6] ?? '') === '✔';
            $isAdmin      = trim($row[7] ?? '') === '✔';

            // ====== VALIDASI MINIMAL ======
            if (! $email || ! $nama) {
                $this->command->warn("⚠️ Baris {$index} dilewati (email / nama kosong)");
                continue;
            }

            if (! $isAnggota && ! $isKetua && ! $isBendahara && ! $isAdmin) {
                $this->command->warn("⚠️ {$email} tidak punya role, dilewati");
                continue;
            }

            // ====== 1️⃣ USERS ======
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $nama,
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // ====== 2️⃣ ROLE ======
            // reset agar idempotent
            $user->syncRoles([]);

            if ($isAnggota) {
                $user->assignRole('anggota');
            }
            if ($isKetua) {
                $user->assignRole('ketua');
            }
            if ($isBendahara) {
                $user->assignRole('bendahara');
            }
            if ($isAdmin) {
                $user->assignRole('admin');
            }

            // ====== 3️⃣ ANGGOTAS ======
            // Catatan:
            // - admin tetap dibuatkan anggota (aman & konsisten)
            Anggota::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama'          => $nama,
                    'jenis_kelamin' => $jenisKelamin,
                    'jabatan'       => $jabatan,
                    'status'        => 'aktif',
                    'tanggal_masuk' => now(),
                ]
            );
        }

        $this->command->info('✅ Import users + roles + anggotas selesai');
    }
}
