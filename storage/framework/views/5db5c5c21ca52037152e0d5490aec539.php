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
        <div>
            <h2 class="page-title">Laporan & Export</h2>
            <p class="text-xs text-surface-500 mt-0.5">Unduh data ke format Excel (.xlsx)</p>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5">

        <?php if(session('success')): ?>
            <div class="alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        
        <div class="card">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-surface-200 bg-brand-50">
                <div class="w-9 h-9 rounded-lg bg-brand-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-surface-800">Omset Harian</h3>
                    <p class="text-xs text-surface-500">QRIS, tunai, dan total per hari</p>
                </div>
            </div>
            <form method="GET" action="<?php echo e(route('laporan.export-omset')); ?>" class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Mode Filter</label>
                        <select name="mode" id="omset-mode" onchange="toggleOmsetMode()"
                                class="w-full border-surface-300 rounded-lg text-sm">
                            <option value="month">Per Bulan</option>
                            <option value="range">Rentang Tanggal</option>
                            <option value="year">Per Tahun</option>
                            <option value="all">Semua Data</option>
                        </select>
                    </div>
                    <div id="omset-month-group">
                        <label class="block text-xs font-medium text-surface-600 mb-1">Bulan</label>
                        <select name="month" class="w-full border-surface-300 rounded-lg text-sm">
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($num); ?>" <?php if($num == date('n')): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div id="omset-year-group">
                        <label class="block text-xs font-medium text-surface-600 mb-1">Tahun</label>
                        <select name="year" class="w-full border-surface-300 rounded-lg text-sm">
                            <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($y); ?>" <?php if($y == date('Y')): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div id="omset-range-group" class="hidden sm:col-span-2">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-surface-600 mb-1">Dari Tanggal</label>
                                <input type="date" name="from" class="w-full border-surface-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-600 mb-1">Sampai Tanggal</label>
                                <input type="date" name="to" class="w-full border-surface-300 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit"
                        class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

        
        <div class="card">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-surface-200 bg-emerald-50">
                <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-surface-800">Transaksi</h3>
                    <p class="text-xs text-surface-500">Pemasukan & pengeluaran dengan status persetujuan</p>
                </div>
            </div>
            <form method="GET" action="<?php echo e(route('laporan.export-transaksi')); ?>" class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="from" value="<?php echo e(date('Y-m-01')); ?>"
                               class="w-full border-surface-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="to" value="<?php echo e(date('Y-m-d')); ?>"
                               class="w-full border-surface-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Kategori</label>
                        <select name="category" class="w-full border-surface-300 rounded-lg text-sm">
                            <option value="">Semua Kategori</option>
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Status</label>
                        <select name="status" class="w-full border-surface-300 rounded-lg text-sm">
                            <option value="">Semua Status</option>
                            <option value="approved">Disetujui</option>
                            <option value="pending">Menunggu</option>
                            <option value="draft">Draft</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                </div>
                <button type="submit"
                        class="btn-primary">                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

        
        <div class="card">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-surface-200 bg-red-50">
                <div class="w-9 h-9 rounded-lg bg-red-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-surface-800">Cashbon / Hutang</h3>
                    <p class="text-xs text-surface-500">Data hutang internal & eksternal beserta status bayar</p>
                </div>
            </div>
            <form method="GET" action="<?php echo e(route('laporan.export-cashbon')); ?>" class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Dari Tanggal Hutang</label>
                        <input type="date" name="from" class="w-full border-surface-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Sampai Tanggal Hutang</label>
                        <input type="date" name="to" class="w-full border-surface-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Status</label>
                        <select name="status" class="w-full border-surface-300 rounded-lg text-sm">
                            <option value="">Semua Status</option>
                            <option value="belum_bayar">Belum Bayar</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>
                </div>
                <button type="submit"
                        class="btn-primary">                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

        
        <div class="card">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-surface-200 bg-purple-50">
                <div class="w-9 h-9 rounded-lg bg-purple-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-surface-800">Gaji Karyawan</h3>
                    <p class="text-xs text-surface-500">Rekap gaji pokok, bonus, potongan, dan status bayar</p>
                </div>
            </div>
            <form method="GET" action="<?php echo e(route('laporan.export-gaji')); ?>" class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Bulan</label>
                        <select name="month" class="w-full border-surface-300 rounded-lg text-sm">
                            <option value="">Semua Bulan</option>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($num); ?>" <?php if($num == date('n')): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Tahun</label>
                        <select name="year" class="w-full border-surface-300 rounded-lg text-sm">
                            <option value="">Semua Tahun</option>
                            <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($y); ?>" <?php if($y == date('Y')): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 mb-1">Karyawan</label>
                        <select name="employee_id" class="w-full border-surface-300 rounded-lg text-sm">
                            <option value="">Semua Karyawan</option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <button type="submit"
                        class="btn-primary">                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>

    </div>

    <script>
    function toggleOmsetMode() {
        const mode = document.getElementById('omset-mode').value;
        const monthGroup = document.getElementById('omset-month-group');
        const yearGroup  = document.getElementById('omset-year-group');
        const rangeGroup = document.getElementById('omset-range-group');

        monthGroup.classList.toggle('hidden', !['month'].includes(mode));
        yearGroup.classList.toggle('hidden',  !['month', 'year'].includes(mode));
        rangeGroup.classList.toggle('hidden', mode !== 'range');
    }
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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/laporan/index.blade.php ENDPATH**/ ?>