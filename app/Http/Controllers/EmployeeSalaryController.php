<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeSalaryController extends Controller
{
    /** Cari transaction_type_id untuk "Gaji Karyawan" (category=pengeluaran) */
    private function gajiTypeId(): int
    {
        return TransactionType::where('category', 'pengeluaran')
            ->where('name', 'like', '%Gaji%')
            ->value('id')
            ?? TransactionType::where('category', 'pengeluaran')->value('id');
    }

    /** Buat transaksi pengeluaran gaji dan kembalikan id-nya */
    private function createSalaryTransaction(EmployeeSalary $salary, Employee $employee): int
    {
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $period = ($months[$salary->period_month] ?? $salary->period_month).' '.$salary->period_year;

        $trx = Transaction::create([
            'transaction_date' => $salary->paid_at ?? now()->toDateString(),
            'transaction_type_id' => $this->gajiTypeId(),
            'amount' => $salary->total_salary,
            'description' => 'Gaji '.$employee->name.' - '.$period,
            'status' => 'approved',
            'created_by' => Auth::id(),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return $trx->id;
    }

    /** Hapus transaksi yang terhubung (jika ada) */
    private function deleteSalaryTransaction(EmployeeSalary $salary): void
    {
        if ($salary->transaction_id) {
            Transaction::where('id', $salary->transaction_id)->delete();
            $salary->update(['transaction_id' => null]);
        }
    }

    public function create(Employee $employee)
    {
        $existingMonths = $employee->salaries()->pluck('period_month', 'period_year')->toArray();

        return view('employee-salaries.create', compact('employee', 'existingMonths'));
    }

    public function store(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2099',
            'base_salary' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:30',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $exists = $employee->salaries()
            ->where('period_month', $data['period_month'])
            ->where('period_year', $data['period_year'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['period_month' => 'Data gaji bulan ini sudah ada.'])->withInput();
        }

        $data['employee_id'] = $employee->id;
        $data['bonus'] = $data['bonus'] ?? 0;
        $data['deductions'] = $data['deductions'] ?? 0;
        $data['total_salary'] = $data['base_salary'] + $data['bonus'] - $data['deductions'];
        $data['created_by'] = Auth::id();

        DB::transaction(function () use (&$data, $employee) {
            $salary = EmployeeSalary::create($data);
            if (! empty($data['paid_at'])) {
                $salary->update(['transaction_id' => $this->createSalaryTransaction($salary, $employee)]);
            }
        });

        return redirect()->route('employees.show', $employee)->with('success', 'Data gaji berhasil disimpan dan saldo omset diperbarui.');
    }

    public function edit(Employee $employee, EmployeeSalary $salary)
    {
        return view('employee-salaries.edit', compact('employee', 'salary'));
    }

    public function update(Request $request, Employee $employee, EmployeeSalary $salary)
    {
        $data = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:30',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['bonus'] = $data['bonus'] ?? 0;
        $data['deductions'] = $data['deductions'] ?? 0;
        $data['total_salary'] = $data['base_salary'] + $data['bonus'] - $data['deductions'];

        DB::transaction(function () use ($data, $salary, $employee) {
            $wasUnpaid = empty($salary->paid_at);
            $isNowPaid = ! empty($data['paid_at']);

            $salary->update($data);

            if ($wasUnpaid && $isNowPaid && ! $salary->transaction_id) {
                // Baru dibayar — buat transaksi
                $salary->update(['transaction_id' => $this->createSalaryTransaction($salary, $employee)]);
            } elseif (! $wasUnpaid && ! $isNowPaid && $salary->transaction_id) {
                // Dibatalkan pembayaran — hapus transaksi
                $this->deleteSalaryTransaction($salary);
            } elseif ($salary->transaction_id && $isNowPaid) {
                // Update nominal transaksi yang sudah ada
                Transaction::where('id', $salary->transaction_id)->update([
                    'amount' => $salary->total_salary,
                    'transaction_date' => $salary->paid_at,
                ]);
            }
        });

        return redirect()->route('employees.show', $employee)->with('success', 'Data gaji diperbarui.');
    }

    public function destroy(Employee $employee, EmployeeSalary $salary)
    {
        DB::transaction(function () use ($salary) {
            $this->deleteSalaryTransaction($salary);
            $salary->delete();
        });

        return redirect()->route('employees.show', $employee)->with('success', 'Data gaji dihapus.');
    }

    public function markPaid(Request $request, Employee $employee, EmployeeSalary $salary)
    {
        $data = $request->validate([
            'paid_at' => 'required|date',
            'payment_method' => 'nullable|string|max:30',
        ]);

        DB::transaction(function () use ($data, $salary, $employee) {
            $salary->update($data);

            if (! $salary->transaction_id) {
                $salary->update(['transaction_id' => $this->createSalaryTransaction($salary, $employee)]);
            } else {
                Transaction::where('id', $salary->transaction_id)->update([
                    'amount' => $salary->total_salary,
                    'transaction_date' => $salary->paid_at,
                ]);
            }
        });

        return back()->with('success', 'Gaji '.$employee->name.' ('.$salary->period_label.') ditandai lunas dan saldo omset diperbarui.');
    }

    public function slip(Employee $employee, EmployeeSalary $salary)
    {
        return view('employee-salaries.slip', compact('employee', 'salary'));
    }
}
