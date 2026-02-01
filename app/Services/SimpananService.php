<?php

namespace App\Services;
use Carbon\Carbon;
use App\Models\Simpanan;
use App\Models\Anggota;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Services\ClosingService;
use App\Models\RekeningKoperasi;
use App\Models\ArusKas;


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

    protected function rekeningAktif(): RekeningKoperasi
    {
        return RekeningKoperasi::where('aktif', true)
            ->firstOrFail();
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
       
        // 🧱 SIMPANAN POKOK
        if ($jenis === 'pokok') {
            $sudahAda = Simpanan::where('anggota_id', $anggotaId)
                ->where('jenis_simpanan', 'pokok')
                ->exists();

            if ($sudahAda) {
                throw new Exception(
                    'Simpanan pokok hanya boleh satu kali di awal keanggotaan'
                );
            }

            if (!in_array($sumber, ['saldo_awal', 'manual'])) {
                throw new Exception('Sumber simpanan pokok tidak valid');
            }
        }

        // 🔒 CLOSING BULAN (GLOBAL)
        $bulan = Carbon::now()->format('Y-m');
        if (app(ClosingService::class)->isLocked($bulan, 'simpanan')) {
            throw new Exception(
                'Bulan ini sudah ditutup, tidak bisa input simpanan'
            );
        }
    
        // 📅 SIMPANAN BULANAN
        if (in_array($jenis, ['wajib', 'sukarela']) && $alasan === 'biasa') {
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

        // 💰 VALIDASI JUMLAH
        if ($jumlah <= 0) {
            throw new Exception('Jumlah simpanan harus lebih dari 0');
        }

        // 👤 VALIDASI ANGGOTA
        $this->validateAnggotaAktif($anggotaId);

        DB::transaction(function () use (
            $anggotaId, $jenis, $jumlah, $sumber, $alasan, $keterangan
        ) {
            $simpanan = Simpanan::create([
                'anggota_id'     => $anggotaId,
                'tanggal'        => now(),
                'jenis_simpanan' => $jenis,
                'jumlah'         => $jumlah,
                'sumber'         => $sumber,
                'alasan'         => $alasan,
                'keterangan'     => $keterangan,
            ]);

            $rekening = $this->rekeningAktif();
            // ⬇️ TAMBAHAN BARU (ARUS KAS)
            ArusKas::create([
                'tanggal' => $simpanan->tanggal,
                'rekening_koperasi_id'  => $rekening->id,
                'jenis_arus' => 'koperasi',
                'tipe' => $jumlah > 0 ? 'masuk' : 'keluar',
                'kategori' => 'simpanan',
                'sub_kategori' => $jenis,
                'jumlah' => abs($jumlah),
                'anggota_id' => $anggotaId,
                'created_by' => Auth::id(),
                'keterangan' => 'Transaksi simpanan',
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

            $rekening = $this->rekeningAktif();

            ArusKas::create([
                'tanggal'              => now(),
                'rekening_koperasi_id' => $rekening->id,
                'jenis_arus'           => 'koperasi',
                'tipe'                 => 'keluar',
                'kategori'             => 'simpanan',
                'sub_kategori'         => $jenis,
                'jumlah'               => $jumlah,
                'anggota_id'           => $anggotaId,
                'created_by'           => Auth::id(),
                'keterangan'           => $keterangan ?? 'Pengurangan simpanan',
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
            throw new Exception(
                'Transaksi hanya dapat dilakukan oleh anggota aktif'
            );
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
                'jumlah'         => -$jumlah,
                'sumber'         => $sumber,
                'alasan'         => 'pengambilan',
                'keterangan'     => $keterangan,
            ]);

            $rekening = $this->rekeningAktif();

            ArusKas::create([
                'tanggal'              => now(),
                'rekening_koperasi_id' => $rekening->id,
                'jenis_arus'           => 'koperasi',
                'tipe'                 => 'keluar',
                'kategori'             => 'simpanan',
                'sub_kategori'         => 'sukarela',
                'jumlah'               => $jumlah,
                'anggota_id'           => $anggotaId,
                'created_by'           => Auth::id(),
                'keterangan'           => $keterangan,
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

        $rekening = $this->rekeningAktif();
        
        DB::transaction(function () use ($anggotaId, $saldos, $alasan, $rekening) {
            foreach ($saldos as $saldo) {
                if ($saldo->total > 0) {
                    Simpanan::create([
                        'anggota_id'     => $anggotaId,
                        'tanggal'        => now(),
                        'jenis_simpanan' => $saldo->jenis_simpanan,
                        'jumlah'         => -$saldo->total,
                        'sumber'         => 'manual',
                        'alasan'         => $alasan,
                        'keterangan'     => 'Pengembalian simpanan karena ' . $alasan,
                    ]);

                    ArusKas::create([
                        'tanggal'              => now(),
                        'rekening_koperasi_id' => $rekening->id,
                        'jenis_arus'           => 'koperasi',
                        'tipe'                 => 'keluar',
                        'kategori'             => 'simpanan',
                        'sub_kategori'         => $saldo->jenis_simpanan,
                        'jumlah'               => $saldo->total,
                        'anggota_id'           => $anggotaId,
                        'created_by'           => Auth::id(),
                        'keterangan'           => 'Pengembalian simpanan karena ' . $alasan,
                    ]);
                }
            }
        });
    }
}
