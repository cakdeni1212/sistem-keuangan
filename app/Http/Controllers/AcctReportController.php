<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcctReportController extends Controller
{
    public function trialBalance(Request $request)
    {
        $accounts = Account::with('journalLines')->active()->orderBy('code')->get();

        $grouped = [];
        $totalDebit = 0;
        $totalCredit = 0;

        $typeLabels = [
            'asset' => 'Aset (Asset)',
            'liability' => 'Kewajiban (Liability)',
            'equity' => 'Ekuitas (Equity)',
            'revenue' => 'Pendapatan (Revenue)',
            'expense' => 'Beban (Expense)',
        ];

        foreach (Account::accountTypes() as $type) {
            $typeAccounts = $accounts->where('account_type', $type);
            if ($typeAccounts->isEmpty()) {
                continue;
            }

            $groupTotalDebit = 0;
            $groupTotalCredit = 0;
            $items = [];

            foreach ($typeAccounts as $account) {
                $balance = $account->balance;

                if ($account->normal_balance === 'debit') {
                    $debitBalance = max($balance, 0);
                    $creditBalance = $balance < 0 ? abs($balance) : 0;
                } else {
                    $creditBalance = max($balance, 0);
                    $debitBalance = $balance < 0 ? abs($balance) : 0;
                }

                $items[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'debit_balance' => $debitBalance,
                    'credit_balance' => $creditBalance,
                ];

                $groupTotalDebit += $debitBalance;
                $groupTotalCredit += $creditBalance;
            }

            $grouped[] = [
                'type' => $type,
                'label' => $typeLabels[$type],
                'items' => $items,
                'total_debit' => $groupTotalDebit,
                'total_credit' => $groupTotalCredit,
            ];

            $totalDebit += $groupTotalDebit;
            $totalCredit += $groupTotalCredit;
        }

        return view('acct-reports.trial-balance', compact('grouped', 'totalDebit', 'totalCredit'));
    }

    public function incomeStatement(Request $request)
    {
        $revenueAccounts = Account::active()->byType('revenue')->get();
        $expenseAccounts = Account::active()->byType('expense')->get();

        $revenueItems = [];
        $totalRevenue = 0;

        foreach ($revenueAccounts as $account) {
            $balance = $account->balance;
            $revenueItems[] = [
                'code' => $account->code,
                'name' => $account->name,
                'amount' => $balance,
            ];
            $totalRevenue += $balance;
        }

        $expenseItems = [];
        $totalExpense = 0;

        foreach ($expenseAccounts as $account) {
            $balance = $account->balance;
            $expenseItems[] = [
                'code' => $account->code,
                'name' => $account->name,
                'amount' => $balance,
            ];
            $totalExpense += $balance;
        }

        $netIncome = $totalRevenue - $totalExpense;

        return view('acct-reports.income-statement', compact(
            'revenueItems', 'totalRevenue',
            'expenseItems', 'totalExpense',
            'netIncome'
        ));
    }

    public function balanceSheet(Request $request)
    {
        $revenueAccounts = Account::active()->byType('revenue')->get();
        $expenseAccounts = Account::active()->byType('expense')->get();

        $totalRevenue = $revenueAccounts->sum(fn ($a) => $a->balance);
        $totalExpense = $expenseAccounts->sum(fn ($a) => $a->balance);
        $currentYearIncome = $totalRevenue - $totalExpense;

        $assetAccounts = Account::active()->byType('asset')->get();
        $assetItems = [];
        $totalAssets = 0;

        foreach ($assetAccounts as $account) {
            $balance = $account->balance;
            $assetItems[] = [
                'code' => $account->code,
                'name' => $account->name,
                'amount' => $balance,
            ];
            $totalAssets += $balance;
        }

        $liabilityAccounts = Account::active()->byType('liability')->get();
        $liabilityItems = [];
        $totalLiabilities = 0;

        foreach ($liabilityAccounts as $account) {
            $balance = $account->balance;
            $liabilityItems[] = [
                'code' => $account->code,
                'name' => $account->name,
                'amount' => $balance,
            ];
            $totalLiabilities += $balance;
        }

        $equityAccounts = Account::active()->byType('equity')->get();
        $equityItems = [];
        $totalEquity = 0;

        foreach ($equityAccounts as $account) {
            $balance = $account->balance;
            $equityItems[] = [
                'code' => $account->code,
                'name' => $account->name,
                'amount' => $balance,
            ];
            $totalEquity += $balance;
        }

        $totalEquityWithIncome = $totalEquity + $currentYearIncome;
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquityWithIncome;

        return view('acct-reports.balance-sheet', compact(
            'assetItems', 'totalAssets',
            'liabilityItems', 'totalLiabilities',
            'equityItems', 'totalEquity',
            'currentYearIncome', 'totalEquityWithIncome',
            'totalLiabilitiesAndEquity'
        ));
    }

    public function generalLedger(Request $request)
    {
        $accounts = Account::active()->orderBy('code')->get();

        $accountId = $request->input('account_id');
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $selectedAccount = null;
        $lines = collect();
        $beginningBalance = 0;
        $endingBalance = 0;

        if ($accountId) {
            $selectedAccount = Account::with('journalLines')->findOrFail($accountId);

            $beginningDebit = JournalEntryLine::where('account_id', $accountId)
                ->whereHas('journal', function ($q) use ($startDate) {
                    $q->where('journal_date', '<', $startDate);
                })
                ->sum('debit');

            $beginningCredit = JournalEntryLine::where('account_id', $accountId)
                ->whereHas('journal', function ($q) use ($startDate) {
                    $q->where('journal_date', '<', $startDate);
                })
                ->sum('credit');

            if ($selectedAccount->normal_balance === 'debit') {
                $beginningBalance = $beginningDebit - $beginningCredit;
            } else {
                $beginningBalance = $beginningCredit - $beginningDebit;
            }

            $lines = JournalEntryLine::with('journal')
                ->where('account_id', $accountId)
                ->whereHas('journal', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('journal_date', [$startDate, $endDate]);
                })
                ->orderBy(
                    JournalEntryLine::select('journal_date')
                        ->from('journals')
                        ->whereColumn('journals.id', 'journal_entry_lines.journal_id')
                        ->limit(1),
                    'asc'
                )
                ->orderBy('id', 'asc')
                ->get();

            $periodDebit = $lines->sum('debit');
            $periodCredit = $lines->sum('credit');

            if ($selectedAccount->normal_balance === 'debit') {
                $endingBalance = $beginningBalance + $periodDebit - $periodCredit;
            } else {
                $endingBalance = $beginningBalance + $periodCredit - $periodDebit;
            }

            $runningBalance = $beginningBalance;
            $lines = $lines->map(function ($line) use (&$runningBalance, $selectedAccount) {
                if ($selectedAccount->normal_balance === 'debit') {
                    $runningBalance += $line->debit - $line->credit;
                } else {
                    $runningBalance += $line->credit - $line->debit;
                }
                $line->running_balance = $runningBalance;
                return $line;
            });
        }

        return view('acct-reports.general-ledger', compact(
            'accounts', 'selectedAccount', 'lines',
            'accountId', 'startDate', 'endDate',
            'beginningBalance', 'endingBalance'
        ));
    }
}
