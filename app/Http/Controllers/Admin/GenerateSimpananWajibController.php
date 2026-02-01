<?php

namespace App\Http\Controllers\Admin;
use App\Services\ClosingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SimpananService;
use App\Models\Anggota;
use Exception;

class GenerateSimpananWajibController extends Controller
{
    public function index()
    {
        $this->authorize('manage simpanan anggota');

        return view('admin.simpanan.generate-wajib');
    }

    public function process(
        Request $request,
        SimpananService $simpananService,
        ClosingService $closingService
    ) {
        $this->authorize('manage simpanan anggota');

        // 🔒 1. CEK CLOSING DI PALING AWAL
        $bulan = $request->bulan ?? now()->format('Y-m');

        if ($closingService->isLocked($bulan, 'simpanan')) {
            return back()->withErrors([
                'generate' => 'Bulan ini sudah ditutup, tidak bisa generate simpanan wajib.',
            ]);
        }

        // 🔹 2. VALIDASI INPUT
        $request->validate([
            'bulan' => ['required', 'date_format:Y-m'],
        ]);

        $bulanDipilih = $request->bulan;
        $bulanBerjalan = now()->format('Y-m');

        // 🔒 3. RULE: hanya boleh bulan berjalan
        if ($bulanDipilih !== $bulanBerjalan) {
            return back()->withErrors([
                'bulan' => 'Generate simpanan wajib hanya boleh untuk bulan berjalan.',
            ]);
        }

        // 🔹 4. AMBIL ANGGOTA YANG BELUM ADA SIMPANAN WAJIB BULAN INI
        $anggotas = Anggota::where('status', 'aktif')
            ->whereDoesntHave('simpanans', function ($q) use ($bulanDipilih) {
                $q->where('jenis_simpanan', 'wajib')
                ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulanDipilih]);
            })
            ->get();

        $berhasil = 0;
        $skip = 0;

        // 🔹 5. GENERATE
        foreach ($anggotas as $anggota) {
            try {
                $simpananService->tambah(
                    $anggota->id,
                    'wajib',
                    config('koperasi.simpanan_wajib'),
                    'otomatis',
                    'otomatis',
                    'Generate simpanan wajib bulan ' . $bulanDipilih
                );

                $berhasil++;
            } catch (\Exception $e) {
                $skip++;
            }
        }

        return back()->with('success',
            "Generate selesai. Berhasil: {$berhasil}, Skip: {$skip}"
        );
    }


}
