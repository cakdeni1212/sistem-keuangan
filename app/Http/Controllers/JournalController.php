<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = Journal::with(['creator'])
            ->orderByDesc('journal_date')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('journal_number', 'like', "%{$search}%");
        }

        if ($request->filled('from')) {
            $query->whereDate('journal_date', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('journal_date', '<=', $request->input('to'));
        }

        $journals = $query->paginate(15)->withQueryString();

        $filters = $request->only(['search', 'from', 'to']);

        return view('journals.index', compact('journals', 'filters'));
    }

    public function create()
    {
        $accounts = Account::active()
            ->orderBy('code')
            ->get()
            ->groupBy('account_type');

        $journalTypes = Journal::journalTypes();

        return view('journals.create', compact('accounts', 'journalTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'journal_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'reference' => ['nullable', 'string', 'max:255'],
            'journal_type' => ['required', 'string', 'in:' . implode(',', Journal::journalTypes())],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:accounts,id'],
            'lines.*.description' => ['nullable', 'string', 'max:500'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $totalDebit = collect($request->lines)->sum(fn ($l) => (float) ($l['debit'] ?? 0));
        $totalCredit = collect($request->lines)->sum(fn ($l) => (float) ($l['credit'] ?? 0));

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->with('error', 'Jurnal tidak seimbang. Total Debit: ' . number_format($totalDebit, 2) . ', Total Kredit: ' . number_format($totalCredit, 2));
        }

        $journal = DB::transaction(function () use ($request, $totalDebit, $totalCredit) {
            $journal = Journal::create([
                'journal_number' => Journal::generateNumber(),
                'journal_date' => $request->journal_date,
                'description' => $request->description,
                'reference' => $request->reference,
                'journal_type' => $request->journal_type,
                'created_by' => auth()->id(),
                'is_posted' => false,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            foreach ($request->lines as $line) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);

                if ($debit == 0 && $credit == 0) {
                    continue;
                }

                $journal->lines()->create([
                    'account_id' => $line['account_id'],
                    'description' => $line['description'] ?? null,
                    'debit' => $debit,
                    'credit' => $credit,
                ]);
            }

            return $journal;
        });

        return redirect()->route('journals.show', $journal)
            ->with('success', 'Jurnal ' . $journal->journal_number . ' berhasil disimpan.');
    }

    public function show(Journal $journal)
    {
        $journal->load(['lines.account', 'creator']);

        return view('journals.show', compact('journal'));
    }

    public function post(Journal $journal)
    {
        if ($journal->is_posted) {
            return back()->with('error', 'Jurnal sudah diposting.');
        }

        try {
            $journal->post();

            return back()->with('success', 'Jurnal ' . $journal->journal_number . ' berhasil diposting.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function unpost(Journal $journal)
    {
        if (! $journal->is_posted) {
            return back()->with('error', 'Jurnal belum diposting.');
        }

        $journal->unpost();

        return back()->with('success', 'Jurnal ' . $journal->journal_number . ' berhasil di-unpost.');
    }
}
