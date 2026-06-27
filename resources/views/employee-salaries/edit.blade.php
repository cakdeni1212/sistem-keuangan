<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.show', $employee) }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Edit Gaji — {{ $employee->name }}</h2>
            <span class="text-sm text-surface-500">{{ $salary->period_label }}</span>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">

            @if($errors->any())
            <div class="alert-error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('employee-salaries.update', [$employee, $salary]) }}" method="POST"
                  x-data="{
                      base: {{ old('base_salary', $salary->base_salary) }},
                      bonus: {{ old('bonus', $salary->bonus) }},
                      deductions: {{ old('deductions', $salary->deductions) }},
                      get total() { return parseFloat(this.base||0) + parseFloat(this.bonus||0) - parseFloat(this.deductions||0); },
                      fmt(n) { return 'Rp ' + Number(Math.max(0,n)).toLocaleString('id-ID'); }
                  }"
                  class="space-y-5">
                @csrf
                @method('PUT')

                <div class="card">
                    {{-- Period (read-only) --}}
                    <div class="p-5">
                        <h3 class="text-sm font-bold text-surface-700 uppercase tracking-wide mb-3">Periode</h3>
                        <div class="bg-surface-50 rounded-lg px-4 py-3 text-sm text-surface-700 font-medium">
                            {{ $salary->period_label }}
                            <span class="text-xs text-surface-400 ml-2">(periode tidak dapat diubah)</span>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <h3 class="text-sm font-bold text-surface-700 uppercase tracking-wide">Komponen Gaji</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Gaji Pokok <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-surface-500 text-sm">Rp</span>
                                    <input type="number" name="base_salary" x-model="base" min="0" step="10000" required
                                        class="w-full border border-surface-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Bonus</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-surface-500 text-sm">Rp</span>
                                    <input type="number" name="bonus" x-model="bonus" min="0" step="10000"
                                        class="w-full border border-surface-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Potongan</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-surface-500 text-sm">Rp</span>
                                    <input type="number" name="deductions" x-model="deductions" min="0" step="10000"
                                        class="w-full border border-surface-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                            </div>
                        </div>

                        {{-- Live Total --}}
                        <div class="bg-brand-50 border border-brand-100 rounded-xl p-4 flex items-center justify-between">
                            <span class="text-sm text-brand-600 font-medium">Total Gaji Diterima</span>
                            <span class="text-2xl font-extrabold text-brand-700" x-text="fmt(total)"></span>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <h3 class="text-sm font-bold text-surface-700 uppercase tracking-wide">Pembayaran</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Metode Bayar</label>
                                <select name="payment_method" class="w-full input-field">
                                    <option value="">-- Pilih --</option>
                                    <option value="Transfer" {{ old('payment_method', $salary->payment_method) == 'Transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="Tunai" {{ old('payment_method', $salary->payment_method) == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Tanggal Bayar</label>
                                <input type="date" name="paid_at" value="{{ old('paid_at', $salary->paid_at?->format('Y-m-d')) }}"
                                    class="w-full input-field">
                                <p class="text-xs text-surface-400 mt-1">Kosongkan jika belum dibayar.</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Catatan</label>
                            <textarea name="notes" rows="2" placeholder="Catatan tambahan..."
                                class="w-full input-field">{{ old('notes', $salary->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('employees.show', $employee) }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
