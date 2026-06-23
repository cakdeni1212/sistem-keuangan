<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('daily-revenues.index') }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Input Omset Harian</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6 space-y-5">

                {{-- Warning penjualan harian sudah ada --}}
                @if($penjualanWarning)
                <div class="flex items-start gap-3 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <span class="text-amber-500 text-lg mt-0.5">⚠️</span>
                    <div class="text-sm text-amber-800">
                        <p class="font-semibold">Data penjualan harian sudah terinput untuk tanggal ini</p>
                        <p class="mt-0.5 text-amber-700">
                            Total omset: <strong>Rp {{ number_format($penjualanWarning->total_omset, 0, ',', '.') }}</strong>
                            ({{ number_format($penjualanWarning->total_qty) }} pcs terjual)
                        </p>
                        <p class="mt-1 text-xs text-amber-600">Pastikan input omset QRIS + Tunai sesuai dengan data penjualan harian.</p>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('daily-revenues.store') }}" class="space-y-5">
                    @csrf

                    {{-- Tanggal --}}
                    <div>
                        <x-input-label for="date" value="Tanggal" />
                        <x-text-input type="date" name="date" value="{{ old('date', $today) }}" max="{{ now()->toDateString() }}" required />
                        <x-input-error :messages="$errors->get('date')" />
                    </div>

                    {{-- QRIS --}}
                    <div>
                        <x-input-label value="Pendapatan QRIS" />
                        <div class="flex rounded-xl border border-surface-300 overflow-hidden focus-within:ring-2 focus-within:ring-brand-500/20 focus-within:border-brand-500 transition">
                            <span class="flex items-center px-4 py-2.5 bg-surface-50 text-surface-500 text-sm font-semibold border-r border-surface-300">Rp</span>
                            <input type="number" name="qris_amount" value="{{ old('qris_amount', 0) }}" min="0" step="any"
                                   class="flex-1 px-4 py-2.5 text-sm text-surface-900 border-0 focus:outline-none bg-white" required>
                        </div>
                        <x-input-error :messages="$errors->get('qris_amount')" />
                    </div>

                    {{-- Tunai --}}
                    <div>
                        <x-input-label value="Pendapatan Tunai" />
                        <div class="flex rounded-xl border border-surface-300 overflow-hidden focus-within:ring-2 focus-within:ring-brand-500/20 focus-within:border-brand-500 transition">
                            <span class="flex items-center px-4 py-2.5 bg-surface-50 text-surface-500 text-sm font-semibold border-r border-surface-300">Rp</span>
                            <input type="number" name="tunai_amount" value="{{ old('tunai_amount', 0) }}" min="0" step="any"
                                   class="flex-1 px-4 py-2.5 text-sm text-surface-900 border-0 focus:outline-none bg-white" required>
                        </div>
                        <x-input-error :messages="$errors->get('tunai_amount')" />
                    </div>

                    {{-- Live total preview --}}
                    <div class="bg-brand-50 rounded-xl p-4 flex justify-between items-center"
                         x-data="{
                            qris: {{ old('qris_amount', 0) }},
                            tunai: {{ old('tunai_amount', 0) }},
                            get total() { return parseInt(this.qris||0) + parseInt(this.tunai||0); },
                            fmt(n) { return 'Rp ' + parseInt(n||0).toLocaleString('id-ID'); }
                         }"
                         @input.window="
                            let q = document.querySelector('[name=qris_amount]');
                            let t = document.querySelector('[name=tunai_amount]');
                            if(q) qris = q.value;
                            if(t) tunai = t.value;
                         ">
                        <span class="text-sm font-medium text-brand-700">Total Omset</span>
                        <span class="text-lg font-bold text-brand-800" x-text="fmt(total)">Rp 0</span>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <x-input-label for="notes" value="Catatan (opsional)" />
                        <textarea name="notes" rows="2" class="input-field !resize-y" placeholder="Misal: ramai karena acara, promo weekend...">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" />
                    </div>

                    <div class="flex gap-3 pt-2">
                        <x-primary-button>Simpan Omset</x-primary-button>
                        <a href="{{ route('daily-revenues.index') }}" class="btn-secondary">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
