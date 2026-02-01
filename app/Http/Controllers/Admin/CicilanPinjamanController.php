<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Services\PinjamanService;
use Illuminate\Http\Request;

class CicilanPinjamanController extends Controller
{
    public function index()
    {
        $this->authorize('manage cicilan pinjaman');
        $pinjamans = Pinjaman::with('anggota')
            ->where('status', 'aktif')
            ->orderBy('tanggal_pinjam')
            ->get();

        return view('admin.pinjaman.cicilan.index', compact('pinjamans'));
    }

    public function create(Pinjaman $pinjaman)
    {
        $this->authorize('manage cicilan pinjaman');

        if ($pinjaman->status !== 'aktif') {
            return redirect()
                ->back()
                ->withErrors('Pinjaman tidak aktif atau sudah lunas.');
        }

        return view('admin.pinjaman.cicilan.create', compact('pinjaman'));
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

        $service->cicil(
            $pinjaman,
            (int) $request->jumlah,
            $request->keterangan
        );

        return redirect()
            ->route('admin.pinjaman.aktif.index')
            ->with('success', 'Cicilan berhasil disimpan');
    }

}