<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Services\SimpananService;
use Illuminate\Http\Request;
use App\Models\ArusKas;

class SimpananController extends Controller
{
    public function index()
    {
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
            'anggotas'
        ));
    }

    public function create(Anggota $anggota)
    {
        return view('admin.simpanan.create', compact('anggota'));
    }

    public function store(
        Request $request,
        Anggota $anggota,
        SimpananService $simpananService
    ) {
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


    public function ambil(
        Request $request, 
        Anggota $anggota, 
        SimpananService $service
    ){
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            $service->ambil(
                $anggota->id,
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