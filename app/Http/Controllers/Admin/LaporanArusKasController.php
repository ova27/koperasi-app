<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArusKas;
use Illuminate\Http\Request;
use App\Exports\LaporanArusKasExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanArusKasController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view laporan arus kas');

        $bulan = $request->get('bulan', now()->format('Y-m'));

        $masuk = ArusKas::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->where('tipe', 'masuk')
            ->selectRaw('jenis_arus, SUM(jumlah) as total')
            ->groupBy('jenis_arus')
            ->pluck('total', 'jenis_arus');

        $keluar = ArusKas::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->where('tipe', 'keluar')
            ->selectRaw('jenis_arus, SUM(jumlah) as total')
            ->groupBy('jenis_arus')
            ->pluck('total', 'jenis_arus');

        $totalMasuk = $masuk->sum();
        $totalKeluar = $keluar->sum();

        return view('keuangan.laporan-arus-kas', compact(
            'bulan',
            'masuk',
            'keluar',
            'totalMasuk',
            'totalKeluar'
        ));
    }

    public function export(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        return Excel::download(
            new LaporanArusKasExport($bulan),
            'laporan-arus-kas-' . $bulan . '.xlsx'
        );
    }
}
