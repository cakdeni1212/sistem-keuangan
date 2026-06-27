<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="page-title">Omset Harian</h2>
                <p class="text-xs text-surface-500 mt-0.5">Input dan pantau omset QRIS & tunai per hari</p>
            </div>
            @can('create daily revenues')
            <div class="flex items-center gap-2">
                <a href="{{ route('daily-revenues.upload-form') }}"
                   class="btn-secondary">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload Excel
                </a>
                <a href="{{ route('daily-revenues.create') }}"
                   class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Input Omset
                </a>
            </div>
            @endcan
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-4">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert-success text-sm">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="alert-info">{{ session('info') }}</div>
        @endif
        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="alert-warning">
                <p class="font-medium text-sm mb-1">⚠️ Beberapa baris dilewati:</p>
                <ul class="text-xs list-disc list-inside space-y-0.5">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Filter Bar --}}
        <div class="card px-5 py-4">
            <form method="GET" action="{{ route('daily-revenues.index') }}" id="omset-filter-form"
                  class="flex flex-wrap items-center gap-3">

                <span class="text-sm font-semibold text-surface-600 mr-1">🗓 Filter Periode:</span>

                <select name="month" onchange="document.getElementById('omset-filter-form').submit()"
                        class="border-surface-300 rounded-lg text-sm py-1.5 pr-8">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" @selected($num == $month)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="year" onchange="document.getElementById('omset-filter-form').submit()"
                        class="border-surface-300 rounded-lg text-sm py-1.5 pr-8">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>

                {{-- Shortcut pills --}}
                @php
                    $nowM = now()->month; $nowY = now()->year;
                    $prevM = now()->subMonth()->month; $prevY = now()->subMonth()->year;
                    $prev2M = now()->subMonths(2)->month; $prev2Y = now()->subMonths(2)->year;
                @endphp
                <a href="{{ route('daily-revenues.index', ['month' => $nowM, 'year' => $nowY]) }}"
                   class="px-3 py-1 text-xs rounded-full border transition {{ ($month == $nowM && $year == $nowY) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('daily-revenues.index', ['month' => $prevM, 'year' => $prevY]) }}"
                   class="px-3 py-1 text-xs rounded-full border transition {{ ($month == $prevM && $year == $prevY) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400' }}">
                    Bulan Lalu
                </a>
                <a href="{{ route('daily-revenues.index', ['month' => $prev2M, 'year' => $prev2Y]) }}"
                   class="px-3 py-1 text-xs rounded-full border transition hidden sm:inline-block {{ ($month == $prev2M && $year == $prev2Y) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400' }}">
                    {{ $months[$prev2M] }} {{ $prev2Y }}
                </a>
                @foreach($years as $y)
                    @if($y != $nowY)
                    <a href="{{ route('daily-revenues.index', ['month' => 1, 'year' => $y, '_range' => 'year']) }}"
                       class="px-3 py-1 text-xs rounded-full border transition hidden md:inline-block {{ ($year == $y && $month == 1) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400' }}">
                        {{ $y }}
                    </a>
                    @endif
                @endforeach

                {{-- Export --}}
                @if($records->count() > 0)
                <a href="{{ route('laporan.export-omset', ['month' => $month, 'year' => $year]) }}"
                   class="ml-auto btn-primary">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export {{ $months[$month] }} {{ $year }}
                </a>
                @endif
            </form>
        </div>

        {{-- All-Time Omset Banner --}}
        <div class="bg-gradient-to-r from-blue-50 to-brand-50 border border-brand-200 rounded-xl p-5">
            <h3 class="text-xs font-bold text-brand-700 uppercase tracking-wide mb-3">📈 Total Omset — {{ $periodLabel }}</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-brand-700">Rp {{ number_format($allTimeOmset, 0, ',', '.') }}</p>
                    <p class="text-xs text-brand-500 mt-1">Total Omset</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-purple-700">Rp {{ number_format($allTimeQris, 0, ',', '.') }}</p>
                    <p class="text-xs text-purple-500 mt-1">Total QRIS</p>
                    @if($allTimeOmset > 0)
                    <div class="mt-2 w-full max-w-xs mx-auto bg-purple-100 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ round($allTimeQris / $allTimeOmset * 100) }}%"></div>
                    </div>
                    <p class="text-xs text-purple-400 mt-1">{{ round($allTimeQris / $allTimeOmset * 100) }}%</p>
                    @endif
                </div>
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-orange-600">Rp {{ number_format($allTimeTunai, 0, ',', '.') }}</p>
                    <p class="text-xs text-orange-500 mt-1">Total Tunai</p>
                    @if($allTimeOmset > 0)
                    <div class="mt-2 w-full max-w-xs mx-auto bg-orange-100 rounded-full h-2">
                        <div class="bg-orange-400 h-2 rounded-full" style="width: {{ round($allTimeTunai / $allTimeOmset * 100) }}%"></div>
                    </div>
                    <p class="text-xs text-orange-400 mt-1">{{ round($allTimeTunai / $allTimeOmset * 100) }}%</p>
                    @endif
                </div>
                <div class="text-center border-t-2 lg:border-t-0 lg:border-l-2 border-brand-100 pt-3 lg:pt-0 lg:pl-4 col-span-2 lg:col-span-1">
                    <p class="text-2xl font-extrabold text-green-600">Rp {{ number_format($avgOmset, 0, ',', '.') }}</p>
                    <p class="text-xs text-green-500 mt-1">Rata-rata/Hari</p>
                    @if($recordCount > 0)
                    <p class="text-xs text-surface-400 mt-1">dari {{ $recordCount }} hari data</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Bar Chart (full width, hanya jika ada data) --}}
        @if($records->count() > 0)
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-surface-700 mb-4">Grafik Omset Harian — {{ $months[$month] }} {{ $year }}</h3>
            <div style="height: 280px;">
                <canvas id="chartOmsetHarian"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const labels = {{ Illuminate\Support\Js::from($chartLabels) }};
            const qris   = {{ Illuminate\Support\Js::from($chartQris) }};
            const tunai  = {{ Illuminate\Support\Js::from($chartTunai) }};
            const total  = {{ Illuminate\Support\Js::from($chartTotal) }};

            const fmtRp = n => {
                if (n >= 1e6) return 'Rp ' + (n / 1e6).toFixed(1).replace('.0', '') + 'jt';
                if (n >= 1e3) return 'Rp ' + (n / 1e3).toFixed(0) + 'rb';
                return 'Rp ' + n;
            };

            new Chart(document.getElementById('chartOmsetHarian'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'QRIS',
                            data: qris,
                            backgroundColor: 'rgba(139,92,246,0.8)',
                            borderRadius: 4,
                            stack: 'omset',
                        },
                        {
                            label: 'Tunai',
                            data: tunai,
                            backgroundColor: 'rgba(249,115,22,0.8)',
                            borderRadius: 4,
                            stack: 'omset',
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
                        tooltip: {
                            callbacks: {
                                label: ctx => ' ' + ctx.dataset.label + ': ' + fmtRp(ctx.raw),
                                footer: items => ' Total: ' + fmtRp(total[items[0].dataIndex]),
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: {
                                font: (ctx) => ({
                                    size: 10,
                                    weight: ctx.tick && ctx.tick.label === ctx.chart.data.labels[ctx.index]?.[0] ? '600' : '400',
                                }),
                                color: (ctx) => {
                                    const label = ctx.chart.data.labels[ctx.index];
                                    if (!Array.isArray(label)) return '#6b7280';
                                    const day = label[0];
                                    return (day === 'Min' || day === 'Sab') ? '#ef4444' : '#6b7280';
                                },
                                maxRotation: 0,
                                autoSkip: false,
                            }
                        },
                        y: {
                            stacked: true,
                            ticks: { font: { size: 10 }, callback: v => fmtRp(v) },
                            grid: { color: 'rgba(0,0,0,0.05)' },
                        }
                    }
                }
            });
        })();
        </script>
        @endif

        {{-- Tabel Data --}}
        <div class="card">
            <table class="w-full text-sm">
                <thead class="table-th">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Tanggal</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-brand-600 uppercase">Penjualan Harian</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-purple-600 uppercase">QRIS</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-orange-600 uppercase">Tunai</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-surface-700 uppercase">Total Manual</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Catatan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Oleh</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @forelse($allDates as $dateStr)
                    @php
                        $rec = $records->first(fn($r) => $r->date->toDateString() === $dateStr);
                        $pj  = $penjualanHarian->get($dateStr);
                        $carbonDate = \Carbon\Carbon::parse($dateStr);
                    @endphp
                    <tr class="hover:bg-surface-50 {{ $pj && !$rec ? 'bg-amber-50' : '' }}">
                        <td class="px-5 py-3 font-medium text-surface-800">
                            {{ $carbonDate->isoFormat('ddd, D MMM Y') }}
                            @if($carbonDate->isToday())
                                <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-brand-100 text-brand-700">Hari ini</span>
                            @endif
                        </td>

                        {{-- Kolom omset dari penjualan harian --}}
                        <td class="px-5 py-3 text-right">
                            @if($pj)
                                <div class="flex flex-col items-end gap-0.5">
                                    <span class="font-semibold text-brand-700">Rp {{ number_format($pj->total_omset, 0, ',', '.') }}</span>
                                    <span class="text-xs text-surface-400">{{ number_format($pj->total_qty) }} pcs</span>
                                </div>
                                @if(!$rec)
                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">
                                        ⚠️ Belum input omset manual
                                    </span>
                                @endif
                            @else
                                <span class="text-surface-300 text-xs">—</span>
                            @endif
                        </td>

                        {{-- QRIS, Tunai, Total dari daily_revenues --}}
                        @if($rec)
                            <td class="px-5 py-3 text-right text-purple-700 font-medium">Rp {{ number_format($rec->qris_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right text-orange-600 font-medium">Rp {{ number_format($rec->tunai_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-bold text-surface-800">
                                Rp {{ number_format($rec->total, 0, ',', '.') }}
                                @if($pj)
                                @php $selisih = $rec->total - $pj->total_omset; @endphp
                                @if(abs($selisih) > 0)
                                    <div class="text-xs font-normal {{ $selisih > 0 ? 'text-blue-500' : 'text-red-500' }} mt-0.5">
                                        {{ $selisih > 0 ? '+' : '' }}Rp {{ number_format($selisih, 0, ',', '.') }} vs penjualan
                                    </div>
                                @else
                                    <div class="text-xs text-green-500 font-normal mt-0.5">✓ Cocok</div>
                                @endif
                                @endif
                            </td>
                            <td class="px-5 py-3 text-surface-500 max-w-xs truncate">{{ $rec->notes ?? '-' }}</td>
                            <td class="px-5 py-3 text-surface-500">{{ $rec->creator->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @can('edit daily revenues')
                                    <a href="{{ route('daily-revenues.edit', $rec) }}" title="Edit"
                                       class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('delete daily revenues')
                                    <form method="POST" action="{{ route('daily-revenues.destroy', $rec) }}"
                                          onsubmit="return confirm('Hapus data omset {{ $rec->date->format('d M Y') }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        @else
                            {{-- Hanya ada penjualan harian, belum ada omset manual --}}
                            <td class="px-5 py-3 text-right text-surface-300 text-xs">—</td>
                            <td class="px-5 py-3 text-right text-surface-300 text-xs">—</td>
                            <td class="px-5 py-3 text-right text-surface-300 text-xs">—</td>
                            <td class="px-5 py-3 text-surface-400 text-xs italic">Belum ada input omset manual</td>
                            <td class="px-5 py-3 text-surface-300 text-xs">—</td>
                            <td class="px-5 py-3 text-right">
                                @can('create daily revenues')
                                <a href="{{ route('daily-revenues.create', ['date' => $dateStr]) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 bg-brand-50 border border-brand-200 text-brand-600 text-xs font-medium rounded-lg hover:bg-brand-100 transition">
                                    ➕ Input Omset
                                </a>
                                @endcan
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-surface-400">
                            Belum ada data untuk {{ $months[$month] }} {{ $year }}.
                            @can('create daily revenues')
                            <a href="{{ route('daily-revenues.create') }}" class="text-brand-600 hover:underline ml-1">+ Input sekarang</a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($allDates->isNotEmpty())
                <tfoot class="bg-surface-50 border-t-2 border-surface-200">
                    <tr>
                        <td class="px-5 py-3 text-sm font-semibold text-surface-600">Total {{ $allDates->count() }} hari</td>
                        <td class="px-5 py-3 text-right font-bold text-brand-700">Rp {{ number_format($penjualanHarian->sum('total_omset'), 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-bold text-purple-700">Rp {{ number_format($totalQris, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-bold text-orange-600">Rp {{ number_format($totalTunai, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-bold text-surface-800">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

    </div>
</x-app-layout>

