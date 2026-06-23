<?php

namespace App\Http\Controllers;

use App\Models\DailyRevenue;
use App\Models\Transaction;
use App\Models\TransactionType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'status', 'from', 'to']);

        $transactions = Transaction::with(['transactionType', 'creator'])
            ->filter($filters)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // Rentang tanggal dari filter (from/to)
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        // Total pemasukan diambil dari omset harian (QRIS + Tunai), ikut filter periode
        $revenueQuery = DailyRevenue::query();
        if ($from) {
            $revenueQuery->whereDate('date', '>=', $from);
        }
        if ($to) {
            $revenueQuery->whereDate('date', '<=', $to);
        }

        $totalOmsetQris = (clone $revenueQuery)->sum('qris_amount');
        $totalOmsetTunai = (clone $revenueQuery)->sum('tunai_amount');

        $pengeluaranQuery = Transaction::whereHas('transactionType', fn ($q) => $q->where('category', 'pengeluaran'))
            ->where('status', 'approved');
        if ($from) {
            $pengeluaranQuery->whereDate('transaction_date', '>=', $from);
        }
        if ($to) {
            $pengeluaranQuery->whereDate('transaction_date', '<=', $to);
        }

        $summary = [
            'pemasukan' => $totalOmsetQris + $totalOmsetTunai,
            'pemasukan_qris' => $totalOmsetQris,
            'pemasukan_tunai' => $totalOmsetTunai,
            'pengeluaran' => $pengeluaranQuery->sum('amount'),
        ];
        $summary['saldo'] = $summary['pemasukan'] - $summary['pengeluaran'];

        // Label periode untuk ditampilkan di summary card
        $periodLabel = $from && $to
            ? Carbon::parse($from)->translatedFormat('d M Y').' – '.Carbon::parse($to)->translatedFormat('d M Y')
            : 'Semua Periode';

        return view('transactions.index', compact('transactions', 'filters', 'summary', 'periodLabel'));
    }

    public function create()
    {
        $types = TransactionType::active()->orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('transactions.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_date' => ['required', 'date'],
            'transaction_type_id' => ['required', 'exists:transaction_types,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
            'nota' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'status' => ['required', 'in:draft,pending'],
        ]);

        $notaPath = null;
        if ($request->hasFile('nota')) {
            $notaPath = $request->file('nota')->store('notas', 'public');
        }

        $transaction = Transaction::create([
            'transaction_date' => $request->transaction_date,
            'transaction_type_id' => $request->transaction_type_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'nota_path' => $notaPath,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('transactions.index')
            ->with('success', "Transaksi #{$transaction->id} berhasil disimpan.");
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['transactionType', 'creator', 'approver']);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $isAdmin = auth()->user()->hasAnyRole(['admin', 'owner']);

        if (! $isAdmin && in_array($transaction->status, ['approved', 'rejected'])) {
            return back()->with('error', 'Transaksi yang sudah disetujui/ditolak tidak dapat diedit.');
        }

        $types = TransactionType::active()->orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('transactions.edit', compact('transaction', 'types'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $isAdmin = auth()->user()->hasAnyRole(['admin', 'owner']);

        if (! $isAdmin && in_array($transaction->status, ['approved', 'rejected'])) {
            return back()->with('error', 'Transaksi yang sudah disetujui/ditolak tidak dapat diedit.');
        }

        // Admin editing approved transaction keeps status locked to 'approved'
        $statusRules = $isAdmin && $transaction->status === 'approved'
            ? ['required', 'in:approved']
            : ['required', 'in:draft,pending'];

        $request->validate([
            'transaction_date' => ['required', 'date'],
            'transaction_type_id' => ['required', 'exists:transaction_types,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
            'nota' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'status' => $statusRules,
            'remove_nota' => ['nullable', 'boolean'],
        ]);

        $notaPath = $transaction->nota_path;

        if ($request->boolean('remove_nota') && $notaPath) {
            Storage::disk('public')->delete($notaPath);
            $notaPath = null;
        }

        if ($request->hasFile('nota')) {
            if ($notaPath) {
                Storage::disk('public')->delete($notaPath);
            }
            $notaPath = $request->file('nota')->store('notas', 'public');
        }

        $transaction->update([
            'transaction_date' => $request->transaction_date,
            'transaction_type_id' => $request->transaction_type_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'nota_path' => $notaPath,
            'status' => $isAdmin && $transaction->status === 'approved' ? 'approved' : $request->status,
        ]);

        return redirect()->route('transactions.index')
            ->with('success', "Transaksi #{$transaction->id} berhasil diperbarui.");
    }

    public function destroy(Transaction $transaction)
    {
        // Admin/owner bisa hapus semua status, user lain hanya bisa hapus non-approved
        if (! auth()->user()->hasAnyRole(['admin', 'owner']) && $transaction->status === 'approved') {
            return back()->with('error', 'Transaksi yang sudah disetujui tidak dapat dihapus.');
        }

        if ($transaction->nota_path) {
            Storage::disk('public')->delete($transaction->nota_path);
        }

        $id = $transaction->id;
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', "Transaksi #{$id} berhasil dihapus.");
    }

    public function approve(Transaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Hanya transaksi berstatus "Menunggu Approval" yang dapat disetujui.');
        }

        $transaction->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Transaksi #{$transaction->id} telah disetujui.");
    }

    public function reject(Request $request, Transaction $transaction)
    {
        $request->validate(['rejection_reason' => ['required', 'string', 'max:255']]);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Hanya transaksi berstatus "Menunggu Approval" yang dapat ditolak.');
        }

        $transaction->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "Transaksi #{$transaction->id} telah ditolak.");
    }
}
