<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use Illuminate\Support\Facades\Auth;

class SimpananSayaController extends Controller
{
    public function index()
    {
        $this->authorize('view simpanan saya');

        $anggota = Auth::user()->anggota;
        abort_if(! $anggota, 403);

        // Get all simpanan for saldo calculation
        $allSimpanan = Simpanan::where('anggota_id', $anggota->id)
            ->get();
    
        $saldo = $allSimpanan
            ->groupBy('jenis_simpanan')
            ->map(fn ($items) => $items->sum('jumlah'));

        // Build query for paginated table
        $query = Simpanan::where('anggota_id', $anggota->id);
        
        // Apply filter if provided
        if (request('filter')) {
            $validFilters = ['pokok', 'wajib', 'sukarela'];
            if (in_array(request('filter'), $validFilters)) {
                $query->where('jenis_simpanan', request('filter'));
            }
        }
        
        // Paginate for table display (10 per page) with query string preserved
        $simpanan = $query->orderByDesc('tanggal')
            ->paginate(10)
            ->appends(request()->query());

        return view('anggota.simpanan.index', compact(
            'simpanan',
            'saldo'
        ));
    }
}
