<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RekeningKoperasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekeningKoperasiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            abort_unless(
                $user instanceof User && $user->hasAnyRole(['admin', 'ketua', 'bendahara']),
                403
            );

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $status = (string) $request->get('status', '');

        $rekeningKoperasis = RekeningKoperasi::query()
            ->where('jenis', 'bank')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('nomor_rekening', 'like', '%' . $search . '%')
                        ->orWhere('nama_pemilik', 'like', '%' . $search . '%');
                });
            })
            ->when(in_array($status, ['1', '0'], true), function ($query) use ($status) {
                $query->where('aktif', $status === '1');
            })
            ->orderByDesc('aktif')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('admin.rekening-koperasi.index', [
            'rekeningKoperasis' => $rekeningKoperasis,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create()
    {
        return view('admin.rekening-koperasi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:rekening_koperasis,nama'],
            'nomor_rekening' => ['required', 'string', 'max:100'],
            'nama_pemilik' => ['required', 'string', 'max:255'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        RekeningKoperasi::create([
            'nama' => $validated['nama'],
            'nomor_rekening' => $validated['nomor_rekening'],
            'nama_pemilik' => $validated['nama_pemilik'],
            'jenis' => 'bank',
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()
            ->route('admin.master.rekening-koperasi.index')
            ->with('success', 'Master rekening koperasi berhasil ditambahkan.');
    }

    public function edit(RekeningKoperasi $rekeningKoperasi)
    {
        return view('admin.rekening-koperasi.edit', [
            'rekeningKoperasi' => $rekeningKoperasi,
        ]);
    }

    public function update(Request $request, RekeningKoperasi $rekeningKoperasi)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:rekening_koperasis,nama,' . $rekeningKoperasi->id],
            'nomor_rekening' => ['required', 'string', 'max:100'],
            'nama_pemilik' => ['required', 'string', 'max:255'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $rekeningKoperasi->update([
            'nama' => $validated['nama'],
            'nomor_rekening' => $validated['nomor_rekening'],
            'nama_pemilik' => $validated['nama_pemilik'],
            'jenis' => 'bank',
            'aktif' => $request->boolean('aktif', false),
        ]);

        return redirect()
            ->route('admin.master.rekening-koperasi.index')
            ->with('success', 'Master rekening koperasi berhasil diperbarui.');
    }

    public function destroy(RekeningKoperasi $rekeningKoperasi)
    {
        if ($rekeningKoperasi->arusKas()->exists()) {
            return back()->with('error', 'Rekening tidak bisa dihapus karena sudah dipakai pada transaksi arus kas.');
        }

        $rekeningKoperasi->delete();

        return redirect()
            ->route('admin.master.rekening-koperasi.index')
            ->with('success', 'Master rekening koperasi berhasil dihapus.');
    }
}
