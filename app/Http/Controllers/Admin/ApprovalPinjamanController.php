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

        $riwayatApproval = PengajuanPinjaman::whereIn('status', ['disetujui', 'ditolak', 'dicairkan'])
                        ->orderBy('updated_at', 'desc')
                        ->get();

        return view('admin.pinjaman.pengajuan.index', compact('pengajuans','riwayatApproval'));
    }

    public function show(PengajuanPinjaman $pengajuan)
    {
        // Jika request adalah AJAX, kirimkan view khusus detail saja
        if (request()->ajax()) {
            return view('admin.pinjaman.pengajuan._detail_content', compact('pengajuan'));
        }

        // Fallback jika diakses manual (opsional)
        return view('admin.pinjaman.pengajuan.show', compact('pengajuan'));
    }

    public function setujui(
        Request $request, 
        PengajuanPinjaman $pengajuan,
        PinjamanService $service
    ) {
        try {
            $jumlahMurni = str_replace(['Rp', '.', ' '], '', $request->jumlah_diajukan);

            $data = [
                'jumlah_diajukan' => $jumlahMurni,
                'tenor'           => $request->tenor,
                'bulan_pinjam'    => $request->bulan_pinjam . '-01', // Pastikan jadi format Y-m-d
            ];

            $service->setujui($pengajuan, Auth::id(), $data);

            return redirect()
                ->route('admin.pinjaman.pengajuan.index')
                ->with('success', 'Pengajuan berhasil diproses/diperbarui');
                
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function tolak(Request $request, PengajuanPinjaman $pengajuan)
    {
        if (!in_array($pengajuan->status, ['diajukan', 'disetujui'])) {
            return redirect()->back()->withErrors('Gagal: Pengajuan sudah dicairkan.');
        }

        $request->validate([
            'alasan' => 'required|string|max:255',
        ]);

        $pengajuan->update([
            'status' => 'ditolak',
            'keterangan' => $request->alasan,
            'tgl_persetujuan' => now(),
        ]);

        return redirect()
            ->route('admin.pinjaman.pengajuan.index')
            ->with('success', 'Pengajuan pinjaman ditolak');
    }
}
