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
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('penjualan-harian.index')); ?>"
                   class="btn-secondary btn-sm">
                    ← Kembali
                </a>
                <div>
                    <h1 class="page-title">
                        Detail Penjualan –
                        <?php if($shift === 'pagi'): ?> ☀️ Shift Pagi <?php else: ?> 🌆 Shift Sore <?php endif; ?>
                        · <?php echo e(\Carbon\Carbon::parse($date)->translatedFormat('l, d F Y')); ?>

                    </h1>
                    <p class="text-xs text-surface-500"><?php echo e($items->count()); ?> produk terjual</p>
                </div>
            </div>
            <a href="<?php echo e(route('penjualan-harian.create', ['date' => $date, 'shift' => $shift])); ?>"
               class="btn-secondary text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5">

        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card p-4">
                <p class="text-xs text-surface-500 uppercase font-medium">📦 Total Terjual</p>
                <p class="text-2xl font-extrabold text-brand-600 mt-1"><?php echo e(number_format($totalQty)); ?> pcs</p>
            </div>
            <div class="stat-card p-4">
                <p class="text-xs text-surface-500 uppercase font-medium">💰 Omset</p>
                <p class="text-2xl font-extrabold text-surface-900 mt-1">Rp <?php echo e(number_format($totalOmset, 0, ',', '.')); ?></p>
            </div>
            <div class="stat-card p-4">
                <p class="text-xs text-surface-500 uppercase font-medium">🧮 HPP</p>
                <p class="text-2xl font-extrabold text-amber-600 mt-1">Rp <?php echo e(number_format($totalHpp, 0, ',', '.')); ?></p>
            </div>
            <div class="stat-card p-4">
                <p class="text-xs text-surface-500 uppercase font-medium">📈 Keuntungan</p>
                <p class="text-2xl font-extrabold mt-1 <?php echo e($totalProfit >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                    Rp <?php echo e(number_format($totalProfit, 0, ',', '.')); ?>

                </p>
                <?php if($totalOmset > 0): ?>
                <p class="text-xs text-surface-400 mt-0.5">Margin <?php echo e(round(($totalProfit/$totalOmset)*100, 1)); ?>%</p>
                <?php endif; ?>
            </div>
        </div>

        
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
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-surface-50">
                            <td class="px-5 py-3 font-medium text-surface-800"><?php echo e($item->product_name); ?></td>
                            <td class="px-4 py-3 text-right text-surface-600">Rp <?php echo e(number_format($item->unit_price, 0, ',', '.')); ?></td>
                            <td class="px-4 py-3 text-right font-semibold text-brand-600"><?php echo e(number_format($item->quantity_sold)); ?> pcs</td>
                            <td class="px-4 py-3 text-right text-surface-700">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
                            <td class="px-4 py-3 text-right text-amber-600">
                                <?php if($item->hpp_per_unit > 0): ?>
                                    Rp <?php echo e(number_format($item->hpp_total, 0, ',', '.')); ?>

                                <?php else: ?>
                                    <span class="text-surface-300">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold <?php echo e($item->profit >= 0 ? 'text-green-600' : 'text-red-500'); ?>">
                                <?php if($item->hpp_per_unit > 0): ?>
                                    Rp <?php echo e(number_format($item->profit, 0, ',', '.')); ?>

                                <?php else: ?>
                                    <span class="text-surface-300">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-surface-50 font-bold border-t-2 border-surface-200">
                            <td colspan="2" class="px-5 py-3 text-surface-600">Total</td>
                            <td class="px-4 py-3 text-right text-brand-600"><?php echo e(number_format($totalQty)); ?> pcs</td>
                            <td class="px-4 py-3 text-right text-surface-800">Rp <?php echo e(number_format($totalOmset, 0, ',', '.')); ?></td>
                            <td class="px-4 py-3 text-right text-amber-600">Rp <?php echo e(number_format($totalHpp, 0, ',', '.')); ?></td>
                            <td class="px-4 py-3 text-right <?php echo e($totalProfit >= 0 ? 'text-green-600' : 'text-red-500'); ?>">
                                Rp <?php echo e(number_format($totalProfit, 0, ',', '.')); ?>

                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/penjualan-harian/show.blade.php ENDPATH**/ ?>