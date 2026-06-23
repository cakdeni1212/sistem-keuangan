<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('raw-materials.index') }}"
               class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Tambah Bahan Baku</h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto" x-data="{
            stock: 0,
            price: 0,
            get totalValue() { return parseFloat(this.stock||0) * parseFloat(this.price||0); },
            fmt(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
        }">
            <form action="{{ route('raw-materials.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="card">

                    {{-- Info Bahan --}}
                    <div class="p-6 space-y-4">
                        <h3 class="font-semibold text-surface-700 text-sm uppercase tracking-wide">Informasi Bahan Baku</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">
                                    Nama Bahan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                                    placeholder="Cth: Kopi Arabika, Susu UHT">
                                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">
                                    Kategori
                                </label>
                                <input type="text" name="category" value="{{ old('category') }}"
                                    class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                                    placeholder="Cth: Kopi, Susu, Sirup">
                                @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">
                                Satuan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="unit" id="unit-input" value="{{ old('unit') }}" required
                                class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                                placeholder="Cth: gram, ml, pcs">
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach(['gram', 'ml', 'kg', 'liter', 'pcs', 'sdm', 'bungkus'] as $unit)
                                <button type="button"
                                    onclick="document.getElementById('unit-input').value='{{ $unit }}'"
                                    class="px-2 py-1 text-xs border border-surface-300 rounded-md hover:bg-brand-50 hover:border-brand-400 hover:text-brand-700 transition">
                                    {{ $unit }}
                                </button>
                                @endforeach
                            </div>
                            @error('unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Stok & Harga --}}
                    <div class="p-6 space-y-4">
                        <h3 class="font-semibold text-surface-700 text-sm uppercase tracking-wide">Stok & Harga</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">
                                    Stok Awal <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
                                    min="0" step="0.001" x-model="stock"
                                    class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                @error('stock_quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">
                                    Harga per Satuan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-surface-500 text-sm">Rp</span>
                                    <input type="number" name="price_per_unit" value="{{ old('price_per_unit', 0) }}"
                                        min="0" step="1" x-model="price"
                                        class="w-full border border-surface-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                                @error('price_per_unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Live preview --}}
                        <div class="bg-brand-50 border border-brand-200 rounded-lg p-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-brand-600 font-medium">Total Nilai Stok</p>
                                <p class="text-xs text-surface-500 mt-0.5">Stok × Harga per satuan</p>
                            </div>
                            <p class="text-lg font-bold text-brand-700" x-text="fmt(totalValue)"></p>
                        </div>
                    </div>

                    {{-- Catatan & Status --}}
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Catatan</label>
                            <textarea name="notes" rows="2"
                                class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                                placeholder="Informasi tambahan, supplier, dll.">{{ old('notes') }}</textarea>
                            @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                class="rounded text-brand-600">
                            <label for="is_active" class="text-sm text-surface-700">Bahan Baku Aktif</label>
                        </div>
                    </div>

                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="px-6 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 font-medium text-sm transition">
                        Simpan Bahan Baku
                    </button>
                    <a href="{{ route('raw-materials.index') }}"
                        class="px-6 py-2 bg-white border border-surface-300 text-surface-700 rounded-lg hover:bg-surface-50 font-medium text-sm transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
