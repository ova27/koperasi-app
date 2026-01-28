<?php

namespace App\Exports;

use App\Models\Simpanan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SimpananBulananExport implements FromCollection, WithHeadings
{
    protected string $bulan;

    public function __construct(string $bulan)
    {
        $this->bulan = $bulan;
    }

    public function collection()
    {
        $start = Carbon::createFromFormat('Y-m', $this->bulan)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $this->bulan)->endOfMonth();

        return Simpanan::with('anggota')
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->get()
            ->map(function ($s) {
                return [
                    'Tanggal' => $s->tanggal->format('Y-m-d'),
                    'Nama Anggota' => $s->anggota->nama ?? '-',
                    'Jenis Simpanan' => ucfirst($s->jenis_simpanan),
                    'Jumlah' => $s->jumlah,
                    'Sumber' => $s->sumber,
                    'Alasan' => $s->alasan,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Anggota',
            'Jenis Simpanan',
            'Jumlah',
            'Sumber',
            'Alasan',
        ];
    }
}
