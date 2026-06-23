<?php

namespace App\Exports;

use App\Models\DailyRevenue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyRevenueExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        protected ?string $from = null,
        protected ?string $to = null,
        protected ?int $month = null,
        protected ?int $year = null,
    ) {}

    public function collection()
    {
        $q = DailyRevenue::query()->with('creator')->orderBy('date');

        if ($this->from && $this->to) {
            $q->whereBetween('date', [$this->from, $this->to]);
        } elseif ($this->month && $this->year) {
            $q->whereMonth('date', $this->month)->whereYear('date', $this->year);
        } elseif ($this->year) {
            $q->whereYear('date', $this->year);
        }

        return $q->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'QRIS (Rp)', 'Tunai (Rp)', 'Total (Rp)', 'Catatan', 'Di-input oleh'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row->date->format('d/m/Y'),
            (float) $row->qris_amount,
            (float) $row->tunai_amount,
            (float) $row->total,
            $row->notes ?? '',
            $row->creator->name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = $sheet->getHighestRow();

        // Header style
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']],
        ]);

        // Currency columns C, D, E
        $sheet->getStyle("C2:E{$last}")->getNumberFormat()
            ->setFormatCode('#,##0');

        // Borders
        $sheet->getStyle("A1:G{$last}")->getBorders()->getAllBorders()
            ->setBorderStyle('thin')
            ->setColor(new Color('DDDDDD'));

        // Totals row
        if ($last > 1) {
            $sheet->setCellValue('A'.($last + 1), 'TOTAL');
            $sheet->setCellValue('C'.($last + 1), "=SUM(C2:C{$last})");
            $sheet->setCellValue('D'.($last + 1), "=SUM(D2:D{$last})");
            $sheet->setCellValue('E'.($last + 1), "=SUM(E2:E{$last})");
            $sheet->getStyle('A'.($last + 1).':G'.($last + 1))->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F3F4F6']],
            ]);
            $sheet->getStyle('C'.($last + 1).':E'.($last + 1))->getNumberFormat()
                ->setFormatCode('#,##0');
        }

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 16, 'C' => 18, 'D' => 18, 'E' => 18, 'F' => 30, 'G' => 20];
    }

    public function title(): string
    {
        return 'Omset Harian';
    }
}
