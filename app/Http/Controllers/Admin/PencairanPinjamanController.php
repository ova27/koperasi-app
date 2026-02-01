<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPinjaman;
use App\Services\PinjamanService;
use Illuminate\Support\Facades\Auth;

class PencairanPinjamanController extends Controller
{
    public function index()
    {
        $this->authorize('pencairan pinjaman');
        $pengajuans = PengajuanPinjaman::with('anggota')
            ->where('status', 'disetujui')
            ->orderBy('tanggal_persetujuan')
            ->get();

        return view('admin.pinjaman.pencairan.index', compact('pengajuans'));
    }

    public function process(
        PengajuanPinjaman $pengajuan,
        PinjamanService $service
    ) {
        $this->authorize('pencairan pinjaman');
        abort_if($pengajuan->status !== 'disetujui', 400);
        
        $service->cairkan($pengajuan, Auth::id());

        return redirect()
            ->route('admin.pinjaman.pencairan.index')
            ->with('success', 'Pinjaman berhasil dicairkan');
    }
}
