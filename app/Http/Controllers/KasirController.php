<?php

namespace App\Http\Controllers;

use App\Models\DailySale;
use App\Models\HppProduct;
use App\Models\KasirItem;
use App\Models\KasirSession;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $products = HppProduct::active()
            ->with('ingredients.rawMaterial')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $categories = $products->pluck('category')->filter()->unique()->sort()->prepend('Semua')->values();

        // Rekap hari ini
        $todaySessions = KasirSession::whereDate('date', today())
            ->with('items')
            ->latest()
            ->get();

        $todayTotal = $todaySessions->sum('total_amount');
        $todayTxCount = $todaySessions->count();

        return view('kasir.index', compact(
            'products', 'categories',
            'todaySessions', 'todayTotal', 'todayTxCount'
        ));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:hpp_products,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:qris,tunai',
            'shift' => 'required|in:pagi,sore',
            'notes' => 'nullable|string|max:500',
            'transaction_date' => 'nullable|date|before_or_equal:today',
        ]);

        DB::transaction(function () use ($request) {
            $total = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = HppProduct::with('ingredients.rawMaterial')->findOrFail($item['id']);
                $qty = (int) $item['qty'];
                $price = (float) $product->harga_jual;
                $subtotal = $price * $qty;
                $total += $subtotal;

                $itemsData[] = [
                    'hpp_product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];

                // Kurangi stok bahan baku
                foreach ($product->ingredients as $ingredient) {
                    $deductQty = $ingredient->quantity * $qty;
                    $rm = $ingredient->rawMaterial;
                    if ($rm) {
                        $rm->decrement('stock_quantity', $deductQty);
                    }
                }
            }

            // Simpan kasir session
            $session = KasirSession::create([
                'date' => $request->filled('transaction_date') ? $request->transaction_date : today(),
                'shift' => $request->shift,
                'payment_method' => $request->payment_method,
                'total_amount' => $total,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            foreach ($itemsData as $d) {
                $session->items()->create($d);
            }
        });

        return response()->json(['success' => true, 'message' => 'Transaksi berhasil disimpan!']);
    }

    public function data(Request $request)
    {
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);
        $filterCat = $request->get('cat');

        // ── 1. Data dari Penjualan Harian (daily_sales) ──────────────────
        $salesRows = DailySale::whereMonth('sale_date', $month)
            ->whereYear('sale_date', $year)
            ->get();

        // (date, shift) yang sudah ada di daily_sales → jadi master
        $dailyKeys = $salesRows
            ->groupBy(fn ($r) => Carbon::parse($r->sale_date)->toDateString().'_'.$r->shift)
            ->keys()
            ->toArray();

        // ── 2. Kasir POS: ambil hanya (date, shift) yang TIDAK ada di daily_sales ──
        $kasirSessions = KasirSession::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->filter(function ($s) use ($dailyKeys) {
                $key = Carbon::parse($s->date)->toDateString().'_'.$s->shift;

                return ! in_array($key, $dailyKeys);
            });

        $kasirOnlyIds = $kasirSessions->pluck('id');

        // Kasir-only: hitung HPP dari kasir_items + hpp_products
        $kasirHpp = 0;
        if ($kasirOnlyIds->isNotEmpty()) {
            $kasirHpp = KasirItem::whereIn('kasir_session_id', $kasirOnlyIds)
                ->leftJoin('hpp_products', 'kasir_items.hpp_product_id', '=', 'hpp_products.id')
                ->selectRaw('SUM(kasir_items.quantity * COALESCE(hpp_products.bahan_baku + hpp_products.tenaga_kerja + hpp_products.overhead, 0)) as total_hpp')
                ->value('total_hpp') ?? 0;
        }

        // ── 3. Hitung totals gabungan ─────────────────────────────────────
        $dailyOmset = $salesRows->sum('subtotal');
        $dailyHpp = $salesRows->sum('hpp_total');
        $dailyProfit = $salesRows->sum('profit');

        $kasirOmset = $kasirSessions->sum('total_amount');
        $kasirProfit = $kasirOmset - $kasirHpp;

        $totalOmset = $dailyOmset + $kasirOmset;
        $totalHpp = $dailyHpp + $kasirHpp;
        $totalProfit = $dailyProfit + $kasirProfit;

        // Jumlah transaksi = unique (date+shift) dari kedua sumber
        $kasirTxCount = $kasirSessions
            ->groupBy(fn ($s) => Carbon::parse($s->date)->toDateString().'_'.$s->shift)
            ->count();
        $dailyTxCount = $salesRows
            ->groupBy(fn ($r) => Carbon::parse($r->sale_date)->toDateString().'_'.$r->shift)
            ->count();
        $totalTx = $dailyTxCount + $kasirTxCount;
        $avgTx = $totalTx > 0 ? $totalOmset / $totalTx : 0;

        // ── 4. Rekap Shift ────────────────────────────────────────────────
        $pagiOmset = $salesRows->where('shift', 'pagi')->sum('subtotal')
            + $kasirSessions->where('shift', 'pagi')->sum('total_amount');
        $soreOmset = $salesRows->where('shift', 'sore')->sum('subtotal')
            + $kasirSessions->where('shift', 'sore')->sum('total_amount');

        $pagiTx = $salesRows->where('shift', 'pagi')
            ->groupBy(fn ($r) => Carbon::parse($r->sale_date)->toDateString())->count()
            + $kasirSessions->where('shift', 'pagi')
                ->groupBy(fn ($s) => Carbon::parse($s->date)->toDateString())->count();
        $soreTx = $salesRows->where('shift', 'sore')
            ->groupBy(fn ($r) => Carbon::parse($r->sale_date)->toDateString())->count()
            + $kasirSessions->where('shift', 'sore')
                ->groupBy(fn ($s) => Carbon::parse($s->date)->toDateString())->count();

        // ── 5. Metode Pembayaran (dari kasir_sessions semua bulan ini) ────
        $allKasirSessions = KasirSession::whereMonth('date', $month)->whereYear('date', $year)->get();
        $qrisOmset = $allKasirSessions->where('payment_method', 'qris')->sum('total_amount');
        $tunaiOmset = $allKasirSessions->where('payment_method', 'tunai')->sum('total_amount');

        // ── 6. Produk Terlaris (daily_sales + kasir-only items) ──────────
        // Ambil kategori per hpp_product_id
        $hppCategories = HppProduct::pluck('category', 'id');
        $categories = HppProduct::whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $produkMap = [];

        // Dari daily_sales
        foreach ($salesRows as $row) {
            $name = $row->product_name;
            $cat = $hppCategories[$row->hpp_product_id] ?? null;
            if (! isset($produkMap[$name])) {
                $produkMap[$name] = ['qty' => 0, 'omset' => 0, 'hpp' => 0, 'profit' => 0, 'harga' => $row->unit_price, 'hpp_unit' => $row->hpp_per_unit, 'category' => $cat];
            }
            $produkMap[$name]['qty'] += $row->quantity_sold;
            $produkMap[$name]['omset'] += $row->subtotal;
            $produkMap[$name]['hpp'] += $row->hpp_total;
            $produkMap[$name]['profit'] += $row->profit;
        }

        // Dari kasir-only items
        if ($kasirOnlyIds->isNotEmpty()) {
            $kasirItems = KasirItem::whereIn('kasir_session_id', $kasirOnlyIds)
                ->leftJoin('hpp_products', 'kasir_items.hpp_product_id', '=', 'hpp_products.id')
                ->selectRaw('
                    kasir_items.product_name,
                    hpp_products.category,
                    SUM(kasir_items.quantity) as total_qty,
                    SUM(kasir_items.subtotal) as total_omset,
                    SUM(kasir_items.quantity * COALESCE(hpp_products.bahan_baku + hpp_products.tenaga_kerja + hpp_products.overhead, 0)) as total_hpp,
                    AVG(kasir_items.product_price) as harga
                ')
                ->groupBy('kasir_items.product_name', 'hpp_products.category')
                ->get();

            foreach ($kasirItems as $item) {
                $name = $item->product_name;
                if (! isset($produkMap[$name])) {
                    $produkMap[$name] = ['qty' => 0, 'omset' => 0, 'hpp' => 0, 'profit' => 0, 'harga' => $item->harga, 'hpp_unit' => 0, 'category' => $item->category];
                }
                $produkMap[$name]['qty'] += $item->total_qty;
                $produkMap[$name]['omset'] += $item->total_omset;
                $produkMap[$name]['hpp'] += $item->total_hpp;
                $produkMap[$name]['profit'] += $item->total_omset - $item->total_hpp;
            }
        }

        $produkTerlaris = collect($produkMap)
            ->map(function ($v, $name) {
                return (object) [
                    'product_name' => $name,
                    'total_qty' => $v['qty'],
                    'total_omset' => $v['omset'],
                    'total_hpp' => $v['hpp'],
                    'total_profit' => $v['profit'],
                    'harga_jual' => $v['harga'],
                    'hpp_per_unit' => $v['hpp_unit'],
                    'category' => $v['category'],
                    'margin' => $v['omset'] > 0 ? round(($v['profit'] / $v['omset']) * 100, 1) : 0,
                ];
            })
            ->when($filterCat, fn ($c) => $c->filter(fn ($p) => $p->category === $filterCat))
            ->sortByDesc('total_qty')
            ->values();

        $months = [
            1 => 'Januari',   2 => 'Februari',  3 => 'Maret',    4 => 'April',
            5 => 'Mei',       6 => 'Juni',       7 => 'Juli',     8 => 'Agustus',
            9 => 'September', 10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];
        $years = range(now()->year - 1, now()->year + 1);
        $periodLabel = ($months[$month] ?? '').' '.$year;

        return view('kasir.data', compact(
            'month', 'year', 'months', 'years', 'periodLabel',
            'totalOmset', 'totalTx', 'avgTx', 'totalProfit', 'totalHpp',
            'pagiOmset', 'soreOmset', 'pagiTx', 'soreTx',
            'qrisOmset', 'tunaiOmset',
            'produkTerlaris', 'categories', 'filterCat'
        ));
    }

    public function riwayat(Request $request)
    {
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        $sessions = KasirSession::with(['items', 'creator'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->latest('date')
            ->latest('id')
            ->get();

        $totalOmset = $sessions->sum('total_amount');
        $totalQris = $sessions->where('payment_method', 'qris')->sum('total_amount');
        $totalTunai = $sessions->where('payment_method', 'tunai')->sum('total_amount');
        $totalTx = $sessions->count();

        // Best sellers bulan ini
        $bestSellers = KasirItem::whereHas('session', fn ($q) => $q->whereMonth('date', $month)->whereYear('date', $year)
        )
            ->selectRaw('product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $years = range(now()->year - 1, now()->year + 1);
        $periodLabel = ($months[$month] ?? '').' '.$year;

        return view('kasir.riwayat', compact(
            'sessions', 'month', 'year', 'months', 'years', 'periodLabel',
            'totalOmset', 'totalQris', 'totalTunai', 'totalTx', 'bestSellers'
        ));
    }

    public function update(Request $request, KasirSession $kasirSession): RedirectResponse
    {
        $data = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'shift' => 'required|in:pagi,sore',
            'payment_method' => 'required|in:qris,tunai',
            'notes' => 'nullable|string|max:500',
        ]);

        $kasirSession->update($data);

        return back()->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(KasirSession $kasirSession): RedirectResponse
    {
        $kasirSession->items()->delete();
        $kasirSession->delete();

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }
}
