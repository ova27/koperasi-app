<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Services\AnggotaExitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnggotaExitController extends Controller
{
    public function confirm(Anggota $anggota)
    {
        $this->authorize('manage simpanan anggota');
        // hitung saldo per jenis
        $saldos = Simpanan::where('anggota_id', $anggota->id)
            ->select('jenis_simpanan', DB::raw('SUM(jumlah) as total'))
            ->groupBy('jenis_simpanan')
            ->get()
            ->keyBy('jenis_simpanan');

        return view('admin.anggota.keluar', compact(
            'anggota',
            'saldos'
        ));
    }

    public function process(
        Request $request,
        Anggota $anggota,
        AnggotaExitService $service
    ) {
        $this->authorize('manage simpanan anggota');
        $request->validate([
            'alasan' => 'required|in:pensiun,mutasi',
        ]);

        try {
            $service->keluar($anggota->id, $request->alasan);

            return redirect()
                ->route('admin.anggota.show', $anggota)
                ->with('success', 'Anggota berhasil diproses keluar');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}
