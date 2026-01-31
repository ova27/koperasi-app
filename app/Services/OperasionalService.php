<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\ArusKas;
use App\Models\Anggota;
use Carbon\Carbon;

class OperasionalService
{
    public function generateIuranBulanan(int $userId): void
    {
        $bulan = Carbon::now()->format('Y-m');
        $closingService = app(ClosingService::class);
        
        if ($closingService->isLocked($bulan, 'operasional')) {
            throw new \Exception('Bulan ini sudah ditutup, tidak bisa generate iuran operasional');
        }

        DB::transaction(function () use ($bulan, $userId) {

            // 🔒 CEK SUDAH PERNAH GENERATE?
            $sudahAda = ArusKas::where('jenis_arus', 'operasional')
                ->where('kategori', 'iuran')
                ->where('sub_kategori', 'bulanan')
                ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
                ->exists();

            if ($sudahAda) {
                throw new \Exception('Iuran operasional bulan ini sudah digenerate');
            }

            $anggotas = Anggota::where('status', 'aktif')->get();

            foreach ($anggotas as $anggota) {
                ArusKas::create([
                    'tanggal' => Carbon::now()->startOfMonth(),
                    'rekening_koperasi_id' => 1,
                    'jenis_arus' => 'operasional',
                    'tipe' => 'masuk',
                    'kategori' => 'iuran',
                    'sub_kategori' => 'bulanan',
                    'jumlah' => 5000,
                    'anggota_id' => $anggota->id,
                    'created_by' => $userId,
                    'keterangan' => 'Iuran operasional bulan ' . $bulan,
                ]);
            }
        });
    }
}
