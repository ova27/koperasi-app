<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use App\Models\TransaksiPinjaman;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPinjamanExport;

class LaporanPinjamanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();

        // transaksi bulan ini
        $transaksis = TransaksiPinjaman::with('pinjaman.anggota')
            ->whereBetween('tanggal', [$start, $end])
            ->get();

        $totalPencairan = $transaksis
            ->whereIn('jenis', ['pencairan', 'topup'])
            ->sum('jumlah');

        $totalCicilan = $transaksis
            ->where('jenis', 'cicilan')
            ->sum('jumlah');

        $totalPelunasan = $transaksis
            ->where('jenis', 'pelunasan')
            ->count();

        // snapshot pinjaman akhir bulan
        $pinjamans = Pinjaman::with('anggota')->get();

        return view('admin.laporan.pinjaman.index', [
            'bulan' => $bulan,
            'totalPencairan' => $totalPencairan,
            'totalCicilan' => $totalCicilan,
            'totalPelunasan' => $totalPelunasan,
            'pinjamans' => $pinjamans,
            'transaksis' => $transaksis,
        ]);
    }

    public function export(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        return Excel::download(
            new LaporanPinjamanExport($bulan),
            'laporan-pinjaman-' . $bulan . '.xlsx'
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
