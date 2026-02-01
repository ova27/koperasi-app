<?php

namespace App\Http\Controllers\Anggota;

use Illuminate\Http\Request;
use App\Models\PengajuanPinjaman;
use App\Models\Pinjaman;
use App\Services\PinjamanService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PengajuanPinjamanController extends Controller
{
    public function create(PinjamanService $pinjamanService)
    {
        $this->authorize('create pinjaman');

        $anggota = Auth::user()->anggota;
        abort_if(! $anggota, 403);

        $riwayatPengajuan = PengajuanPinjaman::where('anggota_id', $anggota->id)
            ->orderByDesc('created_at')
            ->get();

        $pengajuanAktif = $riwayatPengajuan->first(fn ($p) =>
            in_array($p->status, ['diajukan', 'disetujui'])
        );

        // 👇 UI hanya "nanya", bukan mutusin
        $bolehAjukan = $pinjamanService->bolehAjukan($anggota->id);
        $ringkasan = $pinjamanService->ringkasanAnggota($anggota->id);

        return view('anggota.pinjaman.form', compact(
            'anggota',
            'riwayatPengajuan',
            'pengajuanAktif',
            'bolehAjukan',
            'ringkasan'
        ));
    }

    public function store(
        Request $request,
        PinjamanService $service
    ) {
        $request->validate([
            'jumlah_diajukan' => 'required|integer|min:100000',
            'tenor'           => 'required|integer|min:1|max:20', 
            'bulan_pinjam'    => 'required',                     
            'keterangan'      => 'nullable|string',
        ]);

        $this->authorize('create pinjaman');
        $anggota = Auth::user()->anggota;
        abort_if(! $anggota, 403);

        try {
            $service->ajukan(
                anggotaId   : $anggota->id,
                jumlah      : (int) $request->jumlah_diajukan,
                tenor       : (int) $request->tenor,        
                bulan       : $request->bulan_pinjam,       
                userId      : Auth::id(),
                tujuan      : $request->keterangan
            );

            return redirect()
                ->route('anggota.pinjaman.ajukan')
                ->with('success', 'Pengajuan pinjaman berhasil dikirim');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['pengajuan' => $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(PengajuanPinjaman $pengajuan)
    {
        $this->authorize('edit pinjaman');
        abort_if(
            $pengajuan->anggota_id !== Auth::user()->anggota->id,
            403
        );
        abort_if(
            ! in_array($pengajuan->status, ['diajukan','ditolak']),
            403
        );

        return view('anggota.pinjaman.edit', compact('pengajuan'));
    }

    public function update(
        Request $request,
        PengajuanPinjaman $pengajuan,
        PinjamanService $pinjamanService
    ) {
        $this->authorize('edit pinjaman');
        abort_if(
            $pengajuan->anggota_id !== Auth::user()->anggota->id,
            403
        );

        $request->validate([
            'jumlah_diajukan' => 'required|integer|min:1',
            'tenor'           => 'required|integer|min:1|max:20', 
            'bulan_pinjam'    => 'required|date_format:Y-m|after_or_equal:' . now()->format('Y-m'),
            'keterangan'      => 'nullable|string', 
        ]);

        try {
            $pinjamanService->updatePengajuan(
                $pengajuan,
                $request->jumlah_diajukan,
                $request->tenor,
                $request->bulan_pinjam,
                $request->keterangan // Pastikan ini juga diganti
            );

            return redirect()
                ->route('anggota.pinjaman.ajukan')
                ->with('success', 'Pengajuan pinjaman berhasil diperbarui');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['jumlah_diajukan' => $e->getMessage()]);
        }
    }

    public function destroy($id) 
    {
        $this->authorize('delete pinjaman');

        $pengajuan = PengajuanPinjaman::where('id', $id)
            ->where('anggota_id', Auth::user()->anggota->id)
            ->where('status', 'diajukan') 
            ->firstOrFail();

        $pengajuan->delete();
        
        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }
    
}
