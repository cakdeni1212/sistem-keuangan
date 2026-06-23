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
                <h2 class="page-title">Data Transaksi</h2>
                <p class="text-xs text-surface-400 mt-0.5">Riwayat pemasukan dan pengeluaran</p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create transactions')): ?>
            <a href="<?php echo e(route('transactions.create')); ?>"
               class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Transaksi
            </a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

        <?php if(session('success')): ?>
            <div class="alert-success">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert-error">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="col-span-full text-xs text-surface-400 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Periode: <span class="font-semibold text-surface-700"><?php echo e($periodLabel); ?></span>
            </div>
            <div class="stat-card border-l-[3px] border-emerald-500">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Pemasukan</span>
                </div>
                <p class="text-xl font-bold text-emerald-700">Rp <?php echo e(number_format($summary['pemasukan'], 0, ',', '.')); ?></p>
                <div class="flex gap-4 mt-2 text-xs text-surface-400">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-purple-400"></span> QRIS: Rp <?php echo e(number_format($summary['pemasukan_qris'], 0, ',', '.')); ?></span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-400"></span> Tunai: Rp <?php echo e(number_format($summary['pemasukan_tunai'], 0, ',', '.')); ?></span>
                </div>
            </div>
            <div class="stat-card border-l-[3px] border-red-400">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Pengeluaran</span>
                </div>
                <p class="text-xl font-bold text-red-600">Rp <?php echo e(number_format($summary['pengeluaran'], 0, ',', '.')); ?></p>
            </div>
            <div class="stat-card border-l-[3px] <?php echo e($summary['saldo'] >= 0 ? 'border-blue-500' : 'border-orange-500'); ?>">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg <?php echo e($summary['saldo'] >= 0 ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600'); ?> flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Saldo</span>
                </div>
                <p class="text-xl font-bold <?php echo e($summary['saldo'] >= 0 ? 'text-blue-700' : 'text-orange-700'); ?>">Rp <?php echo e(number_format($summary['saldo'], 0, ',', '.')); ?></p>
            </div>
        </div>

        
        <div class="filter-card">
            <form method="GET" action="<?php echo e(route('transactions.index')); ?>" id="tx-filter-form"
                  class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">

                <div class="col-span-2 sm:col-span-3 lg:col-span-6">
                    <label class="block text-xs font-semibold text-surface-600 mb-1.5">Cari (jenis, jumlah, atau catatan)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-surface-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                        </span>
                        <input type="text" name="search" id="tx-search"
                               value="<?php echo e($filters['search'] ?? ''); ?>"
                               placeholder="Contoh: bahan baku, 50000, nota pembelian…"
                               class="block w-full pl-11 rounded-xl border-surface-300 bg-white px-4 py-2.5 text-sm text-surface-900 placeholder-surface-400 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 transition shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-surface-600 mb-1.5">Bulan</label>
                    <select id="tx-quick-month" onchange="txApplyMonth()" class="input-field">
                        <option value="">-- Semua --</option>
                        <?php $__currentLoopData = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $nm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($n); ?>" <?php echo e($n == (int)date('n', strtotime($filters['from'] ?? '')) && ($filters['from'] ?? '') ? 'selected' : ''); ?>><?php echo e($nm); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-surface-600 mb-1.5">Tahun</label>
                    <select id="tx-quick-year" onchange="txApplyMonth()" class="input-field">
                        <?php $__currentLoopData = range(date('Y'), date('Y') - 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($y == (int)date('Y', strtotime($filters['from'] ?? date('Y-01-01'))) ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-surface-600 mb-1.5">Kategori</label>
                    <select name="category" class="input-field">
                        <option value="">Semua</option>
                        <option value="pemasukan"   <?php echo e(($filters['category'] ?? '') === 'pemasukan'   ? 'selected' : ''); ?>>Pemasukan</option>
                        <option value="pengeluaran" <?php echo e(($filters['category'] ?? '') === 'pengeluaran' ? 'selected' : ''); ?>>Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-surface-600 mb-1.5">Status</label>
                    <select name="status" class="input-field">
                        <option value="">Semua</option>
                        <option value="draft"    <?php echo e(($filters['status'] ?? '') === 'draft'    ? 'selected' : ''); ?>>Draft</option>
                        <option value="pending"  <?php echo e(($filters['status'] ?? '') === 'pending'  ? 'selected' : ''); ?>>Menunggu</option>
                        <option value="approved" <?php echo e(($filters['status'] ?? '') === 'approved' ? 'selected' : ''); ?>>Disetujui</option>
                        <option value="rejected" <?php echo e(($filters['status'] ?? '') === 'rejected' ? 'selected' : ''); ?>>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-surface-600 mb-1.5">Dari</label>
                    <input type="date" name="from" id="tx-from" value="<?php echo e($filters['from'] ?? ''); ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-surface-600 mb-1.5">Sampai</label>
                    <input type="date" name="to" id="tx-to" value="<?php echo e($filters['to'] ?? ''); ?>" class="input-field">
                </div>

                <div class="col-span-2 sm:col-span-3 lg:col-span-6 flex flex-wrap items-center gap-2">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    <a href="<?php echo e(route('transactions.index')); ?>" class="btn-secondary text-xs">Reset</a>
                    <span class="text-surface-300 hidden sm:inline">|</span>
                    <button type="button" onclick="txSetMonth(<?php echo e(date('n')); ?>, <?php echo e(date('Y')); ?>)" class="btn-secondary text-xs">Bulan Ini</button>
                    <button type="button" onclick="txSetMonth(<?php echo e(date('n') == 1 ? 12 : date('n') - 1); ?>, <?php echo e(date('n') == 1 ? date('Y') - 1 : date('Y')); ?>)" class="btn-secondary text-xs">Bulan Lalu</button>
                    <button type="button" onclick="txSetYear(<?php echo e(date('Y')); ?>)" class="btn-secondary text-xs">Tahun Ini</button>
                </div>
            </form>
        </div>

        <script>
        function txApplyMonth() {
            const m = document.getElementById('tx-quick-month').value;
            const y = document.getElementById('tx-quick-year').value;
            if (!m || !y) {
                document.getElementById('tx-from').value = '';
                document.getElementById('tx-to').value = '';
                return;
            }
            const lastDay = new Date(y, m, 0).getDate();
            const mm = String(m).padStart(2, '0');
            document.getElementById('tx-from').value = `${y}-${mm}-01`;
            document.getElementById('tx-to').value   = `${y}-${mm}-${String(lastDay).padStart(2,'0')}`;
            document.getElementById('tx-filter-form').submit();
        }
        function txSetMonth(m, y) {
            document.getElementById('tx-quick-month').value = m;
            document.getElementById('tx-quick-year').value  = y;
            txApplyMonth();
        }
        function txSetYear(y) {
            document.getElementById('tx-quick-month').value = '';
            document.getElementById('tx-quick-year').value  = y;
            const lastDay = new Date(y, 12, 0).getDate();
            document.getElementById('tx-from').value = `${y}-01-01`;
            document.getElementById('tx-to').value   = `${y}-12-${String(lastDay).padStart(2,'0')}`;
            document.getElementById('tx-filter-form').submit();
        }
        (function () {
            let timer;
            document.getElementById('tx-search').addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    document.getElementById('tx-filter-form').submit();
                }, 400);
            });
        })();
        </script>

        
        <div class="bg-white rounded-2xl border border-surface-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-wrap">
                    <thead>
                        <tr class="bg-surface-50">
                            <th class="table-th table-head">Tanggal</th>
                            <th class="table-th table-head">Jenis</th>
                            <th class="table-th table-head">Kategori</th>
                            <th class="table-th table-head text-right">Jumlah</th>
                            <th class="table-th table-head">Keterangan</th>
                            <th class="table-th table-head text-center">Nota</th>
                            <th class="table-th table-head">Status</th>
                            <th class="table-th table-head text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $cat = $tx->transactionType->category;
                        ?>
                        <tr class="hover:bg-surface-50 transition-colors">
                            <td class="table-td whitespace-nowrap"><?php echo e($tx->transaction_date->format('d/m/Y')); ?></td>
                            <td class="table-td font-medium text-surface-900"><?php echo e($tx->transactionType->name); ?></td>
                            <td class="table-td">
                                <span class="badge <?php echo e($cat === 'pemasukan' ? 'badge-green' : 'badge-red'); ?>">
                                    <?php echo e(ucfirst($cat)); ?>

                                </span>
                            </td>
                            <td class="table-td text-right font-semibold whitespace-nowrap <?php echo e($cat === 'pemasukan' ? 'text-emerald-600' : 'text-red-500'); ?>">
                                <?php echo e($cat === 'pemasukan' ? '+' : '-'); ?> Rp <?php echo e(number_format($tx->amount, 0, ',', '.')); ?>

                            </td>
                            <td class="table-td text-surface-400 max-w-xs truncate"><?php echo e($tx->description ?? '—'); ?></td>
                            <td class="table-td text-center">
                                <?php if($tx->nota_path): ?>
                                    <?php $ext = pathinfo($tx->nota_path, PATHINFO_EXTENSION); ?>
                                    <?php if(in_array(strtolower($ext), ['jpg','jpeg','png'])): ?>
                                        <button type="button"
                                                onclick="window.dispatchEvent(new CustomEvent('open-nota', { detail: { url: this.dataset.url } }))"
                                                data-url="<?php echo e($tx->nota_url); ?>"
                                                class="inline-flex items-center text-brand-600 hover:text-brand-700 text-xs font-semibold">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Lihat
                                        </button>
                                    <?php else: ?>
                                        <a href="<?php echo e($tx->nota_url); ?>" target="_blank"
                                           class="inline-flex items-center text-brand-600 hover:text-brand-700 text-xs font-semibold">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            PDF
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-surface-300">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="table-td">
                                <?php
                                    $statusBadge = match($tx->status) {
                                        'draft' => 'badge-gray',
                                        'pending' => 'badge-yellow',
                                        'approved' => 'badge-green',
                                        'rejected' => 'badge-red',
                                        default => 'badge-gray',
                                    };
                                ?>
                                <span class="<?php echo e($statusBadge); ?>"><?php echo e($tx->status_label); ?></span>
                            </td>
                            <td class="table-td text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="<?php echo e(route('transactions.show', $tx)); ?>" title="Detail"
                                       class="w-8 h-8 flex items-center justify-center text-surface-400 hover:text-surface-600 hover:bg-surface-100 rounded-xl transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit transactions')): ?>
                                    <?php if(auth()->user()->hasAnyRole(['admin', 'owner']) || !in_array($tx->status, ['approved','rejected'])): ?>
                                    <a href="<?php echo e(route('transactions.edit', $tx)); ?>" title="Edit"
                                       class="w-8 h-8 flex items-center justify-center text-amber-500 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approve transactions')): ?>
                                    <?php if($tx->status === 'pending'): ?>
                                    <form method="POST" action="<?php echo e(route('transactions.approve', $tx)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" title="Setujui"
                                                class="w-8 h-8 flex items-center justify-center text-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete transactions')): ?>
                                    <?php if(auth()->user()->hasAnyRole(['admin', 'owner']) || $tx->status !== 'approved'): ?>
                                    <form method="POST" action="<?php echo e(route('transactions.destroy', $tx)); ?>" class="inline"
                                          onsubmit="return confirm('Hapus transaksi #<?php echo e($tx->id); ?>?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" title="Hapus"
                                                class="w-8 h-8 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-surface-400">Belum ada transaksi.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($transactions->hasPages()): ?>
                <div class="px-6 py-4 border-t border-surface-100"><?php echo e($transactions->links()); ?></div>
            <?php endif; ?>
        </div>
    </div>

    
    <div x-data="{ open: false, url: '' }"
         @open-nota.window="open = true; url = $event.detail.url"
         @keydown.escape.window="open = false"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
         @click.self="open = false">
        <div class="relative max-w-2xl w-full">
            <button @click="open = false"
                    class="absolute -top-12 right-0 w-9 h-9 rounded-full bg-white/20 backdrop-blur text-white hover:bg-white/30 flex items-center justify-center transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <img :src="url" alt="Nota Transaksi"
                 class="w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl border border-white/10">
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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/transactions/index.blade.php ENDPATH**/ ?>