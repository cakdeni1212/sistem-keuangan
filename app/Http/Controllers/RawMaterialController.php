<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RawMaterialController extends Controller
{
    public function index()
    {
        $rawMaterials = RawMaterial::withCount('ingredients')
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(20);

        $totalItems = RawMaterial::count();
        $totalActive = RawMaterial::where('is_active', true)->count();
        $lowStock = RawMaterial::where('stock_quantity', '<', 100)->where('is_active', true)->count();

        return view('raw-materials.index', compact('rawMaterials', 'totalItems', 'totalActive', 'lowStock'));
    }

    public function create()
    {
        return view('raw-materials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'required|string|max:30',
            'stock_quantity' => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:60',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $data['created_by'] = Auth::id();
        $data['is_active'] = $request->boolean('is_active', true);

        $material = RawMaterial::create($data);

        return redirect()->route('raw-materials.index')
            ->with('success', "Bahan baku \"{$material->name}\" berhasil disimpan.");
    }

    public function edit(RawMaterial $rawMaterial)
    {
        $rawMaterial->load('ingredients.hppProduct');

        return view('raw-materials.edit', compact('rawMaterial'));
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'required|string|max:30',
            'stock_quantity' => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:60',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $data['updated_by'] = Auth::id();
        $data['is_active'] = $request->boolean('is_active', true);

        $rawMaterial->update($data);

        return redirect()->route('raw-materials.index')
            ->with('success', "Bahan baku \"{$rawMaterial->name}\" berhasil diperbarui.");
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        if ($rawMaterial->ingredients()->exists()) {
            return back()->with('error', "Bahan baku \"{$rawMaterial->name}\" tidak dapat dihapus karena digunakan dalam resep HPP produk.");
        }

        $name = $rawMaterial->name;
        $rawMaterial->delete();

        return back()->with('success', "Bahan baku \"{$name}\" berhasil dihapus.");
    }
}
