<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPinjaman;
use App\Models\Pinjaman;
use Illuminate\Http\Request;

class PinjamanAnggotaController extends Controller
{
    public function index()
    {
        $this->authorize('view pengajuan pinjaman'); // Ketua & Bendahara only
        $pinjamansAktif = Pinjaman::with(['anggota', 'transaksi'])
            ->where('status', 'aktif')
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate(10, ['*'], 'aktif_page');

        $pinjamansLunas = Pinjaman::with(['anggota', 'transaksi'])
                        ->where('status', 'lunas')
                        ->orderByDesc('updated_at') // ✅ pakai ini
                        ->paginate(10, ['*'], 'lunas_page');

        $pengajuans = PengajuanPinjaman::with('anggota')
                    ->where('status', 'diajukan')
                    ->orderBy('tanggal_pengajuan')
                    ->paginate(5, ['*'], 'pengajuans_page');

        $riwayatApproval = PengajuanPinjaman::whereIn('status', ['disetujui', 'ditolak', 'dicairkan'])
                        ->orderBy('updated_at', 'desc')
                        ->paginate(10, ['*'], 'riwayatApproval_page');

        return view('admin.pinjaman.data-anggota.index', array_merge(
            compact(
                'pinjamansAktif',
                'pinjamansLunas',
                'pengajuans',
                'riwayatApproval')))
                ->with('tab', request()->get('tab'))
                ->with('success', session('success'))
                ->with('error', session('error'));
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

    public function show(Pinjaman $pinjaman)
    {
        $this->authorize('view pengajuan pinjaman');

        return view('admin.pinjaman.data-anggota.show', compact('pinjaman'));
    }

    public function edit(Pinjaman $pinjaman)
    {
        $this->authorize('edit pinjaman'); // Ketua & Bendahara
        
        return view('admin.pinjaman.data-anggota.edit', compact('pinjaman'));
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
            ->route('admin.pinjaman.data-anggota.index', ['tab' => 'aktif'])
            ->with('success', 'Data pinjaman berhasil diperbarui');
    }
}
