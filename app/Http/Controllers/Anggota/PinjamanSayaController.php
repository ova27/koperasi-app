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

        // 🔹 Query dasar (biar gak nulis ulang)
        $pinjaman = Pinjaman::with('transaksi')
            ->where('anggota_id', $anggota->id);

        // 🔹 Pinjaman aktif (pakai get karena butuh sum)
        $pinjamanAktif = (clone $pinjaman)
            ->whereIn('status', ['aktif', 'pengajuan', 'disetujui', 'ditolak'])
            ->orderByRaw("CASE WHEN status = 'aktif' THEN 1 ELSE 2 END")
            ->orderByDesc('tanggal_pinjam')
            ->get();

        // 🔹 Pinjaman lunas (pakai paginate ✅)
        $pinjamanLunas = (clone $pinjaman)
            ->where('status', 'lunas')
            ->orderByDesc('tanggal_pinjam')
            ->paginate(10);

         // 🔹 Perhitungan
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