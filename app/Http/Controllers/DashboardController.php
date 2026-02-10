<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\PengajuanPinjaman;
use App\Models\TransaksiPinjaman;

class DashboardController extends Controller
{
    public function index()
    {
        $bulan = now()->translatedFormat('F');
        $tahun = now()->year;

        $anggotaAktif = Anggota::where('status', 'aktif')->count();
        $pinjamanAktif = Pinjaman::where('status', 'aktif')->count();
        $antrianPinjaman = PengajuanPinjaman::whereIn('status', ['diajukan','disetujui'])->count();
        $simpananPokok = Simpanan::where('jenis_simpanan', 'pokok')
                        ->sum('jumlah');
        $simpananWajib = Simpanan::where('jenis_simpanan', 'wajib')
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->sum('jumlah');
        $simpananSukarela = Simpanan::where('jenis_simpanan', 'sukarela')
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->sum('jumlah');
        $totalSimpanan = Simpanan::sum('jumlah');
        $totalPinjaman = Pinjaman::where('status', 'aktif')->sum('jumlah_pinjaman');
        $sisaPinjamanAktif = Pinjaman::where('status', 'aktif')
                        ->get()
                        ->sum(function ($pinjaman) {
                            $totalCicilan = TransaksiPinjaman::where('pinjaman_id', $pinjaman->id)
                                ->where('jenis', 'cicilan')
                                ->sum('jumlah');
            return max(0, $pinjaman->jumlah_pinjaman - $totalCicilan);
        });

        $lastUpdated = collect([
                        Simpanan::max('updated_at'),
                        Pinjaman::max('updated_at'),
                        Anggota::max('updated_at'),
                    ])->filter()->max();
                    
        return view('dashboard', [
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'anggotaAktif'      => $anggotaAktif,
            'totalSimpanan'     => $totalSimpanan,
            'totalPinjaman'     => $totalPinjaman,
            'pinjamanAktif'     => $pinjamanAktif,
            'antrianPinjaman'   => $antrianPinjaman,
            'simpananPokok'     => $simpananPokok,
            'simpananWajib'     => $simpananWajib,
            'simpananSukarela'  => $simpananSukarela,
            'sisaPinjamanAktif' => $sisaPinjamanAktif,
            'lastUpdated'       => $lastUpdated
                                    ? Carbon::parse($lastUpdated)
                                    : null,
        ]);
    }

}
