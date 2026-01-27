<?php

namespace App\Http\Controllers\Admin;
use App\Services\SimpananService;
use App\Services\PinjamanService;
use App\Http\Controllers\Controller;
use App\Models\Anggota;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggotas = Anggota::orderBy('nama')->get();

        return view('admin.anggota.index', compact('anggotas'));
    }

    public function show(
        Anggota $anggota,
        SimpananService $simpananService,
        PinjamanService $pinjamanService
    ) {
        $anggota->load([
            'simpanans' => function ($q) {
                $q->orderByDesc('tanggal');
            },
            'pinjamans',
        ]);

        $saldoSimpanan = $simpananService
            ->saldoPerJenis($anggota->id);

        $ringkasanPinjaman = $pinjamanService
            ->ringkasanAnggota($anggota->id);

        return view(
            'admin.anggota.show',
            compact('anggota', 'saldoSimpanan', 'ringkasanPinjaman')
        );
    }
}
