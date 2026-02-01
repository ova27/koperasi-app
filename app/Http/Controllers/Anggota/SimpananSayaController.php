<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use Illuminate\Support\Facades\Auth;

class SimpananSayaController extends Controller
{
    public function index()
    {
        $this->authorize('view simpanan saya');

        $anggota = Auth::user()->anggota;
        abort_if(! $anggota, 403);

        $simpanan = Simpanan::where('anggota_id', $anggota->id)
            ->orderByDesc('tanggal')
            ->get();
    
        $saldo = $simpanan
            ->groupBy('jenis_simpanan')
            ->map(fn ($items) => $items->sum('jumlah'));

        return view('anggota.simpanan.index', compact(
            'simpanan',
            'saldo'
        ));
    }
}
