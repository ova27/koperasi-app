<?php

namespace App\Exports;

use App\Models\ArusKas;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ArusKasKoperasiExport implements FromCollection, WithHeadings, WithMapping
{
    protected string $bulan;

    public function __construct(string $bulan)
    {
        $this->bulan = $bulan;
    }

    public function collection()
    {
        return ArusKas::where('jenis_arus', 'koperasi')
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$this->bulan])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Keterangan',
            'Kategori',
            'Masuk',
            'Keluar',
        ];
    }

    public function map($item): array
    {
        return [
            $item->tanggal->format('d-m-Y'),
            $item->keterangan,
            ucfirst($item->kategori),
            $item->tipe === 'masuk' ? $item->jumlah : '',
            $item->tipe === 'keluar' ? $item->jumlah : '',
        ];
    }
}
