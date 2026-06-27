<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="page-title">Neraca (Balance Sheet)</h2>
            <p class="text-xs text-surface-500 mt-0.5">Per {{ now()->format('d F Y') }}</p>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5">

        @if(abs($totalAssets - $totalLiabilitiesAndEquity) > 0.01)
            <div class="alert-warning">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <span>Neraca <strong>tidak seimbang</strong>. Total Aset: Rp {{ number_format($totalAssets, 0, ',', '.') }} &ne; Kewajiban + Ekuitas: Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}. Selisih: Rp {{ number_format(abs($totalAssets - $totalLiabilitiesAndEquity), 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- ASET --}}
            <div class="card">
                <div class="px-5 py-4 border-b border-surface-200 bg-blue-50">
                    <h3 class="font-bold text-surface-900 text-lg">Aset (Assets)</h3>
                </div>
                @if(count($assetItems) > 0)
                    <table class="table-wrap">
                        <tbody>
                            @foreach($assetItems as $item)
                                <tr class="border-b border-surface-100 hover:bg-surface-50 transition">
                                    <td class="table-td font-mono text-xs w-20">{{ $item['code'] }}</td>
                                    <td class="table-td">{{ $item['name'] }}</td>
                                    <td class="table-td text-right font-mono">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-blue-50 border-t-2 border-blue-200">
                                <td colspan="2" class="px-4 py-3 text-sm font-bold text-blue-800">Total Aset</td>
                                <td class="px-4 py-3 text-right text-sm font-bold font-mono text-blue-800">Rp {{ number_format($totalAssets, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="empty-state">Belum ada akun aset.</div>
                @endif
            </div>

            {{-- KEWAJIBAN & EKUITAS --}}
            <div class="space-y-5">

                <div class="card">
                    <div class="px-5 py-4 border-b border-surface-200 bg-red-50">
                        <h3 class="font-bold text-surface-900 text-lg">Kewajiban (Liabilities)</h3>
                    </div>
                    @if(count($liabilityItems) > 0)
                        <table class="table-wrap">
                            <tbody>
                                @foreach($liabilityItems as $item)
                                    <tr class="border-b border-surface-100 hover:bg-surface-50 transition">
                                        <td class="table-td font-mono text-xs w-20">{{ $item['code'] }}</td>
                                        <td class="table-td">{{ $item['name'] }}</td>
                                        <td class="table-td text-right font-mono">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-red-50 border-t-2 border-red-200">
                                    <td colspan="2" class="px-4 py-3 text-sm font-bold text-red-800">Total Kewajiban</td>
                                    <td class="px-4 py-3 text-right text-sm font-bold font-mono text-red-800">Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="empty-state">Belum ada akun kewajiban.</div>
                    @endif
                </div>

                <div class="card">
                    <div class="px-5 py-4 border-b border-surface-200 bg-emerald-50">
                        <h3 class="font-bold text-surface-900 text-lg">Ekuitas (Equity)</h3>
                    </div>
                    <table class="table-wrap">
                        <tbody>
                            @foreach($equityItems as $item)
                                <tr class="border-b border-surface-100 hover:bg-surface-50 transition">
                                    <td class="table-td font-mono text-xs w-20">{{ $item['code'] }}</td>
                                    <td class="table-td">{{ $item['name'] }}</td>
                                    <td class="table-td text-right font-mono">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="border-b border-surface-100 hover:bg-surface-50 transition">
                                <td class="table-td font-mono text-xs w-20"></td>
                                <td class="table-td text-surface-600">Laba/Rugi Tahun Berjalan</td>
                                <td class="table-td text-right font-mono {{ $currentYearIncome >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                                    Rp {{ number_format($currentYearIncome, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-emerald-50 border-t-2 border-emerald-200">
                                <td colspan="2" class="px-4 py-3 text-sm font-bold text-emerald-800">Total Ekuitas</td>
                                <td class="px-4 py-3 text-right text-sm font-bold font-mono text-emerald-800">Rp {{ number_format($totalEquityWithIncome, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="card border-2 {{ abs($totalAssets - $totalLiabilitiesAndEquity) > 0.01 ? 'border-red-300 bg-red-50/30' : 'border-emerald-300 bg-emerald-50/30' }}">
                    <div class="flex justify-between items-center px-5 py-4">
                        <span class="text-sm font-bold text-surface-800">Total Kewajiban & Ekuitas</span>
                        <span class="text-lg font-bold font-mono text-surface-900">Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}</span>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
