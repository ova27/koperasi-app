<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArusKas;
use App\Models\RekeningKoperasi;
use Illuminate\Http\Request;
use App\Exports\ArusOperasionalExport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ArusOperasionalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view arus operasional');

        $bulan = $request->get('bulan', now()->format('Y-m'));

        $items = ArusKas::with('rekening')
            ->where('jenis_arus', 'operasional')
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->paginate(20);

        $rekenings = RekeningKoperasi::where('aktif', true)
            ->orderBy('nama')
            ->get();

        return view('keuangan.arus-operasional', compact('items', 'bulan', 'rekenings'));
    }

    public function store(Request $request)
    {
        $this->authorize('view arus operasional');

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'rekening_koperasi_id' => ['required', 'exists:rekening_koperasis,id'],
            'tipe' => ['required', 'in:masuk,keluar'],
            'kategori' => ['required', 'string', 'max:100'],
            'jumlah' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string'],
        ]);

        ArusKas::create([
            'tanggal' => $validated['tanggal'],
            'rekening_koperasi_id' => $validated['rekening_koperasi_id'],
            'jenis_arus' => 'operasional',
            'tipe' => $validated['tipe'],
            'kategori' => strtolower($validated['kategori']),
            'jumlah' => $validated['jumlah'],
            'anggota_id' => null,
            'created_by' => Auth::id(),
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('admin.keuangan.arus.operasional', [
                'bulan' => Carbon::parse($validated['tanggal'])->format('Y-m'),
            ])
            ->with('success', 'Arus operasional berhasil ditambahkan.');
    }

    public function destroy(ArusKas $arusKas)
    {
        $this->authorize('view arus operasional');

        abort_unless($arusKas->jenis_arus === 'operasional', 404);

        $bulan = optional($arusKas->tanggal)->format('Y-m') ?? now()->format('Y-m');

        $arusKas->delete();

        return redirect()
            ->route('admin.keuangan.arus.operasional', ['bulan' => $bulan])
            ->with('success', 'Arus operasional berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $this->authorize('view arus operasional');
        
        $bulan = $request->get('bulan', now()->format('Y-m'));

        return Excel::download(
            new ArusOperasionalExport($bulan),
            'arus-operasional-' . $bulan . '.xlsx'
        );
    }
}
