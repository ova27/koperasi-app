<?php

namespace App\Services;

use App\Models\Anggota;
use Exception;

class AnggotaService
{
    public function pastikanAktif(int $anggotaId): void
    {
        $anggota = Anggota::findOrFail($anggotaId);

        if ($anggota->status !== 'aktif') {
            throw new Exception(
                'Transaksi tidak diperbolehkan karena anggota tidak aktif'
            );
        }
    }
}
