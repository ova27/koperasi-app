<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArusKas;
use Illuminate\Http\Request;
use App\Exports\ArusOperasionalExport;
use Maatwebsite\Excel\Facades\Excel;

class ArusOperasionalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('lihat-keuangan-global');

        $bulan = $request->get('bulan', now()->format('Y-m'));

        $items = ArusKas::where('jenis_arus', 'operasional')
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        return view('keuangan.arus-operasional', compact('items', 'bulan'));
    }

    public function export(Request $request)
    {
        $this->authorize('lihat-keuangan-global');

        $bulan = $request->get('bulan', now()->format('Y-m'));

        return Excel::download(
            new ArusOperasionalExport($bulan),
            'arus-operasional-' . $bulan . '.xlsx'
        );
    }
}

