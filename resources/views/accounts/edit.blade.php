<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounts.index') }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Edit: {{ $account->name }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('accounts.update', $account) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="code" value="Kode Akun" />
                        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
                            value="{{ old('code', $account->code) }}" required autofocus />
                        <x-input-error :messages="$errors->get('code')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="name" value="Nama Akun" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name', $account->name) }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="account_type" value="Tipe Akun" />
                        <select id="account_type" name="account_type"
                            class="mt-1 input-field w-full" required>
                            <option value="asset" {{ old('account_type', $account->account_type) === 'asset' ? 'selected' : '' }}>Aset</option>
                            <option value="liability" {{ old('account_type', $account->account_type) === 'liability' ? 'selected' : '' }}>Liabilitas</option>
                            <option value="equity" {{ old('account_type', $account->account_type) === 'equity' ? 'selected' : '' }}>Ekuitas</option>
                            <option value="revenue" {{ old('account_type', $account->account_type) === 'revenue' ? 'selected' : '' }}>Pendapatan</option>
                            <option value="expense" {{ old('account_type', $account->account_type) === 'expense' ? 'selected' : '' }}>Beban</option>
                        </select>
                        <x-input-error :messages="$errors->get('account_type')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="parent_id" value="Induk Akun (opsional)" />
                        <select id="parent_id" name="parent_id"
                            class="mt-1 input-field w-full">
                            <option value="">-- Tanpa Induk --</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $account->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->code }} - {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-surface-400 mt-1">Pilih induk jika akun ini merupakan sub-akun.</p>
                        <x-input-error :messages="$errors->get('parent_id')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description" value="Deskripsi (opsional)" />
                        <textarea id="description" name="description" rows="2"
                            class="mt-1 input-field w-full">{{ old('description', $account->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div class="mb-6 flex items-center gap-2">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                            class="rounded border-surface-300 text-brand-600"
                            {{ old('is_active', $account->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <x-input-label for="is_active" value="Aktif" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('accounts.index') }}" class="btn-secondary">Batal</a>
                        <x-primary-button>Perbarui</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
