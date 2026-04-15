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

        $bulanInput = (int) $request->get('bulan_filter');
        $tahunInput = (int) $request->get('tahun_filter');

        if ($bulanInput >= 1 && $bulanInput <= 12 && $tahunInput >= 2000 && $tahunInput <= 2100) {
            $bulan = sprintf('%04d-%02d', $tahunInput, $bulanInput);
        } else {
            $bulan = $request->get('bulan', now()->format('Y-m'));
        }

        $filter = $request->get('filter', 'semua');

        $query = ArusKas::query()
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->when(in_array($filter, ['koperasi', 'operasional']), function ($q) use ($filter) {
                $q->where('jenis_arus', $filter);
            });

        $masuk = (clone $query)
            ->where('tipe', 'masuk')
            ->selectRaw('jenis_arus, SUM(jumlah) as total')
            ->groupBy('jenis_arus')
            ->pluck('total', 'jenis_arus');

        $keluar = (clone $query)
            ->where('tipe', 'keluar')
            ->selectRaw('jenis_arus, SUM(jumlah) as total')
            ->groupBy('jenis_arus')
            ->pluck('total', 'jenis_arus');

        $totalMasuk = $masuk->sum();
        $totalKeluar = $keluar->sum();
        $saldoBersih = $totalMasuk - $totalKeluar;

        $items = (clone $query)
            ->with(['anggota', 'rekening'])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        return view('keuangan.laporan-arus-kas', compact(
            'bulan',
            'filter',
            'masuk',
            'keluar',
            'totalMasuk',
            'totalKeluar',
            'saldoBersih',
            'items'
        ));
    }

    public function export(Request $request)
    {
        $bulanInput = (int) $request->get('bulan_filter');
        $tahunInput = (int) $request->get('tahun_filter');

        if ($bulanInput >= 1 && $bulanInput <= 12 && $tahunInput >= 2000 && $tahunInput <= 2100) {
            $bulan = sprintf('%04d-%02d', $tahunInput, $bulanInput);
        } else {
            $bulan = $request->get('bulan', now()->format('Y-m'));
        }

        return Excel::download(
            new LaporanArusKasExport($bulan),
            'laporan-arus-kas-' . $bulan . '.xlsx'
        );
    }
}
