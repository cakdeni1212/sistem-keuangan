<?php

namespace App\Http\Controllers;

use App\Models\DailySale;
use App\Models\HppProduct;
use App\Models\KasirItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DailySaleController extends Controller
{
    public function index(Request $request): View
    {
        $filterDate = $request->get('date');
        $filterCat = $request->get('cat'); // 'minuman', 'snack', 'makanan', or null = semua
        $sortBy = in_array($request->get('sort'), ['produk', 'harga', 'qty', 'omset', 'hpp', 'profit'])
            ? $request->get('sort')
            : 'omset';
        $sortDir = $request->get('dir') === 'asc' ? 'asc' : 'desc';
        $month = $filterDate
            ? (int) Carbon::parse($filterDate)->format('m')
            : $request->integer('month', now()->month);
        $year = $filterDate
            ? (int) Carbon::parse($filterDate)->format('Y')
            : $request->integer('year', now()->year);

        // Peta filter kategori → nama kategori DB
        $categoryGroups = [
            'coffe' => ['Coffe'],
            'minuman' => ['Minuman'],
            'snack' => ['Snack'],
            'makanan' => ['Makanan'],
        ];
        $filterCategories = $filterCat ? ($categoryGroups[$filterCat] ?? null) : null;

        // --- Dari Penjualan Harian ---
        $dailyQuery = DailySale::selectRaw('
                daily_sales.sale_date,
                daily_sales.shift,
                SUM(daily_sales.quantity_sold)     as total_qty,
                SUM(daily_sales.subtotal)           as total_omset,
                SUM(daily_sales.hpp_total)          as total_hpp,
                SUM(daily_sales.profit)             as total_profit,
                COUNT(DISTINCT daily_sales.hpp_product_id) as total_produk
            ');

        if ($filterCategories) {
            $dailyQuery->join('hpp_products', 'daily_sales.hpp_product_id', '=', 'hpp_products.id')
                ->whereIn('hpp_products.category', $filterCategories);
        }

        if ($filterDate) {
            $dailyQuery->whereDate('daily_sales.sale_date', $filterDate);
        } else {
            $dailyQuery->whereMonth('daily_sales.sale_date', $month)->whereYear('daily_sales.sale_date', $year);
        }

        $dailyRows = $dailyQuery->groupBy('daily_sales.sale_date', 'daily_sales.shift')
            ->get()
            ->keyBy(fn ($r) => $r->sale_date.'_'.$r->shift);

        // --- Dari Kasir POS ---
        $kasirQuery = KasirItem::join('kasir_sessions', 'kasir_items.kasir_session_id', '=', 'kasir_sessions.id')
            ->selectRaw('
                DATE(kasir_sessions.date) as sale_date,
                kasir_sessions.shift,
                SUM(kasir_items.quantity) as total_qty,
                SUM(kasir_items.subtotal) as total_omset,
                SUM(kasir_items.quantity * COALESCE(hpp_products.bahan_baku + hpp_products.tenaga_kerja + hpp_products.overhead, 0)) as total_hpp,
                COUNT(DISTINCT kasir_items.product_name) as total_produk
            ');

        if ($filterCategories) {
            $kasirQuery->join('hpp_products', 'kasir_items.hpp_product_id', '=', 'hpp_products.id')
                ->whereIn('hpp_products.category', $filterCategories);
        } else {
            $kasirQuery->leftJoin('hpp_products', 'kasir_items.hpp_product_id', '=', 'hpp_products.id');
        }

        if ($filterDate) {
            $kasirQuery->whereDate('kasir_sessions.date', $filterDate);
        } else {
            $kasirQuery->whereMonth('kasir_sessions.date', $month)->whereYear('kasir_sessions.date', $year);
        }

        $kasirRows = $kasirQuery->groupBy('kasir_sessions.date', 'kasir_sessions.shift')->get();

        // Merge: daily_sales adalah master. Kasir hanya mengisi baris yang belum ada di daily_sales.
        foreach ($kasirRows as $kr) {
            $key = $kr->sale_date.'_'.$kr->shift;
            if (! isset($dailyRows[$key])) {
                $kr->total_profit = $kr->total_omset - $kr->total_hpp;
                $kr->source = 'kasir';
                $dailyRows[$key] = $kr;
            }
        }

        // Urutkan: tanggal terbaru dulu, shift pagi sebelum sore
        $rows = $dailyRows->sortBy([
            fn ($a, $b) => strcmp($b->sale_date, $a->sale_date),
            fn ($a, $b) => strcmp($a->shift, $b->shift),
        ])->values();

        $totalOmset = $rows->sum('total_omset');
        $totalProfit = $rows->sum('total_profit');
        $totalQty = $rows->sum('total_qty');

        // Grup per tanggal (pagi+sore jadi 1 baris), default sort tanggal terbaru
        $groupedRows = $rows
            ->groupBy(fn ($r) => Carbon::parse($r->sale_date)->toDateString())
            ->map(fn ($dateRows) => (object) [
                'sale_date' => $dateRows->first()->sale_date,
                'shifts' => $dateRows->values(),
                'total_qty' => $dateRows->sum('total_qty'),
                'total_omset' => $dateRows->sum('total_omset'),
                'total_hpp' => $dateRows->sum('total_hpp'),
                'total_profit' => $dateRows->sum('total_profit'),
            ])
            ->sortByDesc(fn ($g) => $g->sale_date)
            ->values();

        // --- Breakdown per kategori (selalu tanpa filter kategori) ---
        $catQueryBase = DailySale::join('hpp_products', 'daily_sales.hpp_product_id', '=', 'hpp_products.id')
            ->selectRaw('hpp_products.category, SUM(daily_sales.quantity_sold) as qty, SUM(daily_sales.subtotal) as omset, SUM(daily_sales.profit) as profit, SUM(daily_sales.hpp_total) as hpp');

        if ($filterDate) {
            $catQueryBase->whereDate('daily_sales.sale_date', $filterDate);
        } else {
            $catQueryBase->whereMonth('daily_sales.sale_date', $month)->whereYear('daily_sales.sale_date', $year);
        }

        $catRaw = $catQueryBase->groupBy('hpp_products.category')->get()->keyBy('category');

        // Kasir-only rows → tambahkan ke breakdown
        $kasirOnlyPairs = $rows->filter(fn ($r) => ($r->source ?? '') === 'kasir')->values();

        if ($kasirOnlyPairs->isNotEmpty()) {
            $kasirCatQuery = KasirItem::join('kasir_sessions', 'kasir_items.kasir_session_id', '=', 'kasir_sessions.id')
                ->leftJoin('hpp_products', 'kasir_items.hpp_product_id', '=', 'hpp_products.id')
                ->selectRaw('
                    hpp_products.category,
                    SUM(kasir_items.quantity) as qty,
                    SUM(kasir_items.subtotal) as omset,
                    SUM(kasir_items.quantity * COALESCE(hpp_products.bahan_baku + hpp_products.tenaga_kerja + hpp_products.overhead, 0)) as hpp
                ')
                ->where(function ($q) use ($kasirOnlyPairs) {
                    foreach ($kasirOnlyPairs as $pair) {
                        $q->orWhere(function ($inner) use ($pair) {
                            $inner->whereDate('kasir_sessions.date', $pair->sale_date)
                                ->where('kasir_sessions.shift', $pair->shift);
                        });
                    }
                })
                ->groupBy('hpp_products.category')
                ->get();

            foreach ($kasirCatQuery as $row) {
                $cat = $row->category;
                if (isset($catRaw[$cat])) {
                    $catRaw[$cat]->qty += $row->qty;
                    $catRaw[$cat]->omset += $row->omset;
                    $catRaw[$cat]->hpp += $row->hpp;
                    $catRaw[$cat]->profit = $catRaw[$cat]->omset - $catRaw[$cat]->hpp;
                } else {
                    $row->profit = $row->omset - $row->hpp;
                    $catRaw[$cat] = $row;
                }
            }
        }

        $sumCat = fn (array $cats) => [
            'qty' => collect($cats)->sum(fn ($c) => $catRaw[$c]->qty ?? 0),
            'omset' => collect($cats)->sum(fn ($c) => $catRaw[$c]->omset ?? 0),
            'hpp' => collect($cats)->sum(fn ($c) => $catRaw[$c]->hpp ?? 0),
            'profit' => collect($cats)->sum(fn ($c) => $catRaw[$c]->profit ?? 0),
        ];

        $catBreakdown = [
            ['key' => 'coffe',   'label' => 'Coffee',  'unit' => 'Cup',   ...$sumCat(['Coffe'])],
            ['key' => 'minuman', 'label' => 'Minuman', 'unit' => 'Cup',   ...$sumCat(['Minuman'])],
            ['key' => 'snack',   'label' => 'Snack',   'unit' => 'Porsi', ...$sumCat(['Snack'])],
            ['key' => 'makanan', 'label' => 'Makanan',  'unit' => 'Porsi', ...$sumCat(['Makanan'])],
        ];

        $months = [
            1 => 'Januari',   2 => 'Februari',  3 => 'Maret',    4 => 'April',
            5 => 'Mei',       6 => 'Juni',       7 => 'Juli',     8 => 'Agustus',
            9 => 'September', 10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];
        $years = range(now()->year - 1, now()->year + 1);
        $periodLabel = $filterDate
            ? Carbon::parse($filterDate)->translatedFormat('l, d F Y')
            : ($months[$month] ?? '').' '.$year;

        // Saat filter tanggal, muat detail produk per shift
        $detailByShift = collect();
        if ($filterDate) {
            $detailByShift = DailySale::where('sale_date', $filterDate)
                ->orderBy('shift')
                ->orderBy('product_name')
                ->get()
                ->groupBy('shift');
        }

        return view('penjualan-harian.index', compact(
            'rows', 'groupedRows', 'month', 'year', 'months', 'years', 'periodLabel',
            'totalOmset', 'totalProfit', 'totalQty', 'filterDate', 'filterCat', 'catBreakdown',
            'detailByShift', 'sortBy', 'sortDir'
        ));
    }

    public function check(Request $request): JsonResponse
    {
        $date = $request->get('date');
        $shift = $request->get('shift');

        if (! $date || ! in_array($shift, ['pagi', 'sore'])) {
            return response()->json(['exists' => false]);
        }

        $exists = DailySale::where('sale_date', $date)->where('shift', $shift)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function create(Request $request): View
    {
        $date = $request->get('date', today()->toDateString());
        $shift = in_array($request->get('shift'), ['pagi', 'sore']) ? $request->get('shift') : 'pagi';
        $products = HppProduct::active()
            ->orderBy('name')
            ->get();

        $existingRows = DailySale::where('sale_date', $date)
            ->where('shift', $shift)
            ->get();

        $existing = $existingRows->pluck('quantity_sold', 'hpp_product_id');
        $hasData = $existing->isNotEmpty();
        $existingTotal = $hasData ? (int) $existingRows->sum('subtotal') : 0;
        $existingCount = $hasData ? $existingRows->count() : 0;
        $fromKasir = false;

        // Jika belum ada data daily_sales, coba pre-fill dari kasir POS
        if (! $hasData) {
            $kasirQty = KasirItem::join('kasir_sessions', 'kasir_items.kasir_session_id', '=', 'kasir_sessions.id')
                ->whereDate('kasir_sessions.date', $date)
                ->where('kasir_sessions.shift', $shift)
                ->whereNotNull('kasir_items.hpp_product_id')
                ->selectRaw('kasir_items.hpp_product_id, SUM(kasir_items.quantity) as qty')
                ->groupBy('kasir_items.hpp_product_id')
                ->pluck('qty', 'hpp_product_id');

            if ($kasirQty->isNotEmpty()) {
                $existing = $kasirQty;
                $fromKasir = true;
            }
        }

        return view('penjualan-harian.create', compact(
            'date', 'shift', 'products', 'existing', 'hasData', 'fromKasir',
            'existingTotal', 'existingCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'sale_date' => 'required|date|before_or_equal:today',
            'shift' => 'required|in:pagi,sore',
            'items' => 'required|array',
            'items.*.qty' => 'required|integer|min:0',
        ]);

        $date = $request->sale_date;
        $shift = $request->shift;
        $items = $request->items;
        $userId = Auth::id();

        // Jika tanggal/shift berubah dari yang asli, hapus data lama
        $originalDate = $request->get('original_date');
        $originalShift = $request->get('original_shift');
        $dateChanged = $originalDate && ($originalDate !== $date || $originalShift !== $shift);

        DB::transaction(function () use ($date, $shift, $items, $userId, $originalDate, $originalShift, $dateChanged) {
            // Hapus data di target tanggal/shift (overwrite)
            DailySale::where('sale_date', $date)->where('shift', $shift)->delete();

            // Hapus data di tanggal/shift lama jika berbeda
            if ($dateChanged) {
                DailySale::where('sale_date', $originalDate)->where('shift', $originalShift)->delete();
            }

            foreach ($items as $productId => $item) {
                $qty = (int) ($item['qty'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $product = HppProduct::find($productId);
                if (! $product) {
                    continue;
                }

                $hpp = (float) ($product->bahan_baku + $product->tenaga_kerja + $product->overhead);
                $price = (float) $product->harga_jual;
                $subtotal = $price * $qty;
                $hppTotal = $hpp * $qty;

                DailySale::create([
                    'sale_date' => $date,
                    'shift' => $shift,
                    'hpp_product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $price,
                    'hpp_per_unit' => $hpp,
                    'quantity_sold' => $qty,
                    'subtotal' => $subtotal,
                    'hpp_total' => $hppTotal,
                    'profit' => $subtotal - $hppTotal,
                    'created_by' => $userId,
                ]);
            }
        });

        $label = $shift === 'pagi' ? 'Pagi ☀️' : 'Sore 🌆';

        return redirect()->route('penjualan-harian.index')
            ->with('success', "Data Shift {$label} ".date('d/m/Y', strtotime($date)).' berhasil disimpan.');
    }

    public function show(string $date, string $shift): View
    {
        abort_if(! in_array($shift, ['pagi', 'sore']), 404);

        $items = DailySale::where('sale_date', $date)
            ->where('shift', $shift)
            ->orderBy('product_name')
            ->get();

        abort_if($items->isEmpty(), 404);

        $totalOmset = $items->sum('subtotal');
        $totalHpp = $items->sum('hpp_total');
        $totalProfit = $items->sum('profit');
        $totalQty = $items->sum('quantity_sold');

        return view('penjualan-harian.show', compact(
            'date', 'shift', 'items', 'totalOmset', 'totalHpp', 'totalProfit', 'totalQty'
        ));
    }

    public function edit(string $date, string $shift): RedirectResponse
    {
        return redirect()->route('penjualan-harian.create', ['date' => $date, 'shift' => $shift]);
    }

    public function destroy(string $date, string $shift): RedirectResponse
    {
        abort_if(! in_array($shift, ['pagi', 'sore']), 404);

        DailySale::where('sale_date', $date)->where('shift', $shift)->delete();

        $label = $shift === 'pagi' ? 'Pagi' : 'Sore';

        return redirect()->route('penjualan-harian.index')
            ->with('success', "Data Shift {$label} ".date('d/m/Y', strtotime($date)).' dihapus.');
    }

    public function importForm(): View
    {
        return view('penjualan-harian.import');
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'date' => 'nullable|date|before_or_equal:today',
        ]);

        $manualDate = $request->date;
        $shift = 'pagi';
        $userId = Auth::id();

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $data = $spreadsheet->getActiveSheet()->toArray();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        // Deteksi format file
        $format = null;
        $startRow = null;
        foreach ($data as $i => $row) {
            $h = trim($row[0] ?? '');
            if ($h === 'No Transaksi' || $h === 'No. Transaksi' || str_contains($h, 'No.') && str_contains($h, 'Transaksi')) {
                $format = 'transaction'; // per-transaction (penjualan.xlsx)
                $startRow = $i + 1;
                break;
            }
            if ($h === 'Produk' && trim($row[1] ?? '') === 'SKU') {
                $format = 'product'; // per-product aggregated (penjualan2.xlsx)
                $startRow = $i + 1;
                break;
            }
        }

        if (!$startRow) {
            return back()->with('error', 'Format file tidak dikenal. Gunakan file export POS (No Transaksi) atau Laporan Penjualan Produk (Produk).');
        }

        $allProducts = HppProduct::active()->get();
        $transactionData = [];
        $unmatched = [];
        $totalRows = 0;

        if ($format === 'product') {
            // Format per-produk: Produk|SKU|...|Kategori|...|Jumlah Terjual|...|Penjualan (Rp.)|...|HPP|...|Laba Kotor
            // col 0 = nama produk, col 6 = qty, col 8 = total, col 10 = hpp, col 14 = profit
            for ($i = $startRow; $i < count($data); $i++) {
                $row = $data[$i];
                $name = trim($row[0] ?? '');
                if (empty($name)) continue;
                $totalRows++;

                $qty = (float) str_replace(',', '', (string) ($row[6] ?? '0'));
                $total = (float) str_replace(['Rp', ',', '.00'], ['', '', ''], (string) ($row[8] ?? '0'));
                $hppTotal = (float) str_replace(['Rp', ',', '.00'], ['', '', ''], (string) ($row[10] ?? '0'));
                $profit = (float) str_replace(['Rp', ',', '.00'], ['', '', ''], (string) ($row[14] ?? '0'));

                if ($qty <= 0) continue;

                // Cari match di HPP
                $found = $allProducts->firstWhere('name', $name);
                if (!$found) $found = $allProducts->first(fn($hp) => strtolower($hp->name) === strtolower($name));
                if (!$found) $found = $allProducts->first(fn($hp) => str_contains(strtolower($hp->name), strtolower($name)) || str_contains(strtolower($name), strtolower($hp->name)));

                if ($found) {
                    $key = $found->id;
                    if (!isset($transactionData[$key])) {
                        $transactionData[$key] = ['product' => $found, 'qty' => 0, 'subtotal' => 0, 'hpp_total' => 0];
                    }
                    $transactionData[$key]['qty'] += (int) $qty;
                    $transactionData[$key]['subtotal'] += $total;
                    $transactionData[$key]['hpp_total'] += $hppTotal;
                } else {
                    if (!in_array($name, $unmatched)) $unmatched[] = $name;
                }
            }
        } else {
            // Format per-transaksi: No Transaksi|Waktu Order|...|Produk (comma-separated)|...|Total
            // Grup per tanggal untuk auto-detection full month
            $allDates = [];
            for ($i = $startRow; $i < count($data); $i++) {
                $row = $data[$i];
                $transId = trim($row[0] ?? '');
                if ($transId === '' || !preg_match('/^CS\//', $transId)) continue;

                $productsStr = $row[4] ?? '';
                if (empty(trim($productsStr))) continue;

                // Parse tanggal dari Waktu Order (kolom 1)
                $orderTime = trim($row[1] ?? '');
                $parsedDate = $manualDate;
                if (!$parsedDate && !empty($orderTime)) {
                    try {
                        $dt = Carbon::createFromFormat('d-m-Y H:i:s', $orderTime);
                        if ($dt) $parsedDate = $dt->toDateString();
                    } catch (\Exception $e) {
                        // coba format lain
                        try {
                            $dt = Carbon::parse($orderTime);
                            if ($dt) $parsedDate = $dt->toDateString();
                        } catch (\Exception $e2) {}
                    }
                }
                // Fallback
                if (!$parsedDate) $parsedDate = today()->toDateString();

                if (!isset($allDates[$parsedDate])) $allDates[$parsedDate] = [];

                $totalRows++;

                $actualTotal = (float) str_replace(['Rp', ',', '.00'], ['', '', ''], (string) ($row[7] ?? '0'));

                $productNames = preg_split('/[,\n\r]+/', $productsStr);
                $products = [];

                foreach ($productNames as $p) {
                    $name = trim($p);
                    if (empty($name)) continue;
                    $found = $allProducts->firstWhere('name', $name);
                    if (!$found) $found = $allProducts->first(fn($hp) => strtolower($hp->name) === strtolower($name));
                    if (!$found) $found = $allProducts->first(fn($hp) => str_contains(strtolower($hp->name), strtolower($name)) || str_contains(strtolower($name), strtolower($hp->name)));
                    if ($found) $products[] = $found;
                    else { if (!in_array($name, $unmatched)) $unmatched[] = $name; }
                }

                if (empty($products)) continue;

                $expectedTotal = array_sum(array_map(fn($p) => (float) $p->harga_jual, $products));

                if (count($products) === 1 && $expectedTotal > 0) {
                    $product = $products[0];
                    $price = (float) $product->harga_jual;
                    $hpp = (float) ($product->bahan_baku + $product->tenaga_kerja + $product->overhead);
                    $qty = max(1, (int) round($actualTotal / $price));
                    $key = $product->id;
                    if (!isset($allDates[$parsedDate][$key])) $allDates[$parsedDate][$key] = ['product' => $product, 'qty' => 0, 'subtotal' => 0, 'hpp_total' => 0];
                    $allDates[$parsedDate][$key]['qty'] += $qty;
                    $allDates[$parsedDate][$key]['subtotal'] += $price * $qty;
                    $allDates[$parsedDate][$key]['hpp_total'] += $hpp * $qty;
                } else {
                    $factor = $expectedTotal > 0 ? $actualTotal / $expectedTotal : 1;
                    foreach ($products as $product) {
                        $price = (float) $product->harga_jual;
                        $hpp = (float) ($product->bahan_baku + $product->tenaga_kerja + $product->overhead);
                        $key = $product->id;
                        if (!isset($allDates[$parsedDate][$key])) $allDates[$parsedDate][$key] = ['product' => $product, 'qty' => 0, 'subtotal' => 0, 'hpp_total' => 0];
                        $allDates[$parsedDate][$key]['qty'] += 1;
                        $allDates[$parsedDate][$key]['subtotal'] += $price * $factor;
                        $allDates[$parsedDate][$key]['hpp_total'] += $hpp;
                    }
                }
            }
        }

        // Untuk product format, treat as single date
        if ($format === 'product' && !empty($transactionData)) {
            $productDate = $manualDate ?: today()->toDateString();
            $allDates = [$productDate => $transactionData];
        }

        // Simpan ke DailySale — per tanggal
        $savedCount = 0;
        $details = [];
        $totalTransactions = $totalRows;

        foreach ($allDates as $saleDate => $products) {
            // Hapus data lama untuk tanggal ini
            DailySale::where('sale_date', $saleDate)->where('shift', $shift)->delete();

            foreach ($products as $d) {
                $product = $d['product'];
                $qty = (int) $d['qty'];
                $subtotal = $d['subtotal'];
                $hppTotal = $d['hpp_total'];
                $price = (float) $product->harga_jual;

                DailySale::create([
                    'sale_date' => $saleDate,
                    'shift' => $shift,
                    'hpp_product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $price,
                    'hpp_per_unit' => (float) ($product->bahan_baku + $product->tenaga_kerja + $product->overhead),
                    'quantity_sold' => $qty,
                    'subtotal' => $subtotal,
                    'hpp_total' => $hppTotal,
                    'profit' => $subtotal - $hppTotal,
                    'created_by' => $userId,
                ]);

                $savedCount++;
                $details[] = ['name' => $product->name, 'qty' => $qty, 'price' => $price];
            }
        }

        $totalItems = array_sum(array_column($details, 'qty'));
        $dateCount = count($allDates);

        return redirect()->route('penjualan-harian.import')
            ->with('success', "✅ Berhasil import {$dateCount} tanggal ({$totalRows} transaksi), {$savedCount} produk tersimpan.")
            ->with('results', [
                'transactions' => $totalRows,
                'items' => $totalItems,
                'matched' => $savedCount,
                'details' => $details,
                'unmatched' => $unmatched,
            ]);
    }
}
