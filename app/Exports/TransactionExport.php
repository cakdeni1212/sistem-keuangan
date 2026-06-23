<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        protected ?string $from = null,
        protected ?string $to = null,
        protected ?string $category = null, // pemasukan | pengeluaran | null (all)
        protected ?string $status = null,
    ) {}

    public function collection()
    {
        $q = Transaction::query()
            ->with(['transactionType', 'creator', 'approver'])
            ->orderBy('transaction_date');

        if ($this->from && $this->to) {
            $q->whereBetween('transaction_date', [$this->from, $this->to]);
        }

        if ($this->category) {
            $q->whereHas('transactionType', fn ($t) => $t->where('category', $this->category));
        }

        if ($this->status) {
            $q->where('status', $this->status);
        }

        return $q->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Jenis Transaksi', 'Kategori', 'Jumlah (Rp)', 'Deskripsi', 'Status', 'Di-input oleh', 'Disetujui oleh'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        $categoryMap = [
            'pemasukan' => 'Pemasukan',
            'pengeluaran' => 'Pengeluaran',
        ];
        $statusMap = [
            'draft' => 'Draft',
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return [
            $i,
            $row->transaction_date->format('d/m/Y'),
            $row->transactionType->name ?? '-',
            $categoryMap[$row->transactionType->category ?? ''] ?? ($row->transactionType->category ?? '-'),
            (float) $row->amount,
            $row->description ?? '',
            $statusMap[$row->status] ?? $row->status,
            $row->creator->name ?? '-',
            $row->approver->name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $last = $sheet->getHighestRow();

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '059669']],
        ]);

        $sheet->getStyle("E2:E{$last}")->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle("A1:I{$last}")->getBorders()->getAllBorders()
            ->setBorderStyle('thin')
            ->setColor(new Color('DDDDDD'));

        // Color rows by category
        for ($r = 2; $r <= $last; $r++) {
            $cat = $sheet->getCell("D{$r}")->getValue();
            if ($cat === 'Pengeluaran') {
                $sheet->getStyle("A{$r}:I{$r}")->getFill()->setFillType('solid')
                    ->getStartColor()->setRGB('FFF7ED');
            } elseif ($cat === 'Pemasukan') {
                $sheet->getStyle("A{$r}:I{$r}")->getFill()->setFillType('solid')
                    ->getStartColor()->setRGB('ECFDF5');
            }
        }

        if ($last > 1) {
            $sheet->setCellValue('A'.($last + 1), 'TOTAL');
            $sheet->setCellValue('E'.($last + 1), "=SUM(E2:E{$last})");
            $sheet->getStyle('A'.($last + 1).':I'.($last + 1))->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F3F4F6']],
            ]);
            $sheet->getStyle('E'.($last + 1))->getNumberFormat()->setFormatCode('#,##0');
        }

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 14, 'C' => 22, 'D' => 14, 'E' => 18, 'F' => 35, 'G' => 14, 'H' => 20, 'I' => 20];
    }

    public function title(): string
    {
        return 'Transaksi';
    }
}
