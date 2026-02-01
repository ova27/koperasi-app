<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RekeningKoperasi;
use App\Models\ArusKas;

class SaldoController extends Controller
{
    public function index()
    {
        $this->authorize('view saldo');

        $rekenings = RekeningKoperasi::where('aktif', true)->get();

        $data = $rekenings->map(function ($rekening) {
            $saldo = ArusKas::where('rekening_koperasi_id', $rekening->id)
                ->selectRaw("
                    SUM(
                        CASE 
                            WHEN tipe = 'masuk' THEN jumlah
                            ELSE -jumlah
                        END
                    ) as saldo
                ")
                ->value('saldo') ?? 0;

            return [
                'nama' => $rekening->nama,
                'jenis' => $rekening->jenis,
                'saldo' => $saldo,
            ];
        });

        $totalSaldo = $data->sum('saldo');

        return view('keuangan.saldo', compact('data', 'totalSaldo'));
    }
}
