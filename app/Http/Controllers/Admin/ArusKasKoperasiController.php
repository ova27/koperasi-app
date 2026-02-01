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

        $items = ArusKas::where('jenis_arus', 'koperasi')
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        return view('keuangan.arus-koperasi', compact('items', 'bulan'));
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