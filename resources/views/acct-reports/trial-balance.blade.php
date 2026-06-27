<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="page-title">Neraca Saldo (Trial Balance)</h2>
            <p class="text-xs text-surface-500 mt-0.5">Daftar saldo seluruh akun per {{ now()->format('d F Y') }}</p>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5">

        @if(empty($grouped))
            <div class="empty-state">Belum ada data akun aktif.</div>
        @else
            <div class="card">
                <table class="table-wrap">
                    <thead>
                        <tr class="border-b border-surface-200 bg-surface-50">
                            <th class="table-th">Kode</th>
                            <th class="table-th">Nama Akun</th>
                            <th class="table-th text-right">Debit</th>
                            <th class="table-th text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grouped as $group)
                            <tr class="border-b border-surface-200 bg-brand-50">
                                <td colspan="4" class="px-4 py-3 text-sm font-bold text-surface-800">
                                    {{ $group['label'] }}
                                </td>
                            </tr>
                            @foreach($group['items'] as $item)
                                <tr class="border-b border-surface-100 hover:bg-surface-50 transition">
                                    <td class="table-td font-mono text-xs">{{ $item['code'] }}</td>
                                    <td class="table-td">{{ $item['name'] }}</td>
                                    <td class="table-td text-right font-mono">
                                        {{ $item['debit_balance'] > 0 ? number_format($item['debit_balance'], 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="table-td text-right font-mono">
                                        {{ $item['credit_balance'] > 0 ? number_format($item['credit_balance'], 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="border-b border-surface-200 bg-surface-100">
                                <td colspan="2" class="px-4 py-2 text-xs font-semibold text-surface-600">Subtotal {{ $group['label'] }}</td>
                                <td class="px-4 py-2 text-right text-xs font-bold font-mono text-surface-700">{{ number_format($group['total_debit'], 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right text-xs font-bold font-mono text-surface-700">{{ number_format($group['total_credit'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-surface-800 text-white">
                            <td colspan="2" class="px-4 py-3 text-sm font-bold">TOTAL</td>
                            <td class="px-4 py-3 text-right text-sm font-bold font-mono">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-bold font-mono">{{ number_format($totalCredit, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if(abs($totalDebit - $totalCredit) > 0.01)
                <div class="alert-warning">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <span>Total Debit ({{ number_format($totalDebit, 0, ',', '.') }}) dan Kredit ({{ number_format($totalCredit, 0, ',', '.') }}) tidak seimbang. Selisih: {{ number_format(abs($totalDebit - $totalCredit), 0, ',', '.') }}</span>
                </div>
            @else
                <div class="alert-success">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Total Debit dan Kredit <strong>seimbang</strong>.</span>
                </div>
            @endif
        @endif

    </div>
</x-app-layout>
