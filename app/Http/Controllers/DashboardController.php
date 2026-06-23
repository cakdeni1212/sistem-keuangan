<?php

namespace App\Http\Controllers;

use App\Models\DailyRevenue;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (auth()->user()->hasRole('kasir')) {
            return redirect()->route('kasir.index');
        }

        $thisYear = now()->year;
        $thisMonth = now()->month;
        $lastMonth = now()->subMonth();

        // --- Omset Hari Ini ---
        $hariIni = DailyRevenue::whereDate('date', today())
            ->selectRaw('SUM(qris_amount) as qris, SUM(tunai_amount) as tunai')
            ->first();
        $omsetHariIni = (float) (($hariIni->qris ?? 0) + ($hariIni->tunai ?? 0));
        $qrisHariIni = (float) ($hariIni->qris ?? 0);
        $tunaiHariIni = (float) ($hariIni->tunai ?? 0);

        // --- Omset Bulan Ini ---
        $bulanIni = DailyRevenue::whereYear('date', $thisYear)
            ->whereMonth('date', $thisMonth)
            ->selectRaw('SUM(qris_amount) as qris, SUM(tunai_amount) as tunai, COUNT(*) as hari')
            ->first();

        $omsetBulanIni = (float) (($bulanIni->qris ?? 0) + ($bulanIni->tunai ?? 0));
        $qrisBulanIni = (float) ($bulanIni->qris ?? 0);
        $tunaiBulanIni = (float) ($bulanIni->tunai ?? 0);
        $hariBulanIni = (int) ($bulanIni->hari ?? 0);

        // Omset bulan lalu untuk perbandingan
        $bulanLalu = DailyRevenue::whereYear('date', $lastMonth->year)
            ->whereMonth('date', $lastMonth->month)
            ->selectRaw('SUM(qris_amount + tunai_amount) as total')
            ->first();

        $omsetBulanLalu = (float) ($bulanLalu->total ?? 0);
        $selisihOmset = $omsetBulanIni - $omsetBulanLalu;
        $pctChange = $omsetBulanLalu > 0
            ? ($selisihOmset / $omsetBulanLalu) * 100
            : null;

        // --- Omset Tahun Ini ---
        $tahunIni = DailyRevenue::whereYear('date', $thisYear)
            ->selectRaw('SUM(qris_amount) as qris, SUM(tunai_amount) as tunai, COUNT(*) as hari')
            ->first();
        $omsetTahunIni = (float) (($tahunIni->qris ?? 0) + ($tahunIni->tunai ?? 0));
        $qrisTahunIni = (float) ($tahunIni->qris ?? 0);
        $tunaiTahunIni = (float) ($tahunIni->tunai ?? 0);
        $hariTahunIni = (int) ($tahunIni->hari ?? 0);

        // Omset tahun lalu untuk perbandingan
        $tahunLaluRow = DailyRevenue::whereYear('date', $thisYear - 1)
            ->selectRaw('SUM(qris_amount + tunai_amount) as total')
            ->first();
        $omsetTahunLalu = (float) ($tahunLaluRow->total ?? 0);
        $selisihTahun = $omsetTahunIni - $omsetTahunLalu;
        $pctChangeTahun = $omsetTahunLalu > 0
            ? ($selisihTahun / $omsetTahunLalu) * 100
            : null;

        // --- Summary cards (all-time) ---
        $totalOmset = DailyRevenue::sum(DB::raw('qris_amount + tunai_amount'));
        $totalQris = DailyRevenue::sum('qris_amount');
        $totalTunai = DailyRevenue::sum('tunai_amount');
        $totalPengeluaran = Transaction::whereHas('transactionType', fn ($q) => $q->where('category', 'pengeluaran'))
            ->where('status', 'approved')->sum('amount');
        $saldo = $totalOmset - $totalPengeluaran;
        $hariTercatat = DailyRevenue::count();

        // --- Grafik: Omset 6 bulan terakhir (SQLite-safe) ---
        $monthlyData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $y = $date->year;
            $m = $date->month;
            $row = DailyRevenue::whereYear('date', $y)->whereMonth('date', $m)
                ->selectRaw('SUM(qris_amount) as qris, SUM(tunai_amount) as tunai')
                ->first();
            $monthlyData->push([
                'label' => $date->format('M Y'),
                'qris' => (float) ($row->qris ?? 0),
                'tunai' => (float) ($row->tunai ?? 0),
            ]);
        }

        // --- Grafik: Pengeluaran per jenis transaksi (top 6) ---
        // Optimize: aggregate in the database to avoid loading all transactions into memory
        $pengeluaranPerJenis = Transaction::where('status', 'approved')
            ->whereHas('transactionType', fn ($q) => $q->where('category', 'pengeluaran'))
            ->select('transaction_type_id', DB::raw('SUM(amount) as total'))
            ->groupBy('transaction_type_id')
            ->orderByDesc('total')
            ->limit(6)
            ->with('transactionType')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->transactionType->name,
                'total' => (float) $row->total,
            ]);

        // --- Transaksi terbaru (5) ---
        $recentTransactions = Transaction::with(['transactionType', 'creator'])
            ->orderByDesc('transaction_date')->orderByDesc('id')
            ->limit(5)->get();

        // --- Omset terbaru (5 hari) ---
        $recentOmset = DailyRevenue::orderByDesc('date')->limit(5)
            ->selectRaw('*, (qris_amount + tunai_amount) as total')->get();

        // --- Grafik: Omset harian 14 hari terakhir ---
        $dailyOmsetData = collect();
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $row = DailyRevenue::whereDate('date', $d->toDateString())
                ->selectRaw('SUM(qris_amount) as qris, SUM(tunai_amount) as tunai')
                ->first();
            $dailyOmsetData->push([
                'label' => $d->isoFormat('ddd, D MMM'),
                'qris' => (float) ($row->qris ?? 0),
                'tunai' => (float) ($row->tunai ?? 0),
            ]);
        }

        return view('dashboard', compact(
            'omsetHariIni', 'qrisHariIni', 'tunaiHariIni',
            'omsetBulanIni', 'qrisBulanIni', 'tunaiBulanIni', 'hariBulanIni',
            'omsetBulanLalu', 'selisihOmset', 'pctChange',
            'omsetTahunIni', 'qrisTahunIni', 'tunaiTahunIni', 'hariTahunIni',
            'omsetTahunLalu', 'selisihTahun', 'pctChangeTahun',
            'totalOmset', 'totalQris', 'totalTunai',
            'totalPengeluaran', 'saldo', 'hariTercatat',
            'monthlyData', 'pengeluaranPerJenis',
            'recentTransactions', 'recentOmset', 'dailyOmsetData'
        ));
    }
}
