<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArusKas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\ArusKasKoperasiExport;
use Maatwebsite\Excel\Facades\Excel;

class ArusKasKoperasiController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view arus koperasi');

        $bulan = $request->get('bulan', now()->format('Y-m'));
        $bulanNum = substr($bulan, 5, 2);
        $tahunNum = substr($bulan, 0, 4);

        $items = ArusKas::where('jenis_arus', 'koperasi')
            ->whereMonth('tanggal', $bulanNum)
            ->whereYear('tanggal', $tahunNum)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        $totalMasukBulan = ArusKas::where('jenis_arus', 'koperasi')
            ->where('tipe', 'masuk')
            ->whereMonth('tanggal', $bulanNum)
            ->whereYear('tanggal', $tahunNum)
            ->sum('jumlah');

        $totalKeluarBulan = ArusKas::where('jenis_arus', 'koperasi')
            ->where('tipe', 'keluar')
            ->whereMonth('tanggal', $bulanNum)
            ->whereYear('tanggal', $tahunNum)
            ->sum('jumlah');

        return view('keuangan.arus-koperasi', compact('items', 'bulan', 'totalMasukBulan', 'totalKeluarBulan'));
    }

    public function export(Request $request)
    {
        $this->authorize('view arus koperasi');

        $bulan = $request->get('bulan', now()->format('Y-m'));

        return Excel::download(
            new ArusKasKoperasiExport($bulan),
            'arus-kas-koperasi-' . $bulan . '.xlsx'
        );
    }
}