<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPinjaman;
use App\Models\Pinjaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PinjamanAnggotaController extends Controller
{
    public function index()
    {
        $this->authorize('view pengajuan pinjaman');
        $request = request();

        $pinjamansAktif = Pinjaman::with(['anggota', 'transaksi'])
            ->where('status', 'aktif')
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate(10, ['*'], 'aktif_page');

        $pinjamansLunas = Pinjaman::with(['anggota', 'transaksi'])
            ->where('status', 'lunas')
            ->orderByDesc('updated_at')
            ->paginate(10, ['*'], 'lunas_page');

        return view('admin.pinjaman.data-anggota.index', compact(
            'pinjamansAktif',
            'pinjamansLunas',
        ));
    }

    public function lunas()
    {
        $this->authorize('view pengajuan pinjaman'); // Ketua & Bendahara only
        
        $pinjamanLunas = Pinjaman::with(['anggota', 'transaksi'])
            ->where('status', 'lunas')
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('admin.pinjaman.data-anggota.lunas', compact('pinjamanLunas'))->with('tab', 'lunas');
    }


    public function edit(Pinjaman $pinjaman)
    {
        $this->authorize('edit pinjaman'); // Ketua & Bendahara
        
        return view('admin.pinjaman.data-anggota.index', compact('pinjaman'));
    }

    public function update(Request $request, Pinjaman $pinjaman)
    {
        $this->authorize('edit pinjaman');

        $request->validate([
            'tenor' => 'nullable|integer|min:1|max:20',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $tenorLama = $pinjaman->tenor;
        $tenorBaru = $request->tenor ?? $tenorLama;

        $cicilanBaru = $tenorBaru > 0
            ? ceil($pinjaman->sisa_pinjaman / $tenorBaru)
            : $pinjaman->cicilan_per_bulan;

        $pinjaman->update([
            'tenor' => $tenorBaru,
            'cicilan_per_bulan' => $cicilanBaru,
            'keterangan' => $request->keterangan,
        ]);

       return redirect()
            ->route('admin.pinjaman.data-anggota.index')
            ->with('success', 'Data pinjaman berhasil diperbarui');
    }
}
