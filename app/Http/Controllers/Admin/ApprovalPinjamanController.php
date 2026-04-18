<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPinjaman;
use App\Models\Pinjaman;

use App\Services\PinjamanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalPinjamanController extends Controller
{
    public function index()
    {
        $this->authorize('view pengajuan pinjaman');
        $pengajuans = PengajuanPinjaman::with('anggota')
            ->where('status', 'diajukan')
            ->orderBy('tanggal_pengajuan')
            ->paginate(5, ['*'], 'pengajuans_page');

        $riwayatApproval = PengajuanPinjaman::whereIn('status', ['disetujui', 'ditolak', 'dicairkan'])
                        ->orderBy('updated_at', 'desc')
                        ->paginate(10, ['*'], 'riwayatApproval_page');

        return view('admin.pinjaman.pengajuan.index', compact('pengajuans','riwayatApproval'));
    }

    public function show(PengajuanPinjaman $pengajuan)
    {
        $pinjaman = Pinjaman::where('anggota_id', $pengajuan->anggota_id)
                    ->where('status', 'aktif')
                    ->latest()
                    ->first();

        $validationErrors = session('validation_errors');
        // Jika request adalah AJAX, kirimkan view khusus detail saja
        if (request()->ajax()) {
            return view('admin.pinjaman.pengajuan._detail_content', compact('pengajuan', 'pinjaman', 'validationErrors'));
        }

        // Fallback jika diakses manual (opsional)
        return view('admin.pinjaman.pengajuan.show', compact('pengajuan', 'pinjaman'));
    }

    public function setujui(
        Request $request, 
        PengajuanPinjaman $pengajuan,
        PinjamanService $service
    ) {
        $this->authorize('approve pinjaman');
       
        try {
            $request->validate([
                'jumlah_diajukan' => 'required',
                'tenor' => 'required|integer|min:1',
                'bulan_pinjam' => 'required',
            ]);

            $jumlahMurni = str_replace(['Rp', '.', ' '], '', $request->jumlah_diajukan);

            $data = [
                'jumlah_diajukan' => $jumlahMurni,
                'tenor'           => $request->tenor,
                'bulan_pinjam'    => $request->bulan_pinjam . '-01', // Pastikan jadi format Y-m-d
            ];

            $service->setujui($pengajuan, Auth::id(), $data);

            return redirect()
                ->route('admin.pinjaman.data-anggota.index', ['tab' => 'pengajuan'])
                ->with('success', 'Pengajuan berhasil diproses/diperbarui');
         
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('admin.pinjaman.data-anggota.index', ['tab' => 'pengajuan'])
                ->with('validation_errors', $e->errors()) // 🔥 ganti ini
                ->withInput()
                ->with('open_modal_pengajuan', true)
                ->with('pengajuan_id', $pengajuan->id);
        }

        catch (\Exception $e) {
            return redirect()
                ->route('admin.pinjaman.data-anggota.index', ['tab' => 'pengajuan']) // 🔥 sama
                ->with('validation_errors', [
                    'pengajuan' => [$e->getMessage()]
                ])
                ->withInput()
                ->with('open_modal_pengajuan', true)
                ->with('pengajuan_id', $pengajuan->id);
        }
    }

    public function tolak(Request $request, PengajuanPinjaman $pengajuan)
    {
        $this->authorize('reject pinjaman');

        if (! in_array($pengajuan->status, ['diajukan', 'disetujui'])) {
            return back()->withErrors('Pengajuan sudah ditolak');    
        }

        $request->validate([
            'alasan_tolak' => 'required|string|max:255',
        ]);

        $pengajuan->update([
            'status' => 'ditolak',
            'alasan_tolak' => $request->alasan_tolak,
            'tgl_persetujuan' => now(),
        ]);

        return redirect()
            ->route('admin.pinjaman.data-anggota.index', ['tab' => 'pengajuan'])
            ->with('success', 'Pengajuan pinjaman ditolak');
    }

    public function showPreview($id)
    {
        $pinjaman = Pinjaman::with('anggota')->findOrFail($id);

        // ambil pengajuan terakhir (atau sesuai kebutuhan)
        $pengajuan = $pinjaman->anggota
            ->pengajuanPinjamans()
            ->latest()
            ->first();

        return view('admin.pinjaman.pengajuan._preview', compact('pinjaman', 'pengajuan'));
    }
}
