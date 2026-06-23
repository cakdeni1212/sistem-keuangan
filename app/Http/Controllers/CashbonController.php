<?php

namespace App\Http\Controllers;

use App\Models\Cashbon;
use App\Models\Employee;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CashbonController extends Controller
{
    public function index(Request $request): View
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $years = range(now()->year, max(2024, now()->year - 4));

        $filterMonth = $request->filled('month') ? (int) $request->month : null;
        $filterYear = $request->filled('year') ? (int) $request->year : null;

        $query = Cashbon::with(['employee', 'creator'])
            ->latest('debt_date')
            ->latest('id');

        if ($request->filled('search')) {
            $query->where('debtor_name', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('status') && in_array($request->status, ['belum_bayar', 'lunas'])) {
            $query->where('status', $request->status);
        }
        if ($request->filled('debtor_type') && in_array($request->debtor_type, ['karyawan', 'pelanggan', 'supplier', 'lainnya'])) {
            $query->where('debtor_type', $request->debtor_type);
        }
        if ($filterMonth) {
            $query->whereMonth('debt_date', $filterMonth);
        }
        if ($filterYear) {
            $query->whereYear('debt_date', $filterYear);
        }

        $cashbons = $query->get();

        // Summary — ikut filter bulan/tahun jika aktif
        $summaryQ = fn () => Cashbon::when($filterMonth, fn ($q) => $q->whereMonth('debt_date', $filterMonth))
            ->when($filterYear, fn ($q) => $q->whereYear('debt_date', $filterYear));

        $totalPiutang = $summaryQ()->unpaid()->sum('amount');
        $totalLunas = $summaryQ()->paid()->sum('amount');
        $totalOverdue = $summaryQ()->unpaid()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->sum('amount');
        $periodLabel = ($filterMonth ? $months[$filterMonth].' ' : '').($filterYear ?? '');

        return view('cashbons.index', compact(
            'cashbons', 'totalPiutang', 'totalLunas', 'totalOverdue',
            'months', 'years', 'filterMonth', 'filterYear', 'periodLabel'
        ));
    }

    public function create(): View
    {
        $employees = Employee::active()->orderBy('name')->get(['id', 'name']);
        $debtorNames = Cashbon::query()
            ->distinct()
            ->orderBy('debtor_name')
            ->pluck('debtor_name');

        return view('cashbons.create', compact('employees', 'debtorNames'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'debtor_name' => 'required|string|max:100',
            'debtor_type' => 'required|in:karyawan,pelanggan,supplier,lainnya',
            'employee_id' => 'nullable|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'notes' => 'nullable|string|max:500',
            'receipt' => 'nullable|image|max:5120',
        ]);

        if ($data['debtor_type'] !== 'karyawan') {
            $data['employee_id'] = null;
        }

        $data['status'] = 'belum_bayar';
        $data['created_by'] = Auth::id();
        unset($data['receipt']);

        $cashbon = Cashbon::create($data);

        if ($request->hasFile('receipt')) {
            $cashbon->update([
                'receipt_path' => $request->file('receipt')->store('cashbons/receipts', 'public'),
            ]);
        }

        $outTypeId = TransactionType::where('name', 'Cashbon Keluar')->value('id');

        $trx = Transaction::create([
            'transaction_date' => $cashbon->debt_date->toDateString(),
            'transaction_type_id' => $outTypeId,
            'amount' => $cashbon->amount,
            'description' => 'Cashbon: '.$cashbon->debtor_name.($cashbon->description ? ' - '.$cashbon->description : ''),
            'status' => 'approved',
            'created_by' => Auth::id(),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $cashbon->update(['out_transaction_id' => $trx->id]);

        return redirect()->route('cashbons.index')
            ->with('success', 'Cashbon berhasil ditambahkan.');
    }

    public function edit(Cashbon $cashbon): View
    {
        abort_if($cashbon->status === 'lunas', 403, 'Cashbon yang sudah lunas tidak dapat diedit.');

        $employees = Employee::active()->orderBy('name')->get(['id', 'name']);

        return view('cashbons.edit', compact('cashbon', 'employees'));
    }

    public function update(Request $request, Cashbon $cashbon): RedirectResponse
    {
        abort_if($cashbon->status === 'lunas', 403, 'Cashbon yang sudah lunas tidak dapat diedit.');

        $data = $request->validate([
            'debtor_name' => 'required|string|max:100',
            'debtor_type' => 'required|in:karyawan,pelanggan,supplier,lainnya',
            'employee_id' => 'nullable|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'notes' => 'nullable|string|max:500',
            'receipt' => 'nullable|image|max:5120',
            'remove_receipt' => 'nullable|boolean',
        ]);

        if ($data['debtor_type'] !== 'karyawan') {
            $data['employee_id'] = null;
        }

        unset($data['receipt'], $data['remove_receipt']);

        if ($request->hasFile('receipt')) {
            if ($cashbon->receipt_path) {
                Storage::disk('public')->delete($cashbon->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')->store('cashbons/receipts', 'public');
        } elseif ($request->boolean('remove_receipt') && $cashbon->receipt_path) {
            Storage::disk('public')->delete($cashbon->receipt_path);
            $data['receipt_path'] = null;
        }

        $cashbon->update($data);

        if ($cashbon->out_transaction_id) {
            Transaction::where('id', $cashbon->out_transaction_id)->update([
                'transaction_date' => $cashbon->fresh()->debt_date->toDateString(),
                'amount' => $data['amount'],
                'description' => 'Cashbon: '.$data['debtor_name'].($data['description'] ? ' - '.$data['description'] : ''),
            ]);
        }

        return redirect()->route('cashbons.index')
            ->with('success', 'Cashbon berhasil diperbarui.');
    }

    public function destroy(Cashbon $cashbon): RedirectResponse
    {
        if ($cashbon->receipt_path) {
            Storage::disk('public')->delete($cashbon->receipt_path);
        }
        if ($cashbon->payment_receipt_path) {
            Storage::disk('public')->delete($cashbon->payment_receipt_path);
        }

        if ($cashbon->out_transaction_id) {
            Transaction::destroy($cashbon->out_transaction_id);
        }

        if ($cashbon->in_transaction_id) {
            Transaction::destroy($cashbon->in_transaction_id);
        }

        $cashbon->delete();

        return redirect()->route('cashbons.index')
            ->with('success', 'Cashbon berhasil dihapus.');
    }

    public function markPaid(Request $request, Cashbon $cashbon): RedirectResponse
    {
        abort_if($cashbon->status === 'lunas', 403, 'Cashbon ini sudah lunas.');

        $data = $request->validate([
            'paid_at' => 'required|date',
            'payment_receipt' => 'nullable|image|max:5120',
        ]);

        $inTypeId = TransactionType::where('name', 'Pembayaran Cashbon')->value('id');

        $trx = Transaction::create([
            'transaction_date' => $data['paid_at'],
            'transaction_type_id' => $inTypeId,
            'amount' => $cashbon->amount,
            'description' => 'Bayar Cashbon: '.$cashbon->debtor_name,
            'status' => 'approved',
            'created_by' => Auth::id(),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $updateData = [
            'in_transaction_id' => $trx->id,
            'status' => 'lunas',
            'paid_at' => $data['paid_at'],
        ];

        if ($request->hasFile('payment_receipt')) {
            $updateData['payment_receipt_path'] = $request->file('payment_receipt')
                ->store('cashbons/receipts', 'public');
        }

        $cashbon->update($updateData);

        return redirect()->route('cashbons.index')
            ->with('success', 'Cashbon '.$cashbon->debtor_name.' telah ditandai lunas.');
    }
}
