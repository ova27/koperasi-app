<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Services\PinjamanService;
use Illuminate\Http\Request;

class CicilanPinjamanController extends Controller
{
    public function create(Pinjaman $pinjaman)
    {
        $this->authorize('manage cicilan pinjaman');

        if ($pinjaman->status !== 'aktif') {
            return redirect()
                ->back()
                ->withErrors('Pinjaman tidak aktif atau sudah lunas.');
        }

        $suggestedCicilan = null;
        if ($pinjaman->tenor > 0) {
            $suggestedCicilan = (int) ceil($pinjaman->sisa_pinjaman / $pinjaman->tenor);
        }

        return view('admin.pinjaman.cicilan.create', compact('pinjaman', 'suggestedCicilan'));
    }

    public function store(
        Request $request,
        Pinjaman $pinjaman,
        PinjamanService $service
    ) {
        $this->authorize('manage cicilan pinjaman');

        if ($pinjaman->status !== 'aktif') {
            return redirect()
                ->back()
                ->withErrors('Pinjaman tidak aktif atau sudah lunas.');
        }

        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Validasi tenor & minimal cicilan here untuk hasil pesan UI lebih baik
        if ($pinjaman->tenor && $pinjaman->tenor > 0) {
            $minCicilan = (int) ceil($pinjaman->sisa_pinjaman / $pinjaman->tenor);
            if ((int) $request->jumlah < $minCicilan) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['jumlah' => 'Jumlah cicilan minimal Rp ' . number_format($minCicilan, 0, ',', '.') . ' berdasarkan tenor saat ini']);
            }
        }

        if ((int) $request->jumlah > $pinjaman->sisa_pinjaman) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['jumlah' => 'Jumlah cicilan tidak boleh lebih dari sisa pinjaman']);
        }

        $service->cicil(
            $pinjaman,
            (int) $request->jumlah,
            $request->keterangan
        );

        // Jika berasal dari data pinjaman anggota, kembali ke sana agar perubahan
        // pelunasan langsung terlihat di tabel Pinjaman Lunas Anggota.
        return redirect()
            ->to(url()->previous() ?: route('admin.pinjaman.data-anggota.index'))
            ->with('success', 'Cicilan berhasil disimpan');
    }

}