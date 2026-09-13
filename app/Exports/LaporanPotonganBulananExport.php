<?php

namespace App\Exports;

use App\Models\PotonganBulananDetail;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class LaporanPotonganBulananExport extends DefaultValueBinder implements FromArray, WithStyles, WithColumnWidths, WithEvents, WithCustomValueBinder
{
    private int $dataCount = 0;
    private int $titleEndRow = 6;

    public function __construct(private string $bulanPotongan) {}

    public function array(): array
    {
        // Set locale to Indonesian for month names
        Carbon::setLocale('id');
        
        $bulanPotongan = Carbon::createFromFormat('Y-m', $this->bulanPotongan);
        $bulanSebelumnya = $bulanPotongan->copy()->subMonthNoOverflow();
        $rows = PotonganBulananDetail::query()
            ->where('bulan_potongan', $this->bulanPotongan)
            ->orderBy('nama')
            ->get()
            ->map(function (PotonganBulananDetail $detail, $index) {
                return [
                    'no' => $index + 1,
                    'nama' => $detail->nama,
                    'rekening' => $detail->nomor_rekening ?? '-',
                    'sisa_lalu' => (int) $detail->sisa_pinjaman_lalu,
                    'tenor' => $detail->tenor ?? '-',
                    'ke' => $detail->cicilan_ke ?? '-',
                    'cicilan' => (int) $detail->cicilan,
                    'sisa_sekarang' => (int) $detail->sisa_pinjaman_sekarang,
                    'simpanan' => (int) $detail->simpanan_wajib
                        + (int) $detail->simpanan_sukarela
                        + (int) $detail->iuran_operasional,
                    'dharma' => (int) $detail->iuran_dharma_wanita,
                    'infaq' => (int) $detail->infaq_pegawai,
                    'qurban' => (int) $detail->tabungan_qurban,
                    'jumlah' => (int) $detail->total,
                ];
            });

        $this->dataCount = $rows->count();

        // Calculate totals
        $totalSisaLalu = $rows->sum('sisa_lalu');
        $totalCicilan = $rows->sum('cicilan');
        $totalSisaSekarang = $rows->sum('sisa_sekarang');
        $totalSimpanan = $rows->sum('simpanan');
        $totalDharma = $rows->sum('dharma');
        $totalInfaq = $rows->sum('infaq');
        $totalQurban = $rows->sum('qurban');
        $totalJumlah = $rows->sum('jumlah');

        $dataArray = [];

        // Title rows (rows 1-5)
        $dataArray[] = ['DAFTAR POTONGAN ANGGOTA KOPERASI SIMPATIK'];
        $dataArray[] = ['BPS PROVINSI BANTEN TAHUN ' . $bulanPotongan->format('Y')];
        $dataArray[] = [''];
        $dataArray[] = ['Bulan ' . $bulanPotongan->translatedFormat('F') . ' ' . $bulanPotongan->format('Y')];
        $dataArray[] = [''];

        // Header row (row 6)
        $dataArray[] = [
            'No',
            'Nama',
            'No Rekening BRI/BSI',
            'Sisa Pinjaman Terakhir (' . $bulanSebelumnya->translatedFormat('F Y') . ')',
            'Tenor',
            'ke-',
            'Angsuran ' . $bulanPotongan->translatedFormat('F Y'),
            'Sisa Pinjaman',
            'Simpanan Wajib/Sukarela + Iuran Operasional',
            'Iuran Dharma Wanita',
            'Infaq Pegawai',
            'Tabungan Qurban',
            'Jumlah',
        ];

        // Data rows (starting from row 7)
        foreach ($rows as $row) {
            $dataArray[] = [
                $row['no'],
                $row['nama'],
                $row['rekening'],
                $row['sisa_lalu'],
                $row['tenor'],
                $row['ke'],
                $row['cicilan'],
                $row['sisa_sekarang'],
                $row['simpanan'],
                $row['dharma'],
                $row['infaq'],
                $row['qurban'],
                $row['jumlah'],
            ];
        }

        // Total row
        $dataArray[] = [
            '',
            'Jumlah',
            '',
            $totalSisaLalu,
            '',
            '',
            $totalCicilan,
            $totalSisaSekarang,
            $totalSimpanan,
            $totalDharma,
            $totalInfaq,
            $totalQurban,
            $totalJumlah,
        ];

        // Signature rows
        $dataArray[] = [''];
        $dataArray[] = [''];
        $dataArray[] = ['', 'Yang Menerima', '', '', '', '', '', '', '', '', 'Serang, ' . now()->translatedFormat('d F Y')];
        $dataArray[] = ['', 'Bendahara Koperasi', '', '', 'Ketua Koperasi', '', '', '', '', '', 'Bendahara Upah/Gaji', '', ''];
        $dataArray[] = [''];
        $dataArray[] = [''];
        $dataArray[] = [''];
        $dataArray[] = ['', 'Oktavanyta Ariani', '', '', 'Dandi Iswandi', '', '', '', '', '', 'Puwiwi', '', ''];
        $dataArray[] = ['', 'NIP. 19961027 201901 2 001', '', '', 'NIP. 19770605 199901 1 001', '', '', '', '', '', 'NIP. 19830206 201101 2 008', '', ''];

        return $dataArray;
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $sheet->getHighestRow();
        $headerRow = $this->titleEndRow;
        $dataStartRow = $this->titleEndRow + 1;

        // Helper function for black borders
        $blackBorder = [
            'borders' => [
                'allBorders' => [
                    'style' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000'],
                ],
            ]
        ];

        // Merge and style title rows
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A1:M1')->applyFromArray($blackBorder);

        $sheet->mergeCells('A2:M2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A2:M2')->applyFromArray($blackBorder);

        $sheet->mergeCells('A4:M4');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A4:M4')->applyFromArray($blackBorder);

        // Add borders to title row 3 (empty) and row 5 (empty)
        for ($col = 'A'; $col <= 'M'; $col++) {
            $sheet->getStyle($col . '3')->applyFromArray($blackBorder);
            $sheet->getStyle($col . '5')->applyFromArray($blackBorder);
        }

        // Header row styling (row 6) - yellow background
        for ($col = 'A'; $col <= 'M'; $col++) {
            $style = $sheet->getStyle($col . $headerRow);
            $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            
            // Gray background for column D, yellow for others
            if ($col === 'D') {
                $style->getFill()->getStartColor()->setARGB('FFD3D3D3');
            } else {
                $style->getFill()->getStartColor()->setARGB('FFFF00');
            }
            
            $style->getFont()->setBold(true);
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $style->getAlignment()->setWrapText(true);

            // Add black border
            $style->applyFromArray($blackBorder);
        }

        // Data rows formatting
        $totalRowNum = $dataStartRow + $this->dataCount;

        for ($row = $dataStartRow; $row < $totalRowNum; $row++) {
            for ($col = 'A'; $col <= 'M'; $col++) {
                $style = $sheet->getStyle($col . $row);

                // Add black borders
                $style->applyFromArray($blackBorder);

                // Gray background for column D (Sisa Pinjaman Terakhir)
                if ($col === 'D') {
                    $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $style->getFill()->getStartColor()->setARGB('FFD3D3D3');
                }

                // Right align for numeric columns
                if (in_array($col, ['D', 'G', 'H', 'I', 'J', 'K', 'L', 'M'])) {
                    $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $style->getNumberFormat()->setFormatCode('#,##0');
                }

                // Bold untuk kolom Jumlah
                if ($col === 'M') {
                    $style->getFont()->setBold(true);
                }
            }
        }

        // Total row styling
        $sheet->getStyle('A' . $totalRowNum . ':M' . $totalRowNum)->getFont()->setBold(true);
        for ($col = 'A'; $col <= 'M'; $col++) {
            $style = $sheet->getStyle($col . $totalRowNum);
            
            // Gray background for column D
            if ($col === 'D') {
                $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $style->getFill()->getStartColor()->setARGB('FFD3D3D3');
            }
            
            $style->applyFromArray($blackBorder);
            if (in_array($col, ['D', 'G', 'H', 'I', 'J', 'K', 'L', 'M'])) {
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $style->getNumberFormat()->setFormatCode('#,##0');
            }
        }

        // Pengamanan: pastikan baris dengan label "Jumlah" selalu bold
        $jumlahRow = $totalRowNum;
        for ($row = $dataStartRow; $row <= $totalRows; $row++) {
            if (trim((string) $sheet->getCell('B' . $row)->getValue()) === 'Jumlah') {
                $jumlahRow = $row;
                $sheet->getStyle('A' . $row . ':M' . $row)->getFont()->setBold(true);
                break;
            }
        }

        // Pastikan border tabel terlihat jelas dari judul sampai baris Jumlah
        $sheet->getStyle('A1:M' . $jumlahRow)->applyFromArray($blackBorder);

        // Pastikan warna abu-abu kolom D berhenti di baris Jumlah
        for ($row = $totalRowNum + 1; $row <= $totalRows; $row++) {
            $style = $sheet->getStyle('D' . $row);
            $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE);
        }

        // Signature title row styling (Bendahara/Ketua/Bendahara Upah-Gaji)
        for ($row = $dataStartRow; $row <= $totalRows; $row++) {
            if (trim((string) $sheet->getCell('B' . $row)->getValue()) === 'Bendahara Koperasi') {
                $sheet->getStyle('B' . $row)->getFont()->setBold(true);
                $sheet->getStyle('E' . $row)->getFont()->setBold(true);
                $sheet->getStyle('K' . $row)->getFont()->setBold(true);
                break;
            }
        }
        
        // Add borders to other signature rows
        for ($row = $totalRows - 3; $row <= $totalRows; $row++) {
            for ($col = 'A'; $col <= 'M'; $col++) {
                $sheet->getStyle($col . $row)->applyFromArray($blackBorder);
            }
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->freezePane('A7');
                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);
                $sheet->getPageMargins()
                    ->setTop(0.4)
                    ->setRight(0.3)
                    ->setBottom(0.4)
                    ->setLeft(0.3);
            },
        ];
    }

    public function bindValue(Cell $cell, mixed $value): bool
    {
        $lastDataRow = 6 + $this->dataCount;

        if ($cell->getColumn() === 'C' && $cell->getRow() >= 7 && $cell->getRow() <= $lastDataRow) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 22,
            'C' => 18,
            'D' => 20,
            'E' => 4,
            'F' => 6,
            'G' => 15,
            'H' => 15,
            'I' => 18,
            'J' => 18,
            'K' => 16,
            'L' => 16,
            'M' => 15,
        ];
    }
}
