<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('penjualan-harian.index') }}"
                   class="btn-secondary btn-sm">
                    ← Kembali
                </a>
                <div>
                    <h1 class="page-title">
                        Detail Penjualan –
                        @if($shift === 'pagi') ☀️ Shift Pagi @else 🌆 Shift Sore @endif
                        · {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </h1>
                    <p class="text-xs text-surface-500">{{ $items->count() }} produk terjual</p>
                </div>
            </div>
            <a href="{{ route('penjualan-harian.create', ['date' => $date, 'shift' => $shift]) }}"
               class="btn-secondary text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5">

        {{-- Ringkasan --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card p-4">
                <p class="text-xs text-surface-500 uppercase font-medium">📦 Total Terjual</p>
                <p class="text-2xl font-extrabold text-brand-600 mt-1">{{ number_format($totalQty) }} pcs</p>
            </div>
            <div class="stat-card p-4">
                <p class="text-xs text-surface-500 uppercase font-medium">💰 Omset</p>
                <p class="text-2xl font-extrabold text-surface-900 mt-1">Rp {{ number_format($totalOmset, 0, ',', '.') }}</p>
            </div>
            <div class="stat-card p-4">
                <p class="text-xs text-surface-500 uppercase font-medium">🧮 HPP</p>
                <p class="text-2xl font-extrabold text-amber-600 mt-1">Rp {{ number_format($totalHpp, 0, ',', '.') }}</p>
            </div>
            <div class="stat-card p-4">
                <p class="text-xs text-surface-500 uppercase font-medium">📈 Keuntungan</p>
                <p class="text-2xl font-extrabold mt-1 {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($totalProfit, 0, ',', '.') }}
                </p>
                @if($totalOmset > 0)
                <p class="text-xs text-surface-400 mt-0.5">Margin {{ round(($totalProfit/$totalOmset)*100, 1) }}%</p>
                @endif
            </div>
        </div>

        {{-- Tabel produk --}}
        <div class="stat-card p-4">
            <div class="px-5 py-4 border-b border-surface-200">
                <h2 class="text-sm font-bold text-surface-700">Rincian Produk</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-th">
                            <th class="px-5 py-3 text-left">Produk</th>
                            <th class="px-4 py-3 text-right">Harga</th>
                            <th class="px-4 py-3 text-right">Terjual</th>
                            <th class="px-4 py-3 text-right">Omset</th>
                            <th class="px-4 py-3 text-right">HPP</th>
                            <th class="px-4 py-3 text-right">Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach($items as $item)
                        <tr class="hover:bg-surface-50">
                            <td class="px-5 py-3 font-medium text-surface-800">{{ $item->product_name }}</td>
                            <td class="px-4 py-3 text-right text-surface-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-brand-600">{{ number_format($item->quantity_sold) }} pcs</td>
                            <td class="px-4 py-3 text-right text-surface-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-amber-600">
                                @if($item->hpp_per_unit > 0)
                                    Rp {{ number_format($item->hpp_total, 0, ',', '.') }}
                                @else
                                    <span class="text-surface-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold {{ $item->profit >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                @if($item->hpp_per_unit > 0)
                                    Rp {{ number_format($item->profit, 0, ',', '.') }}
                                @else
                                    <span class="text-surface-300">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-surface-50 font-bold border-t-2 border-surface-200">
                            <td colspan="2" class="px-5 py-3 text-surface-600">Total</td>
                            <td class="px-4 py-3 text-right text-brand-600">{{ number_format($totalQty) }} pcs</td>
                            <td class="px-4 py-3 text-right text-surface-800">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-amber-600">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                Rp {{ number_format($totalProfit, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
