<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use Illuminate\Http\Request;

class TransactionTypeController extends Controller
{
    public function index()
    {
        $types = TransactionType::orderBy('category')->orderBy('name')->get();

        return view('transaction-types.index', compact('types'));
    }

    public function create()
    {
        return view('transaction-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:pemasukan,pengeluaran'],
            'grup' => ['nullable', 'string', 'in:Dapur,BAR,Operasional'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        TransactionType::create([
            'name' => $request->name,
            'category' => $request->category,
            'grup' => $request->category === 'pengeluaran' ? $request->grup : null,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('transaction-types.index')
            ->with('success', "Jenis transaksi \"{$request->name}\" berhasil ditambahkan.");
    }

    public function edit(TransactionType $transactionType)
    {
        return view('transaction-types.edit', compact('transactionType'));
    }

    public function update(Request $request, TransactionType $transactionType)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:pemasukan,pengeluaran'],
            'grup' => ['nullable', 'string', 'in:Dapur,BAR,Operasional'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $transactionType->update([
            'name' => $request->name,
            'category' => $request->category,
            'grup' => $request->category === 'pengeluaran' ? $request->grup : null,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('transaction-types.index')
            ->with('success', "Jenis transaksi \"{$transactionType->name}\" berhasil diperbarui.");
    }

    public function destroy(TransactionType $transactionType)
    {
        if ($transactionType->transactions()->exists()) {
            return back()->with('error', "Tidak dapat menghapus \"{$transactionType->name}\" karena sudah digunakan dalam transaksi.");
        }

        $transactionType->delete();

        return redirect()->route('transaction-types.index')
            ->with('success', "Jenis transaksi \"{$transactionType->name}\" berhasil dihapus.");
    }
}
