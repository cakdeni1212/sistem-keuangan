<?php

namespace App\Http\Controllers;

use App\Exports\CashbonExport;
use App\Exports\DailyRevenueExport;
use App\Exports\EmployeeSalaryExport;
use App\Exports\TransactionExport;
use App\Models\Employee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->get(['id', 'name']);
        $years = collect(range(date('Y'), date('Y') - 4))->values();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('laporan.index', compact('employees', 'years', 'months'));
    }

    /** Export omset harian */
    public function exportOmset(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2099',
        ]);

        $export = new DailyRevenueExport(
            from: $request->from,
            to: $request->to,
            month: $request->month ? (int) $request->month : null,
            year: $request->year ? (int) $request->year : null,
        );

        $filename = 'omset-harian-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download($export, $filename);
    }

    /** Export transaksi */
    public function exportTransaksi(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'category' => 'nullable|in:pemasukan,pengeluaran',
            'status' => 'nullable|in:draft,pending,approved,rejected',
        ]);

        $export = new TransactionExport(
            from: $request->from,
            to: $request->to,
            category: $request->category,
            status: $request->status,
        );

        $filename = 'transaksi-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download($export, $filename);
    }

    /** Export cashbon */
    public function exportCashbon(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'status' => 'nullable|in:belum_bayar,lunas',
        ]);

        $export = new CashbonExport(
            from: $request->from,
            to: $request->to,
            status: $request->status,
        );

        $filename = 'cashbon-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download($export, $filename);
    }

    /** Export gaji karyawan */
    public function exportGaji(Request $request)
    {
        $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2099',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $export = new EmployeeSalaryExport(
            month: $request->month ? (int) $request->month : null,
            year: $request->year ? (int) $request->year : null,
            employeeId: $request->employee_id ? (int) $request->employee_id : null,
        );

        $filename = 'gaji-karyawan-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download($export, $filename);
    }
}
