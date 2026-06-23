<?php

namespace App\Exports;

use App\Models\Cashbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashbonExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        protected ?string $from = null,
        protected ?string $to = null,
        protected ?string $status = null, // belum_bayar | lunas | null (all)
    ) {}

    public function collection()
    {
        $q = Cashbon::query()
            ->with(['employee', 'creator'])
            ->orderBy('debt_date');

        if ($this->from && $this->to) {
            $q->whereBetween('debt_date', [$this->from, $this->to]);
        }

        if ($this->status) {
            $q->where('status', $this->status);
        }

        return $q->get();
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Debitur', 'Tipe', 'Karyawan',
            'Jumlah (Rp)', 'Deskripsi',
            'Tgl Hutang', 'Jatuh Tempo', 'Tgl Bayar',
            'Status', 'Catatan',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        $typeMap = ['internal' => 'Karyawan', 'external' => 'Eksternal'];
        $statusMap = ['belum_bayar' => 'Belum Bayar', 'lunas' => 'Lunas'];

        return [
            $i,
            $row->debtor_name,
            $typeMap[$row->debtor_type] ?? $row->debtor_type,
            $row->employee?->name ?? '-',
            (float) $row->amount,
            $row->description ?? '',
            $row->debt_date ? $row->debt_date->format('d/m/Y') : '-',
            $row->due_date ? $row->due_date->format('d/m/Y') : '-',
            $row->paid_at ? $row->paid_at->format('d/m/Y') : '-',
            $statusMap[$row->status] ?? $row->status,
            $row->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = $sheet->getHighestRow();

        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DC2626']],
        ]);

        $sheet->getStyle("E2:E{$last}")->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle("A1:K{$last}")->getBorders()->getAllBorders()
            ->setBorderStyle('thin')
            ->setColor(new Color('DDDDDD'));

        for ($r = 2; $r <= $last; $r++) {
            $status = $sheet->getCell("J{$r}")->getValue();
            if ($status === 'Belum Bayar') {
                $sheet->getStyle("A{$r}:K{$r}")->getFill()->setFillType('solid')
                    ->getStartColor()->setRGB('FFF1F2');
                $sheet->getStyle("J{$r}")->getFont()->getColor()->setRGB('DC2626');
            } elseif ($status === 'Lunas') {
                $sheet->getStyle("J{$r}")->getFont()->getColor()->setRGB('16A34A');
            }
        }

        if ($last > 1) {
            $sheet->setCellValue('A'.($last + 1), 'TOTAL');
            $sheet->setCellValue('E'.($last + 1), "=SUM(E2:E{$last})");
            $sheet->getStyle('A'.($last + 1).':K'.($last + 1))->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F3F4F6']],
            ]);
            $sheet->getStyle('E'.($last + 1))->getNumberFormat()->setFormatCode('#,##0');
        }

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 5, 'B' => 22, 'C' => 12, 'D' => 20, 'E' => 18, 'F' => 30, 'G' => 14, 'H' => 14, 'I' => 14, 'J' => 14, 'K' => 25];
    }

    public function title(): string
    {
        return 'Cashbon';
    }
}
