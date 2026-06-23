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
        <h2 class="page-title">Pengaturan Tampilan</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">

            <?php if(session('success')): ?>
            <div class="alert-success">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?php echo e(session('success')); ?>

            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('settings.update')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="card">
                    <div class="divide-y divide-surface-100">

                        <div class="px-6 py-5">
                            <h3 class="text-sm font-bold text-surface-900 uppercase tracking-wide mb-4">Identitas Bisnis</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="label">Nama Bisnis</label>
                                    <input type="text" name="business_name"
                                           value="<?php echo e(old('business_name', $settings['business_name'])); ?>"
                                           placeholder="FORKA COFFEE & SPACE"
                                           class="input-field <?php $__errorArgs = ['business_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> !border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <p class="text-xs text-surface-400 mt-1">Ditampilkan di slip gaji dan header sidebar.</p>
                                    <?php $__errorArgs = ['business_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="input-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div>
                                    <label class="label">Tagline Sidebar</label>
                                    <input type="text" name="sidebar_tagline"
                                           value="<?php echo e(old('sidebar_tagline', $settings['sidebar_tagline'])); ?>"
                                           placeholder="Coffee Shop Manager"
                                           class="input-field">
                                    <p class="text-xs text-surface-400 mt-1">Teks kecil di bawah nama bisnis pada sidebar.</p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-5">
                            <h3 class="text-sm font-bold text-surface-900 uppercase tracking-wide mb-4">Slip Gaji</h3>
                            <div>
                                <label class="label">Subjudul Slip Gaji</label>
                                <input type="text" name="slip_subtitle"
                                       value="<?php echo e(old('slip_subtitle', $settings['slip_subtitle'])); ?>"
                                       placeholder="Slip Gaji Karyawan"
                                       class="input-field">
                                <p class="text-xs text-surface-400 mt-1">Teks di bawah nama bisnis pada header slip gaji.</p>
                            </div>
                        </div>

                        <div class="px-6 py-5">
                            <h3 class="text-sm font-bold text-surface-900 uppercase tracking-wide mb-4">Landing Page</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="label">WhatsApp Number</label>
                                    <input type="text" name="wa_number"
                                           value="<?php echo e(old('wa_number', $settings['wa_number'] ?? '6281234567890')); ?>"
                                           placeholder="6281234567890"
                                           class="input-field">
                                    <p class="text-xs text-surface-400 mt-1">Tombol WhatsApp floating di landing page.</p>
                                </div>
                                <div>
                                    <label class="label">Alamat / Jam Operasional</label>
                                    <textarea name="landing_address" rows="2" class="input-field !resize-y"
                                        placeholder="Jl. Contoh No. 123, Kota"><?php echo e(old('landing_address', $settings['landing_address'] ?? '')); ?></textarea>
                                    <p class="text-xs text-surface-400 mt-1">Ditampilkan di bagian lokasi landing page.</p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-5 bg-surface-50">
                            <h3 class="text-sm font-bold text-surface-900 uppercase tracking-wide mb-3">Preview Slip Gaji</h3>
                            <div class="bg-brand-600 text-white rounded-lg px-5 py-4 flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-lg" id="preview-name"><?php echo e($settings['business_name']); ?></div>
                                    <div class="text-xs opacity-70 mt-0.5" id="preview-subtitle"><?php echo e($settings['slip_subtitle']); ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs opacity-70">Periode</div>
                                    <div class="font-semibold text-sm mt-0.5">April 2026</div>
                                </div>
                            </div>
                            <p class="text-xs text-surface-400 mt-2">Preview berubah setelah disimpan.</p>
                        </div>

                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>

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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/settings/index.blade.php ENDPATH**/ ?>