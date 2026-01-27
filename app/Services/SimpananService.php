<?php

namespace App\Services;
use Carbon\Carbon;
use App\Models\Simpanan;
use App\Models\Anggota;
use Illuminate\Support\Facades\DB;
use Exception;

class SimpananService
{
    /**
     * Ambil saldo total semua simpanan anggota
     */
    public function saldoAnggota(int $anggotaId): int
    {
        return Simpanan::where('anggota_id', $anggotaId)->sum('jumlah');
    }

    /**
     * Ambil saldo per jenis simpanan
     */
    public function saldoPerJenis(int $anggotaId): array
    {
        return Simpanan::where('anggota_id', $anggotaId)
            ->selectRaw('jenis_simpanan, SUM(jumlah) as saldo')
            ->groupBy('jenis_simpanan')
            ->pluck('saldo', 'jenis_simpanan')
            ->toArray();
    }

    /**
     * Tambah simpanan (masuk)
     */
    public function tambah(
        int $anggotaId,
        string $jenis,
        int $jumlah,
        string $sumber,
        string $alasan = 'biasa',
        ?string $keterangan = null
    ): void {
       
        if ($jenis === 'pokok') {
            $sudahAda = Simpanan::where('anggota_id', $anggotaId)
                ->where('jenis_simpanan', 'pokok')
                ->exists();

            if ($sudahAda) {
                throw new Exception(
                    'Simpanan pokok hanya boleh satu kali di awal keanggotaan'
                );
            }
        }

        if ($jenis === 'pokok' && !in_array($sumber, ['saldo_awal', 'manual'])) {
            throw new Exception('Sumber simpanan pokok tidak valid');
        }

        if (in_array($jenis, ['wajib', 'sukarela']) && $alasan === 'biasa') {

            $bulan = Carbon::now()->format('Y-m');

            $sudahAda = Simpanan::where('anggota_id', $anggotaId)
                ->where('jenis_simpanan', $jenis)
                ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
                ->exists();

            if ($sudahAda) {
                throw new Exception(
                    "Simpanan {$jenis} bulan ini sudah pernah diinput"
                );
            }
        }

        if ($jumlah <= 0) {
            throw new Exception('Jumlah simpanan harus lebih dari 0');
        }

        $this->validateAnggotaAktif($anggotaId);

        DB::transaction(function () use (
            $anggotaId, $jenis, $jumlah, $sumber, $alasan, $keterangan
        ) {
            Simpanan::create([
                'anggota_id'     => $anggotaId,
                'tanggal'        => now(),
                'jenis_simpanan' => $jenis,
                'jumlah'         => $jumlah,
                'sumber'         => $sumber,
                'alasan'         => $alasan,
                'keterangan'     => $keterangan,
            ]);
        });
    }

    /**
     * Ambil / kurangi simpanan
     */
    public function kurangi(
        int $anggotaId,
        string $jenis,
        int $jumlah,
        string $alasan,
        ?string $keterangan = null
    ): void {
        if ($jumlah <= 0) {
            throw new Exception('Jumlah pengambilan harus lebih dari 0');
        }

        $this->validateAnggotaAktif($anggotaId);

        $saldoPerJenis = $this->saldoPerJenis($anggotaId);
        $saldo = $saldoPerJenis[$jenis] ?? 0;

        if ($saldo < $jumlah) {
            throw new Exception('Saldo simpanan tidak mencukupi');
        }

        DB::transaction(function () use (
            $anggotaId, $jenis, $jumlah, $alasan, $keterangan
        ) {
            Simpanan::create([
                'anggota_id'     => $anggotaId,
                'tanggal'        => now(),
                'jenis_simpanan' => $jenis,
                'jumlah'         => -$jumlah,
                'sumber'         => 'manual',
                'alasan'         => $alasan,
                'keterangan'     => $keterangan,
            ]);
        });
    }

    /**
     * Validasi anggota aktif
     */
    protected function validateAnggotaAktif(int $anggotaId): void
    {
        $anggota = Anggota::findOrFail($anggotaId);

        if ($anggota->status !== 'aktif') {
            throw new Exception('Anggota tidak aktif');
        }
    }

    public function ambil(
        int $anggotaId,
        int $jumlah,
        string $sumber = 'manual',
        string $keterangan
    ): void {
        if ($jumlah <= 0) {
            throw new Exception('Jumlah pengambilan harus lebih dari 0');
        }

        $this->validateAnggotaAktif($anggotaId);

        // HITUNG SALDO SUKARELA
        $saldo = Simpanan::where('anggota_id', $anggotaId)
            ->where('jenis_simpanan', 'sukarela')
            ->sum('jumlah');

        if ($saldo < $jumlah) {
            throw new Exception('Saldo simpanan sukarela tidak mencukupi');
        }

        DB::transaction(function () use ($anggotaId, $jumlah, $sumber, $keterangan) {
            Simpanan::create([
                'anggota_id'     => $anggotaId,
                'tanggal'        => now(),
                'jenis_simpanan' => 'sukarela',
                'jumlah'         => -$jumlah, // NEGATIF
                'sumber'         => $sumber,
                'alasan'         => 'pengambilan',
                'keterangan'     => $keterangan,
            ]);
        });
    }

    public function kembalikanSemuaSimpanan(
        int $anggotaId,
        string $alasan // pensiun | mutasi
    ): void {
        if (!in_array($alasan, ['pensiun', 'mutasi'])) {
            throw new Exception('Alasan pengembalian tidak valid');
        }

        // hitung saldo per jenis
        $saldos = Simpanan::where('anggota_id', $anggotaId)
            ->select('jenis_simpanan', DB::raw('SUM(jumlah) as total'))
            ->groupBy('jenis_simpanan')
            ->get();

        DB::transaction(function () use ($anggotaId, $saldos, $alasan) {
            foreach ($saldos as $saldo) {
                if ($saldo->total > 0) {
                    Simpanan::create([
                        'anggota_id'     => $anggotaId,
                        'tanggal'        => now(),
                        'jenis_simpanan' => $saldo->jenis_simpanan,
                        'jumlah'         => -$saldo->total, // NEGATIF
                        'sumber'         => 'manual',
                        'alasan'         => $alasan,
                        'keterangan'     => 'Pengembalian simpanan karena ' . $alasan,
                    ]);
                }
            }
        });
    }

}
