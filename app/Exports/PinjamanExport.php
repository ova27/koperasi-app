<?php

namespace App\Exports;

use App\Models\Pinjaman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PinjamanExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Pinjaman::with('anggota')
            ->orderBy('tanggal_pinjam')
            ->get()
            ->map(function ($p) {
                return [
                    'Nama Anggota' => $p->anggota->nama ?? '-',
                    'Tanggal Pinjam' => optional($p->tanggal_pinjam)->format('Y-m-d'),
                    'Jumlah Pinjaman' => $p->jumlah_pinjaman,
                    'Sisa Pinjaman' => $p->sisa_pinjaman,
                    'Status' => ucfirst($p->status),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama Anggota',
            'Tanggal Pinjam',
            'Jumlah Pinjaman',
            'Sisa Pinjaman',
            'Status',
        ];
    }
}
