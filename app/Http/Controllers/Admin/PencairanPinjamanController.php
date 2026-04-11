<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PengajuanPinjaman;
use App\Services\PinjamanService;
use Illuminate\Support\Facades\Auth;

class PencairanPinjamanController extends Controller
{
    public function index()
    {
        $this->authorize('pencairan pinjaman');
        $request = request();

        /* TABEL 1. SIAP DICAIRKAN */
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

        $pengajuans = PengajuanPinjaman::with('anggota')
            ->where('status', 'disetujui')
            ->orderBy($p_sort, $p_direction)
            ->paginate(10, ['*'], 'p_page')
            ->appends($request->query());

        /* TABEL 2. RIWAYAT PENCAIRAN */
        $r_sort = $request->get('r_sort', 'updated_at');
        $r_direction = $request->get('r_direction', 'desc');

        if (!in_array($r_sort, ['updated_at', 'jumlah_diajukan', 'tenor'])) {
            $r_sort = 'updated_at';
        }
        if (!in_array($r_direction, ['asc', 'desc'])) {
            $r_direction = 'desc';
        }

        $riwayatPencairan = PengajuanPinjaman::with('anggota', 'pinjaman.transaksi')
            ->where('status', 'dicairkan')
            ->whereHas('pinjaman', function ($q) {
                $q->where('status', 'aktif');
            })
            ->whereDoesntHave('pinjaman.transaksi', function ($q) {
                $q->where('jenis', 'pelunasan');
            })
            ->orderBy($r_sort, $r_direction)
            ->paginate(10, ['*'], 'r_page')
            ->appends($request->query());
        
        return view('admin.pinjaman.pencairan.index', compact(
                    'pengajuans', 'riwayatPencairan',
                    'p_sort','p_direction',
                    'r_sort','r_direction'
        ));
    }

    public function process(
        PengajuanPinjaman $pengajuan,
        PinjamanService $service
    ) {
        $this->authorize('pencairan pinjaman');
        abort_if($pengajuan->status !== 'disetujui', 400);
        
        $service->cairkan($pengajuan, Auth::id());

        return back()->with('success', 'Pinjaman berhasil dicairkan');
    }

    public function batalPencairan(PengajuanPinjaman $pengajuan)
    {
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

                // update pinjaman (WAJIB)
                $pinjaman->update([
                    'status' => 'dibatalkan'
                ]);

                // rollback pengajuan
                $pengajuan->update([
                    'status' => 'disetujui'
                ]);
            });

            return back()->with('success', 'Pencairan berhasil dibatalkan.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
