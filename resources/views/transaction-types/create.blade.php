<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('transaction-types.index') }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Tambah Jenis Transaksi</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('transaction-types.store') }}" x-data="{ category: '{{ old('category') }}' }">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" value="Nama Jenis Transaksi" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name') }}" placeholder="contoh: Gas, Kopi, Susu" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="category" value="Kategori" />
                        <select id="category" name="category"
                            x-model="category"
                            class="mt-1 block w-full border-surface-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="pengeluaran" {{ old('category') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                            <option value="pemasukan"   {{ old('category') === 'pemasukan'   ? 'selected' : '' }}>Pemasukan</option>
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-1" />
                    </div>

                    <div class="mb-4" x-show="category === 'pengeluaran'" x-cloak>
                        <x-input-label for="grup" value="Grup Dapur/BAR (opsional)" />
                        <select id="grup" name="grup"
                            class="mt-1 block w-full border-surface-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">
                            <option value="">-- Tanpa Grup --</option>
                            <option value="Dapur" {{ old('grup') === 'Dapur' ? 'selected' : '' }}>🍳 Dapur</option>
                            <option value="BAR"   {{ old('grup') === 'BAR'   ? 'selected' : '' }}>☕ BAR</option>
                            <option value="Operasional" {{ old('grup') === 'Operasional' ? 'selected' : '' }}>⚙️ Operasional</option>
                        </select>
                        <p class="text-xs text-surface-400 mt-1">Pilih untuk mengelompokkan biaya ke Dapur atau BAR.</p>
                        <x-input-error :messages="$errors->get('grup')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description" value="Deskripsi (opsional)" />
                        <textarea id="description" name="description" rows="2"
                            class="mt-1 block w-full border-surface-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm"
                            placeholder="Keterangan singkat jenis transaksi">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div class="mb-6 flex items-center gap-2">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                            class="rounded border-surface-300 text-brand-600"
                            {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <x-input-label for="is_active" value="Aktif (tersedia untuk dipilih saat input transaksi)" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('transaction-types.index') }}" class="px-4 py-2 text-sm text-surface-700 bg-surface-100 rounded-md hover:bg-surface-200">Batal</a>
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
