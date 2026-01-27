<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Support\Facades\DB;
use Exception;

class AnggotaExitService
{
    protected SimpananService $simpananService;

    public function __construct(SimpananService $simpananService)
    {
        $this->simpananService = $simpananService;
    }

    /**
     * Proses pensiun / mutasi anggota
     */
    public function keluar(
        int $anggotaId,
        string $alasanKeluar // pensiun | mutasi
    ): void {
        DB::transaction(function () use ($anggotaId, $alasanKeluar) {

            $anggota = Anggota::findOrFail($anggotaId);

            // 1️⃣ VALIDASI PINJAMAN AKTIF
            $pinjamanAktif = Pinjaman::where('anggota_id', $anggotaId)
                ->where('status', 'aktif')
                ->where('sisa_pinjaman', '>', 0)
                ->exists();

            if ($pinjamanAktif) {
                throw new Exception(
                    'Anggota masih memiliki pinjaman aktif'
                );
            }

            // 2️⃣ AMBIL SALDO SIMPANAN PER JENIS
            $saldoPerJenis = $this->simpananService
                ->saldoPerJenis($anggotaId);

            // 3️⃣ PENGEMBALIAN SIMPANAN
            foreach ($saldoPerJenis as $jenis => $saldo) {
                if ($saldo > 0) {
                    Simpanan::create([
                        'anggota_id'     => $anggotaId,
                        'tanggal'        => now(),
                        'jenis_simpanan' => $jenis,
                        'jumlah'         => -$saldo,
                        'sumber'         => 'pengembalian',
                        'alasan'         => $alasanKeluar,
                        'keterangan'     => 'Pengembalian simpanan karena '
                                             . $alasanKeluar,
                    ]);
                }
            }

            // 4️⃣ UPDATE STATUS ANGGOTA
            $anggota->update([
                'status'          => 'tidak_aktif',
                'tanggal_keluar'  => now(),
            ]);
        });
    }
}
