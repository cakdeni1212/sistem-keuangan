<?php if (isset($component)) { $__componentOriginala43c55a8790e4db80b5b367d2c18853d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala43c55a8790e4db80b5b367d2c18853d = $attributes; } ?>
<?php $component = App\View\Components\KasirLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kasir-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\KasirLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

<style>
input.qty-input::-webkit-outer-spin-button,
input.qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
input.qty-input { -moz-appearance: textfield; }
</style>

    <div class="py-4 px-4 sm:px-6 lg:px-8" x-data="kasirApp()" x-cloak>

        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
            <div class="bg-white rounded-xl border shadow-sm p-3 text-center">
                <p class="text-xs text-gray-400">Transaksi Hari Ini</p>
                <p class="text-xl font-extrabold text-gray-800"><?php echo e($todayTxCount); ?></p>
            </div>
            <div class="bg-white rounded-xl border shadow-sm p-3 text-center">
                <p class="text-xs text-gray-400">Omset Hari Ini</p>
                <p class="text-lg font-extrabold text-indigo-700">Rp <?php echo e(number_format($todayTotal, 0, ',', '.')); ?></p>
            </div>
            <div class="bg-white rounded-xl border shadow-sm p-3 text-center">
                <p class="text-xs text-gray-400">Produk Aktif</p>
                <p class="text-xl font-extrabold text-green-700"><?php echo e($products->count()); ?></p>
            </div>
            
            <div class="bg-white rounded-xl border shadow-sm p-3 space-y-2">
                <div>
                    <p class="text-xs text-gray-400 mb-1">📅 Tanggal</p>
                    <input type="date" x-model="transactionDate" :max="todayDate"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <p x-show="transactionDate !== todayDate" class="text-xs text-amber-600 mt-0.5 font-medium">⚠️ Data historis</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">🕐 Shift</p>
                    <div class="grid grid-cols-2 gap-1.5">
                        <button type="button" @click="shift = 'pagi'"
                            :class="shift === 'pagi' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-600 border-gray-200 hover:border-amber-400'"
                            class="py-1.5 rounded-lg border text-xs font-bold transition">
                            🌤 Pagi
                        </button>
                        <button type="button" @click="shift = 'sore'"
                            :class="shift === 'sore' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-400'"
                            class="py-1.5 rounded-lg border text-xs font-bold transition">
                            🌆 Sore
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="flex flex-col lg:flex-row gap-4">

            
            <div class="flex-1 min-w-0">
                
                <div class="bg-white rounded-xl border shadow-sm p-3 mb-3 space-y-2">
                    <input type="text" x-model="search" placeholder="🔍 Cari menu..."
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <div class="flex gap-1.5 flex-wrap">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button"
                            @click="activeCategory = '<?php echo e($cat); ?>'"
                            :class="activeCategory === '<?php echo e($cat); ?>' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            class="px-3 py-1 rounded-full text-xs font-medium transition">
                            <?php echo e($cat); ?>

                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <?php if($products->isEmpty()): ?>
                <div class="bg-white rounded-xl border shadow-sm p-12 text-center text-gray-400">
                    <p class="text-4xl mb-3">🍽️</p>
                    <p class="text-sm">Belum ada produk aktif.</p>
                    <a href="<?php echo e(route('hpp-produk.create')); ?>" class="mt-2 inline-block text-xs text-indigo-600 hover:underline">+ Tambah produk HPP</a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button"
                        x-show="matchesFilter(<?php echo e(Illuminate\Support\Js::from(['id'=>$product->id,'name'=>$product->name,'category'=>$product->category??'','price'=>(float)$product->harga_jual])); ?>)"
                        @click="addToCart(<?php echo e(Illuminate\Support\Js::from(['id'=>$product->id,'name'=>$product->name,'category'=>$product->category??'','price'=>(float)$product->harga_jual])); ?>)"
                        class="bg-white rounded-xl border shadow-sm p-3 text-left hover:border-indigo-400 hover:shadow-md transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-indigo-400 relative">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-xl mb-2 mx-auto">
                            <?php echo e(in_array(strtolower($product->category??''), ['minuman','drink']) ? '☕' : (in_array(strtolower($product->category??''), ['makanan','food']) ? '🍽️' : '🛒')); ?>

                        </div>
                        <p class="text-xs font-semibold text-gray-800 text-center leading-tight truncate"><?php echo e($product->name); ?></p>
                        <?php if($product->category): ?>
                        <p class="text-xs text-gray-400 text-center mt-0.5"><?php echo e($product->category); ?></p>
                        <?php endif; ?>
                        <p class="text-sm font-extrabold text-indigo-700 text-center mt-1">
                            Rp <?php echo e(number_format($product->harga_jual, 0, ',', '.')); ?>

                        </p>
                        
                        <template x-if="cartQty(<?php echo e($product->id); ?>) > 0">
                            <span class="absolute top-2 right-2 w-5 h-5 rounded-full bg-indigo-600 text-white text-xs flex items-center justify-center font-bold"
                                  x-text="cartQty(<?php echo e($product->id); ?>)"></span>
                        </template>
                    </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="w-full lg:w-80 flex-shrink-0">
                <div class="bg-white rounded-xl border shadow-sm sticky top-4">
                    <div class="px-4 py-3 border-b flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-700">🛒 Keranjang</h3>
                        <button type="button" @click="clearCart()" x-show="cart.length > 0"
                                class="text-xs text-red-400 hover:text-red-600 transition">Kosongkan</button>
                    </div>

                    
                    <div x-show="cart.length === 0" class="py-12 text-center text-gray-300">
                        <p class="text-4xl mb-2">🛒</p>
                        <p class="text-sm">Keranjang kosong</p>
                        <p class="text-xs mt-1">Klik produk untuk menambah</p>
                    </div>

                    
                    <div x-show="cart.length > 0" class="max-h-64 lg:max-h-96 overflow-y-auto divide-y divide-gray-100">
                        <template x-for="(item, idx) in cart" :key="item.id">
                            <div class="flex items-center gap-2 px-4 py-2.5">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-800 truncate" x-text="item.name"></p>
                                    <p class="text-xs text-indigo-600" x-text="fmtRp(item.price)"></p>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button type="button" @click="decQty(idx)"
                                            class="w-6 h-6 rounded-full bg-gray-100 text-gray-600 text-sm font-bold hover:bg-red-50 hover:text-red-600 transition flex items-center justify-center">−</button>
                                    <input type="number" min="1"
                                           x-model.number="item.qty"
                                           @blur="if(!item.qty || item.qty < 1) cart.splice(idx,1)"
                                           @focus="$event.target.select()"
                                           class="qty-input w-10 text-center text-sm font-bold text-gray-800 border border-gray-200 rounded-md py-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                    <button type="button" @click="incQty(idx)"
                                            class="w-6 h-6 rounded-full bg-gray-100 text-gray-600 text-sm font-bold hover:bg-green-50 hover:text-green-600 transition flex items-center justify-center">+</button>
                                </div>
                                <div class="text-right flex-shrink-0 w-20">
                                    <p class="text-xs font-bold text-gray-700" x-text="fmtRp(item.price * item.qty)"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    
                    <div x-show="cart.length > 0" class="px-4 py-3 border-t bg-gray-50 rounded-b-xl space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Total</span>
                                <span class="ml-2 text-xs text-gray-400">(<span x-text="cartTotalQty"></span> item)</span>
                            </div>
                            <span class="text-xl font-extrabold text-gray-900" x-text="fmtRp(cartTotal)"></span>
                        </div>
                        <button type="button" @click="openCheckout()"
                                class="w-full py-3 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 active:scale-95 transition shadow">
                            ✓ Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="checkoutOpen" x-cloak
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.outside="checkoutOpen = false">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">💳 Pembayaran</h3>
                    <button type="button" @click="checkoutOpen = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                
                <div class="px-5 py-3 max-h-48 overflow-y-auto divide-y divide-gray-100">
                    <template x-for="item in cart" :key="item.id">
                        <div class="flex justify-between py-2 text-sm">
                            <span class="text-gray-700" x-text="item.name + ' ×' + item.qty"></span>
                            <span class="font-medium text-gray-800" x-text="fmtRp(item.price * item.qty)"></span>
                        </div>
                    </template>
                </div>

                <div class="px-5 py-3 bg-indigo-50 border-t border-b flex justify-between items-center">
                    <span class="font-bold text-gray-700">Total Bayar</span>
                    <span class="text-2xl font-extrabold text-indigo-700" x-text="fmtRp(cartTotal)"></span>
                </div>

                <div class="px-5 py-4 space-y-4">
                    
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-2">Metode Pembayaran</p>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="paymentMethod = 'tunai'"
                                :class="paymentMethod === 'tunai' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-300 hover:border-green-400'"
                                class="py-3 rounded-xl border-2 font-semibold text-sm transition">
                                💵 Tunai
                            </button>
                            <button type="button" @click="paymentMethod = 'qris'"
                                :class="paymentMethod === 'qris' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-600 border-gray-300 hover:border-purple-400'"
                                class="py-3 rounded-xl border-2 font-semibold text-sm transition">
                                📱 QRIS
                            </button>
                        </div>
                    </div>

                    
                    <div x-show="paymentMethod === 'tunai'" class="space-y-2">
                        <label class="text-xs font-semibold text-gray-500">Uang Diterima</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                            <input type="text" x-model="cashReceived" inputmode="numeric"
                                   @input="cashReceived = $event.target.value.replace(/[^0-9]/g,'')"
                                   placeholder="0"
                                   class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                        <div class="flex gap-1.5 flex-wrap">
                            <template x-for="n in quickCash" :key="n">
                                <button type="button" @click="cashReceived = String(n)"
                                    class="px-2.5 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-700 transition" x-text="fmtRp(n)"></button>
                            </template>
                        </div>
                        <div x-show="cashReceived_num >= cartTotal"
                             class="flex justify-between items-center px-3 py-2 bg-green-50 rounded-lg border border-green-200">
                            <span class="text-sm text-green-700 font-medium">Kembalian</span>
                            <span class="text-lg font-extrabold text-green-700" x-text="fmtRp(kembalian)"></span>
                        </div>
                        <div x-show="cashReceived_num > 0 && cashReceived_num < cartTotal"
                             class="px-3 py-2 bg-red-50 rounded-lg border border-red-200 text-sm text-red-600 text-center">
                            Kurang <span class="font-bold" x-text="fmtRp(cartTotal - cashReceived_num)"></span>
                        </div>
                    </div>

                    
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Catatan (opsional)</label>
                        <input type="text" x-model="checkoutNotes" placeholder="misal: no. meja, nama pelanggan..."
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>

                    <button type="button" @click="submitCheckout()"
                            :disabled="submitting || (paymentMethod === 'tunai' && cashReceived_num < cartTotal)"
                            :class="(submitting || (paymentMethod === 'tunai' && cashReceived_num < cartTotal)) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                            class="w-full py-3 bg-indigo-600 text-white font-bold text-sm rounded-xl transition shadow">
                        <span x-show="!submitting">✓ Konfirmasi Pembayaran</span>
                        <span x-show="submitting">⏳ Memproses...</span>
                    </button>
                </div>
            </div>
        </div>

        
        <div x-show="successOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs text-center p-8">
                <div class="text-6xl mb-4">✅</div>
                <h3 class="text-lg font-extrabold text-gray-800 mb-2">Transaksi Berhasil!</h3>
                <p class="text-sm text-gray-500 mb-1" x-text="'Total: ' + fmtRp(lastTotal)"></p>
                <p class="text-sm text-gray-500 mb-4" x-show="lastKembalian > 0" x-text="'Kembalian: ' + fmtRp(lastKembalian)"></p>
                <p class="text-xs text-indigo-500 mb-5">Stok bahan baku sudah diperbarui otomatis</p>
                <button type="button" @click="successOpen = false"
                        class="w-full py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                    Transaksi Berikutnya
                </button>
            </div>
        </div>

    </div>

