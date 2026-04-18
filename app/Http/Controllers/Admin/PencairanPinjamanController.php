<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPinjaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\PinjamanService;

class PencairanPinjamanController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view pengajuan pinjaman');

        $p_sort = $request->get('p_sort', 'tanggal_persetujuan');
        $p_direction = $request->get('p_direction', 'desc');
        $allowedSorts = [
            'tanggal_pengajuan',
            'tanggal_persetujuan',
            'jumlah_diajukan',
            'bulan_pinjam',
            'tenor',
        ];
        if (!in_array($p_sort, $allowedSorts)) $p_sort = 'tanggal_persetujuan';
        if (!in_array($p_direction, ['asc', 'desc'])) $p_direction = 'desc';

        $pencairanSiapCair = PengajuanPinjaman::with('anggota')
            ->where('status', 'disetujui')
            ->orderBy($p_sort, $p_direction)
            ->paginate(10, ['*'], 'p_page')
            ->appends($request->query());

        $r_sort = $request->get('r_sort', 'updated_at');
        $r_direction = $request->get('r_direction', 'desc');
        if (!in_array($r_sort, ['updated_at', 'jumlah_diajukan', 'tenor'])) {
            $r_sort = 'updated_at';
        }
        if (!in_array($r_direction, ['asc', 'desc'])) {
            $r_direction = 'desc';
        }

        $riwayatPencairan = PengajuanPinjaman::with('anggota.pinjamanAktif.transaksi', 'pinjaman.transaksi')
            ->where('status', 'dicairkan')
            ->where(function ($q) {
                $q->whereHas('pinjaman', function ($pinjaman) {
                    $pinjaman->where('status', 'aktif')
                        ->whereDoesntHave('transaksi', function ($tx) {
                            $tx->where('jenis', 'pelunasan');
                        });
                })
                ->orWhere(function ($topup) {
                    $topup->whereDoesntHave('pinjaman')
                        ->whereHas('anggota.pinjamanAktif');
                });
            })
            ->orderBy($r_sort, $r_direction)
            ->paginate(10, ['*'], 'r_page')
            ->appends($request->query());

        return view('admin.pinjaman.pencairan.index', compact(
            'pencairanSiapCair',
            'riwayatPencairan',
            'p_sort',
            'p_direction',
            'r_sort',
            'r_direction'
        ));
    }

    public function processPencairan(PengajuanPinjaman $pengajuan, PinjamanService $service)
    {
        $this->authorize('pencairan pinjaman');
        abort_if($pengajuan->status !== 'disetujui', 400);
        $service->cairkan($pengajuan, Auth::id());
        return redirect()->route('admin.pinjaman.pencairan.index')
            ->with('success', 'Pinjaman berhasil dicairkan');
    }

    public function batalPencairan(PengajuanPinjaman $pengajuan)
    {
        $this->authorize('pencairan pinjaman');
        if ($pengajuan->status !== 'dicairkan') {
            return back()->with('error', 'Status tidak valid.');
        }
        try {
            DB::transaction(function () use ($pengajuan) {
                $pengajuan->refresh();
                $pinjaman = $pengajuan->pinjaman;
                if (!$pinjaman) {
                    throw new \Exception('Data pinjaman tidak ditemukan.');
                }
                $sudahAdaCicilan = $pinjaman->transaksi()
                    ->where('jenis', 'cicilan')
                    ->exists();
                $sudahLunas = $pinjaman->transaksi()
                    ->where('jenis', 'pelunasan')
                    ->exists();
                if ($sudahAdaCicilan && !$sudahLunas) {
                    throw new \Exception('Tidak bisa dibatalkan, cicilan sudah berjalan dan belum lunas.');
                }
                $pinjaman->update([
                    'status' => 'dibatalkan'
                ]);
                $pengajuan->update([
                    'status' => 'disetujui'
                ]);
            });
            return redirect()->route('admin.pinjaman.pencairan.index')
                ->with('success', 'Pencairan berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.pinjaman.pencairan.index')
                ->with('error', $e->getMessage());
        }
    }
}