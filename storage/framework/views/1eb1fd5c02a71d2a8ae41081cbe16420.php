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
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="page-title">Penjualan Harian</h1>
                <p class="text-xs text-surface-400 mt-0.5">Rekap penjualan &mdash; <?php echo e($periodLabel); ?></p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="<?php echo e(route('penjualan-harian.index')); ?>" id="ph-filter"
                      class="flex items-center gap-1.5">
                    <input type="date" name="date" id="ph-date"
                           value="<?php echo e($filterDate ?? ''); ?>"
                           max="<?php echo e(now()->toDateString()); ?>"
                           onchange="this.form.submit()"
                           class="rounded-xl border border-surface-300 bg-white !w-auto px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <?php if($filterDate): ?>
                        <a href="<?php echo e(route('penjualan-harian.index', ['month' => $month, 'year' => $year])); ?>"
                           class="text-xs text-red-400 hover:text-red-600 font-semibold px-1" title="Reset">&#10005;</a>
                    <?php endif; ?>
                    <select name="month" onchange="document.getElementById('ph-date').value=''; this.form.submit()"
                            class="rounded-xl border border-surface-300 bg-white !w-auto px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition <?php echo e($filterDate ? 'opacity-50' : ''); ?>">
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($n); ?>" <?php if($n == $month): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select name="year" onchange="document.getElementById('ph-date').value=''; this.form.submit()"
                            class="rounded-xl border border-surface-300 bg-white !w-auto px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition <?php echo e($filterDate ? 'opacity-50' : ''); ?>">
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($y); ?>" <?php if($y == $year): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>

                <a href="<?php echo e(route('penjualan-harian.import')); ?>"
                   class="btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Import Excel
                </a>
                <a href="<?php echo e(route('penjualan-harian.create')); ?>"
                   class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Input Manual
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5">

        <?php if(session('success')): ?>
        <div class="alert-success">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        
        <div class="grid grid-cols-3 gap-4">
            <div class="stat-card">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Total Omset</span>
                </div>
                <p class="text-2xl font-extrabold text-surface-900">Rp <?php echo e(number_format($totalOmset, 0, ',', '.')); ?></p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Keuntungan</span>
                </div>
                <p class="text-2xl font-extrabold <?php echo e($totalProfit >= 0 ? 'text-emerald-600' : 'text-red-600'); ?>">
                    Rp <?php echo e(number_format($totalProfit, 0, ',', '.')); ?>

                </p>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Total Terjual</span>
                </div>
                <p class="text-2xl font-extrabold text-brand-600"><?php echo e(number_format($totalQty)); ?> pcs</p>
            </div>
        </div>

        
        <?php
            $catParams = request()->except('cat');
        ?>
        <div class="card">
            <div class="px-5 py-4 border-b border-surface-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-surface-900">Detail per Kategori</h2>
                <?php if($filterCat): ?>
                    <a href="<?php echo e(route('penjualan-harian.index', $catParams)); ?>"
                       class="text-xs text-brand-600 hover:underline font-semibold">&#10005; Semua Kategori</a>
                <?php endif; ?>
            </div>
            <div class="flex divide-x divide-surface-100">
                <?php $__currentLoopData = $catBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isActive = $filterCat === $cat['key'];
                    $linkParams = array_merge($catParams, ['cat' => $cat['key']]);
                    $margin = $cat['omset'] > 0 ? round(($cat['profit'] / $cat['omset']) * 100, 1) : 0;
                ?>
                <a href="<?php echo e($isActive ? route('penjualan-harian.index', $catParams) : route('penjualan-harian.index', $linkParams)); ?>"
                   class="flex-1 px-3 py-3 text-center transition hover:bg-surface-50 <?php echo e($isActive ? 'bg-brand-50 ring-2 ring-inset ring-brand-400' : ''); ?>">
                    <div class="w-8 h-8 mx-auto mb-1 rounded-lg flex items-center justify-center
                        <?php if($cat['key'] === 'coffe'): ?> bg-amber-100 text-amber-600
                        <?php elseif($cat['key'] === 'minuman'): ?> bg-sky-100 text-sky-600
                        <?php elseif($cat['key'] === 'snack'): ?> bg-red-100 text-red-500
                        <?php else: ?> bg-orange-100 text-orange-600
                        <?php endif; ?>">
                        <?php if($cat['key'] === 'coffe'): ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <?php elseif($cat['key'] === 'minuman'): ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <?php elseif($cat['key'] === 'snack'): ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h-2m0 0H8m4 0V6m0 4v4m6-4a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                        <?php else: ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 019.5 3H8"/></svg>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs font-semibold text-surface-600 leading-tight"><?php echo e($cat['label']); ?></p>
                    <p class="text-xl font-extrabold mt-1 leading-none <?php echo e($isActive ? 'text-brand-600' : 'text-surface-900'); ?>"><?php echo e(number_format($cat['qty'])); ?> <span class="text-xs font-normal text-surface-400"><?php echo e($cat['unit']); ?></span></p>
                    <p class="text-xs text-surface-400 mt-1">
                        <span class="text-surface-600 font-medium"><?php echo e(number_format($cat['omset'] / 1000, 0)); ?>k</span>
                        &middot; <span class="<?php echo e($cat['profit'] >= 0 ? 'text-emerald-600' : 'text-red-500'); ?> font-medium"><?php echo e(number_format($cat['profit'] / 1000, 0)); ?>k</span>
                    </p>
                    <span class="inline-flex mt-1.5 px-2 py-0.5 rounded-full text-xs font-semibold
                        <?php echo e($margin >= 30 ? 'bg-emerald-100 text-emerald-700' : ($margin >= 10 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600')); ?>">
                        <?php echo e($margin); ?>%
                    </span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <?php
            $sortUrl = fn(string $col) => route('penjualan-harian.index', array_merge(
                request()->except(['sort','dir']),
                ['sort' => $col, 'dir' => ($sortBy === $col && $sortDir === 'desc') ? 'asc' : 'desc']
            ));
            $sortIcon = fn(string $col) => $sortBy === $col
                ? ($sortDir === 'asc' ? ' &#8593;' : ' &#8595;')
                : ' &#8645;';
            $thClassRight = fn(string $col) => 'px-4 py-3 text-right whitespace-nowrap cursor-pointer select-none transition '
                .($sortBy === $col ? 'text-brand-600 font-bold' : 'text-surface-500 hover:text-brand-600');
        ?>
        <?php if(!$filterCat): ?>
        <div class="card">
            <div class="px-5 py-4 border-b border-surface-100">
                <h2 class="text-sm font-bold text-surface-900">Rekap Per Tanggal</h2>
            </div>

            <?php if($rows->isEmpty()): ?>
            <div class="empty-state">
                Belum ada data penjualan di bulan ini.
                <br>
                <a href="<?php echo e(route('penjualan-harian.create')); ?>" class="text-brand-600 hover:underline mt-2 inline-block font-semibold">
                    + Input sekarang
                </a>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table-wrap">
                    <thead>
                        <tr class="bg-surface-50">
                            <th class="table-th table-head">Tanggal</th>
                            <th class="table-th table-head text-right">Total Terjual</th>
                            <th class="table-th table-head text-right">Omset</th>
                            <th class="table-th table-head text-right">HPP</th>
                            <th class="table-th table-head text-right">Keuntungan</th>
                            <th class="table-th table-head text-right">Margin</th>
                            <th class="table-th table-head text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <?php $__currentLoopData = $groupedRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $margin = $group->total_omset > 0
                                ? round(($group->total_profit / $group->total_omset) * 100, 1) : 0;
                        ?>
                        <tr class="hover:bg-surface-50 transition-colors">
                            <td class="table-td">
                                <p class="font-semibold text-surface-900">
                                    <?php echo e(\Carbon\Carbon::parse($group->sale_date)->translatedFormat('l')); ?>

                                </p>
                                <p class="text-xs text-surface-400">
                                    <?php echo e(\Carbon\Carbon::parse($group->sale_date)->format('d/m/Y')); ?>

                                </p>
                            </td>
                            <td class="table-td text-right font-medium text-surface-700"><?php echo e(number_format($group->total_qty)); ?> pcs</td>
                            <td class="table-td text-right text-surface-700">Rp <?php echo e(number_format($group->total_omset, 0, ',', '.')); ?></td>
                            <td class="table-td text-right text-amber-600">Rp <?php echo e(number_format($group->total_hpp, 0, ',', '.')); ?></td>
                            <td class="table-td text-right font-semibold <?php echo e($group->total_profit >= 0 ? 'text-emerald-600' : 'text-red-500'); ?>">
                                Rp <?php echo e(number_format($group->total_profit, 0, ',', '.')); ?>

                            </td>
                            <td class="table-td text-right">
                                <span class="badge <?php echo e($margin >= 30 ? 'badge-green' : ($margin >= 10 ? 'badge-yellow' : 'badge-red')); ?>">
                                    <?php echo e($margin); ?>%
                                </span>
                            </td>
                            <td class="table-td text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('penjualan-harian.create', ['date' => \Carbon\Carbon::parse($group->sale_date)->toDateString(), 'shift' => 'pagi'])); ?>"
                                       class="text-xs font-semibold text-amber-600 hover:text-amber-700">Edit</a>
                                    <a href="<?php echo e(route('penjualan-harian.show', [\Carbon\Carbon::parse($group->sale_date)->toDateString(), 'pagi'])); ?>"
                                       class="text-xs text-surface-400 hover:text-surface-600">Detail</a>
                                    <form method="POST"
                                          action="<?php echo e(route('penjualan-harian.destroy', [\Carbon\Carbon::parse($group->sale_date)->toDateString(), 'pagi'])); ?>"
                                          onsubmit="return confirm('Hapus data <?php echo e(\Carbon\Carbon::parse($group->sale_date)->format('d/m/Y')); ?>?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="text-xs text-red-400 hover:text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        
        <?php if($filterCat): ?>
        <?php
            $catMap = ['coffe' => 'Coffe', 'minuman' => 'Minuman', 'snack' => 'Snack', 'makanan' => 'Makanan'];
            $catFilter = $catMap[$filterCat] ?? null;
            $detailProducts = \App\Models\DailySale::selectRaw('product_name, unit_price, SUM(quantity_sold) as qty, SUM(subtotal) as omset, SUM(hpp_total) as hpp, SUM(profit) as profit')
                ->whereMonth('sale_date', $month)
                ->whereYear('sale_date', $year)
                ->when($catFilter, fn($q) => $q->whereIn('hpp_product_id', \App\Models\HppProduct::where('category', $catFilter)->pluck('id')))
                ->groupBy('product_name', 'unit_price')
                ->orderByDesc('qty')
                ->get();
        ?>
        <div class="bg-white rounded-2xl border border-surface-200 shadow-sm">
            <div class="px-5 py-4 border-b border-surface-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-surface-900">Detail Produk &mdash; <?php echo e(collect($catBreakdown)->firstWhere('key', $filterCat)['label'] ?? ''); ?></h2>
            </div>
            <div class="overflow-x-auto">
                <table class="table-wrap">
                    <thead>
                        <tr class="bg-surface-50">
                            <th class="table-th table-head">Produk</th>
                            <th class="table-th table-head text-right">Harga</th>
                            <th class="table-th table-head text-right">Terjual</th>
                            <th class="table-th table-head text-right">Omset</th>
                            <th class="table-th table-head text-right">HPP</th>
                            <th class="table-th table-head text-right">Profit</th>
                            <th class="table-th table-head text-right">Margin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <?php $__empty_1 = true; $__currentLoopData = $detailProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $margin = $item->omset > 0 ? round(($item->profit / $item->omset) * 100, 1) : 0; ?>
                        <tr class="hover:bg-surface-50 transition-colors">
                            <td class="table-td font-semibold text-surface-900"><?php echo e($item->product_name); ?></td>
                            <td class="table-td text-right text-surface-500">Rp <?php echo e(number_format($item->unit_price, 0, ',', '.')); ?></td>
                            <td class="table-td text-right font-semibold text-brand-600"><?php echo e(number_format($item->qty)); ?> pcs</td>
                            <td class="table-td text-right text-surface-700">Rp <?php echo e(number_format($item->omset, 0, ',', '.')); ?></td>
                            <td class="table-td text-right text-amber-600">Rp <?php echo e(number_format($item->hpp, 0, ',', '.')); ?></td>
                            <td class="table-td text-right font-semibold <?php echo e($item->profit >= 0 ? 'text-emerald-600' : 'text-red-500'); ?>">Rp <?php echo e(number_format($item->profit, 0, ',', '.')); ?></td>
                            <td class="table-td text-right">
                                <span class="badge <?php echo e($margin >= 30 ? 'badge-green' : ($margin >= 10 ? 'badge-yellow' : 'badge-red')); ?>"><?php echo e($margin); ?>%</span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="px-6 py-8 text-center text-surface-400">Belum ada data penjualan untuk kategori ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/penjualan-harian/index.blade.php ENDPATH**/ ?>