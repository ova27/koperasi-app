<?php

namespace App\Http\Controllers\Anggota;

use Illuminate\Http\Request;
use App\Models\PengajuanPinjaman;
use App\Services\PinjamanService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PengajuanPinjamanController extends Controller
{
    public function create()
    {
        $anggota = Auth::User()->anggota;

        if (!$anggota) {
            abort(403, 'Akun ini belum terdaftar sebagai anggota');
        }

        $pengajuan = PengajuanPinjaman::where('anggota_id', $anggota->id)
            ->where('status', 'diajukan')
            ->first();

        return view('anggota.pinjaman.form', [
            'anggota' => $anggota,
            'pengajuan' => $pengajuan,
            'mode' => $pengajuan ? 'edit' : 'create',
        ]);
    }


    public function store(Request $request, PinjamanService $service)
    {
        $request->validate([
            'jumlah_diajukan' => 'required|integer|min:100000',
            'tujuan' => 'nullable|string',
        ]);

        $anggota = Auth::User()->anggota;

        if (!$anggota) {
            abort(403);
        }

        // 🔍 cek pengajuan aktif
        $pengajuanAktif = PengajuanPinjaman::where('anggota_id', $anggota->id)
            ->where('status', 'diajukan')
            ->first();

        try {
            // ✏️ MODE EDIT
            if ($request->filled('pengajuan_id') && $pengajuanAktif) {

                if ((int) $request->pengajuan_id !== $pengajuanAktif->id) {
                    abort(403);
                }

                $service->updatePengajuan(
                    $pengajuanAktif,
                    (int) $request->jumlah_diajukan,
                    $request->tujuan
                );

                return back()->with('success', 'Pengajuan pinjaman berhasil diperbarui');
            }

            // ❌ BLOK AJUKAN BARU JIKA MASIH ADA PENGAJUAN
            if ($pengajuanAktif) {
                return back()->withErrors([
                    'pengajuan' => 'Masih ada pengajuan pinjaman yang belum diproses. Silakan edit pengajuan tersebut.'
                ]);
            }

            // ➕ MODE CREATE
            $service->ajukan(
                anggotaId: $anggota->id,
                jumlah: (int) $request->jumlah_diajukan,
                userId: \Illuminate\Support\Facades\Auth::id(),
                tujuan: $request->tujuan
            );

            return back()->with('success', 'Pengajuan pinjaman berhasil dikirim');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['pengajuan' => $e->getMessage()])
                ->withInput();
        }
    }


}
