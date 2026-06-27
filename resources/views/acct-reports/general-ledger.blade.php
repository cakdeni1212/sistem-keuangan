<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="page-title">Buku Besar (General Ledger)</h2>
            <p class="text-xs text-surface-500 mt-0.5">Rincian transaksi per akun</p>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5">

        <div class="filter-card">
            <form method="GET" action="{{ route('acct-reports.general-ledger') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="label" for="account_id">Akun</label>
                        <select name="account_id" id="account_id" class="input-field" required>
                            <option value="">-- Pilih Akun --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ $accountId == $account->id ? 'selected' : '' }}>
                                    [{{ $account->code }}] {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="start_date">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date"
                               value="{{ $startDate }}" class="input-field" required>
                    </div>
                    <div>
                        <label class="label" for="end_date">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date"
                               value="{{ $endDate }}" class="input-field" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if($selectedAccount)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="stat-card">
                    <span class="stat-label">Akun</span>
                    <p class="text-lg font-bold text-surface-900 mt-1">[{{ $selectedAccount->code }}] {{ $selectedAccount->name }}</p>
                    <span class="badge mt-1.5
                        @if($selectedAccount->account_type === 'asset') badge-blue
                        @elseif($selectedAccount->account_type === 'liability') badge-red
                        @elseif($selectedAccount->account_type === 'equity') badge-green
                        @elseif($selectedAccount->account_type === 'revenue') badge-green
                        @elseif($selectedAccount->account_type === 'expense') badge-red
                        @endif
                    ">
                        {{ ucfirst($selectedAccount->account_type) }}
                    </span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Saldo Awal</span>
                    <p class="stat-value {{ $beginningBalance >= 0 ? '' : 'text-red-700' }}">Rp {{ number_format($beginningBalance, 0, ',', '.') }}</p>
                    <span class="text-[10px] text-surface-400">per {{ date('d-m-Y', strtotime($startDate)) }}</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Saldo Akhir</span>
                    <p class="stat-value {{ $endingBalance >= 0 ? '' : 'text-red-700' }}">Rp {{ number_format($endingBalance, 0, ',', '.') }}</p>
                    <span class="text-[10px] text-surface-400">per {{ date('d-m-Y', strtotime($endDate)) }}</span>
                </div>
            </div>

            <div class="card">
                @if(count($lines) > 0)
                    <table class="table-wrap">
                        <thead>
                            <tr class="border-b border-surface-200 bg-surface-50">
                                <th class="table-th">Tanggal</th>
                                <th class="table-th">Keterangan</th>
                                <th class="table-th text-right">Debit</th>
                                <th class="table-th text-right">Kredit</th>
                                <th class="table-th text-right">Saldo</th>
                            </tr>
                            <tr class="border-b border-surface-200 bg-surface-50/50">
                                <td class="px-4 py-2 text-xs text-surface-400 font-mono">{{ date('d-m-Y', strtotime($startDate)) }}</td>
                                <td class="px-4 py-2 text-xs text-surface-400 font-medium">Saldo Awal</td>
                                <td class="px-4 py-2"></td>
                                <td class="px-4 py-2"></td>
                                <td class="px-4 py-2 text-right text-xs font-mono font-semibold text-surface-600">Rp {{ number_format($beginningBalance, 0, ',', '.') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lines as $line)
                                <tr class="border-b border-surface-100 hover:bg-surface-50 transition">
                                    <td class="table-td font-mono text-xs whitespace-nowrap">
                                        {{ $line->journal ? $line->journal->journal_date->format('d-m-Y') : '-' }}
                                    </td>
                                    <td class="table-td">
                                        <div class="font-medium text-surface-800">{{ $line->description ?: 'Tanpa keterangan' }}</div>
                                        @if($line->journal)
                                            <div class="text-[11px] text-surface-400 mt-0.5">{{ $line->journal->journal_number }}</div>
                                        @endif
                                    </td>
                                    <td class="table-td text-right font-mono">{{ $line->debit > 0 ? number_format($line->debit, 0, ',', '.') : '-' }}</td>
                                    <td class="table-td text-right font-mono">{{ $line->credit > 0 ? number_format($line->credit, 0, ',', '.') : '-' }}</td>
                                    <td class="table-td text-right font-mono font-semibold {{ $line->running_balance >= 0 ? 'text-surface-800' : 'text-red-700' }}">
                                        Rp {{ number_format($line->running_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-surface-50 border-t-2 border-surface-300">
                                <td colspan="2" class="px-4 py-3 text-sm font-bold text-surface-800">Total Periode</td>
                                <td class="px-4 py-3 text-right text-sm font-bold font-mono text-surface-800">
                                    Rp {{ number_format($lines->sum('debit'), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-bold font-mono text-surface-800">
                                    Rp {{ number_format($lines->sum('credit'), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-bold font-mono {{ $endingBalance >= 0 ? 'text-surface-800' : 'text-red-700' }}">
                                    Rp {{ number_format($endingBalance, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="empty-state">Tidak ada transaksi pada periode {{ date('d-m-Y', strtotime($startDate)) }} s.d. {{ date('d-m-Y', strtotime($endDate)) }}.</div>
                @endif
            </div>
        @else
            <div class="card">
                <div class="empty-state">Silakan pilih akun dan periode untuk melihat buku besar.</div>
            </div>
        @endif

    </div>
</x-app-layout>
