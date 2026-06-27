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
                <h2 class="page-title">Manajemen User</h2>
                <p class="text-xs text-surface-500 mt-0.5">Kelola akun dan hak akses pengguna</p>
            </div>
            <a href="<?php echo e(route('admin.users.create')); ?>"
               class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah User
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

            <div class="card">
                <table class="w-full text-sm">
                    <thead class="table-th">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase tracking-wider">Terdaftar</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-surface-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-surface-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-sm mr-3">
                                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                        </div>
                                        <span class="text-sm font-medium text-surface-900"><?php echo e($user->name); ?></span>
                                        <?php if($user->id === auth()->id()): ?>
                                            <span class="ml-2 text-xs text-surface-400">(Anda)</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-surface-500"><?php echo e($user->email); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $colors = [
                                                'admin'            => 'bg-red-100 text-red-800',
                                                'manajer_keuangan' => 'bg-blue-100 text-blue-800',
                                                'akuntan'          => 'bg-green-100 text-green-800',
                                                'viewer'           => 'bg-surface-100 text-surface-800',
                                            ];
                                            $color = $colors[$role->name] ?? 'bg-surface-100 text-surface-800';
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($color); ?>">
                                            <?php echo e(str_replace('_', ' ', ucfirst($role->name))); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-surface-500">
                                    <?php echo e($user->created_at->format('d M Y')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" title="Edit"
                                           class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <?php if($user->id !== auth()->id()): ?>
                                            <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>"
                                                  class="inline"
                                                  onsubmit="return confirm('Hapus user <?php echo e(addslashes($user->name)); ?>?')">
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
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-surface-400">
                                    Belum ada user terdaftar.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if($users->hasPages()): ?>
                    <div class="px-6 py-4 border-t border-surface-100">
                        <?php echo e($users->links()); ?>

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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/admin/users/index.blade.php ENDPATH**/ ?>