<script>
function kasirApp() {
    const today = new Date().toISOString().split('T')[0];
    return {
        cart: [],
        search: '',
        activeCategory: 'Semua',
        checkoutOpen: false,
        successOpen: false,
        paymentMethod: 'tunai',
        cashReceived: '',
        checkoutNotes: '',
        transactionDate: today,
        todayDate: today,
        shift: 'pagi',
        submitting: false,
        lastTotal: 0,
        lastKembalian: 0,
        quickCash: [5000, 10000, 20000, 50000, 100000],

        get cartTotal() {
            return this.cart.reduce((s, i) => s + i.price * i.qty, 0);
        },
        get cartTotalQty() {
            return this.cart.reduce((s, i) => s + i.qty, 0);
        },
        get cashReceived_num() {
            return parseInt(String(this.cashReceived).replace(/[^0-9]/g, '')) || 0;
        },
        get kembalian() {
            return Math.max(0, this.cashReceived_num - this.cartTotal);
        },

        fmtRp(n) {
            return 'Rp\u00a0' + Math.round(n).toLocaleString('id-ID');
        },

        formatDate(d) {
            if (!d) return '';
            const [y, m, day] = d.split('-');
            const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            return `${parseInt(day)} ${months[parseInt(m)-1]} ${y}`;
        },

        matchesFilter(p) {
            const cat  = this.activeCategory === 'Semua' || p.category === this.activeCategory;
            const srch = !this.search || p.name.toLowerCase().includes(this.search.toLowerCase());
            return cat && srch;
        },

        cartQty(id) {
            const item = this.cart.find(i => i.id === id);
            return item ? item.qty : 0;
        },

        addToCart(product) {
            const idx = this.cart.findIndex(i => i.id === product.id);
            if (idx >= 0) {
                this.cart[idx].qty++;
            } else {
                this.cart.push({ ...product, qty: 1 });
            }
        },

        incQty(idx) { this.cart[idx].qty++; },
        decQty(idx) {
            if (this.cart[idx].qty > 1) {
                this.cart[idx].qty--;
            } else {
                this.cart.splice(idx, 1);
            }
        },
        setQty(idx, val) {
            const n = parseInt(val);
            if (n >= 1) {
                this.cart[idx].qty = n;
            } else {
                this.cart.splice(idx, 1);
            }
        },
        clearCart() { this.cart = []; },

        openCheckout() {
            this.checkoutOpen = true;
            this.cashReceived = '';
            this.checkoutNotes = '';
            this.transactionDate = this.todayDate;
            // Pre-fill quick cash options based on total
            const t = this.cartTotal;
            this.quickCash = [
                Math.ceil(t / 5000) * 5000,
                Math.ceil(t / 10000) * 10000,
                Math.ceil(t / 50000) * 50000,
                100000,
                200000,
            ].filter((v, i, a) => v >= t && a.indexOf(v) === i).slice(0, 5);
        },

        async submitCheckout() {
            if (this.submitting) return;
            this.submitting = true;
            try {
                const res = await fetch('<?php echo e(route("kasir.checkout")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        items: this.cart.map(i => ({ id: i.id, qty: i.qty })),
                        payment_method: this.paymentMethod,
                        shift: this.shift,
                        notes: this.checkoutNotes,
                        transaction_date: this.transactionDate,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.lastTotal     = this.cartTotal;
                    this.lastKembalian = this.paymentMethod === 'tunai' ? this.kembalian : 0;
                    this.checkoutOpen  = false;
                    this.successOpen   = true;
                    this.clearCart();
                    // Reload page setelah 3 detik untuk update top bar
                    setTimeout(() => { if (!this.successOpen) window.location.reload(); }, 3000);
                } else {
                    alert('Terjadi kesalahan: ' + (data.message ?? 'Unknown error'));
                }
            } catch (e) {
                alert('Gagal mengirim transaksi. Coba lagi.');
            } finally {
                this.submitting = false;
            }
        },
    };
}
</script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala43c55a8790e4db80b5b367d2c18853d)): ?>
<?php $attributes = $__attributesOriginala43c55a8790e4db80b5b367d2c18853d; ?>
<?php unset($__attributesOriginala43c55a8790e4db80b5b367d2c18853d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala43c55a8790e4db80b5b367d2c18853d)): ?>
<?php $component = $__componentOriginala43c55a8790e4db80b5b367d2c18853d; ?>
<?php unset($__componentOriginala43c55a8790e4db80b5b367d2c18853d); ?>
<?php endif; ?>
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/kasir/index.blade.php ENDPATH**/ ?>