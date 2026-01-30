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
        $service->cairkan($pengajuan, Auth::id());

        return redirect()
            ->route('admin.pinjaman.pencairan.index')
            ->with('success', 'Pinjaman berhasil dicairkan');
    }
}
