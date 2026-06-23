<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji — {{ $employee->name }} — {{ $salary->period_label }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px;
        }

        /* Tombol aksi — disembunyikan saat print */
        .no-print {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 700px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }
        .btn-print { background: #4f46e5; color: #fff; }
        .btn-print:hover { background: #4338ca; }
        .btn-back { background: #fff; color: #374151; border: 1px solid #d1d5db; }
        .btn-back:hover { background: #f9fafb; }

        /* Slip container */
        .slip {
            width: 100%;
            max-width: 700px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 6px rgba(0,0,0,.10);
            overflow: hidden;
        }

        /* Header slip */
        .slip-header {
            background: #4f46e5;
            color: #fff;
            padding: 24px 28px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .slip-header .company { font-size: 20px; font-weight: 800; letter-spacing: -.5px; }
        .slip-header .company-sub { font-size: 11px; opacity: .75; margin-top: 2px; }
        .slip-header .period-box { text-align: right; }
        .slip-header .period-label { font-size: 11px; opacity: .75; }
        .slip-header .period-value { font-size: 17px; font-weight: 700; margin-top: 2px; }

        /* Divider */
        .divider { height: 1px; background: #e5e7eb; margin: 0 28px; }

        /* Employee section */
        .section { padding: 18px 28px; }
        .section-title {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 24px; }
        .info-item .label { font-size: 11px; color: #9ca3af; }
        .info-item .value { font-size: 13px; font-weight: 600; color: #111827; margin-top: 1px; }

        /* Salary table */
        .sal-table { width: 100%; border-collapse: collapse; }
        .sal-table td { padding: 8px 0; vertical-align: middle; }
        .sal-table tr { border-bottom: 1px dashed #e5e7eb; }
        .sal-table tr:last-child { border-bottom: none; }
        .sal-table .lbl { color: #374151; }
        .sal-table .val { text-align: right; font-variant-numeric: tabular-nums; }
        .sal-table .green { color: #16a34a; font-weight: 600; }
        .sal-table .red { color: #dc2626; font-weight: 600; }

        /* Total box */
        .total-box {
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            border-radius: 8px;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 16px 28px;
        }
        .total-box .total-lbl { font-size: 13px; color: #4b5563; font-weight: 600; }
        .total-box .total-val { font-size: 22px; font-weight: 800; color: #4f46e5; }

        /* Payment info */
        .payment-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
        }
        .chip-green { background: #dcfce7; color: #15803d; }
        .chip-yellow { background: #fef9c3; color: #92400e; }
        .chip-gray { background: #f3f4f6; color: #4b5563; }

        /* Footer */
        .slip-footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 16px 28px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .sign-box { text-align: center; }
        .sign-line { margin: 40px auto 6px; border-bottom: 1px solid #9ca3af; width: 80%; }
        .sign-label { font-size: 11px; color: #6b7280; }

        /* Print styles */
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .slip { box-shadow: none; border-radius: 0; max-width: 100%; }
            .slip-footer { break-inside: avoid; }
        }
    </style>
</head>
<body>

    {{-- Action buttons (hidden on print) --}}
    <div class="no-print">
        <a href="{{ route('employees.show', $employee) }}" class="btn btn-back">
            ← Kembali
        </a>
        <button onclick="window.print()" class="btn btn-print">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="slip">

        {{-- Header --}}
        <div class="slip-header">
            <div>
                <div class="company">{{ \App\Models\AppSetting::get('business_name', 'FORKA COFFEE & SPACE') }}</div>
                <div class="company-sub">{{ \App\Models\AppSetting::get('slip_subtitle', 'Slip Gaji Karyawan') }}</div>
            </div>
            <div class="period-box">
                <div class="period-label">Periode</div>
                <div class="period-value">{{ $salary->period_label }}</div>
            </div>
        </div>

        {{-- Employee Info --}}
        <div class="section">
            <div class="section-title">Informasi Karyawan</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Nama</div>
                    <div class="value">{{ $employee->name }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Jabatan</div>
                    <div class="value">{{ $employee->position }}</div>
                </div>
                @if($employee->department)
                <div class="info-item">
                    <div class="label">Divisi</div>
                    <div class="value">{{ $employee->department }}</div>
                </div>
                @endif
                @if($employee->join_date)
                <div class="info-item">
                    <div class="label">Bergabung Sejak</div>
                    <div class="value">{{ $employee->join_date->format('d M Y') }}</div>
                </div>
                @endif
                @if($employee->bank_name || $employee->account_number)
                <div class="info-item">
                    <div class="label">Bank</div>
                    <div class="value">{{ $employee->bank_name ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">No. Rekening</div>
                    <div class="value" style="font-family:monospace;letter-spacing:.05em">{{ $employee->account_number ?? '—' }}</div>
                </div>
                @if($employee->account_name)
                <div class="info-item" style="grid-column:span 2">
                    <div class="label">Atas Nama</div>
                    <div class="value">{{ $employee->account_name }}</div>
                </div>
                @endif
                @endif
            </div>
        </div>

        <div class="divider"></div>

        {{-- Salary Breakdown --}}
        <div class="section">
            <div class="section-title">Rincian Gaji</div>
            <table class="sal-table">
                <tr>
                    <td class="lbl">Gaji Pokok</td>
                    <td class="val">Rp {{ number_format($salary->base_salary, 0, ',', '.') }}</td>
                </tr>
                @if($salary->bonus > 0)
                <tr>
                    <td class="lbl">Bonus / Tunjangan</td>
                    <td class="val green">+ Rp {{ number_format($salary->bonus, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($salary->deductions > 0)
                <tr>
                    <td class="lbl">Potongan</td>
                    <td class="val red">− Rp {{ number_format($salary->deductions, 0, ',', '.') }}</td>
                </tr>
                @endif
            </table>
        </div>

        {{-- Total --}}
        <div class="total-box">
            <div class="total-lbl">Total Gaji Diterima</div>
            <div class="total-val">Rp {{ number_format($salary->total_salary, 0, ',', '.') }}</div>
        </div>

        {{-- Payment Info --}}
        <div class="section" style="padding-top:0">
            <div class="section-title">Status Pembayaran</div>
            <div class="payment-chips">
                @if($salary->paid_at)
                <span class="chip chip-green">✓ Dibayar {{ $salary->paid_at->format('d M Y') }}</span>
                @else
                <span class="chip chip-yellow">⏳ Belum Dibayar</span>
                @endif
                @if($salary->payment_method)
                <span class="chip chip-gray">{{ ucfirst($salary->payment_method) }}</span>
                @endif
            </div>
            @if($salary->notes)
            <p style="margin-top:10px; font-size:12px; color:#6b7280;">
                <span style="font-weight:600;">Catatan:</span> {{ $salary->notes }}
            </p>
            @endif
        </div>

        {{-- Signature Footer --}}
        <div class="slip-footer">
            <div class="sign-box">
                <div class="sign-line"></div>
                <div class="sign-label">Penerima Gaji</div>
                <div style="font-size:12px; font-weight:600; color:#374151; margin-top:2px;">{{ $employee->name }}</div>
            </div>
            <div class="sign-box">
                <div class="sign-line"></div>
                <div class="sign-label">Dibuat Oleh</div>
                <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                    Dicetak {{ now()->format('d M Y, H:i') }}
                </div>
            </div>
        </div>

    </div>

</body>
</html>
