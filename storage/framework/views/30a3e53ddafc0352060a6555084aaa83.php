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
        <div class="flex items-center justify-between">
            <div>
                <h2 class="page-title">Omset Harian</h2>
                <p class="text-xs text-surface-500 mt-0.5">Input dan pantau omset QRIS & tunai per hari</p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create daily revenues')): ?>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('daily-revenues.upload-form')); ?>"
                   class="btn-secondary">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload Excel
                </a>
                <a href="<?php echo e(route('daily-revenues.create')); ?>"
                   class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Input Omset
                </a>
            </div>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-4">

        
        <?php if(session('success')): ?>
            <div class="alert-success text-sm"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('info')): ?>
            <div class="alert-info"><?php echo e(session('info')); ?></div>
        <?php endif; ?>
        <?php if(session('import_errors') && count(session('import_errors')) > 0): ?>
            <div class="alert-warning">
                <p class="font-medium text-sm mb-1">⚠️ Beberapa baris dilewati:</p>
                <ul class="text-xs list-disc list-inside space-y-0.5">
                    <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <div class="card px-5 py-4">
            <form method="GET" action="<?php echo e(route('daily-revenues.index')); ?>" id="omset-filter-form"
                  class="flex flex-wrap items-center gap-3">

                <span class="text-sm font-semibold text-surface-600 mr-1">🗓 Filter Periode:</span>

                <select name="month" onchange="document.getElementById('omset-filter-form').submit()"
                        class="border-surface-300 rounded-lg text-sm py-1.5 pr-8">
                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($num); ?>" <?php if($num == $month): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="year" onchange="document.getElementById('omset-filter-form').submit()"
                        class="border-surface-300 rounded-lg text-sm py-1.5 pr-8">
                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($y); ?>" <?php if($y == $year): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                
                <?php
                    $nowM = now()->month; $nowY = now()->year;
                    $prevM = now()->subMonth()->month; $prevY = now()->subMonth()->year;
                    $prev2M = now()->subMonths(2)->month; $prev2Y = now()->subMonths(2)->year;
                ?>
                <a href="<?php echo e(route('daily-revenues.index', ['month' => $nowM, 'year' => $nowY])); ?>"
                   class="px-3 py-1 text-xs rounded-full border transition <?php echo e(($month == $nowM && $year == $nowY) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400'); ?>">
                    Bulan Ini
                </a>
                <a href="<?php echo e(route('daily-revenues.index', ['month' => $prevM, 'year' => $prevY])); ?>"
                   class="px-3 py-1 text-xs rounded-full border transition <?php echo e(($month == $prevM && $year == $prevY) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400'); ?>">
                    Bulan Lalu
                </a>
                <a href="<?php echo e(route('daily-revenues.index', ['month' => $prev2M, 'year' => $prev2Y])); ?>"
                   class="px-3 py-1 text-xs rounded-full border transition hidden sm:inline-block <?php echo e(($month == $prev2M && $year == $prev2Y) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400'); ?>">
                    <?php echo e($months[$prev2M]); ?> <?php echo e($prev2Y); ?>

                </a>
                <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($y != $nowY): ?>
                    <a href="<?php echo e(route('daily-revenues.index', ['month' => 1, 'year' => $y, '_range' => 'year'])); ?>"
                       class="px-3 py-1 text-xs rounded-full border transition hidden md:inline-block <?php echo e(($year == $y && $month == 1) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400'); ?>">
                        <?php echo e($y); ?>

                    </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($records->count() > 0): ?>
                <a href="<?php echo e(route('laporan.export-omset', ['month' => $month, 'year' => $year])); ?>"
                   class="ml-auto btn-primary">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export <?php echo e($months[$month]); ?> <?php echo e($year); ?>

                </a>
                <?php endif; ?>
            </form>
        </div>

        
        <div class="bg-gradient-to-r from-blue-50 to-brand-50 border border-brand-200 rounded-xl p-5">
            <h3 class="text-xs font-bold text-brand-700 uppercase tracking-wide mb-3">📈 Total Omset — <?php echo e($periodLabel); ?></h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-brand-700">Rp <?php echo e(number_format($allTimeOmset, 0, ',', '.')); ?></p>
                    <p class="text-xs text-brand-500 mt-1">Total Omset</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-purple-700">Rp <?php echo e(number_format($allTimeQris, 0, ',', '.')); ?></p>
                    <p class="text-xs text-purple-500 mt-1">Total QRIS</p>
                    <?php if($allTimeOmset > 0): ?>
                    <div class="mt-2 w-full max-w-xs mx-auto bg-purple-100 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: <?php echo e(round($allTimeQris / $allTimeOmset * 100)); ?>%"></div>
                    </div>
                    <p class="text-xs text-purple-400 mt-1"><?php echo e(round($allTimeQris / $allTimeOmset * 100)); ?>%</p>
                    <?php endif; ?>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-orange-600">Rp <?php echo e(number_format($allTimeTunai, 0, ',', '.')); ?></p>
                    <p class="text-xs text-orange-500 mt-1">Total Tunai</p>
                    <?php if($allTimeOmset > 0): ?>
                    <div class="mt-2 w-full max-w-xs mx-auto bg-orange-100 rounded-full h-2">
                        <div class="bg-orange-400 h-2 rounded-full" style="width: <?php echo e(round($allTimeTunai / $allTimeOmset * 100)); ?>%"></div>
                    </div>
                    <p class="text-xs text-orange-400 mt-1"><?php echo e(round($allTimeTunai / $allTimeOmset * 100)); ?>%</p>
                    <?php endif; ?>
                </div>
                <div class="text-center border-t-2 lg:border-t-0 lg:border-l-2 border-brand-100 pt-3 lg:pt-0 lg:pl-4 col-span-2 lg:col-span-1">
                    <p class="text-2xl font-extrabold text-green-600">Rp <?php echo e(number_format($avgOmset, 0, ',', '.')); ?></p>
                    <p class="text-xs text-green-500 mt-1">Rata-rata/Hari</p>
                    <?php if($recordCount > 0): ?>
                    <p class="text-xs text-surface-400 mt-1">dari <?php echo e($recordCount); ?> hari data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <?php if($records->count() > 0): ?>
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-surface-700 mb-4">Grafik Omset Harian — <?php echo e($months[$month]); ?> <?php echo e($year); ?></h3>
            <div style="height: 280px;">
                <canvas id="chartOmsetHarian"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const labels = <?php echo e(Illuminate\Support\Js::from($chartLabels)); ?>;
            const qris   = <?php echo e(Illuminate\Support\Js::from($chartQris)); ?>;
            const tunai  = <?php echo e(Illuminate\Support\Js::from($chartTunai)); ?>;
            const total  = <?php echo e(Illuminate\Support\Js::from($chartTotal)); ?>;

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
        <?php endif; ?>

        
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
                    <?php $__empty_1 = true; $__currentLoopData = $allDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dateStr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $rec = $records->first(fn($r) => $r->date->toDateString() === $dateStr);
                        $pj  = $penjualanHarian->get($dateStr);
                        $carbonDate = \Carbon\Carbon::parse($dateStr);
                    ?>
                    <tr class="hover:bg-surface-50 <?php echo e($pj && !$rec ? 'bg-amber-50' : ''); ?>">
                        <td class="px-5 py-3 font-medium text-surface-800">
                            <?php echo e($carbonDate->isoFormat('ddd, D MMM Y')); ?>

                            <?php if($carbonDate->isToday()): ?>
                                <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-brand-100 text-brand-700">Hari ini</span>
                            <?php endif; ?>
                        </td>

                        
                        <td class="px-5 py-3 text-right">
                            <?php if($pj): ?>
                                <div class="flex flex-col items-end gap-0.5">
                                    <span class="font-semibold text-brand-700">Rp <?php echo e(number_format($pj->total_omset, 0, ',', '.')); ?></span>
                                    <span class="text-xs text-surface-400"><?php echo e(number_format($pj->total_qty)); ?> pcs</span>
                                </div>
                                <?php if(!$rec): ?>
                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">
                                        ⚠️ Belum input omset manual
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-surface-300 text-xs">—</span>
                            <?php endif; ?>
                        </td>

                        
                        <?php if($rec): ?>
                            <td class="px-5 py-3 text-right text-purple-700 font-medium">Rp <?php echo e(number_format($rec->qris_amount, 0, ',', '.')); ?></td>
                            <td class="px-5 py-3 text-right text-orange-600 font-medium">Rp <?php echo e(number_format($rec->tunai_amount, 0, ',', '.')); ?></td>
                            <td class="px-5 py-3 text-right font-bold text-surface-800">
                                Rp <?php echo e(number_format($rec->total, 0, ',', '.')); ?>

                                <?php if($pj): ?>
                                <?php $selisih = $rec->total - $pj->total_omset; ?>
                                <?php if(abs($selisih) > 0): ?>
                                    <div class="text-xs font-normal <?php echo e($selisih > 0 ? 'text-blue-500' : 'text-red-500'); ?> mt-0.5">
                                        <?php echo e($selisih > 0 ? '+' : ''); ?>Rp <?php echo e(number_format($selisih, 0, ',', '.')); ?> vs penjualan
                                    </div>
                                <?php else: ?>
                                    <div class="text-xs text-green-500 font-normal mt-0.5">✓ Cocok</div>
                                <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 text-surface-500 max-w-xs truncate"><?php echo e($rec->notes ?? '-'); ?></td>
                            <td class="px-5 py-3 text-surface-500"><?php echo e($rec->creator->name ?? '-'); ?></td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit daily revenues')): ?>
                                    <a href="<?php echo e(route('daily-revenues.edit', $rec)); ?>" title="Edit"
                                       class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete daily revenues')): ?>
                                    <form method="POST" action="<?php echo e(route('daily-revenues.destroy', $rec)); ?>"
                                          onsubmit="return confirm('Hapus data omset <?php echo e($rec->date->format('d M Y')); ?>?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" title="Hapus"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php else: ?>
                            
                            <td class="px-5 py-3 text-right text-surface-300 text-xs">—</td>
                            <td class="px-5 py-3 text-right text-surface-300 text-xs">—</td>
                            <td class="px-5 py-3 text-right text-surface-300 text-xs">—</td>
                            <td class="px-5 py-3 text-surface-400 text-xs italic">Belum ada input omset manual</td>
                            <td class="px-5 py-3 text-surface-300 text-xs">—</td>
                            <td class="px-5 py-3 text-right">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create daily revenues')): ?>
                                <a href="<?php echo e(route('daily-revenues.create', ['date' => $dateStr])); ?>"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 bg-brand-50 border border-brand-200 text-brand-600 text-xs font-medium rounded-lg hover:bg-brand-100 transition">
                                    ➕ Input Omset
                                </a>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-surface-400">
                            Belum ada data untuk <?php echo e($months[$month]); ?> <?php echo e($year); ?>.
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create daily revenues')): ?>
                            <a href="<?php echo e(route('daily-revenues.create')); ?>" class="text-brand-600 hover:underline ml-1">+ Input sekarang</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <?php if($allDates->isNotEmpty()): ?>
                <tfoot class="bg-surface-50 border-t-2 border-surface-200">
                    <tr>
                        <td class="px-5 py-3 text-sm font-semibold text-surface-600">Total <?php echo e($allDates->count()); ?> hari</td>
                        <td class="px-5 py-3 text-right font-bold text-brand-700">Rp <?php echo e(number_format($penjualanHarian->sum('total_omset'), 0, ',', '.')); ?></td>
                        <td class="px-5 py-3 text-right font-bold text-purple-700">Rp <?php echo e(number_format($totalQris, 0, ',', '.')); ?></td>
                        <td class="px-5 py-3 text-right font-bold text-orange-600">Rp <?php echo e(number_format($totalTunai, 0, ',', '.')); ?></td>
                        <td class="px-5 py-3 text-right font-bold text-surface-800">Rp <?php echo e(number_format($totalOmset, 0, ',', '.')); ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>

    </div>
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

<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/daily-revenues/index.blade.php ENDPATH**/ ?>