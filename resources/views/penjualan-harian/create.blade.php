<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('penjualan-harian.index') }}"
                   class="btn-secondary btn-sm">
                    ← Kembali
                </a>
                <div>
                    <h1 class="page-title">
                        {{ $hasData ? '✏️ Edit Penjualan' : '➕ Input Penjualan' }}
                    </h1>
                    <p class="text-xs text-surface-500">Masukkan jumlah terjual per produk</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8"
         x-data="penjualanForm({{ $products->map(fn($p) => [
             'id'    => $p->id,
             'price' => (float) $p->harga_jual,
             'hpp'   => (float) ($p->bahan_baku + $p->tenaga_kerja + $p->overhead),
             'qty'   => (int) ($existing[$p->id] ?? 0),
         ])->values()->toJson() }}, '{{ $shift }}')">

        {{-- Warning: data sudah ada --}}
        @if($hasData)
        <div class="mb-5 alert-warning flex gap-3">
            <div class="text-amber-500 text-xl flex-shrink-0">⚠️</div>
            <div class="flex-1">
                <p class="font-semibold text-amber-800 text-sm">Data sudah ada untuk tanggal ini!</p>
                <p class="text-amber-700 text-xs mt-0.5">
                    <strong>{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</strong>
                    &nbsp;·&nbsp;
                    {{ $existingCount }} produk
                    &nbsp;·&nbsp;
                    Total: <strong>Rp {{ number_format($existingTotal, 0, ',', '.') }}</strong>
                </p>
                <p class="text-amber-600 text-xs mt-1">Jika disimpan, data lama akan <strong>digantikan</strong> dengan data baru.</p>
            </div>
            <div class="flex-shrink-0 flex flex-col gap-1.5 self-start">
                <a href="{{ route('penjualan-harian.show', [$date, 'pagi']) }}"
                   class="text-xs font-medium text-amber-700 underline hover:text-amber-900">
                    Lihat data →
                </a>
                <form method="POST" action="{{ route('penjualan-harian.destroy', [$date, 'pagi']) }}"
                      onsubmit="return confirm('Hapus semua data {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}? Tindakan ini tidak bisa dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-xs font-medium text-red-600 underline hover:text-red-800">
                        🗑 Hapus data
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Notifikasi konflik --}}
        <div x-show="conflictNotice" x-cloak
             class="mb-5 bg-red-50 border border-red-300 rounded-xl p-4 flex gap-3 items-start">
            <span class="text-red-500 text-xl flex-shrink-0">⚠️</span>
            <div class="flex-1 text-sm text-red-800">
                <p class="font-semibold">Data sudah ada di tanggal yang dituju!</p>
                <p class="mt-1 text-red-700">
                    Tanggal <span x-text="conflictNotice?.date"></span> sudah memiliki data penjualan.
                    Menyimpan akan <strong>menimpa</strong> data tersebut.
                </p>
                <div class="flex gap-2 mt-3">
                    <button type="button" @click="confirmOverwrite()"
                            class="px-4 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition">
                        Ya, Timpa & Simpan
                    </button>
                    <button type="button" @click="conflictNotice = null"
                            class="px-4 py-1.5 bg-white border border-surface-300 text-surface-700 text-xs font-medium rounded-lg hover:bg-surface-50 transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('penjualan-harian.store') }}" id="penjualan-form" @submit.prevent="prepareSubmit">
            @csrf

            <input type="hidden" name="original_date" value="{{ $date }}">
            <input type="hidden" name="original_shift" value="pagi">
            <input type="hidden" name="shift" value="pagi">

            {{-- Tanggal --}}
            <div class="stat-card mb-5">
                <div>
                    <label class="block text-sm font-semibold text-surface-700 mb-2">📅 Tanggal Penjualan</label>
                    <input type="date" name="sale_date" id="sale_date"
                           value="{{ old('sale_date', $date) }}"
                           max="{{ today()->toDateString() }}"
                           required
                           class="border-surface-300 rounded-lg text-sm py-2 px-3 focus:ring-brand-500">
                </div>
                @if($fromKasir)
                <p class="mt-3 text-xs text-blue-600 font-medium bg-blue-50 rounded-lg px-3 py-2">
                    🧾 Data diambil dari Kasir POS. Periksa dan simpan untuk menyimpan ke Penjualan Harian.
                </p>
                @elseif($hasData)
                <p class="mt-3 text-xs text-amber-600 font-medium">
                    ⚠️ Sudah ada data untuk tanggal ini. Simpan akan menimpa data lama.
                </p>
                @endif
            </div>

            {{-- Ringkasan live --}}
            <div class="grid grid-cols-3 gap-4 mb-5">
                <div class="bg-brand-50 border border-brand-100 rounded-xl p-4">
                    <p class="text-xs text-brand-500 font-medium uppercase">Total Terjual</p>
                    <p class="text-xl font-extrabold text-brand-700 mt-1" x-text="totalQty + ' pcs'"></p>
                </div>
                <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                    <p class="text-xs text-green-500 font-medium uppercase">Omset</p>
                    <p class="text-xl font-extrabold text-green-700 mt-1" x-text="'Rp ' + fmt(totalOmset)"></p>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4">
                    <p class="text-xs text-emerald-500 font-medium uppercase">Keuntungan</p>
                    <p class="text-xl font-extrabold text-emerald-700 mt-1" x-text="'Rp ' + fmt(totalProfit)"></p>
                </div>
            </div>

            {{-- Semua Produk --}}
            <div class="card mb-4 overflow-hidden">
                <div class="divide-y divide-surface-100">
                    @foreach($products as $product)
                    <div class="px-5 py-3 flex items-center gap-4"
                         x-data="{ get item() { return items.find(i => i.id === {{ $product->id }}) } }">

                        {{-- Nama + harga --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-surface-800 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-surface-400">
                                Rp {{ number_format($product->harga_jual, 0, ',', '.') }} / pcs
                                @if($product->bahan_baku + $product->tenaga_kerja + $product->overhead > 0)
                                · HPP Rp {{ number_format($product->bahan_baku + $product->tenaga_kerja + $product->overhead, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>

                        {{-- Subtotal live --}}
                        <div class="text-right hidden sm:block w-32">
                            <p class="text-xs text-surface-400">Subtotal</p>
                            <p class="text-sm font-semibold text-surface-700"
                               x-text="item.qty > 0 ? 'Rp ' + fmt(item.qty * item.price) : '—'"></p>
                        </div>

                        {{-- Qty input --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button"
                                    @click="if(item.qty > 0) { item.qty--; recalc() }"
                                    class="w-8 h-8 rounded-lg bg-surface-100 hover:bg-surface-200 text-surface-600 font-bold text-lg flex items-center justify-center transition">
                                −
                            </button>
                            <input type="number"
                                   :name="'items[{{ $product->id }}][qty]'"
                                   x-model.number="item.qty"
                                   @input="recalc()"
                                   @focus="$event.target.select()"
                                   min="0"
                                   placeholder="0"
                                   class="w-16 text-center border border-surface-300 rounded-lg py-1.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500 qty-input">
                            <button type="button"
                                    @click="item.qty++; recalc()"
                                    class="w-8 h-8 rounded-lg bg-brand-100 hover:bg-brand-200 text-brand-600 font-bold text-lg flex items-center justify-center transition">
                                +
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between stat-card">
                <div>
                    <p class="text-sm text-surface-500">
                        <span x-text="activeCount"></span> produk diisi
                        · Omset <span class="font-semibold text-surface-800" x-text="'Rp ' + fmt(totalOmset)"></span>
                    </p>
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition">
                    💾 Simpan Penjualan
                </button>
            </div>

            {{-- Spacer agar konten tidak tertutup floating bar --}}
            <div class="h-16"></div>

        </form>
    </div>

<style>
.qty-input::-webkit-inner-spin-button,
.qty-input::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
.qty-input { -moz-appearance: textfield; }
</style>

<script>
function penjualanForm(productData, initialShift) {
    return {
        items: productData,
        shift: initialShift || 'pagi',
        totalQty: 0,
        totalOmset: 0,
        totalProfit: 0,
        activeCount: 0,
        conflictNotice: null,   // { date, shift } jika ada konflik
        submitting: false,

        init() {
            this.recalc();
        },

        recalc() {
            let qty = 0, omset = 0, profit = 0, active = 0;
            this.items.forEach(item => {
                const q = parseInt(item.qty) || 0;
                item.qty = q;
                if (q > 0) {
                    qty    += q;
                    omset  += q * item.price;
                    profit += q * (item.price - item.hpp);
                    active++;
                }
            });
            this.totalQty    = qty;
            this.totalOmset  = omset;
            this.totalProfit = profit;
            this.activeCount = active;
        },

        async prepareSubmit() {
            this.recalc();
            this.conflictNotice = null;

            const formEl   = document.getElementById('penjualan-form');
            const date     = document.getElementById('sale_date').value;
            const origDate = formEl.querySelector('[name=original_date]').value;
            const origShift= formEl.querySelector('[name=original_shift]').value;

            // Kalau tanggal / shift berubah, cek konflik di server
            if (date !== origDate || this.shift !== origShift) {
                try {
                    const resp = await fetch(`{{ route('penjualan-harian.check') }}?date=${date}&shift=${this.shift}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const json = await resp.json();
                    if (json.exists) {
                        // Ada konflik — tampilkan notifikasi, tunggu konfirmasi user
                        this.conflictNotice = { date, shift: this.shift };
                        return; // jangan submit dulu
                    }
                } catch (e) {
                    // Jika fetch gagal, biarkan lanjut submit
                }
            }

            formEl.submit();
        },

        confirmOverwrite() {
            this.conflictNotice = null;
            document.getElementById('penjualan-form').submit();
        },

        fmt(n) {
            return Math.round(n).toLocaleString('id-ID');
        },
    };
}
</script>

</x-app-layout>
