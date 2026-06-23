<?php

namespace App\Imports;

use App\Models\DailyRevenue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DailyRevenueImport implements ToCollection, WithHeadingRow
{
    public array $errors = [];

    public int $imported = 0;

    public int $skipped = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $i => $row) {
            $rowNum = $i + 2; // row 1 = heading

            // Parse tanggal — bisa serial Excel atau string
            try {
                $date = $this->parseDate($row['tanggal'] ?? null);
            } catch (\Throwable) {
                $this->errors[] = "Baris {$rowNum}: Format tanggal tidak valid ({$row['tanggal']}).";
                $this->skipped++;

                continue;
            }

            if (! $date) {
                $this->errors[] = "Baris {$rowNum}: Tanggal kosong.";
                $this->skipped++;

                continue;
            }

            $qris = (float) ($row['qris'] ?? $row['qris_amount'] ?? 0);
            $tunai = (float) ($row['cash'] ?? $row['tunai_amount'] ?? $row['tunai'] ?? 0);

            if ($qris < 0 || $tunai < 0) {
                $this->errors[] = "Baris {$rowNum}: Nilai QRIS/Cash tidak boleh negatif.";
                $this->skipped++;

                continue;
            }

            // Upsert — jika tanggal sudah ada, update; jika belum, buat baru
            DailyRevenue::updateOrCreate(
                ['date' => $date],
                [
                    'qris_amount' => $qris,
                    'tunai_amount' => $tunai,
                    'updated_by' => Auth::id(),
                    'created_by' => Auth::id(),
                ]
            );

            $this->imported++;
        }
    }

    private function parseDate(mixed $value): ?string
    {
        if (empty($value) && $value !== 0) {
            return null;
        }

        // Numeric = Excel serial date
        if (is_numeric($value)) {
            $date = ExcelDate::excelToDateTimeObject((float) $value);

            return $date->format('Y-m-d');
        }

        // Try common string formats
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'd-m-y', 'm/d/Y'];
        foreach ($formats as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, trim((string) $value));
            if ($dt) {
                return $dt->format('Y-m-d');
            }
        }

        // strtotime fallback
        $ts = strtotime((string) $value);
        if ($ts) {
            return date('Y-m-d', $ts);
        }

        throw new \InvalidArgumentException("Cannot parse date: {$value}");
    }
}
