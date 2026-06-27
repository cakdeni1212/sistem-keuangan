<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="page-title">Laporan Laba Rugi</h2>
            <p class="text-xs text-surface-500 mt-0.5">Per {{ now()->format('d F Y') }}</p>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="stat-card">
                <div class="flex items-center gap-3 mb-1">
                    <div class="stat-icon bg-emerald-100 text-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <span class="stat-label">Total Pendapatan</span>
                </div>
                <p class="stat-value text-emerald-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3 mb-1">
                    <div class="stat-icon bg-red-100 text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    </div>
                    <span class="stat-label">Total Beban</span>
                </div>
                <p class="stat-value text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3 mb-1">
                    <div class="stat-icon bg-brand-100 text-brand-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="stat-label">Laba/Rugi Bersih</span>
                </div>
                <p class="stat-value {{ $netIncome >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    Rp {{ number_format($netIncome, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="card">
            <div class="px-5 py-4 border-b border-surface-200 bg-emerald-50">
                <h3 class="font-semibold text-surface-800">Pendapatan (Revenue)</h3>
            </div>
            @if(count($revenueItems) > 0)
                <table class="table-wrap">
                    <thead>
                        <tr class="border-b border-surface-200 bg-surface-50">
                            <th class="table-th">Kode</th>
                            <th class="table-th">Nama Akun</th>
                            <th class="table-th text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueItems as $item)
                            <tr class="border-b border-surface-100 hover:bg-surface-50 transition">
                                <td class="table-td font-mono text-xs">{{ $item['code'] }}</td>
                                <td class="table-td">{{ $item['name'] }}</td>
                                <td class="table-td text-right font-mono">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-surface-50 border-t-2 border-surface-300">
                            <td colspan="2" class="px-4 py-3 text-sm font-bold text-surface-800">Total Pendapatan</td>
                            <td class="px-4 py-3 text-right text-sm font-bold font-mono text-emerald-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="empty-state">Belum ada akun pendapatan.</div>
            @endif
        </div>

        <div class="card">
            <div class="px-5 py-4 border-b border-surface-200 bg-red-50">
                <h3 class="font-semibold text-surface-800">Beban (Expense)</h3>
            </div>
            @if(count($expenseItems) > 0)
                <table class="table-wrap">
                    <thead>
                        <tr class="border-b border-surface-200 bg-surface-50">
                            <th class="table-th">Kode</th>
                            <th class="table-th">Nama Akun</th>
                            <th class="table-th text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenseItems as $item)
                            <tr class="border-b border-surface-100 hover:bg-surface-50 transition">
                                <td class="table-td font-mono text-xs">{{ $item['code'] }}</td>
                                <td class="table-td">{{ $item['name'] }}</td>
                                <td class="table-td text-right font-mono">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-surface-50 border-t-2 border-surface-300">
                            <td colspan="2" class="px-4 py-3 text-sm font-bold text-surface-800">Total Beban</td>
                            <td class="px-4 py-3 text-right text-sm font-bold font-mono text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="empty-state">Belum ada akun beban.</div>
            @endif
        </div>

        <div class="card">
            <div class="flex justify-between items-center px-5 py-4 {{ $netIncome >= 0 ? 'bg-emerald-50' : 'bg-red-50' }}">
                <h3 class="font-bold text-surface-900 text-lg">Laba / Rugi Bersih</h3>
                <span class="text-lg font-bold font-mono {{ $netIncome >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    Rp {{ number_format($netIncome, 0, ',', '.') }}
                </span>
            </div>
            <div class="px-5 py-3 grid grid-cols-2 gap-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-surface-600">Total Pendapatan</span>
                    <span class="font-mono font-semibold text-surface-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-surface-600">Total Beban</span>
                    <span class="font-mono font-semibold text-surface-800">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
