<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-surface-900 leading-tight">Dashboard</h2>
                <p class="text-sm text-surface-400 mt-0.5">Ringkasan keuangan & aktivitas terbaru</p>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('transactions.index') }}" method="GET"
                      class="flex items-center bg-surface-100 rounded-xl px-3 py-2 flex-1 sm:flex-none sm:w-48">
                    <svg class="w-4 h-4 text-surface-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="search" name="q" placeholder="Cari transaksi..."
                           class="bg-transparent focus:outline-none text-sm text-surface-700 ml-2 w-full placeholder-surface-400" />
                </form>
                @can('create transactions')
                <a href="{{ route('transactions.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 gradient-brand text-white text-sm rounded-xl shadow-sm shadow-brand-200 hover:shadow-md transition-all whitespace-nowrap font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Transaksi Baru
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ===== SUMMARY STATS ROW ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card hover:border-emerald-200 hover:shadow-emerald-100/50">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Total Omset</span>
                </div>
                <p class="text-xl font-bold text-surface-900">Rp {{ number_format($totalOmset, 0, ',', '.') }}</p>
                <p class="text-xs text-surface-400 mt-1">{{ $hariTercatat }} hari tercatat</p>
            </div>
            <div class="stat-card hover:border-purple-200 hover:shadow-purple-100/50">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">QRIS</span>
                </div>
                <p class="text-xl font-bold text-purple-700">Rp {{ number_format($totalQris, 0, ',', '.') }}</p>
                @if($totalOmset > 0)
                <p class="text-xs text-surface-400 mt-1">{{ number_format(($totalQris/$totalOmset)*100,1) }}% dari omset</p>
                @endif
            </div>
            <div class="stat-card hover:border-orange-200 hover:shadow-orange-100/50">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Tunai</span>
                </div>
                <p class="text-xl font-bold text-orange-700">Rp {{ number_format($totalTunai, 0, ',', '.') }}</p>
                @if($totalOmset > 0)
                <p class="text-xs text-surface-400 mt-1">{{ number_format(($totalTunai/$totalOmset)*100,1) }}% dari omset</p>
                @endif
            </div>
            <div class="stat-card hover:border-blue-200 hover:shadow-blue-100/50">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg {{ $saldo >= 0 ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Saldo Bersih</span>
                </div>
                <p class="text-xl font-bold {{ $saldo >= 0 ? 'text-blue-700' : 'text-red-700' }}">Rp {{ number_format($saldo, 0, ',', '.') }}</p>
                <p class="text-xs text-surface-400 mt-1">Keluar: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- ===== OMSET PERIOD CARDS ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Hari Ini --}}
            <div class="rounded-2xl p-5 text-white shadow-lg overflow-hidden transform hover:-translate-y-0.5 transition-all gradient-success">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="text-emerald-100 text-xs font-bold uppercase tracking-wider">Hari Ini</span>
                    </div>
                    <span class="text-emerald-200 text-xs font-medium bg-white/10 px-2 py-1 rounded-lg">{{ now()->format('d M Y') }}</span>
                </div>
                <p class="text-2xl font-extrabold tracking-tight">Rp {{ number_format($omsetHariIni, 0, ',', '.') }}</p>
                <div class="flex gap-2 mt-3">
                    <div class="bg-white/10 rounded-xl px-3 py-2 flex-1 text-center">
                        <p class="text-emerald-200 text-[10px] font-semibold uppercase">QRIS</p>
                        <p class="text-white font-bold text-sm">Rp {{ number_format($qrisHariIni, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl px-3 py-2 flex-1 text-center">
                        <p class="text-emerald-200 text-[10px] font-semibold uppercase">Tunai</p>
                        <p class="text-white font-bold text-sm">Rp {{ number_format($tunaiHariIni, 0, ',', '.') }}</p>
                    </div>
                </div>
                @can('create daily revenues')
                @php $todayRecorded = \App\Models\DailyRevenue::whereDate('date', today())->exists(); @endphp
                <div class="mt-3 pt-3 border-t border-white/20">
                    @if(!$todayRecorded)
                    <a href="{{ route('daily-revenues.create') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-medium text-white/80 hover:text-white transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Belum input — Input sekarang
                    </a>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-xs text-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Sudah tercatat
                    </span>
                    @endif
                </div>
                @endcan
            </div>

            {{-- Bulan Ini --}}
            <div class="rounded-2xl p-5 text-white shadow-lg overflow-hidden transform hover:-translate-y-0.5 transition-all gradient-cool">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-indigo-200 text-xs font-bold uppercase tracking-wider">Bulan Ini</span>
                    </div>
                    <span class="text-indigo-300 text-xs font-medium bg-white/10 px-2 py-1 rounded-lg">{{ now()->format('M Y') }}</span>
                </div>
                <p class="text-2xl font-extrabold tracking-tight">Rp {{ number_format($omsetBulanIni, 0, ',', '.') }}</p>
                <div class="flex gap-2 mt-3">
                    <div class="bg-white/10 rounded-xl px-3 py-2 flex-1 text-center">
                        <p class="text-indigo-200 text-[10px] font-semibold uppercase">QRIS</p>
                        <p class="text-white font-bold text-sm">Rp {{ number_format($qrisBulanIni, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl px-3 py-2 flex-1 text-center">
                        <p class="text-indigo-200 text-[10px] font-semibold uppercase">Tunai</p>
                        <p class="text-white font-bold text-sm">Rp {{ number_format($tunaiBulanIni, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-white/20 flex items-center justify-between">
                    <span class="text-indigo-300 text-xs font-medium">{{ $hariBulanIni }} hari tercatat</span>
                    @if($pctChange !== null)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $selisihOmset >= 0 ? 'text-green-300' : 'text-red-300' }}">
                        @if($selisihOmset >= 0)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        +{{ number_format($pctChange, 1) }}%
                        @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                        {{ number_format($pctChange, 1) }}%
                        @endif
                    </span>
                    @endif
                </div>
            </div>

            {{-- Tahun Ini --}}
            <div class="rounded-2xl p-5 text-white shadow-lg overflow-hidden transform hover:-translate-y-0.5 transition-all gradient-warm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <span class="text-amber-200 text-xs font-bold uppercase tracking-wider">Tahun Ini</span>
                    </div>
                    <span class="text-amber-300 text-xs font-medium bg-white/10 px-2 py-1 rounded-lg">{{ now()->year }}</span>
                </div>
                <p class="text-2xl font-extrabold tracking-tight">Rp {{ number_format($omsetTahunIni, 0, ',', '.') }}</p>
                <div class="flex gap-2 mt-3">
                    <div class="bg-white/10 rounded-xl px-3 py-2 flex-1 text-center">
                        <p class="text-amber-200 text-[10px] font-semibold uppercase">QRIS</p>
                        <p class="text-white font-bold text-sm">Rp {{ number_format($qrisTahunIni, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl px-3 py-2 flex-1 text-center">
                        <p class="text-amber-200 text-[10px] font-semibold uppercase">Tunai</p>
                        <p class="text-white font-bold text-sm">Rp {{ number_format($tunaiTahunIni, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-white/20 flex items-center justify-between">
                    <span class="text-amber-200 text-xs font-medium">{{ $hariTahunIni }} hari tercatat</span>
                    @if($pctChangeTahun !== null)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $selisihTahun >= 0 ? 'text-green-300' : 'text-red-300' }}">
                        @if($selisihTahun >= 0)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        +{{ number_format($pctChangeTahun, 1) }}%
                        @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                        {{ number_format($pctChangeTahun, 1) }}%
                        @endif
                    </span>
                    @endif
                </div>
            </div>

        </div>

        {{-- ===== DAILY TREND CHART ===== --}}
        <div class="stat-card p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-surface-900">Tren Omset Harian</h3>
                        <p class="text-xs text-surface-400">14 hari terakhir</p>
                    </div>
                </div>
                @can('view daily revenues')
                <a href="{{ route('daily-revenues.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold flex-shrink-0">Lihat Semua →</a>
                @endcan
            </div>
            <div class="relative h-44 sm:h-52">
                <canvas id="chartDailyOmset"></canvas>
            </div>
        </div>

        {{-- ===== MIDDLE ROW ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            {{-- Recent Transactions --}}
            <div class="stat-card overflow-hidden flex flex-col p-0">
                <div class="px-5 py-4 border-b border-surface-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-surface-900">Transaksi Terbaru</h3>
                    </div>
                    @can('view transactions')
                    <a href="{{ route('transactions.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">Semua →</a>
                    @endcan
                </div>
                <div class="divide-y divide-surface-100 flex-1">
                    @forelse($recentTransactions as $tx)
                    @php $cat = $tx->transactionType->category; @endphp
                    <div class="px-5 py-3.5 flex items-center justify-between hover:bg-surface-50 transition-colors">
                        <div class="min-w-0 mr-3">
                            <p class="text-sm font-semibold text-surface-900 truncate">{{ $tx->transactionType->name }}</p>
                            <p class="text-xs text-surface-400 mt-0.5">{{ $tx->transaction_date->format('d M Y') }}</p>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <span class="text-sm font-bold {{ $cat === 'pemasukan' ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $cat === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </span>
                            <p class="text-xs mt-1">
                                <span class="inline-block px-2 py-0.5 rounded-lg text-[10px] font-semibold
                                    {{ $tx->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($tx->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst($tx->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-12 text-center">
                        <svg class="w-8 h-8 text-surface-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-surface-400 text-sm">Belum ada transaksi</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Omset --}}
            <div class="stat-card overflow-hidden flex flex-col p-0">
                <div class="px-5 py-4 border-b border-surface-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-surface-900">Omset Terbaru</h3>
                    </div>
                    @can('view daily revenues')
                    <a href="{{ route('daily-revenues.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">Semua →</a>
                    @endcan
                </div>
                <div class="divide-y divide-surface-100 hidden sm:block">
                    @forelse($recentOmset as $rec)
                    <div class="px-5 py-3.5 flex items-center justify-between hover:bg-surface-50 transition-colors">
                        <span class="text-sm font-semibold text-surface-900">{{ $rec->date->format('d M Y') }}</span>
                        <span class="text-sm font-bold text-surface-900">Rp {{ number_format($rec->total, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <div class="px-5 py-12 text-center">
                        <p class="text-surface-400 text-sm">Belum ada data</p>
                    </div>
                    @endforelse
                </div>
                <div class="divide-y divide-surface-100 block sm:hidden">
                    @forelse($recentOmset as $rec)
                    <div class="px-4 py-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-surface-900">{{ $rec->date->format('d M Y') }}</span>
                            <span class="text-sm font-bold text-surface-900">Rp {{ number_format($rec->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex gap-4 mt-1">
                            <span class="text-xs text-purple-600">QRIS: Rp {{ number_format($rec->qris_amount, 0, ',', '.') }}</span>
                            <span class="text-xs text-orange-600">Tunai: Rp {{ number_format($rec->tunai_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="px-4 py-12 text-center">
                        <p class="text-surface-400 text-sm">Belum ada data</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Pengeluaran per Jenis --}}
            @if($pengeluaranPerJenis->count() > 0)
            <div class="stat-card md:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-surface-900">Pengeluaran per Jenis</h3>
                </div>
                <div class="space-y-4">
                    @foreach($pengeluaranPerJenis->take(5) as $item)
                    @php
                        $maxVal = $pengeluaranPerJenis->first()['total'];
                        $pct = $maxVal > 0 ? ($item['total'] / $maxVal) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-center text-xs mb-1.5">
                            <span class="text-surface-600 font-medium truncate mr-2">{{ $item['name'] }}</span>
                            <span class="text-surface-900 font-bold whitespace-nowrap">Rp {{ number_format($item['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="h-2 bg-surface-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-red-400 to-red-500 transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- ===== BOTTOM ROW: Charts ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <div class="lg:col-span-2 stat-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-surface-900">Omset 6 Bulan Terakhir</h3>
                            <p class="text-xs text-surface-400">QRIS + Tunai</p>
                        </div>
                    </div>
                </div>
                <div class="relative h-48 sm:h-56">
                    <canvas id="chartOmset"></canvas>
                </div>
            </div>

            <div class="stat-card p-5">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-surface-900">Metode Pembayaran</h3>
                </div>
                <p class="text-xs text-surface-400 mb-4">Semua waktu</p>
                <div class="relative h-44">
                    <canvas id="chartPayment"></canvas>
                </div>
                <div class="flex justify-center gap-6 mt-4 text-xs text-surface-500">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block"></span>QRIS
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-400 inline-block"></span>Tunai
                    </span>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const daily      = @json($dailyOmsetData);
        const monthly    = @json($monthlyData);
        const labels     = monthly.map(d => d.label);
        const qrisData   = monthly.map(d => d.qris);
        const tunaiData  = monthly.map(d => d.tunai);

        const dailyLabels = daily.map(d => d.label);
        const dailyQris   = daily.map(d => d.qris);
        const dailyTunai  = daily.map(d => d.tunai);
        const dailyTotal  = daily.map(d => d.qris + d.tunai);

        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = 'rgba(0,0,0,0.04)';
        const tickColor = '#78716c';

        const formatRp = v => {
            if (v >= 1000000) return 'Rp ' + (v/1000000).toFixed(1) + 'jt';
            if (v >= 1000)    return 'Rp ' + (v/1000).toFixed(0) + 'rb';
            return 'Rp ' + v;
        };

        new Chart(document.getElementById('chartDailyOmset'), {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [
                    {
                        label: 'Total Omset',
                        data: dailyTotal,
                        borderColor: '#d97706',
                        backgroundColor: 'rgba(217,119,6,0.08)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#d97706',
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'QRIS',
                        data: dailyQris,
                        borderColor: 'rgba(139,92,246,0.65)',
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        borderDash: [4, 3],
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        fill: false,
                        tension: 0.4,
                    },
                    {
                        label: 'Tunai',
                        data: dailyTunai,
                        borderColor: 'rgba(251,146,60,0.65)',
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        borderDash: [4, 3],
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        fill: false,
                        tension: 0.4,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 10, font: { size: 11 }, color: tickColor }
                    },
                    tooltip: {
                        callbacks: { label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID') }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: tickColor, maxTicksLimit: 7 }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: { font: { size: 10 }, color: tickColor, callback: formatRp }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartOmset'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'QRIS',  data: qrisData,  backgroundColor: 'rgba(139,92,246,0.8)',  borderRadius: 4 },
                    { label: 'Tunai', data: tunaiData, backgroundColor: 'rgba(251,146,60,0.8)',   borderRadius: 4 },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 10, font: { size: 11 }, color: tickColor } },
                    tooltip: {
                        callbacks: { label: ctx => ' ' + ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID') }
                    }
                },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { color: tickColor, font: { size: 10 } } },
                    y: {
                        stacked: true,
                        grid: { color: gridColor },
                        ticks: { color: tickColor, font: { size: 10 }, callback: formatRp }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartPayment'), {
            type: 'doughnut',
            data: {
                labels: ['QRIS', 'Tunai'],
                datasets: [{
                    data: [{{ $totalQris }}, {{ $totalTunai }}],
                    backgroundColor: ['rgba(139,92,246,0.85)', 'rgba(251,146,60,0.85)'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.label + ': Rp ' + ctx.raw.toLocaleString('id-ID')
                        }
                    }
                },
                cutout: '65%',
            }
        });
    </script>
</x-app-layout>
