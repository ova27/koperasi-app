<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Services\AnggotaExitService;
use Illuminate\Http\Request;

class AnggotaExitController extends Controller
{
    public function process(
        Request $request,
        Anggota $anggota,
        AnggotaExitService $service
    ) {
        $this->authorize('manage simpanan anggota');
        $request->validate([
            'alasan' => 'required|in:pensiun,mutasi',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            $service->keluar(
                $anggota->id, 
                $request->alasan,
                $request->keterangan
            );

            return redirect()
                ->route('admin.simpanan.index', $anggota)
                ->with('success', 'Anggota berhasil diproses keluar');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}
