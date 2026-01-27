<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Services\SimpananService;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function create(Anggota $anggota)
    {
        return view('admin.simpanan.create', compact('anggota'));
    }

    public function store(
        Request $request,
        Anggota $anggota,
        SimpananService $simpananService
    ) {
        $request->validate([
            'jenis_simpanan' => 'required|in:pokok,wajib,sukarela',
            'jumlah'         => 'required|integer',
            'keterangan'     => 'nullable|string',
        ]);

        try {
            $simpananService->tambah(
                $anggota->id,
                $request->jenis_simpanan,
                $request->jumlah,
                'manual',
                'biasa',
                $request->keterangan
            );

            return redirect()
                ->route('admin.anggota.show', $anggota)
                ->with('success', 'Simpanan berhasil disimpan');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['jumlah' => $e->getMessage()]);
        }
    }
}