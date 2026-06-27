<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::orderBy('code')->get()->groupBy('account_type');

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        $parents = Account::whereNull('parent_id')->orderBy('code')->get();

        return view('accounts.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'parent_id' => ['nullable', 'exists:accounts,id'],
            'description' => ['nullable', 'string'],
        ]);

        Account::create([
            'code' => $request->code,
            'name' => $request->name,
            'account_type' => $request->account_type,
            'normal_balance' => Account::normalBalanceFor($request->account_type),
            'parent_id' => $request->parent_id ?: null,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('accounts.index')
            ->with('success', "Akun \"{$request->name}\" berhasil ditambahkan.");
    }

    public function edit(Account $account)
    {
        $parents = Account::whereNull('parent_id')
            ->where('id', '!=', $account->id)
            ->orderBy('code')
            ->get();

        return view('accounts.edit', compact('account', 'parents'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:20', "unique:accounts,code,{$account->id}"],
            'name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'parent_id' => ['nullable', 'exists:accounts,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $account->update([
            'code' => $request->code,
            'name' => $request->name,
            'account_type' => $request->account_type,
            'normal_balance' => Account::normalBalanceFor($request->account_type),
            'parent_id' => $request->parent_id ?: null,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('accounts.index')
            ->with('success', "Akun \"{$account->name}\" berhasil diperbarui.");
    }

    public function destroy(Account $account)
    {
        $account->update(['is_active' => false]);

        return redirect()->route('accounts.index')
            ->with('success', "Akun \"{$account->name}\" dinonaktifkan.");
    }
}
