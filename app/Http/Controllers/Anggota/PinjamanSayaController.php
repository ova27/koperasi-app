<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use Illuminate\Support\Facades\Auth;

class PinjamanSayaController extends Controller
{
    public function index()
    {
        $this->authorize('view pinjaman saya');

        $anggota = Auth::user()->anggota;
        abort_if(! $anggota, 403);

        $pinjaman = Pinjaman::with('transaksi')
            ->where('anggota_id', $anggota->id)
            ->orderByDesc('tanggal_pinjam')
            ->get();

        $totalPinjamanSaya = $pinjaman->sum('jumlah_pinjaman');
        $sisaPinjamanSaya  = $pinjaman->sum('sisa_pinjaman');
        $pinjamanAktifSaya = $pinjaman->where('status', 'aktif')->count() > 0;

        return view('anggota.pinjaman.index', compact(
            'pinjaman',
            'totalPinjamanSaya',
            'sisaPinjamanSaya',
            'pinjamanAktifSaya'
        ));
    }
}