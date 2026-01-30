<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPinjaman;
use App\Services\PinjamanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalPinjamanController extends Controller
{
    public function index()
    {
        $pengajuans = PengajuanPinjaman::with('anggota')
            ->where('status', 'diajukan')
            ->orderBy('tanggal_pengajuan')
            ->get();

        return view('admin.pinjaman.pengajuan.index', compact('pengajuans'));
    }

    public function show(PengajuanPinjaman $pengajuan)
    {
        if ($pengajuan->status !== 'diajukan') {
            abort(404);
        }

        return view('admin.pinjaman.pengajuan.show', compact('pengajuan'));
    }

    public function setujui(
        PengajuanPinjaman $pengajuan,
        PinjamanService $service
    ) {
        $service->setujui($pengajuan, Auth::id());

        return redirect()
            ->route('admin.pinjaman.pengajuan.index')
            ->with('success', 'Pengajuan pinjaman disetujui');
    }

    public function tolak(Request $request, PengajuanPinjaman $pengajuan)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
        ]);

        if ($pengajuan->status !== 'diajukan') {
            abort(400);
        }

        $pengajuan->update([
            'status' => 'ditolak',
            'keterangan' => $request->alasan,
        ]);

        return redirect()
            ->route('admin.pinjaman.pengajuan.index')
            ->with('success', 'Pengajuan pinjaman ditolak');
    }
}
