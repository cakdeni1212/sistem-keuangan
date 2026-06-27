<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('transaction-types.index') }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Edit: {{ $transactionType->name }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('transaction-types.update', $transactionType) }}"
                      x-data="{ category: '{{ old('category', $transactionType->category) }}' }">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="name" value="Nama Jenis Transaksi" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name', $transactionType->name) }}" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="category" value="Kategori" />
                        <select id="category" name="category" x-model="category"
                            class="mt-1 input-field w-full">
                            <option value="pengeluaran" {{ old('category', $transactionType->category) === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                            <option value="pemasukan"   {{ old('category', $transactionType->category) === 'pemasukan'   ? 'selected' : '' }}>Pemasukan</option>
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-1" />
                    </div>

                    <div class="mb-4" x-show="category === 'pengeluaran'" x-cloak>
                        <x-input-label for="grup" value="Grup Dapur/BAR (opsional)" />
                        <select id="grup" name="grup"
                            class="mt-1 input-field w-full">
                            <option value="" {{ old('grup', $transactionType->grup) === null || old('grup', $transactionType->grup) === '' ? 'selected' : '' }}>-- Tanpa Grup --</option>
                            <option value="Dapur" {{ old('grup', $transactionType->grup) === 'Dapur' ? 'selected' : '' }}>Dapur</option>
                            <option value="BAR"   {{ old('grup', $transactionType->grup) === 'BAR'   ? 'selected' : '' }}>BAR</option>
                            <option value="Operasional" {{ old('grup', $transactionType->grup) === 'Operasional' ? 'selected' : '' }}>Operasional</option>
                        </select>
                        <p class="text-xs text-surface-400 mt-1">Pilih untuk mengelompokkan biaya ke Dapur atau BAR.</p>
                        <x-input-error :messages="$errors->get('grup')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description" value="Deskripsi (opsional)" />
                        <textarea id="description" name="description" rows="2"
                            class="mt-1 input-field w-full">{{ old('description', $transactionType->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div class="mb-6 flex items-center gap-2">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                            class="rounded border-surface-300 text-brand-600"
                            {{ old('is_active', $transactionType->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <x-input-label for="is_active" value="Aktif" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('transaction-types.index') }}" class="btn-secondary">Batal</a>
                        <x-primary-button>Perbarui</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
