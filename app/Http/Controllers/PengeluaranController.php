<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengeluaranController extends Controller
{
    public function index(Request $request): View
    {
        $year = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);
        $filterDate = $request->input('date');

        // Pastikan filterDate sesuai bulan/tahun yang dipilih
        if ($filterDate) {
            $fd = Carbon::parse($filterDate);
            if ($fd->year !== $year || $fd->month !== $month) {
                $filterDate = null;
            }
        }

        $months = collect(range(1, 12))->mapWithKeys(
            fn ($m) => [$m => Carbon::create()->month($m)->isoFormat('MMMM')]
        );
        $years = range(now()->year - 2, now()->year + 1);
        $periodLabel = $filterDate
            ? Carbon::parse($filterDate)->isoFormat('D MMMM YYYY')
            : ($months[$month] ?? '-').' '.$year;

        // Semua transaksi bulan ini (untuk chart & stats)
        $monthlyRecords = Transaction::with(['transactionType', 'creator'])
            ->whereHas('transactionType', fn ($q) => $q->where('category', 'pengeluaran'))
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        // Jika ada filter tanggal, stats & per-jenis pakai data hari itu saja
        $allRecords = $filterDate
            ? $monthlyRecords->filter(fn ($t) => $t->transaction_date->format('Y-m-d') === $filterDate)->values()
            : $monthlyRecords;

        // Paginated untuk tabel (filter tanggal diterapkan di query)
        $recordsQuery = Transaction::with(['transactionType', 'creator', 'approver'])
            ->whereHas('transactionType', fn ($q) => $q->where('category', 'pengeluaran'))
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        if ($filterDate) {
            $recordsQuery->whereDate('transaction_date', $filterDate);
        }

        $records = $recordsQuery->paginate(20)->withQueryString();

        // Total hanya dari yang approved
        $approved = $allRecords->where('status', 'approved');
        $totalPengeluaran = $approved->sum('amount');
        $totalAllCount = $allRecords->count();
        $pendingTotal = $allRecords->whereIn('status', ['draft', 'pending'])->sum('amount');

        // Per jenis (approved, semua waktu filtered ke bulan ini)
        $perJenis = $approved
            ->groupBy('transaction_type_id')
            ->map(fn ($group) => [
                'name' => $group->first()->transactionType->name,
                'total' => (float) $group->sum('amount'),
                'count' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values()
            ->take(8);

        // Banner stats periode
        $allTimePengeluaran = $totalPengeluaran;

        // --- Chart: selalu tampilkan full bulan untuk konteks ---
        $chartApproved = $monthlyRecords->where('status', 'approved');
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $dailyMap = $chartApproved->groupBy(fn ($t) => $t->transaction_date->format('Y-m-d'));

        $chartLabels = [];
        $chartData = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d);
            $key = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d M');
            $chartData[] = (float) ($dailyMap->get($key)?->sum('amount') ?? 0);
        }

        // --- Bulan lalu untuk perbandingan ---
        $prevDate = Carbon::create($year, $month)->subMonth();
        $prevTotal = Transaction::whereHas('transactionType', fn ($q) => $q->where('category', 'pengeluaran'))
            ->where('status', 'approved')
            ->whereYear('transaction_date', $prevDate->year)
            ->whereMonth('transaction_date', $prevDate->month)
            ->sum('amount');

        $selisih = $totalPengeluaran - $prevTotal;
        $pctChange = $prevTotal > 0 ? ($selisih / $prevTotal) * 100 : null;
        // Total per grup (Dapur / BAR / Operasional) dari yang approved
        $dapurTotal = $approved->filter(fn ($t) => $t->transactionType?->grup === 'Dapur')->sum('amount');
        $barTotal = $approved->filter(fn ($t) => $t->transactionType?->grup === 'BAR')->sum('amount');
        $operasionalTotal = $approved->filter(fn ($t) => $t->transactionType?->grup === 'Operasional')->sum('amount');

        $approvedCount = $approved->count();

        return view('pengeluaran.index', compact(
            'records', 'year', 'month', 'months', 'years', 'periodLabel', 'filterDate',
            'totalPengeluaran', 'allTimePengeluaran', 'totalAllCount', 'approvedCount', 'pendingTotal',
            'perJenis',
            'chartLabels', 'chartData',
            'prevTotal', 'selisih', 'pctChange',
            'dapurTotal', 'barTotal', 'operasionalTotal',
        ));
    }
}
