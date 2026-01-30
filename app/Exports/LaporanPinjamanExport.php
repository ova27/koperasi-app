<?php

namespace App\Exports;

use App\Models\TransaksiPinjaman;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanPinjamanExport implements FromCollection, WithHeadings, WithMapping
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

        return TransaksiPinjaman::with('pinjaman.anggota')
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Anggota',
            'Jenis Transaksi',
            'Jumlah',
            'Status Pinjaman',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal->format('d-m-Y'),
            $row->pinjaman->anggota->nama,
            ucfirst($row->jenis),
            $row->jumlah,
            ucfirst($row->pinjaman->status),
        ];
    }
}
