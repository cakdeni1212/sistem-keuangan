<?php

namespace App\Http\Controllers;

use App\Models\HppProduct;
use App\Models\HppProductIngredient;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class HppProductController extends Controller
{
    public function index()
    {
        $products = HppProduct::orderBy('category')->orderBy('name')->get();

        $avgMargin = $products->count() > 0
            ? $products->avg('margin_percent')
            : 0;

        $categories = $products->pluck('category')->filter()->unique()->sort()->values();

        return view('hpp-products.index', compact('products', 'avgMargin', 'categories'));
    }

    public function create()
    {
        $rawMaterials = RawMaterial::active()->orderBy('name')->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'unit' => $m->unit,
                'price' => (float) $m->price_per_unit,
            ])->values()->toArray();

        $categories = HppProduct::query()
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->pluck('category');

        return view('hpp-products.create', compact('rawMaterials', 'categories'));
    }

    public function store(Request $request)
    {
        $hasIngredients = $request->filled('ingredients');

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'sku' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:60',
            'satuan' => 'nullable|string|max:50',
            'stok_minimum' => 'nullable|integer|min:0',
            'bahan_baku' => $hasIngredients ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'tenaga_kerja' => 'required|numeric|min:0',
            'overhead' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);
        $data['created_by'] = Auth::id();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['bahan_baku'] = $data['bahan_baku'] ?? 0;

        $product = HppProduct::create($data);

        if ($hasIngredients) {
            $this->syncIngredients($product, $request->input('ingredients', []));
        }

        return redirect()->route('hpp-products.index')
            ->with('success', "HPP produk \"{$product->name}\" berhasil disimpan.");
    }

    public function edit(HppProduct $hppProduct)
    {
        $hppProduct->load('ingredients.rawMaterial');

        $rawMaterials = RawMaterial::active()->orderBy('name')->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'unit' => $m->unit,
                'price' => (float) $m->price_per_unit,
            ])->values()->toArray();

        $categories = HppProduct::query()
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->pluck('category');

        $existingIngredients = $hppProduct->ingredients
            ->map(fn ($i) => [
                'raw_material_id' => $i->raw_material_id,
                'quantity' => (float) $i->quantity,
                'usage_unit' => $i->usage_unit ?? $i->rawMaterial?->unit ?? '',
            ])->values()->toArray();

        return view('hpp-products.edit', compact('hppProduct', 'rawMaterials', 'existingIngredients', 'categories'));
    }

    public function update(Request $request, HppProduct $hppProduct)
    {
        $hasIngredients = $request->filled('ingredients');

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'sku' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:60',
            'satuan' => 'nullable|string|max:50',
            'stok_minimum' => 'nullable|integer|min:0',
            'bahan_baku' => $hasIngredients ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'tenaga_kerja' => 'required|numeric|min:0',
            'overhead' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $data['updated_by'] = Auth::id();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['bahan_baku'] = $data['bahan_baku'] ?? 0;

        $hppProduct->ingredients()->delete();

        if ($hasIngredients) {
            $this->syncIngredients($hppProduct, $request->input('ingredients', []));
        }

        $hppProduct->update($data);

        return redirect()->route('hpp-products.index')
            ->with('success', "HPP produk \"{$hppProduct->name}\" berhasil diperbarui.");
    }

    public function destroy(HppProduct $hppProduct)
    {
        $name = $hppProduct->name;
        $hppProduct->delete();

        return back()->with('success', "Produk \"{$name}\" dihapus.");
    }

    /** Conversion factor: how many usage_units fit in one material unit */
    private function conversionFactor(string $materialUnit, string $usageUnit): float
    {
        $map = [
            'kg' => ['gram' => 1000, 'kg' => 1],
            'liter' => ['ml' => 1000, 'liter' => 1],
        ];

        return $map[$materialUnit][$usageUnit] ?? 1;
    }

    private function syncIngredients(HppProduct $product, array $ingredients): void
    {
        $totalCost = 0;

        foreach ($ingredients as $item) {
            $rawMaterialId = $item['raw_material_id'] ?? null;
            $quantity = (float) ($item['quantity'] ?? 0);

            if (! $rawMaterialId || $quantity <= 0) {
                continue;
            }

            $rawMaterial = RawMaterial::find($rawMaterialId);
            if (! $rawMaterial) {
                continue;
            }

            $usageUnit = $item['usage_unit'] ?? $rawMaterial->unit;
            $factor = $this->conversionFactor($rawMaterial->unit, $usageUnit);
            // price per usage_unit = price_per_material_unit / factor
            $pricePerUsageUnit = (float) $rawMaterial->price_per_unit / $factor;

            HppProductIngredient::create([
                'hpp_product_id' => $product->id,
                'raw_material_id' => $rawMaterialId,
                'quantity' => $quantity,
                'usage_unit' => $usageUnit,
            ]);

            $totalCost += $quantity * $pricePerUsageUnit;
        }

        if ($totalCost > 0) {
            $product->update(['bahan_baku' => $totalCost]);
        }
    }

    public function importForm(): View
    {
        return view('hpp-products.import');
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $data = $spreadsheet->getActiveSheet()->toArray();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel.');
        }

        // Cari header row
        $startRow = null;
        foreach ($data as $i => $row) {
            $r0 = trim($row[0] ?? '');
            $r1 = trim($row[1] ?? '');
            // Deteksi: "Ekspor Daftar Produk" atau kolom "Nama Produk"
            if ($r0 === 'Ekspor Daftar Produk' || $r0 === 'Outlet' || 
                ($r0 === 'Outlet/Grup Outlet' && str_contains($r1, 'Nama')) ||
                (str_contains($r0, 'Nama') && str_contains($r0, 'Produk'))) {
                $startRow = $i + 1;
            }
            // If "Outlet/Grup Outlet" is found on the header row, data starts at row+1
        }

        // Fallback: find first row with numeric price in col 15
        if (!$startRow) {
            foreach ($data as $i => $row) {
                $price = str_replace(['.', ','], ['', '.'], (string) ($row[15] ?? '0'));
                if (is_numeric($price) && (float) $price > 0) {
                    $startRow = $i;
                    break;
                }
            }
        }

        if (!$startRow) {
            return back()->with('error', 'Format file tidak dikenal. Gunakan export produk dari Majo POS.');
        }

        $total = 0;
        $new = 0;
        $existing = 0;

        for ($i = $startRow; $i < count($data); $i++) {
            $row = $data[$i];
            $name = trim($row[1] ?? '');
            if (empty($name)) continue;

            $category = trim($row[6] ?? '');
            $sku = trim($row[16] ?? '');
            $satuan = trim($row[12] ?? '');
            $stokMin = (int) ($row[11] ?? 0);
            $desc = trim($row[7] ?? '');
            $hargaBeli = (float) str_replace(['.', ','], ['', '.'], (string) ($row[14] ?? '0'));
            $hargaJual = (float) str_replace(['.', ','], ['', '.'], (string) ($row[15] ?? '0'));

            if ($hargaJual <= 0) continue;
            $total++;

            // Skip if already exists
            if (HppProduct::where('name', $name)->exists()) {
                $existing++;
                continue;
            }

            HppProduct::create([
                'name' => $name,
                'sku' => $sku ?: null,
                'category' => $category,
                'satuan' => $satuan ?: null,
                'stok_minimum' => $stokMin,
                'bahan_baku' => $hargaBeli,
                'tenaga_kerja' => 0,
                'overhead' => 0,
                'harga_jual' => $hargaJual,
                'notes' => $desc ?: null,
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);
            $new++;
        }

        return redirect()->route('hpp-products.import')
            ->with('success', "✅ Berhasil import {$new} produk baru dari {$total} data.")
            ->with('results', compact('total', 'new', 'existing'));
    }
}
