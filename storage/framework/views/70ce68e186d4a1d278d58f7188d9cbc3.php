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
                <h2 class="page-title">Jenis Transaksi</h2>
                <p class="text-xs text-surface-500 mt-0.5">Kelola jenis pemasukan dan pengeluaran</p>
            </div>
            <a href="<?php echo e(route('transaction-types.create')); ?>"
               class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Jenis
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

            <?php if(session('success')): ?>
                <div class="alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert-error"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            <?php $__currentLoopData = ['pengeluaran' => 'Pengeluaran', 'pemasukan' => 'Pemasukan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $group = $types->where('category', $cat); ?>
            <div class="card mb-6">
                <div class="px-6 py-3 <?php echo e($cat === 'pemasukan' ? 'bg-green-50 border-b border-green-200' : 'bg-red-50 border-b border-red-200'); ?>">
                    <h3 class="font-semibold text-sm <?php echo e($cat === 'pemasukan' ? 'text-green-800' : 'text-red-800'); ?>">
                        <?php echo e($label); ?> (<?php echo e($group->count()); ?> jenis)
                    </h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="table-th">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Nama</th>
                            <?php if($cat === 'pengeluaran'): ?>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Grup</th>
                            <?php endif; ?>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-surface-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <?php $__empty_1 = true; $__currentLoopData = $group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-surface-50">
                            <td class="px-6 py-3 text-sm font-medium text-surface-900"><?php echo e($type->name); ?></td>
                            <?php if($cat === 'pengeluaran'): ?>
                            <td class="px-6 py-3">
                                <?php if($type->grup === 'Dapur'): ?>
                                    <span class="badge inline-flex items-center gap-1 bg-orange-100 text-orange-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                        Dapur</span>
                                <?php elseif($type->grup === 'BAR'): ?>
                                    <span class="badge inline-flex items-center gap-1 bg-blue-100 text-blue-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        BAR</span>
                                <?php elseif($type->grup === 'Operasional'): ?>
                                    <span class="badge inline-flex items-center gap-1 bg-surface-100 text-surface-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Operasional</span>
                                <?php else: ?>
                                    <span class="text-xs text-surface-400">—</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-3 text-sm text-surface-500"><?php echo e($type->description ?? '-'); ?></td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 text-xs rounded-full font-medium <?php echo e($type->is_active ? 'bg-green-100 text-green-700' : 'bg-surface-100 text-surface-500'); ?>">
                                    <?php echo e($type->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="<?php echo e(route('transaction-types.edit', $type)); ?>" title="Edit"
                                       class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('transaction-types.destroy', $type)); ?>" class="inline"
                                          onsubmit="return confirm('Hapus jenis transaksi <?php echo e(addslashes($type->name)); ?>?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" title="Hapus"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($cat === 'pengeluaran' ? 5 : 4); ?>" class="px-6 py-6 text-center text-sm text-surface-400">Belum ada jenis transaksi <?php echo e($label); ?>.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/transaction-types/index.blade.php ENDPATH**/ ?>