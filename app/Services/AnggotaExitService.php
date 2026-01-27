<?php
namespace App\Services;

use App\Models\Anggota;
use Illuminate\Support\Facades\DB;
use Exception;

class AnggotaExitService
{
    public function __construct(
        protected SimpananService $simpananService,
        protected PinjamanService $pinjamanService
    ) {}

    public function keluar(
        int $anggotaId,
        string $alasanKeluar // pensiun | mutasi
    ): void {
        if (!in_array($alasanKeluar, ['pensiun', 'mutasi'])) {
            throw new Exception('Alasan keluar tidak valid');
        }

        DB::transaction(function () use ($anggotaId, $alasanKeluar) {

            $anggota = Anggota::findOrFail($anggotaId);

            if ($anggota->status !== 'aktif') {
                throw new Exception('Anggota tidak aktif');
            }

            // 🔒 VALIDASI PINJAMAN (SATU PINTU)
            if ($this->pinjamanService
                ->anggotaMasihPunyaPinjamanAktif($anggotaId)
            ) {
                throw new Exception(
                    'Anggota masih memiliki pinjaman aktif'
                );
            }

            // 💰 KEMBALIKAN SEMUA SIMPANAN
            $this->simpananService
                ->kembalikanSemuaSimpanan($anggotaId, $alasanKeluar);

            // 🔁 UPDATE STATUS ANGGOTA
            $anggota->update([
                'status'         => 'tidak_aktif',
                'tanggal_keluar' => now(),
            ]);
        });
    }
}
