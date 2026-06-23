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
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">📊 Data Kasir</h1>
                <p class="text-sm text-gray-500 mt-0.5">Ringkasan penjualan – <?php echo e($periodLabel); ?></p>
            </div>
            <form method="GET" action="<?php echo e(route('kasir.data')); ?>" id="data-filter" class="flex items-center gap-2">
                <select name="month" onchange="document.getElementById('data-filter').submit()"
                        class="border-gray-300 rounded-lg text-sm py-1.5 pr-8">
                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($n); ?>" <?php if($n == $month): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="year" onchange="document.getElementById('data-filter').submit()"
                        class="border-gray-300 rounded-lg text-sm py-1.5 pr-8">
                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($y); ?>" <?php if($y == $year): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
        </div>
     <?php $__env->endSlot(); ?>

<div class="py-6 px-4 sm:px-6 lg:px-8 space-y-6">

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        
        <div class="bg-white rounded-xl border shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">💰 Total Omset</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">
                Rp <?php echo e(number_format($totalOmset, 0, ',', '.')); ?>

            </p>
            <p class="text-xs text-gray-400 mt-1"><?php echo e($totalTx); ?> transaksi</p>
        </div>

        
        <div class="bg-white rounded-xl border shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">🧮 Total HPP</p>
            <p class="text-2xl font-extrabold text-amber-600 mt-1">
                Rp <?php echo e(number_format($totalHpp, 0, ',', '.')); ?>

            </p>
            <p class="text-xs text-gray-400 mt-1">Modal produk terjual</p>
        </div>

        
        <div class="bg-white rounded-xl border shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">📈 Keuntungan</p>
            <p class="text-2xl font-extrabold mt-1 <?php echo e($totalProfit >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                Rp <?php echo e(number_format($totalProfit, 0, ',', '.')); ?>

            </p>
            <?php if($totalOmset > 0): ?>
            <p class="text-xs text-gray-400 mt-1">
                Margin <?php echo e(round(($totalProfit / $totalOmset) * 100, 1)); ?>%
            </p>
            <?php endif; ?>
        </div>

        
        <div class="bg-white rounded-xl border shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">🧾 Rata-rata / Transaksi</p>
            <p class="text-2xl font-extrabold text-indigo-600 mt-1">
                Rp <?php echo e(number_format($avgTx, 0, ',', '.')); ?>

            </p>
            <p class="text-xs text-gray-400 mt-1">Per sesi kasir</p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        
        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h2 class="text-sm font-bold text-gray-700 mb-4">🌤 Rekap Shift</h2>

            <?php
                $shiftTotal = $pagiOmset + $soreOmset;
                $pagiPct    = $shiftTotal > 0 ? ($pagiOmset / $shiftTotal) * 100 : 50;
                $sorePct    = $shiftTotal > 0 ? ($soreOmset / $shiftTotal) * 100 : 50;
                $pagiWidth  = round($pagiPct, 1);
                $soreWidth  = $soreOmset > 0 ? max(round($sorePct, 1), 3) : 0;
            ?>

            
            <div class="mb-4">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-sm font-semibold text-yellow-600">☀️ Shift Pagi</span>
                    <span class="text-sm font-bold text-gray-800">Rp <?php echo e(number_format($pagiOmset, 0, ',', '.')); ?></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full" style="height:10px">
                    <div class="bg-yellow-400 rounded-full" style="height:10px;width:<?php echo e($pagiWidth); ?>%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1"><?php echo e($pagiTx); ?> transaksi · <?php echo e(round($pagiPct, 1)); ?>%</p>
            </div>

            
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-sm font-semibold text-indigo-600">🌆 Shift Sore</span>
                    <span class="text-sm font-bold text-gray-800">Rp <?php echo e(number_format($soreOmset, 0, ',', '.')); ?></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full" style="height:10px">
                    <div class="rounded-full" style="height:10px;width:<?php echo e($soreWidth); ?>%;background-color:#818cf8"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1"><?php echo e($soreTx); ?> transaksi · <?php echo e(round($sorePct, 1)); ?>%</p>
            </div>
        </div>

        
        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h2 class="text-sm font-bold text-gray-700 mb-4">💳 Metode Pembayaran</h2>

            <?php
                $bayarTotal = $qrisOmset + $tunaiOmset;
                $qrisPct    = $bayarTotal > 0 ? ($qrisOmset / $bayarTotal) * 100 : 50;
                $tunaiPct   = $bayarTotal > 0 ? ($tunaiOmset / $bayarTotal) * 100 : 50;
            ?>

            
            <div class="mb-4">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-sm font-semibold text-blue-600">📱 QRIS</span>
                    <span class="text-sm font-bold text-gray-800">Rp <?php echo e(number_format($qrisOmset, 0, ',', '.')); ?></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-blue-400 h-2.5 rounded-full transition-all duration-500"
                         style="width: <?php echo e(round($qrisPct, 1)); ?>%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1"><?php echo e(round($qrisPct, 1)); ?>% dari total omset</p>
            </div>

            
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-sm font-semibold text-green-600">💵 Tunai</span>
                    <span class="text-sm font-bold text-gray-800">Rp <?php echo e(number_format($tunaiOmset, 0, ',', '.')); ?></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-green-400 h-2.5 rounded-full transition-all duration-500"
                         style="width: <?php echo e(round($tunaiPct, 1)); ?>%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1"><?php echo e(round($tunaiPct, 1)); ?>% dari total omset</p>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border shadow-sm">
        <div class="px-5 py-4 border-b">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-bold text-gray-700">🏆 Produk Terlaris – <?php echo e($periodLabel); ?></h2>
                    <span class="text-xs text-gray-400"><?php echo e($produkTerlaris->count()); ?> produk</span>
                    <?php if($filterCat): ?>
                        <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">
                            <?php echo e($filterCat); ?>

                        </span>
                    <?php endif; ?>
                </div>

                
                <div class="flex flex-wrap items-center gap-1.5">
                    <a href="<?php echo e(route('kasir.data', ['month' => $month, 'year' => $year])); ?>"
                       class="px-3 py-1 text-xs rounded-full border transition font-medium
                           <?php echo e(!$filterCat ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400'); ?>">
                        Semua
                    </a>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('kasir.data', ['month' => $month, 'year' => $year, 'cat' => $cat])); ?>"
                       class="px-3 py-1 text-xs rounded-full border transition font-medium
                           <?php echo e($filterCat === $cat ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400'); ?>">
                        <?php echo e($cat); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <?php if($produkTerlaris->isEmpty()): ?>
        <div class="px-5 py-12 text-center text-sm text-gray-400">
            Belum ada data penjualan di periode ini.
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 uppercase tracking-wide bg-gray-50">
                        <th class="px-4 py-3 text-left w-8">#</th>
                        <th class="px-4 py-3 text-left">Produk</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-right">Terjual</th>
                        <th class="px-4 py-3 text-right">Omset</th>
                        <th class="px-4 py-3 text-right">HPP Total</th>
                        <th class="px-4 py-3 text-right">Keuntungan</th>
                        <th class="px-4 py-3 text-right">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $maxQty = $produkTerlaris->first()->total_qty ?? 1; ?>
                    <?php $__currentLoopData = $produkTerlaris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 transition">
                        
                        <td class="px-4 py-3 text-gray-400 font-medium">
                            <?php if($i === 0): ?> 🥇
                            <?php elseif($i === 1): ?> 🥈
                            <?php elseif($i === 2): ?> 🥉
                            <?php else: ?> <span class="text-xs"><?php echo e($i + 1); ?></span>
                            <?php endif; ?>
                        </td>

                        
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-800"><?php echo e($p->product_name); ?></p>
                            <div class="mt-1 w-full max-w-[180px] bg-gray-100 rounded-full h-1.5">
                                <div class="bg-indigo-400 h-1.5 rounded-full"
                                     style="width: <?php echo e(round(($p->total_qty / $maxQty) * 100)); ?>%"></div>
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            <?php if($p->category): ?>
                                <a href="<?php echo e(route('kasir.data', ['month' => $month, 'year' => $year, 'cat' => $p->category])); ?>"
                                   class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                       <?php echo e($filterCat === $p->category ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600'); ?> transition">
                                    <?php echo e($p->category); ?>

                                </a>
                            <?php else: ?>
                                <span class="text-gray-300 text-xs">—</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3 text-right font-semibold text-gray-700">
                            <?php echo e(number_format($p->total_qty)); ?> pcs
                        </td>

                        <td class="px-4 py-3 text-right text-gray-700">
                            Rp <?php echo e(number_format($p->total_omset, 0, ',', '.')); ?>

                        </td>

                        <td class="px-4 py-3 text-right text-amber-600">
                            <?php if($p->hpp_per_unit > 0): ?>
                                Rp <?php echo e(number_format($p->total_hpp, 0, ',', '.')); ?>

                            <?php else: ?>
                                <span class="text-gray-300 text-xs">—</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3 text-right font-semibold
                            <?php echo e($p->total_profit >= 0 ? 'text-green-600' : 'text-red-500'); ?>">
                            <?php if($p->hpp_per_unit > 0): ?>
                                Rp <?php echo e(number_format($p->total_profit, 0, ',', '.')); ?>

                            <?php else: ?>
                                <span class="text-gray-300 text-xs">—</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3 text-right">
                            <?php if($p->hpp_per_unit > 0): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                    <?php echo e($p->margin >= 30 ? 'bg-green-100 text-green-700' : ($p->margin >= 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-600')); ?>">
                                    <?php echo e($p->margin); ?>%
                                </span>
                            <?php else: ?>
                                <span class="text-gray-300 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>

                
                <?php
                    $footerQty    = $produkTerlaris->sum('total_qty');
                    $footerOmset  = $produkTerlaris->sum('total_omset');
                    $footerHpp    = $produkTerlaris->sum('total_hpp');
                    $footerProfit = $produkTerlaris->sum('total_profit');
                ?>
                <tfoot>
                    <tr class="bg-gray-50 text-sm font-bold border-t-2 border-gray-200">
                        <td colspan="3" class="px-4 py-3 text-gray-600">
                            Total<?php echo e($filterCat ? ' ('.$filterCat.')' : ''); ?>

                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">
                            <?php echo e(number_format($footerQty)); ?> pcs
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">
                            Rp <?php echo e(number_format($footerOmset, 0, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-right text-amber-600">
                            Rp <?php echo e(number_format($footerHpp, 0, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-right <?php echo e($footerProfit >= 0 ? 'text-green-600' : 'text-red-500'); ?>">
                            Rp <?php echo e(number_format($footerProfit, 0, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-right">
                            <?php if($footerOmset > 0): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                <?php echo e(($footerProfit/$footerOmset*100) >= 30 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'); ?>">
                                <?php echo e(round($footerProfit / $footerOmset * 100, 1)); ?>%
                            </span>
                            <?php endif; ?>
                        </td>
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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/kasir/data.blade.php ENDPATH**/ ?>