<?php

namespace App\Services;

use App\Models\ClosingBulan;
use Illuminate\Support\Facades\Auth;
use Exception;

class ClosingService
{
    public function lock(string $bulan, string $jenis): void
    {
        $sudahDitutup = ClosingBulan::where('bulan', $bulan)
            ->where('jenis', $jenis)
            ->exists();

        if ($sudahDitutup) {
            throw new Exception("Bulan {$bulan} sudah ditutup");
        }

        ClosingBulan::create([
            'bulan' => $bulan,
            'jenis' => $jenis,
            'ditutup_oleh' => Auth::id(),
            'ditutup_pada' => now(),
        ]);
    }

    public function isLocked(string $bulan, string $jenis): bool
    {
        return ClosingBulan::where('bulan', $bulan)
            ->where('jenis', $jenis)
            ->exists();
    }
}