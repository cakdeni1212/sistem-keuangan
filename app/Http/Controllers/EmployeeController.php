<?php

namespace App\Http\Controllers;

use App\Models\DailyRevenue;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    const POSITIONS = ['Manager', 'Supervisor', 'Kasir', 'Barista', 'Kitchen', 'Waiter', 'Cleaning', 'Security', 'Driver', 'Lainnya'];

    const DEPARTMENTS = ['Operasional', 'Dapur', 'Pelayanan', 'Keuangan', 'Umum'];

    const BANKS = ['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI', 'CIMB Niaga', 'Danamon', 'Permata', 'BTN', 'Lainnya'];

    public function index(Request $request)
    {
        $employees = Employee::with('salaries')->orderBy('name')->get();
        $totalAktif = $employees->where('status', 'aktif')->count();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $years = range(now()->year, max(2024, now()->year - 4));

        // Daftar semua periode yang pernah ada data gaji (untuk dropdown filter)
        $periods = EmployeeSalary::selectRaw('period_year, period_month')
            ->groupBy('period_year', 'period_month')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->get();

        // ── FILTER REKAP PER PERIODE ──────────────────────────────────────
        $latestPeriod = $periods->first();
        $filterMonth = (int) $request->get('gaji_month', $latestPeriod?->period_month ?? now()->month);
        $filterYear = (int) $request->get('gaji_year', $latestPeriod?->period_year ?? now()->year);
        $gajiPeriodLabel = ($months[$filterMonth] ?? '-').' '.$filterYear;

        $salaryRecords = EmployeeSalary::with('employee')
            ->where('period_month', $filterMonth)
            ->where('period_year', $filterYear)
            ->orderBy('employee_id')
            ->get();

        $totalGajiMonth = $salaryRecords->sum('total_salary');
        $totalGajiDibayar = $salaryRecords->whereNotNull('paid_at')->sum('total_salary');
        $totalGajiBelumBayar = $salaryRecords->whereNull('paid_at')->sum('total_salary');
        $countDibayar = $salaryRecords->whereNotNull('paid_at')->count();
        $countBelumBayar = $salaryRecords->whereNull('paid_at')->count();

        // All-time cumulative totals
        $allTimeTotalGaji = (float) EmployeeSalary::sum('total_salary');
        $allTimeDibayar = (float) EmployeeSalary::whereNotNull('paid_at')->sum('total_salary');
        $allTimeBelumBayar = (float) EmployeeSalary::whereNull('paid_at')->sum('total_salary');

        // Chart per karyawan (stacked: dibayar vs belum bayar)
        $chartLabels = $salaryRecords->map(fn ($s) => $s->employee->name ?? '-')->values()->all();
        $chartDibayar = $salaryRecords->map(fn ($s) => $s->paid_at ? (float) $s->total_salary : 0)->values()->all();
        $chartBelumBayar = $salaryRecords->map(fn ($s) => ! $s->paid_at ? (float) $s->total_salary : 0)->values()->all();

        // ── FILTER REKAP KESELURUHAN (range bulan) ────────────────────────
        // Default: dari Januari tahun ini sampai bulan ini
        $rfm = (int) $request->get('rfm', 1);
        $rfy = (int) $request->get('rfy', now()->year);
        $rtm = (int) $request->get('rtm', now()->month);
        $rty = (int) $request->get('rty', now()->year);

        $fromPeriod = $rfy * 100 + $rfm;
        $toPeriod = $rty * 100 + $rtm;

        // Rekap per bulan dalam rentang (untuk chart trend)
        $rangeByPeriod = EmployeeSalary::selectRaw('
                period_year, period_month,
                SUM(total_salary) as total_all,
                SUM(CASE WHEN paid_at IS NOT NULL THEN total_salary ELSE 0 END) as total_paid,
                SUM(CASE WHEN paid_at IS NULL THEN total_salary ELSE 0 END) as total_unpaid
            ')
            ->whereRaw('(period_year * 100 + period_month) >= ?', [$fromPeriod])
            ->whereRaw('(period_year * 100 + period_month) <= ?', [$toPeriod])
            ->groupBy('period_year', 'period_month')
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->get();

        $rangeTotalAll = (float) $rangeByPeriod->sum('total_all');
        $rangeTotalDibayar = (float) $rangeByPeriod->sum('total_paid');
        $rangeTotalBelum = (float) $rangeByPeriod->sum('total_unpaid');

        $rangeChartLabels = $rangeByPeriod->map(fn ($r) => ($months[$r->period_month] ?? $r->period_month).' '.$r->period_year)->values()->all();
        $rangeChartPaid = $rangeByPeriod->pluck('total_paid')->map(fn ($v) => (float) $v)->values()->all();
        $rangeChartUnpaid = $rangeByPeriod->pluck('total_unpaid')->map(fn ($v) => (float) $v)->values()->all();

        return view('employees.index', compact(
            'employees', 'totalAktif', 'totalGajiMonth', 'gajiPeriodLabel',
            'months', 'years', 'periods', 'filterMonth', 'filterYear', 'salaryRecords',
            'totalGajiDibayar', 'totalGajiBelumBayar', 'countDibayar', 'countBelumBayar',
            'allTimeTotalGaji', 'allTimeDibayar', 'allTimeBelumBayar',
            'chartLabels', 'chartDibayar', 'chartBelumBayar',
            // range
            'rfm', 'rfy', 'rtm', 'rty',
            'rangeTotalAll', 'rangeTotalDibayar', 'rangeTotalBelum',
            'rangeChartLabels', 'rangeChartPaid', 'rangeChartUnpaid'
        ));
    }

    public function create()
    {
        $positions = self::POSITIONS;
        $departments = self::DEPARTMENTS;
        $banks = self::BANKS;

        return view('employees.create', compact('positions', 'departments', 'banks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'bank_name' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:30',
            'account_name' => 'nullable|string|max:100',
            'position' => 'required|string|max:50',
            'department' => 'nullable|string|max:50',
            'base_salary' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'join_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $employee = Employee::create($data);

        return redirect()->route('employees.show', $employee)->with('success', "Karyawan \"{$employee->name}\" berhasil ditambahkan.");
    }

    public function show(Employee $employee)
    {
        $employee->load('salaries.creator');
        $totalGajiDibayar = $employee->salaries->whereNotNull('paid_at')->sum('total_salary');

        // Saldo omset bulan ini vs total gaji semua karyawan bulan ini
        $year = now()->year;
        $month = now()->month;
        $omsetBulanIni = DailyRevenue::whereYear('date', $year)->whereMonth('date', $month)
            ->selectRaw('SUM(qris_amount + tunai_amount) as total')->value('total') ?? 0;
        $totalGajiBulanIni = EmployeeSalary::where('period_year', $year)
            ->where('period_month', $month)->sum('total_salary');

        $positions = self::POSITIONS;
        $departments = self::DEPARTMENTS;
        $banks = self::BANKS;

        return view('employees.show', compact(
            'employee', 'totalGajiDibayar',
            'omsetBulanIni', 'totalGajiBulanIni',
            'positions', 'departments', 'banks'
        ));
    }

    public function edit(Employee $employee)
    {
        $positions = self::POSITIONS;
        $departments = self::DEPARTMENTS;
        $banks = self::BANKS;

        return view('employees.edit', compact('employee', 'positions', 'departments', 'banks'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'bank_name' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:30',
            'account_name' => 'nullable|string|max:100',
            'position' => 'required|string|max:50',
            'department' => 'nullable|string|max:50',
            'base_salary' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'join_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);
        $data['updated_by'] = Auth::id();
        $employee->update($data);

        return redirect()->route('employees.show', $employee)->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $name = $employee->name;
        $employee->delete();

        return redirect()->route('employees.index')->with('success', "Karyawan \"{$name}\" dihapus.");
    }
}
