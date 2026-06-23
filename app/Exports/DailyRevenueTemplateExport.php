<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyRevenueTemplateExport implements FromArray, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    public function array(): array
    {
        $rows = [
            ['Tanggal', 'QRIS', 'Cash', 'Total'],
        ];

        // 5 example rows
        $examples = [
            ['01/04/2026', 1500000, 800000, 2300000],
            ['02/04/2026', 2000000, 500000, 2500000],
            ['03/04/2026', 1200000, 750000, 1950000],
            ['', '', '', ''],
            ['', '', '', ''],
        ];

        return array_merge($rows, $examples);
    }

    public function title(): string
    {
        return 'Omset Harian';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 18,
            'C' => 18,
            'D' => 18,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Data rows
            'A2:D6' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            // Number format for QRIS, Cash, Total columns
            'B2:D6' => [
                'numberFormat' => ['formatCode' => '#,##0'],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Border all cells
                $sheet->getStyle('A1:D6')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCBD5E1'],
                        ],
                    ],
                ]);

                // Freeze header row
                $sheet->freezePane('A2');

                // Date format for column A rows 2-6
                $sheet->getStyle('A2:A6')->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                // Total column formula D2:D6
                for ($row = 2; $row <= 6; $row++) {
                    $sheet->setCellValue("D{$row}", "=B{$row}+C{$row}");
                }

                // Instructions sheet
                $instructionSheet = $event->sheet->getParent()->createSheet();
                $instructionSheet->setTitle('Petunjuk');
                $instructionSheet->setCellValue('A1', 'PETUNJUK PENGISIAN');
                $instructionSheet->setCellValue('A3', 'Kolom A (Tanggal):');
                $instructionSheet->setCellValue('B3', 'Format: DD/MM/YYYY — contoh: 01/04/2026');
                $instructionSheet->setCellValue('A4', 'Kolom B (QRIS):');
                $instructionSheet->setCellValue('B4', 'Total omset QRIS hari itu (angka, tanpa Rp)');
                $instructionSheet->setCellValue('A5', 'Kolom C (Cash):');
                $instructionSheet->setCellValue('B5', 'Total omset tunai/cash hari itu (angka, tanpa Rp)');
                $instructionSheet->setCellValue('A6', 'Kolom D (Total):');
                $instructionSheet->setCellValue('B6', 'Dihitung otomatis (QRIS + Cash) — tidak perlu diisi manual');
                $instructionSheet->setCellValue('A8', 'Catatan:');
                $instructionSheet->setCellValue('B8', '- Jika tanggal sudah ada di sistem, data lama akan ditimpa (update)');
                $instructionSheet->setCellValue('B9', '- Hapus baris contoh sebelum upload, atau isi langsung menimpa baris contoh');
                $instructionSheet->setCellValue('B10', '- Kolom Total tidak perlu diisi, sistem akan menghitung otomatis');

                $instructionSheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF4F46E5']],
                ]);
                $instructionSheet->getStyle('A3:A10')->applyFromArray([
                    'font' => ['bold' => true],
                ]);
                $instructionSheet->getColumnDimension('A')->setWidth(22);
                $instructionSheet->getColumnDimension('B')->setWidth(60);
            },
        ];
    }
}
