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

    public function updatePengajuan(
        PengajuanPinjaman $pengajuan,
        int $jumlah,
        ?string $tujuan = null
    ): void {
        if ($pengajuan->status !== 'diajukan') {
            throw new Exception('Pengajuan tidak bisa diubah');
        }

        $this->validateBatasPinjaman(
            $pengajuan->anggota_id,
            $jumlah
        );

        $pengajuan->update([
            'jumlah_diajukan' => $jumlah,
            'tujuan' => $tujuan,
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

    protected function validateBatasPinjaman(
        int $anggotaId,
        int $jumlahPengajuan
    ): void {
    
        // ===== KASUS: PINJAMAN BARU =====
        if ($jumlahPengajuan > 20000000) {
            throw new Exception(
                'Jumlah pinjaman maksimal Rp 20.000.000'
            );
        }

        // ambil pinjaman aktif (kalau ada)
        $pinjamanAktif = Pinjaman::where('anggota_id', $anggotaId)
            ->where('status', 'aktif')
            ->first();

        // ===== KASUS: TOP-UP =====
        if ($pinjamanAktif) {

            // 1️⃣ batas sisa pinjaman aktif max 5jt
            if ($pinjamanAktif->sisa_pinjaman > 5000000) {
                throw new Exception(
                    'Top-up hanya boleh jika sisa pinjaman aktif maksimal Rp 5.000.000'
                );
            }

            // 2️⃣ total eksposur max 20jt
            $total = $pinjamanAktif->sisa_pinjaman + $jumlahPengajuan;

            if ($total > 20000000) {
                throw new Exception(
                    'Total pinjaman aktif dan pengajuan melebihi Rp 20.000.000'
                );
            }

            return; // valid
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

    public function anggotaMasihPunyaPinjamanAktif(int $anggotaId): bool
    {
        return Pinjaman::where('anggota_id', $anggotaId)
            ->where('status', 'aktif')
            ->exists();
    }

}
