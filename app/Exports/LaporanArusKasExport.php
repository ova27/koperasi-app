<?php

namespace App\Exports;

use App\Models\ArusKas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanArusKasExport implements FromArray, WithHeadings
{
    protected string $bulan;

    public function __construct(string $bulan)
    {
        $this->bulan = $bulan;
    }

    public function array(): array
    {
        $rows = [];

        $masuk = ArusKas::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$this->bulan])
            ->where('tipe', 'masuk')
            ->selectRaw('jenis_arus, SUM(jumlah) as total')
            ->groupBy('jenis_arus')
            ->get();

        foreach ($masuk as $row) {
            $rows[] = ['Pemasukan', ucfirst($row->jenis_arus), $row->total];
        }

        $keluar = ArusKas::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$this->bulan])
            ->where('tipe', 'keluar')
            ->selectRaw('jenis_arus, SUM(jumlah) as total')
            ->groupBy('jenis_arus')
            ->get();

        foreach ($keluar as $row) {
            $rows[] = ['Pengeluaran', ucfirst($row->jenis_arus), $row->total];
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Kategori', 'Jenis', 'Jumlah'];
    }
}
