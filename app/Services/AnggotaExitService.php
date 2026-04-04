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
        $anggotaId,
        $alasan,
        $keterangan = null
    ): void {
        if (!in_array($alasan, ['pensiun', 'mutasi'])) {
            throw new Exception('Alasan keluar tidak valid');
        }

        DB::transaction(function () use ($anggotaId, $alasan, $keterangan) {

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
                ->kembalikanSemuaSimpanan($anggotaId, $alasan, $keterangan);

            // 🔁 UPDATE STATUS ANGGOTA
            $anggota->update([
                'status'         => 'tidak_aktif',
                'tanggal_keluar' => now(),
            ]);
        });
    }
}
