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
                <h2 class="page-title">👥 Karyawan</h2>
                <p class="text-xs text-surface-500 mt-0.5">Data dan rekap gaji seluruh karyawan</p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create employee')): ?>
            <a href="<?php echo e(route('employees.create')); ?>" class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition">
                + Tambah Karyawan
            </a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5">

        
        <div class="card px-5 py-4">
            <form method="GET" action="<?php echo e(route('employees.index')); ?>" id="top-filter-form"
                  class="flex flex-wrap items-center gap-3">
                
                <input type="hidden" name="rfm" value="<?php echo e($rfm); ?>">
                <input type="hidden" name="rfy" value="<?php echo e($rfy); ?>">
                <input type="hidden" name="rtm" value="<?php echo e($rtm); ?>">
                <input type="hidden" name="rty" value="<?php echo e($rty); ?>">

                <span class="text-sm font-semibold text-surface-600 mr-1">🗓 Filter Periode:</span>

                <select name="gaji_month" onchange="document.getElementById('top-filter-form').submit()"
                        class="border-surface-300 rounded-lg text-sm py-1.5 pr-8">
                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($n); ?>" <?php if($n == $filterMonth): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="gaji_year" onchange="document.getElementById('top-filter-form').submit()"
                        class="border-surface-300 rounded-lg text-sm py-1.5 pr-8">
                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($y); ?>" <?php if($y == $filterYear): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                
                <?php
                    $thisM = now()->month; $thisY = now()->year;
                    $prevM = now()->subMonth()->month; $prevY = now()->subMonth()->year;
                ?>
                <a href="<?php echo e(route('employees.index', ['gaji_month' => $thisM, 'gaji_year' => $thisY, 'rfm' => $rfm, 'rfy' => $rfy, 'rtm' => $rtm, 'rty' => $rty])); ?>"
                   class="px-3 py-1 text-xs rounded-full border transition <?php echo e(($filterMonth == $thisM && $filterYear == $thisY) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400'); ?>">
                    Bulan Ini
                </a>
                <a href="<?php echo e(route('employees.index', ['gaji_month' => $prevM, 'gaji_year' => $prevY, 'rfm' => $rfm, 'rfy' => $rfy, 'rtm' => $rtm, 'rty' => $rty])); ?>"
                   class="px-3 py-1 text-xs rounded-full border transition <?php echo e(($filterMonth == $prevM && $filterYear == $prevY) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400'); ?>">
                    Bulan Lalu
                </a>
                <?php $__currentLoopData = $periods->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $isActive = ($p->period_month == $filterMonth && $p->period_year == $filterYear); ?>
                    <?php if(!($p->period_month == $thisM && $p->period_year == $thisY) && !($p->period_month == $prevM && $p->period_year == $prevY)): ?>
                    <a href="<?php echo e(route('employees.index', ['gaji_month' => $p->period_month, 'gaji_year' => $p->period_year, 'rfm' => $rfm, 'rfy' => $rfy, 'rtm' => $rtm, 'rty' => $rty])); ?>"
                       class="px-3 py-1 text-xs rounded-full border transition hidden sm:inline-block <?php echo e($isActive ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400'); ?>">
                        <?php echo e($months[$p->period_month]); ?> <?php echo e($p->period_year); ?>

                    </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if($salaryRecords->count() > 0): ?>
                <a href="<?php echo e(route('laporan.export-gaji', ['month' => $filterMonth, 'year' => $filterYear])); ?>"
                   class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export <?php echo e($gajiPeriodLabel); ?>

                </a>
                <?php endif; ?>
            </form>
        </div>

        
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="stat-card flex items-center gap-3 col-span-1">
                <div class="w-9 h-9 bg-brand-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">👥</div>
                <div>
                    <p class="text-xs text-surface-500">Total</p>
                    <p class="text-xl font-extrabold text-surface-800"><?php echo e($employees->count()); ?></p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-3 col-span-1">
                <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">✅</div>
                <div>
                    <p class="text-xs text-surface-500">Aktif</p>
                    <p class="text-xl font-extrabold text-green-700"><?php echo e($totalAktif); ?></p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-3 col-span-2 sm:col-span-1">
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">💸</div>
                <div class="min-w-0">
                    <p class="text-xs text-surface-500 truncate">Total Gaji <span class="font-medium text-purple-600"><?php echo e($gajiPeriodLabel); ?></span></p>
                    <p class="text-base font-extrabold text-purple-700">Rp <?php echo e(number_format($totalGajiMonth, 0, ',', '.')); ?></p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-3 col-span-1">
                <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">✓</div>
                <div class="min-w-0">
                    <p class="text-xs text-surface-500">Dibayar <span class="text-emerald-600 font-medium"><?php echo e($gajiPeriodLabel); ?></span></p>
                    <p class="text-base font-extrabold text-emerald-700">Rp <?php echo e(number_format($totalGajiDibayar, 0, ',', '.')); ?></p>
                    <p class="text-xs text-surface-400"><?php echo e($countDibayar); ?> org</p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-3 col-span-1">
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">⏳</div>
                <div class="min-w-0">
                    <p class="text-xs text-surface-500">Belum Bayar <span class="text-amber-600 font-medium"><?php echo e($gajiPeriodLabel); ?></span></p>
                    <p class="text-base font-extrabold text-amber-700">Rp <?php echo e(number_format($totalGajiBelumBayar, 0, ',', '.')); ?></p>
                    <p class="text-xs text-surface-400"><?php echo e($countBelumBayar); ?> org</p>
                </div>
            </div>
        </div>

        
        <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl p-5">
            <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wide mb-3">💰 Total Gaji Dibayar (Semua Periode)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-emerald-700">Rp <?php echo e(number_format($allTimeDibayar, 0, ',', '.')); ?></p>
                    <p class="text-xs text-emerald-600 mt-1">✓ Total Sudah Dibayar</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-amber-600">Rp <?php echo e(number_format($allTimeBelumBayar, 0, ',', '.')); ?></p>
                    <p class="text-xs text-amber-500 mt-1">⏳ Masih Belum Dibayar</p>
                </div>
                <div class="text-center">
                    <?php $pctDibayar = $allTimeTotalGaji > 0 ? round($allTimeDibayar / $allTimeTotalGaji * 100) : 0; ?>
                    <p class="text-2xl font-extrabold text-surface-800"><?php echo e($pctDibayar); ?>%</p>
                    <p class="text-xs text-surface-500 mt-1">Rasio Pembayaran</p>
                    <div class="mt-2 w-full max-w-xs mx-auto bg-surface-200 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: <?php echo e($pctDibayar); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        
        <div class="card">
            <div class="px-5 py-3.5 border-b flex items-center justify-between">
                <h3 class="text-sm font-bold text-surface-700">Daftar Karyawan</h3>
            </div>
            <?php if($employees->isEmpty()): ?>
            <div class="py-16 text-center text-surface-400">
                <p class="text-4xl mb-3">👥</p>
                <p class="text-sm">Belum ada data karyawan.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="table-th">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-surface-600">Nama</th>
                            <th class="px-4 py-3 text-left font-semibold text-surface-600">Jabatan</th>
                            <th class="px-4 py-3 text-left font-semibold text-surface-600 hidden md:table-cell">No HP</th>
                            <th class="px-4 py-3 text-right font-semibold text-surface-600 hidden md:table-cell">Gaji Pokok</th>
                            <th class="px-4 py-3 text-center font-semibold text-surface-600">Status</th>
                            <th class="px-4 py-3 text-center font-semibold text-surface-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-surface-50 transition">
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('employees.show', $emp)); ?>" class="font-medium text-brand-700 hover:underline"><?php echo e($emp->name); ?></a>
                                <?php if($emp->department): ?>
                                <span class="block text-xs text-surface-400"><?php echo e($emp->department); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-surface-700"><?php echo e($emp->position); ?></td>
                            <td class="px-4 py-3 text-surface-500 hidden md:table-cell"><?php echo e($emp->phone ?? '—'); ?></td>
                            <td class="px-4 py-3 text-right text-surface-700 font-medium hidden md:table-cell">
                                Rp <?php echo e(number_format($emp->base_salary, 0, ',', '.')); ?>

                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if($emp->status === 'aktif'): ?>
                                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Aktif</span>
                                <?php else: ?>
                                <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('employees.show', $emp)); ?>" class="text-xs text-brand-600 hover:underline">Detail</a>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit employee')): ?>
                                    <a href="<?php echo e(route('employees.edit', $emp)); ?>" class="text-xs text-yellow-600 hover:underline">Edit</a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete employee')): ?>
                                    <form method="POST" action="<?php echo e(route('employees.destroy', $emp)); ?>" onsubmit="return confirm('Hapus karyawan ini?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="card">
            <div class="px-5 py-4 border-b flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-surface-700">📊 Rekap Kumulatif Gaji</h3>
                    <p class="text-xs text-surface-400 mt-0.5">Total gaji termasuk bulan-bulan sebelumnya</p>
                </div>
                <form method="GET" action="<?php echo e(route('employees.index')); ?>" class="flex flex-wrap items-center gap-2" id="range-form">
                    
                    <input type="hidden" name="gaji_month" value="<?php echo e($filterMonth); ?>">
                    <input type="hidden" name="gaji_year" value="<?php echo e($filterYear); ?>">

                    <span class="text-xs text-surface-500 font-medium">Dari</span>
                    <select name="rfm" onchange="document.getElementById('range-form').submit()"
                            class="border-surface-300 rounded-lg text-sm py-1.5">
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($n); ?>" <?php if($n == $rfm): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select name="rfy" onchange="document.getElementById('range-form').submit()"
                            class="border-surface-300 rounded-lg text-sm py-1.5">
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($y); ?>" <?php if($y == $rfy): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <span class="text-xs text-surface-500 font-medium">Sampai</span>
                    <select name="rtm" onchange="document.getElementById('range-form').submit()"
                            class="border-surface-300 rounded-lg text-sm py-1.5">
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($n); ?>" <?php if($n == $rtm): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select name="rty" onchange="document.getElementById('range-form').submit()"
                            class="border-surface-300 rounded-lg text-sm py-1.5">
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($y); ?>" <?php if($y == $rty): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <?php if($rangeTotalAll > 0): ?>
                    <a href="<?php echo e(route('laporan.export-gaji', ['month' => '', 'year' => $rfy])); ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export
                    </a>
                    <?php endif; ?>
                </form>
            </div>

            
            <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-surface-50 rounded-xl p-4 text-center border">
                    <p class="text-xs text-surface-500 mb-1">Total Gaji</p>
                    <p class="text-xl font-extrabold text-surface-800">Rp <?php echo e(number_format($rangeTotalAll, 0, ',', '.')); ?></p>
                    <?php if($rangeChartLabels): ?>
                    <p class="text-xs text-surface-400 mt-1"><?php echo e(count($rangeChartLabels)); ?> periode</p>
                    <?php endif; ?>
                </div>
                <div class="bg-emerald-50 rounded-xl p-4 text-center border border-emerald-200">
                    <p class="text-xs text-emerald-600 mb-1 font-medium">✓ Sudah Dibayar</p>
                    <p class="text-xl font-extrabold text-emerald-700">Rp <?php echo e(number_format($rangeTotalDibayar, 0, ',', '.')); ?></p>
                    <?php if($rangeTotalAll > 0): ?>
                    <div class="mt-2 w-full bg-emerald-100 rounded-full h-1.5">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?php echo e(min(100, $rangeTotalAll > 0 ? round($rangeTotalDibayar / $rangeTotalAll * 100) : 0)); ?>%"></div>
                    </div>
                    <p class="text-xs text-emerald-500 mt-1"><?php echo e($rangeTotalAll > 0 ? round($rangeTotalDibayar / $rangeTotalAll * 100) : 0); ?>%</p>
                    <?php endif; ?>
                </div>
                <div class="bg-amber-50 rounded-xl p-4 text-center border border-amber-200">
                    <p class="text-xs text-amber-600 mb-1 font-medium">⏳ Belum Dibayar</p>
                    <p class="text-xl font-extrabold text-amber-700">Rp <?php echo e(number_format($rangeTotalBelum, 0, ',', '.')); ?></p>
                    <?php if($rangeTotalAll > 0): ?>
                    <div class="mt-2 w-full bg-amber-100 rounded-full h-1.5">
                        <div class="bg-amber-400 h-1.5 rounded-full" style="width: <?php echo e(min(100, $rangeTotalAll > 0 ? round($rangeTotalBelum / $rangeTotalAll * 100) : 0)); ?>%"></div>
                    </div>
                    <p class="text-xs text-amber-500 mt-1"><?php echo e($rangeTotalAll > 0 ? round($rangeTotalBelum / $rangeTotalAll * 100) : 0); ?>%</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if(count($rangeChartLabels) > 0): ?>
            <div class="px-5 pb-5">
                <div style="height: 220px;">
                    <canvas id="chartRangeGaji"></canvas>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script>
            (function () {
                const labels  = <?php echo e(Illuminate\Support\Js::from($rangeChartLabels)); ?>;
                const paid    = <?php echo e(Illuminate\Support\Js::from($rangeChartPaid)); ?>;
                const unpaid  = <?php echo e(Illuminate\Support\Js::from($rangeChartUnpaid)); ?>;
                const fmtRp   = n => 'Rp ' + Number(n).toLocaleString('id-ID');

                new Chart(document.getElementById('chartRangeGaji'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Sudah Dibayar', data: paid,   backgroundColor: 'rgba(16,185,129,0.85)', borderRadius: 4, stack: 's' },
                            { label: 'Belum Dibayar', data: unpaid, backgroundColor: 'rgba(251,191,36,0.85)',  borderRadius: 4, stack: 's' },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top', labels: { boxWidth: 11, font: { size: 11 } } },
                            tooltip: { callbacks: { label: ctx => ' ' + ctx.dataset.label + ': ' + fmtRp(ctx.raw) } }
                        },
                        scales: {
                            x: { stacked: true, grid: { display: false }, ticks: { font: { size: 10 } } },
                            y: { stacked: true, ticks: { font: { size: 10 }, callback: v => fmtRp(v) }, grid: { color: 'rgba(0,0,0,0.04)' } }
                        }
                    }
                });
            })();
            </script>
            <?php else: ?>
            <div class="px-5 pb-6 text-center text-sm text-surface-400">Tidak ada data gaji pada rentang ini.</div>
            <?php endif; ?>
        </div>

        
        <div class="card">
            <div class="px-5 py-4 border-b flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-surface-700">💸 Rekap Gaji — <?php echo e($gajiPeriodLabel); ?></h3>
                    <p class="text-xs text-surface-400 mt-0.5">Detail gaji seluruh karyawan periode ini</p>
                </div>
                <?php if($salaryRecords->count() > 0): ?>
                <a href="<?php echo e(route('laporan.export-gaji', ['month' => $filterMonth, 'year' => $filterYear])); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Excel
                </a>
                <?php endif; ?>
            </div>

            
            <?php if($salaryRecords->count() > 0): ?>
            <div class="px-5 pt-4 pb-2">
                <div style="height: <?php echo e(min(260, max(160, $salaryRecords->count() * 44))); ?>px;">
                    <canvas id="chartGajiKaryawan"></canvas>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script>
            (function () {
                const labels      = <?php echo e(Illuminate\Support\Js::from($chartLabels)); ?>;
                const dibayar     = <?php echo e(Illuminate\Support\Js::from($chartDibayar)); ?>;
                const belumBayar  = <?php echo e(Illuminate\Support\Js::from($chartBelumBayar)); ?>;

                const fmtRp = n => 'Rp ' + Number(n).toLocaleString('id-ID');

                new Chart(document.getElementById('chartGajiKaryawan'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Sudah Dibayar',
                                data: dibayar,
                                backgroundColor: 'rgba(16,185,129,0.85)',
                                borderRadius: 5,
                                stack: 'gaji',
                            },
                            {
                                label: 'Belum Dibayar',
                                data: belumBayar,
                                backgroundColor: 'rgba(251,191,36,0.85)',
                                borderRadius: 5,
                                stack: 'gaji',
                            },
                        ]
                    },
                    options: {
                        indexAxis: labels.length > 4 ? 'y' : 'x',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
                            tooltip: {
                                callbacks: { label: ctx => ' ' + ctx.dataset.label + ': ' + fmtRp(ctx.raw) }
                            }
                        },
                        scales: {
                            x: { stacked: true, grid: { display: false }, ticks: { font: { size: 10 } } },
                            y: {
                                stacked: true,
                                ticks: {
                                    font: { size: 10 },
                                    callback: v => typeof v === 'number' ? fmtRp(v) : v,
                                },
                                grid: { color: 'rgba(0,0,0,0.04)' },
                            }
                        }
                    }
                });
            })();
            </script>
            <?php endif; ?>

            <?php if($salaryRecords->isEmpty()): ?>
            <div class="py-12 text-center text-surface-400">
                <p class="text-3xl mb-2">📭</p>
                <p class="text-sm">Belum ada data gaji untuk <?php echo e($months[$filterMonth]); ?> <?php echo e($filterYear); ?>.</p>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create salary')): ?>
                <p class="text-xs mt-1">
                    Buka detail karyawan untuk menambah gaji.
                    <a href="<?php echo e(route('employees.index')); ?>" class="text-brand-600 hover:underline">Lihat daftar karyawan</a>
                </p>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="table-th">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-surface-600">Karyawan</th>
                            <th class="px-4 py-3 text-right font-semibold text-surface-600">Gaji Pokok</th>
                            <th class="px-4 py-3 text-right font-semibold text-green-600 hidden sm:table-cell">Bonus</th>
                            <th class="px-4 py-3 text-right font-semibold text-red-500 hidden sm:table-cell">Potongan</th>
                            <th class="px-4 py-3 text-right font-semibold text-surface-700">Total</th>
                            <th class="px-4 py-3 text-center font-semibold text-surface-600">Status</th>
                            <th class="px-4 py-3 text-center font-semibold text-surface-600 hidden md:table-cell">Tgl Bayar</th>
                            <th class="px-4 py-3 text-center font-semibold text-surface-600">Slip</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <?php $__currentLoopData = $salaryRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-surface-50 transition">
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('employees.show', $sal->employee)); ?>" class="font-medium text-brand-700 hover:underline">
                                    <?php echo e($sal->employee->name); ?>

                                </a>
                                <span class="block text-xs text-surface-400"><?php echo e($sal->employee->position ?? ''); ?></span>
                            </td>
                            <td class="px-4 py-3 text-right text-surface-700">Rp <?php echo e(number_format($sal->base_salary, 0, ',', '.')); ?></td>
                            <td class="px-4 py-3 text-right text-green-600 hidden sm:table-cell">
                                <?php echo e($sal->bonus > 0 ? '+Rp '.number_format($sal->bonus, 0, ',', '.') : '—'); ?>

                            </td>
                            <td class="px-4 py-3 text-right text-red-500 hidden sm:table-cell">
                                <?php echo e($sal->deductions > 0 ? '-Rp '.number_format($sal->deductions, 0, ',', '.') : '—'); ?>

                            </td>
                            <td class="px-4 py-3 text-right font-bold text-surface-800">Rp <?php echo e(number_format($sal->total_salary, 0, ',', '.')); ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if($sal->paid_at): ?>
                                    <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Terbayar</span>
                                <?php else: ?>
                                    <span class="inline-block px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Belum Bayar</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center text-surface-500 text-xs hidden md:table-cell">
                                <?php echo e($sal->paid_at ? $sal->paid_at->format('d M Y') : '—'); ?>

                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view salary')): ?>
                                <a href="<?php echo e(route('employee-salaries.slip', [$sal->employee, $sal])); ?>"
                                   class="inline-flex items-center gap-1 px-2 py-1 text-xs text-brand-700 bg-brand-50 border border-brand-200 rounded hover:bg-brand-100 transition">
                                    🧾 Slip
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="border-t-2 border-surface-200 bg-surface-50">
                        <tr>
                            <td class="px-4 py-3 font-semibold text-surface-600 text-sm">
                                Total <?php echo e($salaryRecords->count()); ?> karyawan
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-surface-700">
                                Rp <?php echo e(number_format($salaryRecords->sum('base_salary'), 0, ',', '.')); ?>

                            </td>
                            <td class="px-4 py-3 text-right font-bold text-green-600 hidden sm:table-cell">
                                +Rp <?php echo e(number_format($salaryRecords->sum('bonus'), 0, ',', '.')); ?>

                            </td>
                            <td class="px-4 py-3 text-right font-bold text-red-500 hidden sm:table-cell">
                                -Rp <?php echo e(number_format($salaryRecords->sum('deductions'), 0, ',', '.')); ?>

                            </td>
                            <td class="px-4 py-3 text-right font-bold text-surface-900">
                                Rp <?php echo e(number_format($salaryRecords->sum('total_salary'), 0, ',', '.')); ?>

                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php endif; ?>
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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/employees/index.blade.php ENDPATH**/ ?>