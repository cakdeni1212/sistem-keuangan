<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="page-title">🧮 Kalkulator Keuntungan</h2>
            <p class="text-xs text-surface-500 mt-0.5">Hitung harga jual, keuntungan, dan simulasi penjualan</p>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-6"
         x-data="{
            hpp: '',
            marginPct: '',
            hargaJual: '',
            qty: '',

            get hpp_num()       { return parseFloat(String(this.hpp).replace(/[^0-9]/g,'')) || 0 },
            get margin_num()    { return parseFloat(String(this.marginPct).replace(',','.')) || 0 },
            get hargaJual_num() { return parseFloat(String(this.hargaJual).replace(/[^0-9]/g,'')) || 0 },
            get qty_num()       { return parseInt(String(this.qty).replace(/[^0-9]/g,'')) || 0 },

            // ① HPP → Harga Jual (markup: HPP × (1 + margin%))
            get calcHargaJual() {
                if (!this.hpp_num || this.margin_num <= 0) return 0;
                return this.hpp_num * (1 + this.margin_num / 100);
            },
            get calcUntung1() {
                return this.calcHargaJual ? this.calcHargaJual - this.hpp_num : 0;
            },
            get hasResult1() {
                return this.hpp_num > 0 && this.margin_num > 0;
            },

            // ② Harga Jual → Keuntungan
            get calcUntung2() {
                if (!this.hpp_num || !this.hargaJual_num) return 0;
                return this.hargaJual_num - this.hpp_num;
            },
            get calcMargin2() {
                if (!this.hpp_num || !this.hargaJual_num || this.hargaJual_num <= this.hpp_num) return 0;
                return ((this.hargaJual_num - this.hpp_num) / this.hpp_num) * 100;
            },
            get hasResult2() { return this.hpp_num > 0 && this.hargaJual_num > 0 },
            get isRugi()     { return this.hasResult2 && this.hargaJual_num <= this.hpp_num },

            // Simulasi
            get simHarga()  { return this.hargaJual_num > 0 ? this.hargaJual_num : this.calcHargaJual },
            get simOmset()  { return this.simHarga > 0 && this.qty_num > 0 ? this.simHarga * this.qty_num : 0 },
            get simModal()  { return this.hpp_num > 0 && this.qty_num > 0 ? this.hpp_num * this.qty_num : 0 },
            get simUntung() { return this.simOmset > 0 ? this.simOmset - this.simModal : 0 },
            get hasSim()    { return this.qty_num > 0 && this.simHarga > 0 && this.hpp_num > 0 },

            fmt(n) {
                if (!n && n !== 0) return '—';
                return 'Rp\u00a0' + Math.round(n).toLocaleString('id-ID');
            },
            badgeCls(pct) {
                if (!pct || pct <= 0) return 'bg-surface-100 text-surface-500';
                if (pct >= 100) return 'bg-green-100 text-green-700';
                if (pct >= 50)  return 'bg-yellow-100 text-yellow-700';
                return 'bg-red-100 text-red-700';
            }
         }">

        {{-- ===== ROW 1: Input + Hasil (2 kolom) ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">

            {{-- INPUT BERSAMA --}}
            <div class="card p-5 space-y-4">
                <h3 class="text-sm font-bold text-surface-700">Input Data</h3>

                <div>
                    <label class="block text-xs font-medium text-surface-600 mb-1">Total HPP</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-sm">Rp</span>
                        <input type="text" x-model="hpp" inputmode="numeric"
                            @input="hpp = $event.target.value.replace(/[^0-9]/g,'')"
                            placeholder="contoh: 15000"
                            class="w-full pl-9 pr-3 py-2.5 border border-surface-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-surface-600 mb-1">Target Margin (Markup)</label>
                    <div class="relative">
                        <input type="number" x-model="marginPct" min="1" max="200" step="0.5"
                            placeholder="contoh: 100"
                            class="w-full pl-3 pr-9 py-2.5 border border-surface-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-surface-400 text-sm">%</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <template x-for="m in [30, 50, 75, 100, 125, 150, 200]" :key="m">
                            <button @click="marginPct = m"
                                :class="margin_num === m ? 'bg-brand-600 text-white' : 'bg-surface-100 text-surface-600 hover:bg-surface-200'"
                                class="px-2.5 py-1 rounded-md text-xs font-medium transition"
                                x-text="m + '%'"></button>
                        </template>
                    </div>
                    <p class="text-xs text-surface-400 mt-1.5">Markup = keuntungan ÷ HPP × 100. Contoh: markup 100% → jual 2× HPP</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-surface-600 mb-1">Harga Jual</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 text-sm">Rp</span>
                        <input type="text" x-model="hargaJual" inputmode="numeric"
                            @input="hargaJual = $event.target.value.replace(/[^0-9]/g,'')"
                            placeholder="contoh: 25000"
                            class="w-full pl-9 pr-3 py-2.5 border border-surface-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-surface-600 mb-1">
                        Simulasi Penjualan
                        <span class="text-surface-400 font-normal">(opsional)</span>
                    </label>
                    <div class="relative">
                        <input type="text" x-model="qty" inputmode="numeric"
                            @input="qty = $event.target.value.replace(/[^0-9]/g,'')"
                            placeholder="jumlah unit"
                            class="w-full pl-3 pr-12 py-2.5 border border-surface-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-surface-400 text-xs">unit</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <template x-for="q in [10, 25, 50, 100, 200]" :key="q">
                            <button @click="qty = q"
                                :class="qty_num === q ? 'bg-brand-600 text-white' : 'bg-surface-100 text-surface-600 hover:bg-surface-200'"
                                class="px-2.5 py-1 rounded-md text-xs font-medium transition"
                                x-text="q + ' unit'"></button>
                        </template>
                    </div>
                </div>

                @if($products->count() > 0)
                <div>
                    <label class="block text-xs text-surface-400 mb-1">Atau pilih dari produk:</label>
                    <select @change="hpp = $event.target.value; $event.target.value = ''"
                        class="w-full px-3 py-2 border border-surface-200 rounded-lg text-sm text-surface-600 focus:outline-none focus:ring-2 focus:ring-brand-400">
                        <option value="">— Pilih produk —</option>
                        @foreach($products as $p)
                        <option value="{{ (int) $p->hpp_total }}">{{ $p->name }} — Rp {{ number_format($p->hpp_total, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <button @click="hpp=''; marginPct=''; hargaJual=''; qty=''"
                    class="w-full py-2 text-xs text-surface-400 border border-dashed border-surface-200 rounded-lg hover:bg-surface-50 transition">
                    Reset semua
                </button>
            </div>

            {{-- HASIL GABUNGAN --}}
            <div class="card p-5 space-y-4">
                <h3 class="text-sm font-bold text-surface-700">Hasil Kalkulasi</h3>

                {{-- Empty state --}}
                <div x-show="!hasResult1 && !hasResult2" class="flex flex-col items-center justify-center py-12 text-surface-300">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-surface-400 text-center">Isi HPP dan margin <span class="italic">atau</span> harga jual</p>
                </div>

                {{-- ① HPP → Harga Jual --}}
                <div x-show="hasResult1" class="space-y-3">
                    <p class="text-xs font-semibold text-surface-400 uppercase tracking-wide">HPP → Harga Jual</p>
                    <div class="bg-brand-50 border border-brand-100 rounded-xl p-4 text-center">
                        <p class="text-xs text-brand-400 uppercase font-semibold tracking-wide mb-1">Harga Jual</p>
                        <p class="text-3xl font-extrabold text-brand-700" x-text="fmt(calcHargaJual)"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-green-50 rounded-xl p-3 text-center border border-green-100">
                            <p class="text-xs text-green-500 font-medium mb-1">Keuntungan / Unit</p>
                            <p class="text-lg font-extrabold text-green-700" x-text="fmt(calcUntung1)"></p>
                        </div>
                        <div class="rounded-xl p-3 text-center border" :class="badgeCls(margin_num)">
                            <p class="text-xs font-medium mb-1 opacity-70">Markup</p>
                            <p class="text-lg font-extrabold" x-text="margin_num > 0 ? margin_num.toFixed(1) + '%' : '—'"></p>
                        </div>
                    </div>
                    <div class="bg-surface-50 rounded-lg p-3 text-xs text-surface-500">
                        <p class="font-medium text-surface-600 mb-0.5">Rumus:</p>
                        <p>Harga Jual = HPP × (1 + Markup%)</p>
                        <p class="mt-0.5" x-text="'= ' + fmt(hpp_num) + ' × (1 + ' + margin_num + '%) = ' + fmt(calcHargaJual)"></p>
                    </div>
                </div>

                {{-- Divider --}}
                <div x-show="hasResult1 && (hasResult2 || isRugi)" class="border-t border-dashed border-surface-200"></div>

                {{-- ② Harga Jual → Keuntungan --}}
                <div x-show="isRugi" class="p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center gap-3 text-red-700">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-sm">Harga jual di bawah HPP!</p>
                            <p class="text-xs mt-0.5">Merugi <span class="font-bold" x-text="fmt(hpp_num - hargaJual_num)"></span> per unit.</p>
                        </div>
                    </div>
                </div>

                <div x-show="hasResult2 && !isRugi" class="space-y-3">
                    <p class="text-xs font-semibold text-surface-400 uppercase tracking-wide">Harga Jual → Keuntungan</p>
                    <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-center">
                        <p class="text-xs text-green-500 uppercase font-semibold tracking-wide mb-1">Keuntungan / Unit</p>
                        <p class="text-3xl font-extrabold text-green-700" x-text="fmt(calcUntung2)"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-brand-50 rounded-xl p-3 text-center border border-brand-100">
                            <p class="text-xs text-brand-400 font-medium mb-1">Harga Jual</p>
                            <p class="text-lg font-extrabold text-brand-700" x-text="fmt(hargaJual_num)"></p>
                        </div>
                        <div class="rounded-xl p-3 text-center border" :class="badgeCls(calcMargin2)">
                            <p class="text-xs font-medium mb-1 opacity-70">Markup</p>
                            <p class="text-lg font-extrabold" x-text="calcMargin2 > 0 ? calcMargin2.toFixed(1) + '%' : '—'"></p>
                        </div>
                    </div>
                    <div class="bg-surface-50 rounded-lg p-3 text-xs text-surface-500">
                        <p class="font-medium text-surface-600 mb-0.5">Rumus:</p>
                        <p>Keuntungan = Harga Jual − HPP</p>
                        <p class="mt-0.5" x-text="'= ' + fmt(hargaJual_num) + ' − ' + fmt(hpp_num) + ' = ' + fmt(calcUntung2)"></p>
                    </div>
                </div>

                {{-- Simulasi --}}
                <div x-show="hasSim" class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-center">
                    <p class="text-xs text-blue-400 font-medium mb-1">Simulasi <span x-text="qty_num"></span> Unit</p>
                    <p class="text-2xl font-extrabold text-blue-700" x-text="fmt(simUntung)"></p>
                    <p class="text-xs text-blue-400 mt-1" x-text="'Omset: ' + fmt(simOmset) + '  ·  Modal: ' + fmt(simModal)"></p>
                </div>
            </div>

        </div>

        {{-- ===== TABEL PERBANDINGAN MARGIN ===== --}}
        <div x-show="hpp_num > 0" class="card">
            <div class="px-5 py-3.5 border-b border-surface-200">
                <h3 class="text-sm font-bold text-surface-700">📊 Tabel Perbandingan Markup</h3>
                <p class="text-xs text-surface-400 mt-0.5">Harga jual dan keuntungan pada berbagai level markup</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="table-th">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-surface-500 uppercase">Markup</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-surface-500 uppercase">Harga Jual</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-surface-500 uppercase">Untung / Unit</th>
                            <th x-show="qty_num > 0" class="px-4 py-2.5 text-right text-xs font-semibold text-surface-500 uppercase">
                                Untung <span x-text="qty_num + ' Unit'"></span>
                            </th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-surface-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <template x-for="m in [20, 30, 50, 75, 100, 125, 150, 200]" :key="m">
                            <tr class="hover:bg-surface-50 transition"
                                :class="Math.round(margin_num) === m ? 'bg-brand-50' : ''">
                                <td class="px-4 py-2.5 font-bold"
                                    :class="m >= 100 ? 'text-green-700' : (m >= 50 ? 'text-yellow-700' : 'text-red-600')"
                                    x-text="m + '%'"></td>
                                <td class="px-4 py-2.5 text-right text-surface-800 font-medium"
                                    x-text="fmt(hpp_num * (1 + m/100))"></td>
                                <td class="px-4 py-2.5 text-right text-green-700 font-semibold"
                                    x-text="fmt(hpp_num * m/100)"></td>
                                <td x-show="qty_num > 0" class="px-4 py-2.5 text-right text-blue-700 font-semibold"
                                    x-text="fmt(hpp_num * m/100 * qty_num)"></td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold"
                                        :class="m >= 100 ? 'bg-green-100 text-green-700' : (m >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-600')"
                                        x-text="m >= 100 ? 'Sehat' : (m >= 50 ? 'Cukup' : 'Rendah')"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
