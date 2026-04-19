<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPinjaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\PinjamanService;
use Illuminate\Validation\ValidationException;

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

        $pinjamanBelumLunas = function ($pinjaman) {
            $pinjaman->where('status', 'aktif')
                ->where('sisa_pinjaman', '>', 0)
                ->whereDoesntHave('transaksi', function ($tx) {
                    $tx->where('jenis', 'pelunasan');
                });
        };

        $riwayatPencairan = PengajuanPinjaman::with([
                'anggota.pinjamanAktif.transaksi',
                'pinjaman.transaksi',
            ])
            ->where('status', 'dicairkan')
            ->where(function ($q) use ($pinjamanBelumLunas) {
                $q->whereHas('pinjaman', $pinjamanBelumLunas)
                ->orWhere(function ($topup) use ($pinjamanBelumLunas) {
                    $topup->whereDoesntHave('pinjaman')
                        ->whereHas('anggota.pinjamans', function ($pinjaman) use ($pinjamanBelumLunas) {
                            $pinjamanBelumLunas($pinjaman);
                            $pinjaman->whereColumn('pinjamans.tanggal_pinjam', '<=', 'pengajuan_pinjaman.tanggal_pencairan');
                        });
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

    public function processPencairan(Request $request, PengajuanPinjaman $pengajuan, PinjamanService $service)
    {
        $this->authorize('pencairan pinjaman');
        abort_if($pengajuan->status !== 'disetujui', 400);

        try {
            $data = $request->validate([
                'jumlah_diajukan' => ['required', 'integer', 'min:100000'],
                'tenor' => ['required', 'integer', 'min:1', 'max:20'],
                'bulan_pinjam' => ['required', 'date_format:Y-m'],
                'tujuan' => ['nullable', 'string', 'max:255'],
            ]);

            $data['bulan_pinjam'] = $data['bulan_pinjam'] . '-01';

            $service->cairkan($pengajuan, Auth::id(), $data);

            return redirect()->route('admin.pinjaman.pencairan.index')
                ->with('success', 'Pinjaman berhasil dicairkan');
        } catch (ValidationException $e) {
            return redirect()->route('admin.pinjaman.pencairan.index')
                ->withErrors($e->errors())
                ->withInput()
                ->with('open_pencairan_modal', $pengajuan->id);
        } catch (\Exception $e) {
            return redirect()->route('admin.pinjaman.pencairan.index')
                ->withErrors(['pencairan' => $e->getMessage()])
                ->withInput()
                ->with('open_pencairan_modal', $pengajuan->id);
        }
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
                    $pinjamanAktif = $pengajuan->anggota?->pinjamanAktif;
                    $cicilanSudahBerjalan = $pinjamanAktif?->transaksi()
                        ->where('jenis', 'cicilan')
                        ->exists();

                    if ($pinjamanAktif && $cicilanSudahBerjalan) {
                        throw new \Exception('Top-up tidak bisa dibatalkan karena cicilan pinjaman aktif sudah berjalan.');
                    }

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
