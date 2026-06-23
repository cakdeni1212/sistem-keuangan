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
                <h2 class="page-title">Kalkulasi HPP Produk</h2>
                <p class="text-xs text-surface-400 mt-0.5">Harga pokok dan margin tiap produk</p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create hpp')): ?>
            <a href="<?php echo e(route('hpp-products.create')); ?>"
               class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Produk
            </a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        <?php if(session('success')): ?>
        <div class="alert-success">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="stat-card text-center">
                <div class="w-9 h-9 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <p class="text-xs text-surface-400 font-medium">Total Produk</p>
                <p class="page-title"><?php echo e($products->count()); ?></p>
            </div>
            <div class="stat-card text-center">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-xs text-surface-400 font-medium">Produk Aktif</p>
                <p class="text-xl font-bold text-emerald-600"><?php echo e($products->where('is_active', true)->count()); ?></p>
            </div>
            <div class="stat-card text-center">
                <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <p class="text-xs text-surface-400 font-medium">Rata-rata Margin</p>
                <p class="text-xl font-bold <?php echo e($avgMargin >= 100 ? 'text-emerald-600' : ($avgMargin >= 50 ? 'text-amber-600' : 'text-red-600')); ?>">
                    <?php echo e(number_format($avgMargin, 1)); ?>%
                </p>
            </div>
            <div class="stat-card text-center">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <p class="text-xs text-surface-400 font-medium">Kategori</p>
                <p class="text-xl font-bold text-blue-600"><?php echo e($categories->count()); ?></p>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-surface-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-wrap">
                    <thead>
                        <tr class="bg-surface-50">
                            <th class="table-th table-head">#</th>
                            <th class="table-th table-head">Nama Produk</th>
                            <th class="table-th table-head">SKU</th>
                            <th class="table-th table-head">Kategori</th>
                            <th class="table-th table-head">Satuan</th>
                            <th class="table-th table-head text-right">HPP Modal</th>
                            <th class="table-th table-head text-right">Total HPP</th>
                            <th class="table-th table-head text-right">Harga Jual</th>
                            <th class="table-th table-head text-right">Markup</th>
                            <th class="table-th table-head text-center">Status</th>
                            <th class="table-th table-head text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $marginPct = $p->margin_percent;
                            $marginBadge = $marginPct >= 100 ? 'badge-green'
                                : ($marginPct >= 50 ? 'badge-yellow'
                                : 'badge-red');
                        ?>
                        <tr class="hover:bg-surface-50 transition-colors">
                            <td class="table-td text-surface-300 text-xs"><?php echo e($i + 1); ?></td>
                            <td class="table-td font-semibold text-surface-900">
                                <?php echo e($p->name); ?>

                                <?php if($p->notes): ?>
                                <p class="text-xs text-surface-400 font-normal mt-0.5"><?php echo e(Str::limit($p->notes, 50)); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="table-td text-surface-400 text-xs font-mono"><?php echo e($p->sku ?? '—'); ?></td>
                            <td class="table-td"><?php echo e($p->category ?? '—'); ?></td>
                            <td class="table-td text-sm"><?php echo e($p->satuan ?? '—'); ?></td>
                            <td class="table-td text-right text-surface-600">Rp <?php echo e(number_format($p->bahan_baku, 0, ',', '.')); ?></td>
                            <td class="table-td text-right font-semibold text-surface-900">Rp <?php echo e(number_format($p->hpp_total, 0, ',', '.')); ?></td>
                            <td class="table-td text-right font-semibold text-brand-700">Rp <?php echo e(number_format($p->harga_jual, 0, ',', '.')); ?></td>
                            <td class="table-td text-right">
                                <span class="<?php echo e($marginBadge); ?>"><?php echo e(number_format($marginPct, 1)); ?>%</span>
                                <p class="text-[10px] text-surface-400 mt-1 text-right">Rp <?php echo e(number_format($p->margin_amount, 0, ',', '.')); ?></p>
                            </td>
                            <td class="table-td text-center">
                                <?php if($p->is_active): ?>
                                <span class="badge-green"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>Aktif</span>
                                <?php else: ?>
                                <span class="badge-gray">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="table-td text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit hpp')): ?>
                                    <a href="<?php echo e(route('hpp-products.edit', $p)); ?>"
                                       class="w-8 h-8 flex items-center justify-center text-amber-500 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete hpp')): ?>
                                    <form action="<?php echo e(route('hpp-products.destroy', $p)); ?>" method="POST"
                                          onsubmit="return confirm('Hapus produk <?php echo e(addslashes($p->name)); ?>?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center text-sm text-surface-400">
                                Belum ada data produk. <a href="<?php echo e(route('hpp-products.create')); ?>" class="text-brand-600 font-semibold hover:underline">Tambah sekarang</a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-wrap gap-4 text-xs text-surface-400">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span> Markup &ge; 100% (Sehat)</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Markup 50&ndash;100% (Cukup)</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-400"></span> Markup &lt; 50% (Perlu Evaluasi)</span>
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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/hpp-products/index.blade.php ENDPATH**/ ?>