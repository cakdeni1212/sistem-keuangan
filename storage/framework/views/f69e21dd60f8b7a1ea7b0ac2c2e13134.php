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
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('hpp-products.index')); ?>"
               class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Edit HPP: <?php echo e($hppProduct->name); ?></h2>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto" x-data="{
            bahanBaku: <?php echo e($hppProduct->bahan_baku); ?>,
            tenagaKerja: <?php echo e($hppProduct->tenaga_kerja); ?>,
            overhead: <?php echo e($hppProduct->overhead); ?>,
            hargaJual: <?php echo e($hppProduct->harga_jual); ?>,
            ingredients: <?php echo e(Illuminate\Support\Js::from($existingIngredients)); ?>,
            rawMaterials: <?php echo e(Illuminate\Support\Js::from($rawMaterials)); ?>,
            addIngredient() {
                this.ingredients.push({ raw_material_id: '', quantity: 0, usage_unit: '' });
            },
            removeIngredient(index) {
                this.ingredients.splice(index, 1);
                this.updateBahanBaku();
            },
            getMaterial(id) {
                return this.rawMaterials.find(m => m.id == id) || null;
            },
            getUnit(id) {
                let m = this.getMaterial(id);
                return m ? m.unit : '';
            },
            availableUnits(id) {
                let m = this.getMaterial(id);
                if (!m) return [];
                if (m.unit === 'kg')    return [{v:'gram', l:'gram'}, {v:'kg', l:'kg'}];
                if (m.unit === 'liter') return [{v:'ml', l:'ml'}, {v:'liter', l:'liter'}];
                return [{v: m.unit, l: m.unit}];
            },
            onMaterialChange(item) {
                let units = this.availableUnits(item.raw_material_id);
                item.usage_unit = units.length > 0 ? units[0].v : '';
                this.updateBahanBaku();
            },
            conversionFactor(materialUnit, usageUnit) {
                if (materialUnit === 'kg'    && usageUnit === 'gram') return 1000;
                if (materialUnit === 'liter' && usageUnit === 'ml')   return 1000;
                return 1;
            },
            pricePerUsageUnit(item) {
                let m = this.getMaterial(item.raw_material_id);
                if (!m) return 0;
                let factor = this.conversionFactor(m.unit, item.usage_unit || m.unit);
                return m.price / factor;
            },
            ingredientCost(item) {
                return parseFloat(item.quantity||0) * this.pricePerUsageUnit(item);
            },
            get totalIngredientCost() {
                return this.ingredients.reduce((sum, i) => sum + this.ingredientCost(i), 0);
            },
            updateBahanBaku() {
                if (this.ingredients.length === 0) {
                    this.bahanBaku = 0;
                } else if (this.totalIngredientCost > 0) {
                    this.bahanBaku = this.totalIngredientCost;
                }
            },
            get hasIngredients() { return this.ingredients.length > 0; },
            get hpp() { return parseFloat(this.bahanBaku||0) + parseFloat(this.tenagaKerja||0) + parseFloat(this.overhead||0); },
            get margin() { return parseFloat(this.hargaJual||0) - this.hpp; },
            get marginPct() {
                return this.hpp > 0 ? (this.margin / this.hpp * 100) : 0;
            },
            fmt(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
        }">
            <form action="<?php echo e(route('hpp-products.update', $hppProduct)); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="card">

                    
                    <div class="p-6 space-y-4 border-b border-surface-100">
                        <h3 class="font-semibold text-surface-700 text-sm uppercase tracking-wide">Informasi Produk</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="<?php echo e(old('name', $hppProduct->name)); ?>" required
                                    class="w-full border border-surface-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">SKU</label>
                                <input type="text" name="sku" value="<?php echo e(old('sku', $hppProduct->sku)); ?>"
                                    class="w-full border border-surface-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                                    placeholder="Cth: MNM01">
                                <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Kategori</label>
                                <input type="text" name="category" value="<?php echo e(old('category', $hppProduct->category)); ?>"
                                    list="category-list"
                                    class="w-full border border-surface-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                                    placeholder="Pilih atau ketik kategori baru">
                                <datalist id="category-list">
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat); ?>">
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </datalist>
                                <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Satuan</label>
                                <input type="text" name="satuan" value="<?php echo e(old('satuan', $hppProduct->satuan)); ?>"
                                    class="w-full border border-surface-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                                    placeholder="Cth: Gelas, Porsi, Pcs">
                                <?php $__errorArgs = ['satuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Stok Minimum</label>
                                <input type="number" name="stok_minimum" value="<?php echo e(old('stok_minimum', $hppProduct->stok_minimum)); ?>" min="0"
                                    class="w-full border border-surface-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                                    placeholder="Cth: 10">
                                <?php $__errorArgs = ['stok_minimum'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="p-6 space-y-3 border-b border-surface-100">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-surface-700 text-sm uppercase tracking-wide">Komposisi Bahan Baku</h3>
                            <button type="button" @click="addIngredient()"
                                class="px-3 py-1.5 text-xs bg-brand-50 text-brand-700 hover:bg-brand-100 rounded-xl transition font-medium">
                                + Tambah Bahan
                            </button>
                        </div>
                        <p class="text-xs text-surface-400">Opsional. Jika diisi, biaya bahan baku akan dihitung otomatis dari komposisi.</p>

                        <template x-if="ingredients.length === 0">
                            <p class="text-sm text-surface-400 italic py-2">Belum ada bahan baku. Klik "+ Tambah Bahan" untuk menambahkan.</p>
                        </template>

                        <div class="space-y-2">
                            <template x-for="(item, index) in ingredients" :key="index">
                                <div class="flex items-center gap-2 bg-surface-50 rounded-xl p-3 flex-wrap">
                                    <div class="flex-1 min-w-40">
                                        <select :name="'ingredients['+index+'][raw_material_id]'"
                                            x-model="item.raw_material_id"
                                            @change="onMaterialChange(item)"
                                            class="w-full input-field">
                                            <option value="">-- Pilih Bahan --</option>
                                            <template x-for="m in rawMaterials" :key="m.id">
                                                <option :value="m.id" :selected="item.raw_material_id == m.id" x-text="m.name + ' (' + m.unit + ')'"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="w-28">
                                        <input type="number" :name="'ingredients['+index+'][quantity]'"
                                            x-model="item.quantity"
                                            @input="updateBahanBaku()"
                                            min="0" step="0.001"
                                            placeholder="Jumlah"
                                            class="w-full border border-surface-300 rounded-xl px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    </div>
                                    <div class="w-24">
                                        <select :name="'ingredients['+index+'][usage_unit]'"
                                            x-model="item.usage_unit"
                                            @change="updateBahanBaku()"
                                            class="w-full input-field bg-white">
                                            <template x-for="u in availableUnits(item.raw_material_id)" :key="u.v">
                                                <option :value="u.v" :selected="item.usage_unit == u.v" x-text="u.l"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="w-32 text-xs text-surface-500 text-center"
                                         x-show="item.raw_material_id"
                                         x-text="'Rp ' + Number(pricePerUsageUnit(item)).toLocaleString('id-ID') + '/' + (item.usage_unit || getUnit(item.raw_material_id))">
                                    </div>
                                    <div class="w-28 text-xs text-right text-brand-700 font-medium" x-text="fmt(ingredientCost(item))"></div>
                                    <button type="button" @click="removeIngredient(index)"
                                        class="text-red-400 hover:text-red-600 text-lg leading-none transition">×</button>
                                </div>
                            </template>
                        </div>

                        <template x-if="ingredients.length > 0">
                            <div class="flex justify-end pt-1">
                                <span class="text-sm font-semibold text-surface-700">Total Bahan Baku: <span class="text-brand-700" x-text="fmt(totalIngredientCost)"></span></span>
                            </div>
                        </template>
                    </div>

                    
                    <div class="p-6 space-y-4 border-b border-surface-100">
                        <h3 class="font-semibold text-surface-700 text-sm uppercase tracking-wide">Komponen Biaya (per porsi)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Bahan Baku <span class="text-red-500" x-show="!hasIngredients">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-surface-500 text-sm">Rp</span>
                                    <input type="number" name="bahan_baku" value="<?php echo e(old('bahan_baku', $hppProduct->bahan_baku)); ?>"
                                        min="0" step="any" x-model="bahanBaku"
                                        :readonly="hasIngredients && totalIngredientCost > 0"
                                        :class="hasIngredients && totalIngredientCost > 0 ? 'bg-surface-100 cursor-not-allowed' : ''"
                                        class="w-full border border-surface-300 rounded-xl pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                                <p class="text-xs text-surface-400 mt-1" x-show="hasIngredients && totalIngredientCost > 0">Dihitung otomatis dari komposisi.</p>
                                <?php $__errorArgs = ['bahan_baku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Tenaga Kerja <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-surface-500 text-sm">Rp</span>
                                    <input type="number" name="tenaga_kerja" value="<?php echo e(old('tenaga_kerja', $hppProduct->tenaga_kerja)); ?>"
                                        min="0" step="any" x-model="tenagaKerja"
                                        class="w-full border border-surface-300 rounded-xl pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                                <?php $__errorArgs = ['tenaga_kerja'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Overhead <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-surface-500 text-sm">Rp</span>
                                    <input type="number" name="overhead" value="<?php echo e(old('overhead', $hppProduct->overhead)); ?>"
                                        min="0" step="any" x-model="overhead"
                                        class="w-full border border-surface-300 rounded-xl pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                                <?php $__errorArgs = ['overhead'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="md:w-1/2">
                            <label class="block text-sm font-medium text-surface-700 mb-1">Harga Jual <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-surface-500 text-sm">Rp</span>
                                <input type="number" name="harga_jual" value="<?php echo e(old('harga_jual', $hppProduct->harga_jual)); ?>"
                                    min="0" step="any" x-model="hargaJual"
                                    class="w-full border border-surface-300 rounded-xl pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                            <?php $__errorArgs = ['harga_jual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    
                    <div class="p-6 table-th border-surface-100">
                        <h3 class="font-semibold text-surface-700 text-sm uppercase tracking-wide mb-4">Preview Kalkulasi</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="bg-white rounded-xl border p-3 text-center">
                                <p class="text-xs text-surface-500 mb-1">Total HPP</p>
                                <p class="font-bold text-surface-900 text-sm" x-text="fmt(hpp)"></p>
                            </div>
                            <div class="bg-white rounded-xl border p-3 text-center">
                                <p class="text-xs text-surface-500 mb-1">Harga Jual</p>
                                <p class="font-bold text-brand-700 text-sm" x-text="fmt(hargaJual)"></p>
                            </div>
                            <div class="bg-white rounded-xl border p-3 text-center">
                                <p class="text-xs text-surface-500 mb-1">Laba / Porsi</p>
                                <p class="font-bold text-sm"
                                   :class="margin >= 0 ? 'text-green-700' : 'text-red-700'"
                                   x-text="fmt(margin)"></p>
                            </div>
                            <div class="rounded-xl border p-3 text-center"
                                 :class="marginPct >= 100 ? 'bg-green-50 border-green-300' : (marginPct >= 50 ? 'bg-yellow-50 border-yellow-300' : 'bg-red-50 border-red-300')">
                                <p class="text-xs text-surface-500 mb-1">Markup</p>
                                <p class="font-bold text-lg"
                                   :class="marginPct >= 100 ? 'text-green-700' : (marginPct >= 50 ? 'text-yellow-700' : 'text-red-700')"
                                   x-text="marginPct.toFixed(1) + '%'"></p>
                            </div>
                        </div>
                    </div>

                    
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Catatan</label>
                            <textarea name="notes" rows="2"
                                class="w-full input-field !resize-y"
                                placeholder="Catatan tambahan..."><?php echo e(old('notes', $hppProduct->notes)); ?></textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                <?php echo e(old('is_active', $hppProduct->is_active) ? 'checked' : ''); ?>

                                class="rounded text-brand-600">
                            <label for="is_active" class="text-sm text-surface-700">Produk Aktif</label>
                        </div>
                    </div>

                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="btn-primary">
                        Perbarui Produk
                    </button>
                    <a href="<?php echo e(route('hpp-products.index')); ?>"
                        class="px-6 py-2 bg-white border border-surface-300 text-surface-700 rounded-xl hover:bg-surface-50 font-medium text-sm transition">
                        Batal
                    </a>
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
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/hpp-products/edit.blade.php ENDPATH**/ ?>