<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Services\SimpananService;
use App\Services\PinjamanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnggotaController extends Controller
{
    public function index()
    {
        $this->authorize('view anggota list');

        $anggotas = Anggota::orderBy('nama')->get();

        return view('admin.anggota.index', compact('anggotas'));
    }

    public function show(
        Request $request,
        Anggota $anggota,
        SimpananService $simpananService,
        PinjamanService $pinjamanService
    ) {
        $this->authorize('view anggota list');

        $anggota->load([
            'simpanans' => fn ($q) => $q->orderByDesc('tanggal'),
            'pinjamans',
        ]);

        $saldoSimpanan = $simpananService->saldoPerJenis($anggota->id);
        $ringkasanPinjaman = $pinjamanService->ringkasanAnggota($anggota->id);

        // MODAL
        if ($request->ajax()) {

            // hitung ulang biar view modal simpel
            $pokok    = $saldoSimpanan['pokok'] ?? 0;
            $wajib    = $saldoSimpanan['wajib'] ?? 0;
            $sukarela = $saldoSimpanan['sukarela'] ?? 0;
            $total    = $pokok + $wajib + $sukarela;

            return view('admin.anggota._show_modal', compact(
                'anggota',
                'pokok',
                'wajib',
                'sukarela',
                'total',
                'ringkasanPinjaman'
            ));
        }

        // VIEW (FULL PAGE)
        return view('admin.anggota.show', compact(
            'anggota',
            'saldoSimpanan',
            'ringkasanPinjaman'
        ));
    }


    public function create()
    {
        $this->authorize('create anggota');

        return view('admin.anggota.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create anggota');

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'jabatan' => 'nullable|string|max:255',
            'tanggal_masuk' => 'required|date',
        ]);

        Anggota::create([
            ...$data,
            'status' => 'aktif',
        ]);

        return redirect()
            ->route('admin.anggota.index')
            ->with('success', 'Anggota berhasil ditambahkan');
    }

    public function edit(Request $request, Anggota $anggota)
    {
        $this->authorize('edit anggota');

        if ($request->ajax()) {
            return view('admin.anggota._edit_modal', compact('anggota'));
        }

        // fallback: halaman lama tetap bisa dipakai
        return view('admin.anggota.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota)
    {
        $this->authorize('edit anggota');

        if ($request->status === 'tidak_aktif') {
            // logout paksa jika sedang login
            if ($anggota->user && Auth::id() === $anggota->user->id) {
                Auth::logout();
            }
        }

        $request->validate([
            'nip'            => 'nullable|string|max:50',
            'jenis_kelamin'  => 'required|in:L,P',
            'jabatan'        => 'nullable|string|max:100',
            'status'         => 'required|in:aktif,tugas_belajar,cuti,tidak_aktif',
        ]);

        $anggota->update([
            'nip'           => $request->nip,
            'jenis_kelamin' => $request->jenis_kelamin,
            'jabatan'       => $request->jabatan,
            'status'        => $request->status,
        ]);

        return redirect()
            ->route('admin.anggota.index', $anggota)
            ->with('success', 'Data anggota berhasil diperbarui');
    }


    public function nonaktifkan(Request $request, Anggota $anggota)
    {
        $this->authorize('nonaktifkan anggota');

        $request->validate([
            'status' => 'required|in:cuti,tugas_belajar,tidak_aktif',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $anggota->update([
            'status' => $request->status,
        ]);

        // 🔒 Jika benar-benar tidak aktif → cabut role login
        if ($request->status === 'tidak_aktif' && $anggota->user) {
            $anggota->user->removeRole('anggota');
        }

        return back()->with('success', 'Status anggota berhasil diperbarui');
    }
}
