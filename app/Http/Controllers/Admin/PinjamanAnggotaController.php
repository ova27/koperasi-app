<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use Illuminate\Http\Request;

class PinjamanAnggotaController extends Controller
{
    public function index()
    {
        $this->authorize('view pengajuan pinjaman'); // Ketua & Bendahara only
        
        $pinjamansAktif = Pinjaman::with('anggota')
            ->where('status', 'aktif')
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate(5, ['*'], 'aktif_page');

        $pinjamansLunas = Pinjaman::with('anggota')
            ->where('status', 'lunas')
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate(5, ['*'], 'lunas_page');

        return view('admin.pinjaman.data-anggota.index', compact('pinjamansAktif', 'pinjamansLunas'));
    }

    public function lunas()
    {
        $this->authorize('view pengajuan pinjaman'); // Ketua & Bendahara only
        
        $pinjamanLunas = Pinjaman::with(['anggota', 'transaksi'])
            ->where('status', 'lunas')
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate(15);

        return view('admin.pinjaman.data-anggota.lunas', compact('pinjamanLunas'));
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
            'tenor' => 'nullable|integer|min:1|max:60',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $pinjaman->update([
            'tenor' => $request->tenor ?? $pinjaman->tenor,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()
            ->route('admin.pinjaman.data-anggota.index')
            ->with('success', 'Data pinjaman berhasil diperbarui');
    }
}
