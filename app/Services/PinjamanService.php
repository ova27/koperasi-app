<?php

namespace App\Services;

use App\Models\Pinjaman;
use App\Models\PengajuanPinjaman;
use App\Models\TransaksiPinjaman;
use App\Models\Anggota;
use Illuminate\Support\Facades\DB;
use Exception;

class PinjamanService
{
    /* ======================================================
     *  PENGAJUAN
     * ====================================================== */

    public function ajukan(
        int $anggotaId,
        int $jumlah,
        int $userId,
        ?string $tujuan = null
    ): PengajuanPinjaman {
        $this->validateAnggotaAktif($anggotaId);
        $this->validateBatasPinjaman($anggotaId, $jumlah);

        return PengajuanPinjaman::create([
            'anggota_id'        => $anggotaId,
            'jumlah_diajukan'   => $jumlah,
            'tujuan'            => $tujuan,
            'status'            => 'diajukan',
            'diajukan_oleh'     => $userId,
            'tanggal_pengajuan'=> now(),
        ]);
    }

    /* ======================================================
     *  PERSETUJUAN
     * ====================================================== */

    public function setujui(
        PengajuanPinjaman $pengajuan,
        int $userId
    ): void {
        if ($pengajuan->status !== 'diajukan') {
            throw new Exception('Pengajuan tidak valid');
        }

        $pengajuan->update([
            'status'               => 'disetujui',
            'disetujui_oleh'       => $userId,
            'tanggal_persetujuan'  => now(),
        ]);
    }

    /* ======================================================
     *  PENCAIRAN
     * ====================================================== */

    public function cairkan(
        PengajuanPinjaman $pengajuan,
        int $userId
    ): void {
        if ($pengajuan->status !== 'disetujui') {
            throw new Exception('Pengajuan belum disetujui');
        }

        DB::transaction(function () use ($pengajuan, $userId) {

            // cek pinjaman aktif
            $pinjaman = Pinjaman::where('anggota_id', $pengajuan->anggota_id)
                ->where('status', 'aktif')
                ->first();

            if ($pinjaman) {
                // TOP-UP
                $pinjaman->increment('jumlah_pinjaman', $pengajuan->jumlah_diajukan);
                $pinjaman->increment('sisa_pinjaman', $pengajuan->jumlah_diajukan);

                TransaksiPinjaman::create([
                    'pinjaman_id' => $pinjaman->id,
                    'tanggal'     => now(),
                    'jenis'       => 'topup',
                    'jumlah'      => $pengajuan->jumlah_diajukan,
                    'keterangan'  => 'Top-up pinjaman',
                ]);
            } else {
                // PINJAMAN BARU
                $pinjaman = Pinjaman::create([
                    'anggota_id'      => $pengajuan->anggota_id,
                    'tanggal_pinjam'  => now(),
                    'jumlah_pinjaman' => $pengajuan->jumlah_diajukan,
                    'sisa_pinjaman'   => $pengajuan->jumlah_diajukan,
                    'status'          => 'aktif',
                ]);
            }

            $pengajuan->update([
                'status'               => 'dicairkan',
                'dicairkan_oleh'       => $userId,
                'tanggal_pencairan'    => now(),
            ]);
        });
    }

    /* ======================================================
     *  CICILAN / PELUNASAN
     * ====================================================== */

    public function cicil(
        Pinjaman $pinjaman,
        int $jumlah,
        ?string $keterangan = null
    ): void {
        if ($pinjaman->status !== 'aktif') {
            throw new Exception('Pinjaman sudah lunas');
        }

        if ($jumlah <= 0) {
            throw new Exception('Jumlah cicilan tidak valid');
        }

        DB::transaction(function () use ($pinjaman, $jumlah, $keterangan) {

            $pinjaman->decrement('sisa_pinjaman', $jumlah);

            TransaksiPinjaman::create([
                'pinjaman_id' => $pinjaman->id,
                'tanggal'     => now(),
                'jenis'       => 'cicilan',
                'jumlah'      => $jumlah,
                'keterangan'  => $keterangan,
            ]);

            if ($pinjaman->sisa_pinjaman <= 0) {
                $pinjaman->update([
                    'sisa_pinjaman' => 0,
                    'status' => 'lunas',
                ]);

                TransaksiPinjaman::create([
                    'pinjaman_id' => $pinjaman->id,
                    'tanggal'     => now(),
                    'jenis'       => 'pelunasan',
                    'jumlah'      => 0,
                    'keterangan'  => 'Pinjaman lunas',
                ]);
            }
        });
    }

    /* ======================================================
     *  VALIDASI
     * ====================================================== */

    protected function validateAnggotaAktif(int $anggotaId): void
    {
        $anggota = Anggota::findOrFail($anggotaId);

        if ($anggota->status !== 'aktif') {
            throw new Exception('Anggota tidak aktif');
        }
    }

    protected function validateBatasPinjaman(int $anggotaId, int $pengajuan): void
    {
        $pinjamanAktif = Pinjaman::where('anggota_id', $anggotaId)
            ->where('status', 'aktif')
            ->sum('sisa_pinjaman');

        if ($pinjamanAktif > 0 && $pinjamanAktif >= 3000000) {
            throw new Exception('Sisa pinjaman masih di atas batas top-up');
        }

        if (($pinjamanAktif + $pengajuan) > 20000000) {
            throw new Exception('Melebihi batas maksimal pinjaman');
        }
    }

    public function ringkasanAnggota(int $anggotaId): array
    {
        $pinjamans = \App\Models\Pinjaman::where('anggota_id', $anggotaId)->get();

        return [
            'aktif' => $pinjamans->where('status', 'aktif')->count(),
            'lunas' => $pinjamans->where('status', 'lunas')->count(),
            'sisa'  => $pinjamans->where('status', 'aktif')->sum('sisa_pinjaman'),
        ];
    }

}
