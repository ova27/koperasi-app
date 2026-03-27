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
            ->orderByRaw("CASE WHEN status = 'aktif' THEN 1 ELSE 2 END")
            ->orderByDesc('tanggal_pinjam')
            ->get();

        $pinjamanAktif = $pinjaman->whereIn('status', ['aktif', 'pengajuan', 'disetujui', 'ditolak']);
        $pinjamanLunas = $pinjaman->where('status', 'lunas');

        $totalPinjamanSaya = $pinjamanAktif->sum('jumlah_pinjaman');
        $sisaPinjamanSaya  = $pinjamanAktif->sum('sisa_pinjaman');
        $pinjamanAktifSaya = $pinjamanAktif->isNotEmpty();

        return view('anggota.pinjaman.index', compact(
            'pinjaman',
            'pinjamanAktif',
            'pinjamanLunas',
            'totalPinjamanSaya',
            'sisaPinjamanSaya',
            'pinjamanAktifSaya'
        ));
    }
}