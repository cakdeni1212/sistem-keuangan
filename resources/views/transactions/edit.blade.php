<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.index') }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Edit Transaksi #{{ $transaction->id }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6">
                <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="transaction_date" value="Tanggal Transaksi" />
                        <x-text-input id="transaction_date" name="transaction_date" type="date"
                            class="mt-1 block w-full"
                            value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required />
                        <x-input-error :messages="$errors->get('transaction_date')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="transaction_type_id" value="Jenis Transaksi" />
                        <select id="transaction_type_id" name="transaction_type_id"
                            class="mt-1 block w-full input-field" required>
                            @foreach(['pengeluaran' => 'Pengeluaran', 'pemasukan' => 'Pemasukan'] as $cat => $label)
                                @if(isset($types[$cat]) && $types[$cat]->count())
                                <optgroup label="{{ $label }}">
                                    @foreach($types[$cat] as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('transaction_type_id', $transaction->transaction_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                @endif
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('transaction_type_id')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="amount" value="Jumlah (Rp)" />
                        <div class="mt-1 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-surface-500 text-sm">Rp</span>
                            <x-text-input id="amount" name="amount" type="number" min="1" step="1"
                                class="pl-10 block w-full"
                                value="{{ old('amount', (int)$transaction->amount) }}" required />
                        </div>
                        <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description" value="Keterangan (opsional)" />
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 block w-full input-field !resize-y">{{ old('description', $transaction->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    {{-- Nota --}}
                    <div class="mb-4">
                        <x-input-label value="Nota / Bukti Transaksi" />
                        @if($transaction->nota_path)
                            <div class="mt-1 p-3 bg-surface-50 rounded-xl flex items-center justify-between">
                                <a href="{{ $transaction->nota_url }}" target="_blank" class="text-sm text-brand-600 hover:underline flex items-center gap-1">
                                    📎 Lihat nota saat ini
                                </a>
                                <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                                    <input type="checkbox" name="remove_nota" value="1" class="rounded border-surface-300 text-red-600">
                                    Hapus nota ini
                                </label>
                            </div>
                        @endif
                        <div class="mt-2">
                            <label for="nota"
                                class="flex items-center justify-center w-full h-20 border-2 border-surface-300 border-dashed rounded-xl cursor-pointer bg-surface-50 hover:bg-surface-100 transition">
                                <span class="text-sm text-surface-500" id="nota-label">
                                    {{ $transaction->nota_path ? 'Ganti dengan file baru (opsional)' : 'Upload nota (JPG, PNG, PDF, maks. 2MB)' }}
                                </span>
                            </label>
                            <input id="nota" name="nota" type="file" accept=".jpg,.jpeg,.png,.pdf" class="hidden"
                                onchange="document.getElementById('nota-label').textContent = this.files[0]?.name ?? 'Upload nota';" />
                        </div>
                        <x-input-error :messages="$errors->get('nota')" class="mt-1" />
                    </div>

                    <div class="mb-6">
                        <x-input-label value="Status" />
                        @if(auth()->user()->hasAnyRole(['admin', 'owner']) && $transaction->status === 'approved')
                            <input type="hidden" name="status" value="approved">
                            <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 rounded-xl text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Disetujui — status tidak berubah saat diedit admin
                            </div>
                        @else
                        <div class="mt-2 flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="draft"
                                    {{ old('status', $transaction->status) === 'draft' ? 'checked' : '' }}
                                    class="text-brand-600">
                                <span class="text-sm text-surface-700">Draft</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="pending"
                                    {{ old('status', $transaction->status) === 'pending' ? 'checked' : '' }}
                                    class="text-brand-600">
                                <span class="text-sm text-surface-700">Kirim untuk Approval</span>
                            </label>
                        </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('transactions.index') }}" class="btn-secondary">Batal</a>
                        <x-primary-button>Perbarui Transaksi</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
