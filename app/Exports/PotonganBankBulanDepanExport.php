<?php

namespace App\Exports;

use App\Models\PotonganBulananDetail;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PotonganBankBulanDepanExport implements FromArray, WithColumnWidths, WithStyles, WithEvents
{
    private int $dataCount = 0;

    public function __construct(private string $bulanPotongan, private ?string $namaBank = null)
    {
    }

    public function array(): array
    {
        Carbon::setLocale('id');

        $rows = PotonganBulananDetail::query()
            ->where('bulan_potongan', $this->bulanPotongan)
            ->when(
                filled($this->namaBank),
                fn ($query) => $query->where('bank', $this->namaBank)
            )
            ->orderBy('nama')
            ->get()
            ->values()
            ->map(function (PotonganBulananDetail $detail) {
                return [
                    $detail->nama,
                    $detail->bank ?? '-',
                    (string) ($detail->nomor_rekening ?? '-'),
                    (int) $detail->total,
                ];
            });

        $this->dataCount = $rows->count();

        $bulan = Carbon::createFromFormat('Y-m', $this->bulanPotongan);
        $bankLabel = $this->namaBank ? strtoupper($this->namaBank) : 'SEMUA BANK';
        $judulBulan = strtoupper($bulan->translatedFormat('F Y'));

        $data = [
            ['POTONGAN KOPERASI PEGAWAI BPS PROVINSI BANTEN'],
            ['DI ' . $bankLabel . ' BULAN ' . $judulBulan],
            [''],
            ['No', 'Nama', 'Rekening', '', 'Potongan Koperasi (Rp)'],
            ['(1)', '(2)', '(3)', '(4)', '(5)'],
        ];

        foreach ($rows->values() as $index => $row) {
            $data[] = [
                $index + 1,
                $row[0],
                $row[1],
                $row[2],
                $row[3],
            ];
        }

        $data[] = ['JUMLAH', '', '', '', (int) $rows->sum(fn ($row) => $row[3] ?? 0)];
        $data[] = [''];
        $data[] = ['Mengetahui,', '', '', 'Serang, ' . now()->translatedFormat('d F Y'), ''];
        $data[] = ['Kepala Bagian Umum BPS Provinsi Banten,', '', '', 'Bendahara Pengeluaran,', ''];
        $data[] = [''];
        $data[] = [''];
        $data[] = [''];
        $data[] = ['Ridwan Hidayat, S.Si', '', '', 'Intan Putri Firdaus, A.Md', ''];
        $data[] = ['NIP. 19720306 199512 1 001', '', '', 'NIP. 19910508 201403 2 003', ''];

        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        $dataStartRow = 6;
        $dataEndRow = $this->dataCount > 0 ? ($dataStartRow + $this->dataCount - 1) : ($dataStartRow - 1);
        $totalRow = $dataEndRow + 1;
        $signatureStartRow = $totalRow + 2;

        // ── Title rows ──────────────────────────────────────────────
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');

        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(8);

        $sheet->getStyle('A1:E2')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1:E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:E2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // ── Header rows (4-5) ────────────────────────────────────────
        // Rekening spans C4:D4; other columns NOT merged vertically so row 5 shows numbers
        $sheet->mergeCells('C4:D4');

        $sheet->getRowDimension(4)->setRowHeight(28);
        $sheet->getRowDimension(5)->setRowHeight(16);

        $sheet->getStyle('A4:E5')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A4:E5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:E5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A4:E5')->getAlignment()->setWrapText(true);

        // Row 5 nomor kolom: font kecil, tidak bold
        $sheet->getStyle('A5:E5')->getFont()->setBold(false)->setSize(9);

        // ── Data rows ────────────────────────────────────────────────
        if ($dataEndRow >= $dataStartRow) {
            $sheet->getStyle('E' . $dataStartRow . ':E' . $dataEndRow)
                ->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('E' . $dataStartRow . ':E' . $dataEndRow)
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('A' . $dataStartRow . ':A' . $dataEndRow)
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // ── JUMLAH row ───────────────────────────────────────────────
        $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
        $sheet->getStyle('A' . $totalRow . ':E' . $totalRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $totalRow . ':D' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $totalRow . ':D' . $totalRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('E' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getRowDimension($totalRow)->setRowHeight(20);

        // ── Borders ──────────────────────────────────────────────────
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'style' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A4:E' . $totalRow)->applyFromArray($borderStyle);

        // ── Signature block ──────────────────────────────────────────
        $r = $signatureStartRow;
        $sheet->mergeCells('A' . $r       . ':C' . $r);
        $sheet->mergeCells('D' . $r       . ':E' . $r);
        $sheet->mergeCells('A' . ($r + 1) . ':C' . ($r + 1));
        $sheet->mergeCells('D' . ($r + 1) . ':E' . ($r + 1));
        $sheet->mergeCells('A' . ($r + 5) . ':C' . ($r + 5));
        $sheet->mergeCells('D' . ($r + 5) . ':E' . ($r + 5));
        $sheet->mergeCells('A' . ($r + 6) . ':C' . ($r + 6));
        $sheet->mergeCells('D' . ($r + 6) . ':E' . ($r + 6));

        $sheet->getStyle('A' . $r . ':E' . ($r + 6))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $r . ':E' . ($r + 6))
            ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A' . ($r + 5) . ':E' . ($r + 6))
            ->getFont()->setBold(true)->setSize(11);

        $sheet->getRowDimension($r)     ->setRowHeight(18); // Mengetahui / tanggal
        $sheet->getRowDimension($r + 1) ->setRowHeight(18); // Jabatan
        $sheet->getRowDimension($r + 2) ->setRowHeight(10);
        $sheet->getRowDimension($r + 3) ->setRowHeight(10);
        $sheet->getRowDimension($r + 4) ->setRowHeight(28); // ruang tanda tangan
        $sheet->getRowDimension($r + 5) ->setRowHeight(20); // Nama
        $sheet->getRowDimension($r + 6) ->setRowHeight(18); // NIP

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $dataStartRow = 6;
                $dataEndRow = $this->dataCount > 0 ? ($dataStartRow + $this->dataCount - 1) : ($dataStartRow - 1);

                if ($dataEndRow >= $dataStartRow) {
                    $ws = $event->sheet->getDelegate();
                    for ($row = $dataStartRow; $row <= $dataEndRow; $row++) {
                        $cell = $ws->getCell('D' . $row);
                        $cell->setValueExplicit(
                            (string) $cell->getValue(),
                            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                        );
                    }
                }
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 38,
            'C' => 10,
            'D' => 26,
            'E' => 22,
        ];
    }
}
