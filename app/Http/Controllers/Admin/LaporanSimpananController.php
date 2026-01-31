<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\SimpananBulananExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ClosingService;
use App\Models\ClosingBulan;

class LaporanSimpananController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('lihat-laporan-simpanan');
        // default: bulan ini
        $bulan = $request->get('bulan', now()->format('Y-m'));

        $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();

        $data = Simpanan::whereBetween('tanggal', [$start, $end])
            ->selectRaw('
                jenis_simpanan,
                SUM(jumlah) as total
            ')
            ->groupBy('jenis_simpanan')
            ->get()
            ->keyBy('jenis_simpanan');
        
        $rincian = Simpanan::with('anggota')
            ->whereBetween('tanggal', [$start, $end])
            ->selectRaw('
                anggota_id,
                jenis_simpanan,
                SUM(jumlah) as total
            ')
            ->groupBy('anggota_id', 'jenis_simpanan')
            ->get();

        $isLocked = ClosingBulan::where('bulan', $bulan)
            ->where('jenis', 'simpanan')
            ->exists();

        return view('admin.laporan.simpanan-bulanan', [
            'bulan' => $bulan,
            'data' => $data,
            'rincian' => $rincian,
            'isLocked' => $isLocked,
        ]);
    }

    public function export(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        return Excel::download(
            new SimpananBulananExport($bulan),
            "laporan-simpanan-{$bulan}.xlsx"
        );
    }

    public function lock(Request $request, ClosingService $closingService)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        $closingService->lock($bulan, 'simpanan');

        return redirect()
            ->back()
            ->with('success', "Simpanan bulan {$bulan} berhasil ditutup");
    }
}
