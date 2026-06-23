<?php

namespace App\Exports;

use App\Models\EmployeeSalary;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeSalaryExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private ?Collection $data = null;

    public function __construct(
        protected ?int $month = null,
        protected ?int $year = null,
        protected ?int $employeeId = null,
    ) {}

    public function collection(): Collection
    {
        $q = EmployeeSalary::query()
            ->with(['employee', 'creator'])
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->orderBy('employee_id');

        if ($this->month) {
            $q->where('period_month', $this->month);
        }
        if ($this->year) {
            $q->where('period_year', $this->year);
        }
        if ($this->employeeId) {
            $q->where('employee_id', $this->employeeId);
        }

        $this->data = $q->get();

        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Karyawan', 'Jabatan', 'Periode',
            'Gaji Pokok (Rp)', 'Bonus (Rp)', 'Potongan (Rp)', 'Total Gaji (Rp)',
            'Metode Bayar', 'Tgl Bayar', 'Status', 'Catatan',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        $isPaid = ! is_null($row->paid_at);

        return [
            $i,
            $row->employee->name ?? '-',
            $row->employee->position ?? '-',
            $row->period_label,
            (float) $row->base_salary,
            (float) $row->bonus,
            (float) $row->deductions,
            (float) $row->total_salary,
            $row->payment_method ?? '-',
            $isPaid ? $row->paid_at->format('d/m/Y') : '-',
            $isPaid ? 'Terbayar' : 'Belum Dibayar',
            $row->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = $sheet->getHighestRow();

        // Header
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '7C3AED']],
        ]);

        // Currency columns
        $currCols = ['E', 'F', 'G', 'H'];
        foreach ($currCols as $col) {
            $sheet->getStyle("{$col}2:{$col}{$last}")->getNumberFormat()->setFormatCode('#,##0');
        }

        // Borders
        $sheet->getStyle("A1:L{$last}")->getBorders()->getAllBorders()
            ->setBorderStyle('thin')
            ->setColor(new Color('DDDDDD'));

        // Row colors by status
        for ($r = 2; $r <= $last; $r++) {
            $status = $sheet->getCell("K{$r}")->getValue();
            if ($status === 'Belum Dibayar') {
                $sheet->getStyle("A{$r}:L{$r}")->getFill()->setFillType('solid')
                    ->getStartColor()->setRGB('FFF7ED');
                $sheet->getStyle("K{$r}")->getFont()->getColor()->setRGB('DC2626');
            } elseif ($status === 'Terbayar') {
                $sheet->getStyle("K{$r}")->getFont()->getColor()->setRGB('16A34A');
            }
        }

        if ($last > 1) {
            // TOTAL row
            $totalRow = $last + 1;
            $sheet->setCellValue("A{$totalRow}", 'TOTAL');
            foreach (['E', 'F', 'G', 'H'] as $col) {
                $sheet->setCellValue("{$col}{$totalRow}", "=SUM({$col}2:{$col}{$last})");
                $sheet->getStyle("{$col}{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
            }
            $sheet->getStyle("A{$totalRow}:L{$totalRow}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EDE9FE']],
            ]);

            // Sudah Dibayar row
            $paidTotal = $this->data ? (float) $this->data->whereNotNull('paid_at')->sum('total_salary') : 0;
            $unpaidTotal = $this->data ? (float) $this->data->whereNull('paid_at')->sum('total_salary') : 0;
            $paidCount = $this->data ? $this->data->whereNotNull('paid_at')->count() : 0;
            $unpaidCount = $this->data ? $this->data->whereNull('paid_at')->count() : 0;

            $paidRow = $last + 3;
            $unpaidRow = $last + 4;

            $sheet->setCellValue("A{$paidRow}", '✓ Sudah Dibayar');
            $sheet->setCellValue("B{$paidRow}", "{$paidCount} karyawan");
            $sheet->setCellValue("H{$paidRow}", $paidTotal);
            $sheet->getStyle("H{$paidRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("A{$paidRow}:L{$paidRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '166534']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DCFCE7']],
            ]);

            $sheet->setCellValue("A{$unpaidRow}", '✗ Belum Dibayar');
            $sheet->setCellValue("B{$unpaidRow}", "{$unpaidCount} karyawan");
            $sheet->setCellValue("H{$unpaidRow}", $unpaidTotal);
            $sheet->getStyle("H{$unpaidRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("A{$unpaidRow}:L{$unpaidRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '991B1B']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FEE2E2']],
            ]);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 22, 'C' => 18, 'D' => 14, 'E' => 18, 'F' => 14, 'G' => 14, 'H' => 18, 'I' => 14, 'J' => 14, 'K' => 14, 'L' => 25];
    }

    public function title(): string
    {
        return 'Gaji Karyawan';
    }
}
