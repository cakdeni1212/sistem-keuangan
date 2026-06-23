<?php

namespace App\Http\Controllers;

use App\Exports\DailyRevenueTemplateExport;
use App\Imports\DailyRevenueImport;
use App\Models\DailyRevenue;
use App\Models\DailySale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DailyRevenueController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        $records = DailyRevenue::forMonth($year, $month)
            ->orderByDesc('date')
            ->with('creator')
            ->get();

        $totalQris = $records->sum('qris_amount');
        $totalTunai = $records->sum('tunai_amount');
        $totalOmset = $totalQris + $totalTunai;
        $recordCount = $records->count();
        $avgOmset = $recordCount > 0 ? $totalOmset / $recordCount : 0;
        $avgQris = $recordCount > 0 ? $totalQris / $recordCount : 0;
        $avgTunai = $recordCount > 0 ? $totalTunai / $recordCount : 0;

        $months = collect(range(1, 12))->mapWithKeys(fn ($m) => [$m => Carbon::create()->month($m)->isoFormat('MMMM')]);
        $years = range(now()->year - 2, now()->year + 1);

        $periodLabel = ($months[$month] ?? '-').' '.$year;

        // Stats berdasarkan filter periode aktif
        $allTimeOmset = $totalOmset;
        $allTimeQris = $totalQris;
        $allTimeTunai = $totalTunai;

        // Sort ascending untuk chart
        $chartRecords = $records->sortBy('date')->values();
        $chartLabels = $chartRecords->map(fn ($r) => [
            $r->date->isoFormat('ddd'),
            $r->date->format('d M'),
        ])->toArray();
        $chartQris = $chartRecords->map(fn ($r) => (float) $r->qris_amount)->toArray();
        $chartTunai = $chartRecords->map(fn ($r) => (float) $r->tunai_amount)->toArray();
        $chartTotal = $chartRecords->map(fn ($r) => (float) $r->qris_amount + (float) $r->tunai_amount)->toArray();

        // Data penjualan harian (daily_sales) per tanggal untuk bulan yang sama
        $penjualanHarian = DailySale::selectRaw(
            'sale_date, SUM(subtotal) as total_omset, SUM(hpp_total) as total_hpp, SUM(profit) as total_profit, SUM(quantity_sold) as total_qty'
        )
            ->whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month)
            ->groupBy('sale_date')
            ->get()
            ->keyBy(fn ($r) => Carbon::parse($r->sale_date)->toDateString());

        // Gabungkan semua tanggal (dari daily_revenues + penjualan_harian) agar baris tidak hilang
        $allDates = collect($records->map(fn ($r) => $r->date->toDateString())->all())
            ->merge($penjualanHarian->keys())
            ->unique()
            ->sort()
            ->reverse()
            ->values();

        return view('daily-revenues.index', compact(
            'records', 'year', 'month', 'months', 'years', 'periodLabel',
            'totalQris', 'totalTunai', 'totalOmset', 'recordCount',
            'avgOmset', 'avgQris', 'avgTunai',
            'allTimeOmset', 'allTimeQris', 'allTimeTunai',
            'chartLabels', 'chartQris', 'chartTunai', 'chartTotal',
            'penjualanHarian', 'allDates'
        ));
    }

    public function create(Request $request)
    {
        $today = $request->get('date', now()->toDateString());
        $existing = DailyRevenue::where('date', $today)->first();
        if ($existing) {
            return redirect()->route('daily-revenues.edit', $existing)
                ->with('info', 'Omset untuk tanggal ini sudah ada, silakan edit.');
        }

        // Cek apakah ada data penjualan harian untuk tanggal ini
        $penjualanWarning = DailySale::whereDate('sale_date', $today)->exists()
            ? DailySale::selectRaw('SUM(subtotal) as total_omset, SUM(quantity_sold) as total_qty')
                ->whereDate('sale_date', $today)
                ->first()
            : null;

        return view('daily-revenues.create', compact('today', 'penjualanWarning'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date|unique:daily_revenues,date',
            'qris_amount' => 'required|numeric|min:0',
            'tunai_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['created_by'] = Auth::id();

        DailyRevenue::create($data);

        return redirect()->route('daily-revenues.index')
            ->with('success', 'Omset harian berhasil disimpan.');
    }

    public function edit(DailyRevenue $dailyRevenue)
    {
        return view('daily-revenues.edit', compact('dailyRevenue'));
    }

    public function update(Request $request, DailyRevenue $dailyRevenue)
    {
        $data = $request->validate([
            'date' => 'required|date|unique:daily_revenues,date,'.$dailyRevenue->id,
            'qris_amount' => 'required|numeric|min:0',
            'tunai_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['updated_by'] = Auth::id();
        $dailyRevenue->update($data);

        return redirect()->route('daily-revenues.index')
            ->with('success', 'Omset harian berhasil diperbarui.');
    }

    public function destroy(DailyRevenue $dailyRevenue)
    {
        $dailyRevenue->delete();

        return back()->with('success', 'Data omset dihapus.');
    }

    /** Download template Excel */
    public function downloadTemplate()
    {
        return Excel::download(
            new DailyRevenueTemplateExport,
            'template-omset-harian.xlsx'
        );
    }

    /** Show upload form */
    public function uploadForm()
    {
        return view('daily-revenues.upload');
    }

    /** Process uploaded Excel */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new DailyRevenueImport;
        Excel::import($import, $request->file('file'));

        $msg = "Berhasil mengimpor {$import->imported} data omset.";
        if ($import->skipped > 0) {
            $msg .= " {$import->skipped} baris dilewati.";
        }

        return redirect()->route('daily-revenues.index')
            ->with('success', $msg)
            ->with('import_errors', $import->errors);
    }
}
