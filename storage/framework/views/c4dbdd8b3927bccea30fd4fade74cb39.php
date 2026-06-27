<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="page-title">Dashboard</h2>
                <p class="page-desc">Ringkasan keuangan & aktivitas terbaru</p>
            </div>
            <div class="flex items-center gap-2">
                <form action="<?php echo e(route('transactions.index')); ?>" method="GET"
                      class="flex items-center bg-surface-100 rounded-xl px-3 py-2 flex-1 sm:flex-none sm:w-48">
                    <svg class="w-4 h-4 text-surface-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="search" name="q" placeholder="Cari transaksi..."
                           class="bg-transparent focus:outline-none text-sm text-surface-700 ml-2 w-full placeholder-surface-400" />
                </form>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create transactions')): ?>
                <a href="<?php echo e(route('transactions.create')); ?>"
                   class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Transaksi Baru
                </a>
                <?php endif; ?>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card hover:border-emerald-200 hover:shadow-emerald-100/50">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Total Omset</span>
                </div>
                <p class="page-title">Rp <?php echo e(number_format($totalOmset, 0, ',', '.')); ?></p>
                <p class="text-xs text-surface-400 mt-1"><?php echo e($hariTercatat); ?> hari tercatat</p>
            </div>
            <div class="stat-card hover:border-purple-200 hover:shadow-purple-100/50">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">QRIS</span>
                </div>
                <p class="text-xl font-bold text-purple-700">Rp <?php echo e(number_format($totalQris, 0, ',', '.')); ?></p>
                <?php if($totalOmset > 0): ?>
                <p class="text-xs text-surface-400 mt-1"><?php echo e(number_format(($totalQris/$totalOmset)*100,1)); ?>% dari omset</p>
                <?php endif; ?>
            </div>
            <div class="stat-card hover:border-orange-200 hover:shadow-orange-100/50">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Tunai</span>
                </div>
                <p class="text-xl font-bold text-orange-700">Rp <?php echo e(number_format($totalTunai, 0, ',', '.')); ?></p>
                <?php if($totalOmset > 0): ?>
                <p class="text-xs text-surface-400 mt-1"><?php echo e(number_format(($totalTunai/$totalOmset)*100,1)); ?>% dari omset</p>
                <?php endif; ?>
            </div>
            <div class="stat-card hover:border-blue-200 hover:shadow-blue-100/50">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg <?php echo e($saldo >= 0 ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-600'); ?> flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Saldo Bersih</span>
                </div>
                <p class="text-xl font-bold <?php echo e($saldo >= 0 ? 'text-blue-700' : 'text-red-700'); ?>">Rp <?php echo e(number_format($saldo, 0, ',', '.')); ?></p>
                <p class="text-xs text-surface-400 mt-1">Keluar: Rp <?php echo e(number_format($totalPengeluaran, 0, ',', '.')); ?></p>
            </div>
        </div>

        
        <div class="stat-card p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-surface-900">Tren Omset Harian</h3>
                        <p class="text-xs text-surface-400">14 hari terakhir</p>
                    </div>
                </div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view daily revenues')): ?>
                <a href="<?php echo e(route('daily-revenues.index')); ?>" class="text-xs text-brand-600 hover:text-brand-700 font-semibold flex-shrink-0">Lihat Semua →</a>
                <?php endif; ?>
            </div>
            <div class="relative h-44 sm:h-52">
                <canvas id="chartDailyOmset"></canvas>
            </div>
        </div>

        
        <div class="stat-card p-5">
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
                <div class="relative h-44 sm:h-52">
                    <canvas id="chartOmset"></canvas>
                </div>
            </div>

        
        <?php
            $monthStart = now()->startOfMonth()->toDateString();
            $monthEnd = now()->endOfMonth()->toDateString();

            $topProducts = \App\Models\DailySale::selectRaw('hpp_product_id, product_name, SUM(quantity_sold) as total_qty')
                ->whereBetween('sale_date', [$monthStart, $monthEnd])
                ->groupBy('hpp_product_id', 'product_name')
                ->orderByDesc('total_qty')
                ->limit(50)
                ->get();

            $topCoffe = $topProducts->filter(fn($p) => \App\Models\HppProduct::where('id', $p->hpp_product_id)->value('category') === 'Coffe')->take(7);
            $topMakanan = $topProducts->filter(fn($p) => \App\Models\HppProduct::where('id', $p->hpp_product_id)->value('category') === 'Makanan')->take(7);
            $topSnack = $topProducts->filter(fn($p) => \App\Models\HppProduct::where('id', $p->hpp_product_id)->value('category') === 'Snack')->take(7);
        ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <div class="stat-card p-0">
                <div class="px-5 py-4 border-b border-surface-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-surface-900">Coffe Terlaris</h3>
                    </div>
                </div>
                <div class="divide-y divide-surface-100">
                    <?php $__empty_1 = true; $__currentLoopData = $topCoffe; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-5 py-2.5 flex items-center justify-between hover:bg-surface-50 transition-colors">
                        <span class="text-sm font-medium text-surface-800"><?php echo e($item->product_name); ?></span>
                        <span class="text-xs font-semibold text-brand-600"><?php echo e(number_format($item->total_qty)); ?> terjual</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-5 py-8 text-center text-surface-400 text-sm">Belum ada penjualan coffee bulan ini</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="stat-card p-0">
                <div class="px-5 py-4 border-b border-surface-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 019.5 3H8"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-surface-900">Makanan Terlaris</h3>
                    </div>
                </div>
                <div class="divide-y divide-surface-100">
                    <?php $__empty_1 = true; $__currentLoopData = $topMakanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-5 py-2.5 flex items-center justify-between hover:bg-surface-50 transition-colors">
                        <span class="text-sm font-medium text-surface-800"><?php echo e($item->product_name); ?></span>
                        <span class="text-xs font-semibold text-brand-600"><?php echo e(number_format($item->total_qty)); ?> terjual</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-5 py-8 text-center text-surface-400 text-sm">Belum ada penjualan makanan bulan ini</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="stat-card p-0">
                <div class="px-5 py-4 border-b border-surface-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-red-100 text-red-500 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h-2m0 0H8m4 0V6m0 4v4m6-4a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-surface-900">Snack Terlaris</h3>
                    </div>
                </div>
                <div class="divide-y divide-surface-100">
                    <?php $__empty_1 = true; $__currentLoopData = $topSnack; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-5 py-2.5 flex items-center justify-between hover:bg-surface-50 transition-colors">
                        <span class="text-sm font-medium text-surface-800"><?php echo e($item->product_name); ?></span>
                        <span class="text-xs font-semibold text-brand-600"><?php echo e(number_format($item->total_qty)); ?> terjual</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-5 py-8 text-center text-surface-400 text-sm">Belum ada penjualan snack bulan ini</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const daily      = <?php echo json_encode($dailyOmsetData, 15, 512) ?>;
        const monthly    = <?php echo json_encode($monthlyData, 15, 512) ?>;
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

    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/dashboard.blade.php ENDPATH**/ ?>