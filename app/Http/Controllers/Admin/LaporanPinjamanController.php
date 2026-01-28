<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use App\Exports\PinjamanExport;
use Maatwebsite\Excel\Facades\Excel;


class LaporanPinjamanController extends Controller
{
    public function index(Request $request)
    {
        $semuaPinjaman = Pinjaman::with('anggota')->get();

        $pinjamanAktif = $semuaPinjaman->where('status', 'aktif');

        return view('admin.laporan.pinjaman', [
            'jumlahAktif' => $pinjamanAktif->count(),
            'totalSisa'   => $pinjamanAktif->sum('sisa_pinjaman'),
            'pinjamans'   => $semuaPinjaman,
        ]);
    }

    public function export()
    {
        return Excel::download(
            new PinjamanExport,
            'laporan-pinjaman.xlsx'
        );
    }

    public function show(Pinjaman $pinjaman)
    {
        $pinjaman->load(['anggota', 'transaksi']);

        return view('admin.laporan.pinjaman-detail', [
            'pinjaman' => $pinjaman,
        ]);
    }

}
