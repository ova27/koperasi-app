<?php

namespace App\Services;

use Exception;
use App\Models\Anggota;
use App\Models\ArusKas;
use App\Models\Pinjaman;
use Illuminate\Support\Carbon;
use App\Models\RekeningKoperasi;
use App\Models\PengajuanPinjaman;
use App\Models\TransaksiPinjaman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class PinjamanService
{
    /* ======================================================
     *  PENGAJUAN
     * ====================================================== */
    public function bolehAjukan(
        int $anggotaId,
        int $jumlahDummy = 0,
        ?int $pengajuanId = null
    ): bool {
        try {
            // pakai aturan YANG SAMA PERSIS
            $this->validateBatasPinjaman(
                $anggotaId,
                $jumlahDummy,
                $pengajuanId
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function ajukan(
        int $anggotaId,
        int $jumlah,
        int $tenor,       
        string $bulan,    
        int $userId,
        ?string $tujuan = null
    ): PengajuanPinjaman {
        $this->validateAnggotaAktif($anggotaId);
        $this->validateBatasPinjaman($anggotaId, $jumlah);

        return PengajuanPinjaman::create([
            'anggota_id'        => $anggotaId,
            'jumlah_diajukan'   => $jumlah,
            'tenor'             => $tenor,    
            'bulan_pinjam'      => $bulan,    
            'tujuan'            => $tujuan,
            'status'            => 'diajukan',
            'diajukan_oleh'     => $userId,
            'tanggal_pengajuan' => now(),
        ]);
    }

    public function updatePengajuan(
        $pengajuan, $jumlah, $tenor, $bulan, $keterangan
    ): void {

        // Cek apakah bulan yang diinput < bulan sekarang
        if (Carbon::parse($bulan)->startOfMonth()->isPast() && !Carbon::parse($bulan)->isCurrentMonth()) {
            throw new Exception('Tidak boleh mengajukan pinjaman untuk bulan yang sudah lewat.');
        }

        if ($pengajuan->status !== 'diajukan') {
            throw new Exception('Pengajuan tidak bisa diubah');
        }

        $this->validateBatasPinjaman(
            $pengajuan->anggota_id,
            $jumlah,
            $pengajuan->id
        );

        // Update semua kolom yang dikirim dari modal
        $pengajuan->update([
            'jumlah_diajukan' => $jumlah,
            'tenor'           => $tenor,      // Sebelumnya ini tidak ada
            'bulan_pinjam'    => $bulan,     // Sebelumnya ini tidak ada
            'tujuan'          => $keterangan, // Gunakan variabel $keterangan yang diterima
        ]);
    }

    protected function rekeningAktif(): RekeningKoperasi
    {
        return RekeningKoperasi::where('aktif', true)->firstOrFail();
    }

    /* ======================================================
     *  PERSETUJUAN
     * ====================================================== */

    public function setujui(
        PengajuanPinjaman $pengajuan, 
        int $userId, 
        array $data = []
    ):void {
        
        if (!in_array($pengajuan->status, ['diajukan', 'disetujui'])) {
            throw new Exception('Pengajuan tidak valid atau sudah dicairkan');
        }

        $pengajuan->update([
            'jumlah_diajukan' => $data['jumlah_diajukan'], 
            'tenor'           => $data['tenor'],           
            'bulan_pinjam'    => $data['bulan_pinjam'],    
            'status'          => 'disetujui',
            'disetujui_oleh'  => $userId,
            'tgl_persetujuan' => now(), 
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
                $sisaSebelumnya = $pinjaman->sisa_pinjaman;
                $sisaBaru = $sisaSebelumnya + $pengajuan->jumlah_diajukan;

                $pinjaman->update([
                    'jumlah_pinjaman' => $pinjaman->jumlah_pinjaman + $pengajuan->jumlah_diajukan,
                    'sisa_pinjaman'   => $sisaBaru,
                ]);

                TransaksiPinjaman::create([
                    'pinjaman_id'  => $pinjaman->id,
                    'tanggal'      => now(),
                    'jenis'        => 'topup',
                    'jumlah'       => $pengajuan->jumlah_diajukan,
                    'sisa_setelah' => $sisaBaru, // 🔑 INI YANG HILANG
                    'keterangan'   => 'Top-up pinjaman',
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

                TransaksiPinjaman::create([
                    'pinjaman_id'  => $pinjaman->id,
                    'tanggal'      => now(),
                    'jenis'        => 'pencairan',
                    'jumlah'       => $pengajuan->jumlah_diajukan,
                    'sisa_setelah' => $pengajuan->jumlah_diajukan, // 🔑
                    'keterangan'   => 'Pencairan pinjaman',
                ]);
            }

            $rekening = $this->rekeningAktif();

            // 🔹 ARUS KAS (SELALU SEKALI SAAT CAIR)
            ArusKas::create([
                'tanggal' => now(),
                'rekening_koperasi_id' => $rekening->id,
                'jenis_arus' => 'koperasi',
                'tipe' => 'keluar',
                'kategori' => 'pinjaman',
                'sub_kategori' => $pinjaman->wasRecentlyCreated ? 'pencairan' : 'topup',
                'jumlah' => $pengajuan->jumlah_diajukan,
                'anggota_id' => $pengajuan->anggota_id,
                'created_by' => $userId,
                'keterangan' => 'Pencairan pinjaman',
            ]);

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

            $sisaSebelumnya = $pinjaman->sisa_pinjaman;
            $sisaBaru = $sisaSebelumnya - $jumlah;

            if ($sisaBaru < 0) {
                throw new Exception('Jumlah cicilan melebihi sisa pinjaman');
            }

            // 🔥 Kalau langsung lunas
            if ($sisaBaru === 0) {

                $pinjaman->update([
                    'sisa_pinjaman' => 0,
                    'status'        => 'lunas',
                ]);

                TransaksiPinjaman::create([
                    'pinjaman_id'  => $pinjaman->id,
                    'tanggal'      => now(),
                    'jenis'        => 'pelunasan',
                    'jumlah'       => $jumlah,
                    'sisa_setelah' => 0,
                    'keterangan'   => 'Pelunasan pinjaman',
                ]);

            } else {

                // 🔹 Cicilan biasa
                $pinjaman->update([
                    'sisa_pinjaman' => $sisaBaru,
                ]);

                TransaksiPinjaman::create([
                    'pinjaman_id'  => $pinjaman->id,
                    'tanggal'      => now(),
                    'jenis'        => 'cicilan',
                    'jumlah'       => $jumlah,
                    'sisa_setelah' => $sisaBaru,
                    'keterangan'   => $keterangan,
                ]);
            }

            $rekening = $this->rekeningAktif();

            ArusKas::create([
                'tanggal' => now(),
                'rekening_koperasi_id' => $rekening->id,
                'jenis_arus' => 'koperasi',
                'tipe' => 'masuk',
                'kategori' => 'pinjaman',
                'sub_kategori' => 'cicilan',
                'jumlah' => $jumlah,
                'anggota_id' => $pinjaman->anggota_id,
                'created_by' => Auth::id(),
                'keterangan' => 'Cicilan pinjaman',
            ]);
        });
    }

    /* ======================================================
     *  VALIDASI
     * ====================================================== */

    protected function validateAnggotaAktif(int $anggotaId): void
    {
        $anggota = Anggota::findOrFail($anggotaId);

        if ($anggota->status !== 'aktif') {
            throw new Exception(
                'Transaksi hanya dapat dilakukan oleh anggota aktif'
            );
        }
    }

    protected function validateBatasPinjaman(
        int $anggotaId,
        int $jumlahPengajuan,
        ?int $pengajuanId = null // 👈 untuk edit
    ): void {

        // ambil pinjaman aktif
        $pinjamanAktif = Pinjaman::where('anggota_id', $anggotaId)
            ->where('status', 'aktif')
            ->first();

        $sisaPinjamanAktif = $pinjamanAktif?->sisa_pinjaman ?? 0;

        // ambil pengajuan yang masih mengikat dana
        $queryPengajuan = PengajuanPinjaman::where('anggota_id', $anggotaId)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->when($pengajuanId, function ($q) use ($pengajuanId) {
                $q->where('id', '!=', $pengajuanId);
            });
        if ($queryPengajuan->exists()) {
            throw new Exception(
                'Anda masih memiliki pengajuan yang berstatus diajukan atau disetujui. ' . 
                'Harap tunggu proses selesai sebelum mengajukan kembali.'
            );
        }
        $totalPengajuanAktif = $queryPengajuan->sum('jumlah_diajukan');
        $totalEksposur = $sisaPinjamanAktif + $totalPengajuanAktif + $jumlahPengajuan;

        // 1️⃣ batas total eksposur
        if ($totalEksposur > 20000000) {
            throw new Exception(
                'Total pinjaman aktif dan pengajuan maksimal Rp 20.000.000'
            );
        }

        // 2️⃣ aturan top-up
        if ($pinjamanAktif && $pinjamanAktif->sisa_pinjaman > 5000000) {
            throw new Exception(
                'Top-up hanya boleh jika sisa pinjaman aktif maksimal Rp 5.000.000'
            );
        }
    }

    public function ringkasanAnggota(int $anggotaId): array
    {
        $pinjamans = Pinjaman::where('anggota_id', $anggotaId)->get();

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
