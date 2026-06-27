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
            <h2 class="page-title">📦 Stok Bahan Baku</h2>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create raw-material')): ?>
            <a href="<?php echo e(route('raw-materials.create')); ?>" class="btn-primary">
                + Tambah Bahan Baku
            </a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

        
        <?php if(session('success')): ?>
        <div class="alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
        <div class="alert-error"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="card p-5">
                <p class="text-sm text-surface-500">Total Item</p>
                <p class="text-2xl font-bold text-surface-900 mt-1"><?php echo e($totalItems); ?></p>
            </div>
            <div class="card p-5">
                <p class="text-sm text-surface-500">Total Aktif</p>
                <p class="text-2xl font-bold text-green-700 mt-1"><?php echo e($totalActive); ?></p>
            </div>
            <div class="card p-5">
                <p class="text-sm text-surface-500">Stok Rendah (&lt;100)</p>
                <p class="text-2xl font-bold <?php echo e($lowStock > 0 ? 'text-red-600' : 'text-surface-900'); ?> mt-1"><?php echo e($lowStock); ?></p>
            </div>
        </div>

        
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                            <thead class="table-th">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide">#</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide">Nama</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide">Satuan</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide">Kategori</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide text-right">Stok Sekarang</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide text-right">Harga/Satuan</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide text-center">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide text-center">Digunakan di</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <?php $__empty_1 = true; $__currentLoopData = $rawMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-surface-50 transition">
                            <td class="px-4 py-3 text-surface-400"><?php echo e($loop->iteration + ($rawMaterials->currentPage() - 1) * $rawMaterials->perPage()); ?></td>
                            <td class="px-4 py-3 font-medium text-surface-900"><?php echo e($item->name); ?></td>
                            <td class="px-4 py-3 text-surface-600"><?php echo e($item->unit); ?></td>
                            <td class="px-4 py-3 text-surface-500"><?php echo e($item->category ?: '—'); ?></td>
                            <td class="px-4 py-3 text-right font-medium <?php echo e((float)$item->stock_quantity < 100 ? 'text-red-600' : 'text-surface-800'); ?>">
                                <?php echo e(number_format($item->stock_quantity, 3, ',', '.')); ?>

                            </td>
                            <td class="px-4 py-3 text-right text-surface-700">Rp <?php echo e(number_format($item->price_per_unit, 0, ',', '.')); ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if($item->is_active): ?>
                                    <span class="badge badge-green">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-red">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center text-surface-500">
                                <?php echo e($item->ingredients_count); ?> produk
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit raw-material')): ?>
                                    <a href="<?php echo e(route('raw-materials.edit', $item)); ?>"
                                       class="px-3 py-1 text-xs bg-brand-50 text-brand-700 hover:bg-brand-100 rounded-md transition">Edit</a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete raw-material')): ?>
                                    <form action="<?php echo e(route('raw-materials.destroy', $item)); ?>" method="POST"
                                          onsubmit="return confirm('Hapus bahan baku \'<?php echo e($item->name); ?>\'?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                class="px-3 py-1 text-xs bg-red-50 text-red-700 hover:bg-red-100 rounded-md transition">
                                            Hapus
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-surface-400">
                                Belum ada data bahan baku. <a href="<?php echo e(route('raw-materials.create')); ?>" class="text-brand-600 hover:underline">Tambah sekarang</a>.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($rawMaterials->hasPages()): ?>
            <div class="px-4 py-3 border-t bg-surface-50">
                <?php echo e($rawMaterials->links()); ?>

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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/raw-materials/index.blade.php ENDPATH**/ ?>