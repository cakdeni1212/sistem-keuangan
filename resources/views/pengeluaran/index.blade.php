<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-surface-800 dark:text-surface-100 leading-tight">Pengeluaran</h2>
                <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">Pantau dan analisis pengeluaran per periode</p>
            </div>
            @can('create transactions')
            <a href="{{ route('transactions.create') }}"
               class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Input Transaksi
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-4">

        {{-- Filter Bar --}}
        <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 shadow-sm px-5 py-4">
            <form method="GET" action="{{ route('pengeluaran.index') }}" id="filter-form"
                  class="flex flex-wrap items-center gap-3">

                <span class="text-sm font-semibold text-surface-600 dark:text-surface-300 mr-1">🗓 Filter Periode:</span>

                <select name="month" onchange="document.getElementById('filter-form').submit()"
                        class="border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-surface-200 rounded-lg text-sm py-1.5 pr-8">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" @selected($num == $month)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="year" onchange="document.getElementById('filter-form').submit()"
                        class="border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-surface-200 rounded-lg text-sm py-1.5 pr-8">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>

                {{-- Filter Tanggal --}}
                @php
                    $minDate = \Carbon\Carbon::create($year, $month, 1)->format('Y-m-d');
                    $maxDate = \Carbon\Carbon::create($year, $month)->endOfMonth()->format('Y-m-d');
                @endphp
                <div class="flex items-center gap-1.5">
                    <input type="date" name="date"
                           value="{{ $filterDate ?? '' }}"
                           min="{{ $minDate }}" max="{{ $maxDate }}"
                           onchange="document.getElementById('filter-form').submit()"
                           class="border-surface-300 dark:border-surface-600 dark:bg-surface-700 dark:text-surface-200 rounded-lg text-sm py-1.5 px-3">
                    @if($filterDate)
                        <a href="{{ route('pengeluaran.index', ['month' => $month, 'year' => $year]) }}"
                           class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900 transition" title="Hapus filter tanggal">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>

                @php
                    $nowM = now()->month; $nowY = now()->year;
                    $prevM = now()->subMonth()->month; $prevY = now()->subMonth()->year;
                    $prev2M = now()->subMonths(2)->month; $prev2Y = now()->subMonths(2)->year;
                @endphp
                <a href="{{ route('pengeluaran.index', ['month' => $nowM, 'year' => $nowY]) }}"
                   class="px-3 py-1 text-xs rounded-full border transition {{ ($month == $nowM && $year == $nowY && !$filterDate) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white dark:bg-surface-700 text-surface-600 dark:text-surface-300 border-surface-300 dark:border-surface-600 hover:border-brand-400' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('pengeluaran.index', ['month' => $prevM, 'year' => $prevY]) }}"
                   class="px-3 py-1 text-xs rounded-full border transition {{ ($month == $prevM && $year == $prevY && !$filterDate) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white dark:bg-surface-700 text-surface-600 dark:text-surface-300 border-surface-300 dark:border-surface-600 hover:border-brand-400' }}">
                    Bulan Lalu
                </a>
                <a href="{{ route('pengeluaran.index', ['month' => $prev2M, 'year' => $prev2Y]) }}"
                   class="px-3 py-1 text-xs rounded-full border transition hidden sm:inline-block {{ ($month == $prev2M && $year == $prev2Y && !$filterDate) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white dark:bg-surface-700 text-surface-600 dark:text-surface-300 border-surface-300 dark:border-surface-600 hover:border-brand-400' }}">
                    {{ $months[$prev2M] }} {{ $prev2Y }}
                </a>

                {{-- Export --}}
                @if($totalAllCount > 0)                <a href="{{ route('laporan.export-transaksi', ['month' => $month, 'year' => $year, 'category' => 'pengeluaran']) }}"
                   class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 text-white text-xs font-medium rounded-lg hover:bg-brand-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export {{ $months[$month] }} {{ $year }}
                </a>
                @endif
            </form>
        </div>

        {{-- Card 1: Ringkasan Pengeluaran --}}
        <div class="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border border-red-100 dark:border-red-800 rounded-xl shadow-sm p-5">
            <p class="text-xs font-bold text-red-500 dark:text-red-400 uppercase tracking-widest mb-4">💸 Ringkasan Pengeluaran — {{ $periodLabel }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- Total Disetujui --}}
                <div class="bg-white/70 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-red-400 dark:text-red-400 font-medium">Total Disetujui</p>
                        <p class="text-xl font-extrabold text-red-700 dark:text-red-300 leading-tight">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                        @if($pctChange !== null)
                        <p class="text-xs font-semibold {{ $selisih >= 0 ? 'text-red-500' : 'text-green-500' }}">
                            {{ $selisih >= 0 ? '▲' : '▼' }} {{ number_format(abs($pctChange), 1) }}% vs bln lalu
                        </p>
                        @endif
                    </div>
                </div>

                {{-- Menunggu Approval --}}
                <div class="bg-white/70 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800 rounded-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-yellow-500 dark:text-yellow-400 font-medium">Menunggu Approval</p>
                        <p class="text-xl font-extrabold text-yellow-600 dark:text-yellow-300 leading-tight">Rp {{ number_format($pendingTotal, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Total Transaksi --}}
                <div class="bg-white/70 dark:bg-surface-700/50 border border-surface-200 dark:border-surface-600 rounded-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-100 dark:bg-surface-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-surface-600 dark:text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-surface-500 dark:text-surface-400 font-medium">Total Transaksi</p>
                        <p class="text-xl font-extrabold text-surface-700 dark:text-surface-200 leading-tight">{{ $totalAllCount }} <span class="text-sm font-normal text-surface-400">transaksi</span></p>
                        <p class="text-xs text-surface-400">{{ $approvedCount }} disetujui</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Card 2: Rincian per Kategori --}}
        <div class="bg-gradient-to-r from-brand-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 border border-brand-100 dark:border-brand-800 rounded-xl shadow-sm p-5">
            <p class="text-xs font-bold text-brand-500 dark:text-brand-400 uppercase tracking-widest mb-4">📂 Rincian per Kategori</p>
            @php
                $grandTotal = $dapurTotal + $barTotal + $operasionalTotal;
                $grupItems = [
                    [
                        'label'    => 'Dapur',
                        'total'    => $dapurTotal,
                        'boxBg'    => 'bg-white/70 dark:bg-orange-900/20',
                        'boxBorder'=> 'border-orange-100 dark:border-orange-800',
                        'iconBg'   => 'bg-orange-100 dark:bg-orange-900/50',
                        'iconColor'=> 'text-orange-600 dark:text-orange-400',
                        'valColor' => 'text-orange-700 dark:text-orange-300',
                        'subColor' => 'text-orange-400',
                        'barHex'   => '#fb923c',
                        'icon'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>',
                    ],
                    [
                        'label'    => 'BAR',
                        'total'    => $barTotal,
                        'boxBg'    => 'bg-white/70 dark:bg-blue-900/20',
                        'boxBorder'=> 'border-blue-100 dark:border-blue-800',
                        'iconBg'   => 'bg-blue-100 dark:bg-blue-900/50',
                        'iconColor'=> 'text-blue-600 dark:text-blue-400',
                        'valColor' => 'text-blue-700 dark:text-blue-300',
                        'subColor' => 'text-blue-400',
                        'barHex'   => '#60a5fa',
                        'icon'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                    ],
                    [
                        'label'    => 'Operasional',
                        'total'    => $operasionalTotal,
                        'boxBg'    => 'bg-white/70 dark:bg-slate-700/50',
                        'boxBorder'=> 'border-slate-200 dark:border-slate-600',
                        'iconBg'   => 'bg-slate-100 dark:bg-slate-600',
                        'iconColor'=> 'text-slate-600 dark:text-slate-300',
                        'valColor' => 'text-slate-700 dark:text-slate-200',
                        'subColor' => 'text-slate-400',
                        'barHex'   => '#94a3b8',
                        'icon'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                    ],
                ];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach($grupItems as $g)
                <div class="{{ $g['boxBg'] }} border {{ $g['boxBorder'] }} rounded-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg {{ $g['iconBg'] }} flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 {{ $g['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $g['icon'] !!}
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs {{ $g['subColor'] }} font-medium">{{ $g['label'] }}</p>
                        <p class="text-xl font-extrabold {{ $g['valColor'] }} leading-tight truncate">
                            Rp {{ number_format($g['total'], 0, ',', '.') }}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="flex-1 h-2 bg-surface-200 dark:bg-surface-600 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="width: {{ $grandTotal > 0 ? number_format($g['total'] / $grandTotal * 100, 1, '.', '') : 0 }}%; background-color: {{ $g['barHex'] }};"></div>
                            </div>
                            <span class="text-xs font-bold {{ $g['subColor'] }} shrink-0">
                                {{ $grandTotal > 0 ? number_format($g['total'] / $grandTotal * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- 2-column: Chart + Per Jenis --}}
        @if($totalAllCount > 0 && !$filterDate)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Bar Chart --}}
            <div class="lg:col-span-2 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-100 mb-4">
                    📊 Grafik Pengeluaran Harian — {{ $months[$month] }} {{ $year }}
                </h3>
                <div style="height: 280px;">
                    <canvas id="chartPengeluaranHarian"></canvas>
                </div>
            </div>

            {{-- Per Jenis --}}
            <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 shadow-sm overflow-hidden"
                 x-data="{
                     rows: {{ Illuminate\Support\Js::from($perJenis) }},
                     sortCol: 'total',
                     sortDir: 'desc',
                     sort(col) {
                         if (this.sortCol === col) {
                             this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                         } else {
                             this.sortCol = col;
                             this.sortDir = col === 'name' ? 'asc' : 'desc';
                         }
                     },
                     get sorted() {
                         return [...this.rows].sort((a, b) => {
                             let va = a[this.sortCol], vb = b[this.sortCol];
                             if (typeof va === 'string') va = va.toLowerCase(), vb = vb.toLowerCase();
                             if (va < vb) return this.sortDir === 'asc' ? -1 : 1;
                             if (va > vb) return this.sortDir === 'asc' ? 1 : -1;
                             return 0;
                         });
                     },
                     totalAll: {{ $totalPengeluaran ?? 0 }},
                 }">

                <div class="px-4 py-3 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-100">🏷️ Per Jenis Transaksi</h3>
                    <span class="text-xs text-surface-400 dark:text-surface-500" x-text="rows.length + ' jenis'"></span>
                </div>

                @if($perJenis->count() > 0)
                <div class="overflow-auto max-h-80">
                    <table class="w-full text-xs">
                        <thead class="bg-surface-50 dark:bg-surface-700/50 sticky top-0">
                            <tr>
                                {{-- Jenis --}}
                                <th class="px-4 py-2.5 text-left">
                                    <button @click="sort('name')"
                                            class="flex items-center gap-1 text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase hover:text-brand-600 dark:hover:text-brand-400 transition">
                                        Jenis
                                        <span class="flex flex-col leading-none">
                                            <svg class="w-2.5 h-2.5" :class="sortCol==='name' && sortDir==='asc' ? 'text-brand-600 dark:text-brand-400' : 'text-surface-300 dark:text-surface-600'" fill="currentColor" viewBox="0 0 10 6"><path d="M5 0L0 6h10z"/></svg>
                                            <svg class="w-2.5 h-2.5" :class="sortCol==='name' && sortDir==='desc' ? 'text-brand-600 dark:text-brand-400' : 'text-surface-300 dark:text-surface-600'" fill="currentColor" viewBox="0 0 10 6"><path d="M5 6L0 0h10z"/></svg>
                                        </span>
                                    </button>
                                </th>
                                {{-- Jumlah Tx --}}
                                <th class="px-4 py-2.5 text-center">
                                    <button @click="sort('count')"
                                            class="flex items-center gap-1 text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase hover:text-brand-600 dark:hover:text-brand-400 transition mx-auto">
                                        Tx
                                        <span class="flex flex-col leading-none">
                                            <svg class="w-2.5 h-2.5" :class="sortCol==='count' && sortDir==='asc' ? 'text-brand-600 dark:text-brand-400' : 'text-surface-300 dark:text-surface-600'" fill="currentColor" viewBox="0 0 10 6"><path d="M5 0L0 6h10z"/></svg>
                                            <svg class="w-2.5 h-2.5" :class="sortCol==='count' && sortDir==='desc' ? 'text-brand-600 dark:text-brand-400' : 'text-surface-300 dark:text-surface-600'" fill="currentColor" viewBox="0 0 10 6"><path d="M5 6L0 0h10z"/></svg>
                                        </span>
                                    </button>
                                </th>
                                {{-- Total --}}
                                <th class="px-4 py-2.5 text-right">
                                    <button @click="sort('total')"
                                            class="flex items-center gap-1 text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase hover:text-brand-600 dark:hover:text-brand-400 transition ml-auto">
                                        Total
                                        <span class="flex flex-col leading-none">
                                            <svg class="w-2.5 h-2.5" :class="sortCol==='total' && sortDir==='asc' ? 'text-brand-600 dark:text-brand-400' : 'text-surface-300 dark:text-surface-600'" fill="currentColor" viewBox="0 0 10 6"><path d="M5 0L0 6h10z"/></svg>
                                            <svg class="w-2.5 h-2.5" :class="sortCol==='total' && sortDir==='desc' ? 'text-brand-600 dark:text-brand-400' : 'text-surface-300 dark:text-surface-600'" fill="currentColor" viewBox="0 0 10 6"><path d="M5 6L0 0h10z"/></svg>
                                        </span>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 dark:divide-gray-700">
                            <template x-for="(row, i) in sorted" :key="i">
                                <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                                    <td class="px-4 py-2.5 text-surface-700 dark:text-surface-200 font-medium" x-text="row.name"></td>
                                    <td class="px-4 py-2.5 text-center text-surface-500 dark:text-surface-400" x-text="row.count"></td>
                                    <td class="px-4 py-2.5 text-right font-semibold text-red-600 dark:text-red-400"
                                        x-text="'Rp ' + row.total.toLocaleString('id-ID')"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-surface-400 dark:text-surface-500 text-center py-8">Belum ada data</p>
                @endif
            </div>

        </div>
        @endif

        {{-- Tabel Transaksi --}}
        <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-100">Daftar Transaksi Pengeluaran</h3>
                <span class="text-xs text-surface-400 dark:text-surface-500">{{ $totalAllCount }} transaksi</span>
            </div>

            {{-- Mobile: card list --}}
            <div class="divide-y divide-surface-100 dark:divide-gray-700 block sm:hidden">
                @forelse($records as $tx)
                <div class="px-4 py-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-surface-800 dark:text-surface-100 truncate">{{ $tx->transactionType->name }}</p>
                            <p class="text-xs text-surface-400 dark:text-surface-500 mt-0.5">
                                {{ $tx->transaction_date->isoFormat('ddd, D MMM Y') }}
                            </p>
                            @if($tx->description)
                            <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5 truncate">{{ $tx->description }}</p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-red-600 dark:text-red-400">Rp {{ number_format($tx->amount, 0, ',', '.') }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full font-medium
                                {{ $tx->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
                                : ($tx->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'
                                : ($tx->status === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'
                                : 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400')) }}">
                                {{ $tx->status_label }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-surface-400 dark:text-surface-500 text-sm">
                    Belum ada pengeluaran untuk {{ $months[$month] }} {{ $year }}.
                </div>
                @endforelse
            </div>

            {{-- Desktop: table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-50 dark:bg-surface-700/50 border-b border-surface-200 dark:border-surface-700">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-surface-600 dark:text-surface-400 uppercase">Tanggal</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-surface-600 dark:text-surface-400 uppercase">Jenis</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-red-600 dark:text-red-400 uppercase">Jumlah</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-surface-600 dark:text-surface-400 uppercase">Keterangan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-surface-600 dark:text-surface-400 uppercase">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-surface-600 dark:text-surface-400 uppercase">Oleh</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100 dark:divide-gray-700">
                        @forelse($records as $tx)
                        <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                            <td class="px-5 py-3 text-surface-700 dark:text-surface-300 whitespace-nowrap">
                                <p class="font-medium">{{ $tx->transaction_date->format('d M Y') }}</p>
                                <p class="text-xs text-surface-400 dark:text-surface-500">{{ $tx->transaction_date->isoFormat('dddd') }}</p>
                            </td>
                            <td class="px-5 py-3 text-surface-800 dark:text-surface-100 font-medium">
                                {{ $tx->transactionType->name }}
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-red-600 dark:text-red-400 whitespace-nowrap">
                                Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-surface-500 dark:text-surface-400 max-w-xs truncate">
                                {{ $tx->description ?? '-' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $tx->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
                                    : ($tx->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'
                                    : ($tx->status === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'
                                    : 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400')) }}">
                                    {{ $tx->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-surface-500 dark:text-surface-400 text-xs">
                                {{ $tx->creator->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @can('view transactions')
                                    <a href="{{ route('transactions.show', $tx) }}" title="Detail"
                                       class="p-1.5 text-brand-600 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/30 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('edit transactions')
                                    @if(in_array($tx->status, ['draft','pending','rejected']))
                                    <a href="{{ route('transactions.edit', $tx) }}" title="Edit"
                                       class="p-1.5 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @endif
                                    @endcan
                                    @can('delete transactions')
                                    <form method="POST" action="{{ route('transactions.destroy', $tx) }}"
                                          onsubmit="return confirm('Hapus transaksi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-surface-400 dark:text-surface-500">
                                Belum ada pengeluaran untuk {{ $months[$month] }} {{ $year }}.
                                @can('create transactions')
                                <a href="{{ route('transactions.create') }}" class="text-brand-600 dark:text-brand-400 hover:underline ml-1">+ Tambah sekarang</a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($totalPengeluaran > 0)
                    <tfoot class="bg-surface-50 dark:bg-surface-700/50 border-t-2 border-surface-200 dark:border-surface-600">
                        <tr>
                            <td colspan="2" class="px-5 py-3 text-sm font-semibold text-surface-600 dark:text-surface-300">
                                Total Disetujui ({{ $approvedCount }} transaksi)
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-red-600 dark:text-red-400 whitespace-nowrap">
                                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            {{-- Pagination --}}
            @if($records->hasPages())
            <div class="px-5 py-4 border-t dark:border-surface-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-surface-500 dark:text-surface-400">
                    Menampilkan {{ $records->firstItem() }}–{{ $records->lastItem() }} dari {{ $records->total() }} transaksi
                </p>
                <div>
                    {{ $records->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>

    @if($totalAllCount > 0 && !$filterDate)
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function () {
        const labels = {{ Illuminate\Support\Js::from($chartLabels) }};
        const data   = {{ Illuminate\Support\Js::from($chartData) }};

        const fmtRp = n => {
            if (n >= 1e6) return 'Rp ' + (n / 1e6).toFixed(1).replace('.0', '') + 'jt';
            if (n >= 1e3) return 'Rp ' + (n / 1e3).toFixed(0) + 'rb';
            return 'Rp ' + n;
        };

        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.04)';
        const tickColor = isDark ? '#9ca3af' : '#6b7280';

        new Chart(document.getElementById('chartPengeluaranHarian'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Pengeluaran',
                    data,
                    backgroundColor: data.map(v => v > 0 ? 'rgba(239,68,68,0.75)' : 'rgba(239,68,68,0.15)'),
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' Pengeluaran: ' + fmtRp(ctx.raw),
                            title: items => {
                                const lbl = items[0].label;
                                return Array.isArray(lbl) ? lbl.join(', ') : lbl;
                            },
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 },
                            color: ctx => {
                                const lbl = ctx.chart.data.labels[ctx.index];
                                const day = Array.isArray(lbl) ? lbl[0] : '';
                                return (day === 'Min' || day === 'Sab') ? '#ef4444' : tickColor;
                            },
                            maxRotation: 0,
                            autoSkip: false,
                        }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: { font: { size: 10 }, color: tickColor, callback: v => fmtRp(v) },
                    }
                }
            }
        });
    })();
    </script>
    @endif

</x-app-layout>
