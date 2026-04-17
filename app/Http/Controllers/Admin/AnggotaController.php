<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Services\PinjamanService;
use App\Services\SimpananService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view anggota list');

        $search = $request->query('search');

        $allowedSorts = ['nama', 'jabatan', 'status', 'tanggal_masuk'];
        $sort = $request->query('sort', 'nama');
        $direction = $request->query('direction', 'asc');

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'nama';
        }

        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $anggotas = Anggota::when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                      ->orWhere('jabatan', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        // ambil alasan keluar terakhir untuk semua anggota yang tidak aktif
        $alasanKeluarMap = \App\Models\Simpanan::whereIn('anggota_id', $anggotas->pluck('id'))
            ->whereIn('alasan', ['pensiun', 'mutasi'])
            ->orderByDesc('tanggal')
            ->get()
            ->groupBy('anggota_id')
            ->map(fn($items) => $items->first()->alasan);

        return view('admin.anggota.index', compact(
            'anggotas', 'search', 'sort', 'direction', 'alasanKeluarMap'
        ));
    }

    public function show(
        Request $request,
        Anggota $anggota,
        SimpananService $simpananService,
        PinjamanService $pinjamanService
    ) {
        $this->authorize('view anggota list');
        
        // Check if user can view full details (simpanan & pinjaman)
        // Only admin/bendahara/ketua can see financial details of other anggota
        $canViewFullDetails = Gate::allows('manage simpanan anggota') ||
                             Gate::allows('nonaktifkan anggota') ||
                             Gate::allows('manage users') ||
                             Gate::allows('view pengajuan pinjaman');

        $anggota->loadMissing(['user', 'rekeningAktif']);

        // ambil alasan keluar terakhir untuk anggota ini
        $alasanKeluarMap = [
            $anggota->id => \App\Models\Simpanan::where('anggota_id', $anggota->id)
                        ->whereIn('alasan', ['pensiun', 'mutasi'])
                        ->latest('tanggal')
                        ->first()?->alasan,
        ];

        // MODAL AJAX: cukup ambil data ringkas agar respon cepat
        if ($request->ajax()) {
            $pokok = 0;
            $wajib = 0;
            $sukarela = 0;
            $total = 0;
            $ringkasanPinjaman = ['aktif' => 0, 'lunas' => 0, 'sisa' => 0];

            if ($canViewFullDetails) {
                $saldoSimpanan = $simpananService->saldoPerJenis($anggota->id);
                $ringkasanPinjaman = $pinjamanService->ringkasanAnggota($anggota->id);

                $pokok    = $saldoSimpanan['pokok'] ?? 0;
                $wajib    = $saldoSimpanan['wajib'] ?? 0;
                $sukarela = $saldoSimpanan['sukarela'] ?? 0;
                $total    = $pokok + $wajib + $sukarela;
            }

            return view('admin.anggota._show_modal', compact(
                'anggota',
                'pokok',
                'wajib',
                'sukarela',
                'total',
                'ringkasanPinjaman',
                'canViewFullDetails',
                'alasanKeluarMap'
            ));
        }

        $status = $request->get('status_pinjaman');

        // 🔹 Query dasar (biar gak nulis ulang)
        $queryPinjaman = Pinjaman::with('transaksi')
                        ->where('anggota_id', $anggota->id);
        $pinjamans = (clone $queryPinjaman)
                    ->when($status, fn($q) => $q->where('status', $status))
                    ->orderByDesc('tanggal_pinjam')
                    ->paginate(5, ['*'], 'pinjaman_page')
                    ->withQueryString();

        $pinjamanAktif = (clone $queryPinjaman)
                    ->whereIn('status', ['aktif', 'pengajuan', 'disetujui'])
                    ->orderByRaw("CASE WHEN status = 'aktif' THEN 1 ELSE 2 END")
                    ->orderByDesc('tanggal_pinjam')
                    ->get();

        $pinjamanLunas = (clone $queryPinjaman)
                    ->where('status', 'lunas')
                    ->orderByDesc('tanggal_pinjam')
                    ->paginate(10, ['*'], 'pinjaman_lunas_page')
                    ->withQueryString();
            
        $jenis = $request->get('jenis'); // pokok / wajib / sukarela
        $simpanans = $anggota->simpanans()
            ->when($jenis, fn($q) => $q->where('jenis_simpanan', $jenis))
            ->orderByDesc('tanggal')
            ->paginate(5, ['*'], 'simpanan_page')
            ->withQueryString();

        $saldoSimpanan = $simpananService->saldoPerJenis($anggota->id);
        $ringkasanPinjaman = $pinjamanService->ringkasanAnggota($anggota->id);

        // VIEW (FULL PAGE)
        return view('admin.anggota.show', compact(
            'anggota',
            'saldoSimpanan',
            'ringkasanPinjaman',
            'canViewFullDetails',
            'alasanKeluarMap',
            'simpanans',
            'pinjamans',
            'pinjamanAktif',
            'pinjamanLunas'
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

        return redirect()->route('admin.anggota.show', [
            'anggota' => $anggota,
            'open_edit' => 1,
        ]);
    }

    public function update(Request $request, Anggota $anggota)
    {
        $this->authorize('edit anggota');

        $validated = $request->validate([
            'nama'           => ['required', 'string', 'max:255'],
            'email'          => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($anggota->user_id),
            ],
            'nip'            => ['nullable', 'string', 'max:50', Rule::unique('anggotas', 'nip')->ignore($anggota->id)],
            'jenis_kelamin'  => ['required', 'in:L,P'],
            'jabatan'        => ['nullable', 'string', 'max:100'],
            'tanggal_masuk'  => ['required', 'date'],
            'tanggal_keluar' => ['nullable', 'date', 'after_or_equal:tanggal_masuk'],
            'status'         => ['required', 'in:aktif,tugas_belajar,cuti,tidak_aktif'],
            'nama_bank'      => ['nullable', 'string', 'max:100'],
            'nomor_rekening' => ['nullable', 'string', 'max:50'],
            'nama_pemilik'   => ['nullable', 'string', 'max:255'],
        ]);

        if (($validated['status'] ?? null) === 'tidak_aktif' && $anggota->status !== 'tidak_aktif') {
            $message = 'Status Pensiun/Mutasi tidak bisa diubah dari Edit Profil. Gunakan proses keluar anggota setelah pengembalian seluruh simpanan.';

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => ['status' => [$message]],
                ], 422);
            }

            return back()->withErrors(['status' => $message])->withInput();
        }

        if ($validated['status'] === 'tidak_aktif') {
            // logout paksa jika sedang login
            if ($anggota->user && Auth::id() === $anggota->user->id) {
                Auth::logout();
            }
        }

        $anggota->update([
            'nama'          => $validated['nama'],
            'nip'           => $validated['nip'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'jabatan'       => $validated['jabatan'],
            'status'        => $validated['status'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'tanggal_keluar' => $validated['status'] === 'tidak_aktif'
                ? ($validated['tanggal_keluar'] ?? $anggota->tanggal_keluar)
                : null,
        ]);

        if ($anggota->user && !empty($validated['email'])) {
            $anggota->user->update(['email' => $validated['email']]);
        }

        $hasRekeningInput = filled($validated['nama_bank']) ||
            filled($validated['nomor_rekening']) ||
            filled($validated['nama_pemilik']);

        if ($hasRekeningInput) {
            $anggota->rekeningAktif()->updateOrCreate(
                ['aktif' => true],
                [
                    'nama_bank' => $validated['nama_bank'] ?? '-',
                    'nomor_rekening' => $validated['nomor_rekening'] ?? '-',
                    'nama_pemilik' => $validated['nama_pemilik'] ?? $validated['nama'],
                    'aktif' => true,
                ]
            );
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data anggota berhasil diperbarui'
            ]);
        }

        return redirect()
            ->route('admin.anggota.index')
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
