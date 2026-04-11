<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\ArusKas;
use App\Models\Simpanan;
use App\Services\SimpananService;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index()
    {
        $this->authorize('view simpanan anggota');
        $bulan = now()->format('Y-m');

        // cek apakah simpanan wajib bulan ini sudah digenerate
        $sudahGenerateWajib = ArusKas::where('jenis_arus', 'koperasi')
            ->where('kategori', 'simpanan')
            ->where('sub_kategori', 'wajib')
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->exists();

        // daftar anggota aktif untuk input manual
        $anggotas = Anggota::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('admin.simpanan.index', compact(
            'bulan',
            'sudahGenerateWajib',
            'anggotas',
        ));
    }

    public function create(Anggota $anggota)
    {
        $this->authorize('manage simpanan anggota');
        return view('admin.simpanan.create', compact('anggota'));
    }

    public function store(
        Request $request,
        Anggota $anggota,
        SimpananService $simpananService
    ) {
        $this->authorize('manage simpanan anggota');

        $request->validate([
            'jenis_simpanan' => 'required|in:pokok,wajib,sukarela',
            'jumlah'         => 'required|integer',
            'keterangan'     => 'nullable|string',
        ]);

        try {
            $simpananService->tambah(
                $anggota->id,
                $request->jenis_simpanan,
                $request->jumlah,
                'manual',
                'biasa',
                $request->keterangan
            );

            return redirect()
                ->route('admin.anggota.show', $anggota)
                ->with('success', 'Simpanan berhasil disimpan');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['jumlah' => $e->getMessage()]);
        }
    }

    public function storeManual(
        Request $request,
        SimpananService $simpananService
    ) {
        $this->authorize('manage simpanan anggota');

        $request->validate([
            'anggota_id'     => 'required|exists:anggotas,id',
            'jenis_simpanan' => 'required|in:pokok,wajib,sukarela',
            'jumlah'         => 'required|integer|min:1',
            'keterangan'     => 'nullable|string',
        ]);

        try {
            $simpananService->tambah(
                $request->anggota_id,
                $request->jenis_simpanan,
                $request->jumlah,
                'manual',
                'biasa',
                $request->keterangan
            );

            return back()->with('success', 'Simpanan berhasil disimpan');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['manual' => $e->getMessage()]);
        }
    }

    public function saldo($anggotaId)
    {
        $pokok = Simpanan::where('anggota_id', $anggotaId)
            ->where('jenis_simpanan', 'pokok')
            ->sum('jumlah');

        $wajib = Simpanan::where('anggota_id', $anggotaId)
            ->where('jenis_simpanan', 'wajib')
            ->sum('jumlah');

        $sukarela = Simpanan::where('anggota_id', $anggotaId)
            ->where('jenis_simpanan', 'sukarela')
            ->sum('jumlah');

        return response()->json([
            'pokok' => $pokok,
            'wajib' => $wajib,
            'sukarela' => $sukarela,
            'total' => $pokok + $wajib + $sukarela,
        ]);
    }

    public function ambil(
        Request $request, 
        SimpananService $service
    ){
        $this->authorize('manage simpanan anggota');
        $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            $service->ambil(
                $request->anggota_id,
                $request->jumlah,
                'manual',
                $request->keterangan
            );

            return back()->with('success', 'Pengambilan simpanan berhasil');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['jumlah' => $e->getMessage()]);
        }
    }

}