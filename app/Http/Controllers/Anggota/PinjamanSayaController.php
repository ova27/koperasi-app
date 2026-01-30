<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use Illuminate\Support\Facades\Auth;

class PinjamanSayaController extends Controller
{
    public function index()
    {
        $anggota = Auth::user()->anggota;

        $pinjaman = Pinjaman::with('transaksis')
            ->where('anggota_id', $anggota->id)
            ->orderByDesc('tanggal_pinjam')
            ->get();

        return view('anggota.pinjaman.index', compact('pinjaman'));
    }
}
