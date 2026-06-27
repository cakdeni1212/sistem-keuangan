<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('journals.index') }}" class="btn-secondary text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
            <div>
                <h2 class="page-title">Input Jurnal Baru</h2>
                <p class="text-xs text-surface-400 mt-0.5">Catat ayat jurnal akuntansi dengan Debit dan Kredit seimbang</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="journalForm()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="alert-error mb-6">{{ session('error') }}</div>
            @endif

            <div class="card p-6 space-y-5">
                <form method="POST" action="{{ route('journals.store') }}" @submit="validateBalanced">
                    @csrf

                    {{-- Header Fields --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label for="journal_date" class="label">Tanggal Jurnal</label>
                            <input type="date" id="journal_date" name="journal_date"
                                   value="{{ old('journal_date', date('Y-m-d')) }}"
                                   class="input-field" required>
                            <x-input-error :messages="$errors->get('journal_date')" />
                        </div>

                        <div>
                            <label for="journal_type" class="label">Tipe Jurnal</label>
                            <select id="journal_type" name="journal_type" class="input-field" required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($journalTypes as $type)
                                    <option value="{{ $type }}" {{ old('journal_type') == $type ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('journal_type')" />
                        </div>

                        <div>
                            <label for="reference" class="label">Referensi (opsional)</label>
                            <input type="text" id="reference" name="reference"
                                   value="{{ old('reference') }}"
                                   placeholder="No. invoice / dokumen"
                                   class="input-field">
                            <x-input-error :messages="$errors->get('reference')" />
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="label">Deskripsi Jurnal</label>
                        <textarea id="description" name="description" rows="2"
                                  class="input-field !resize-y"
                                  placeholder="Keterangan umum mengenai jurnal ini...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" />
                    </div>

                    {{-- Lines Table --}}
                    <div class="border border-surface-200 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 bg-surface-50 border-b border-surface-200 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-surface-700">Ayat Jurnal</h3>
                            <button type="button" @click="addLine()" class="btn-primary text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Baris
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table-wrap">
                                <thead>
                                    <tr class="bg-surface-50">
                                        <th class="table-th table-head w-10 text-center">#</th>
                                        <th class="table-th table-head">Akun</th>
                                        <th class="table-th table-head">Deskripsi</th>
                                        <th class="table-th table-head text-right w-40">Jumlah</th>
                                        <th class="table-th table-head text-center w-24">D/K</th>
                                        <th class="table-th table-head w-12"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(line, index) in lines" :key="index">
                                        <tr class="hover:bg-surface-50 transition-colors">
                                            <td class="table-td text-center text-surface-400 text-sm" x-text="index + 1"></td>
                                            <td class="table-td">
                                                <select :name="'lines['+index+'][account_id]'"
                                                        x-model="line.account_id"
                                                        class="input-field"
                                                        required>
                                                    <option value="">-- Pilih Akun --</option>
                                                    <template x-for="(groupAccounts, groupName) in accountGroups" :key="groupName">
                                                        <optgroup :label="groupName">
                                                            <template x-for="account in groupAccounts" :key="account.id">
                                                                <option :value="account.id"
                                                                        x-text="account.code + ' - ' + account.name"></option>
                                                            </template>
                                                        </optgroup>
                                                    </template>
                                                </select>
                                            </td>
                                            <td class="table-td">
                                                <input type="text"
                                                       :name="'lines['+index+'][description]'"
                                                       x-model="line.description"
                                                       class="input-field"
                                                       placeholder="Keterangan...">
                                            </td>
                                            <td class="table-td">
                                                <input type="number"
                                                       :name="line.dk === 'debit' ? 'lines['+index+'][debit]' : 'lines['+index+'][credit]'"
                                                       x-model="line.amount"
                                                       @input="calcTotals()"
                                                       min="0"
                                                       step="1"
                                                       class="input-field text-right"
                                                       placeholder="0"
                                                       required>
                                                <input type="hidden"
                                                       :name="line.dk === 'debit' ? 'lines['+index+'][credit]' : 'lines['+index+'][debit]'"
                                                       value="0">
                                            </td>
                                            <td class="table-td text-center">
                                                <button type="button"
                                                        @click="line.dk = (line.dk === 'debit' ? 'credit' : 'debit'); calcTotals()"
                                                        :class="line.dk === 'debit' ? 'text-blue-600 bg-blue-50 border-blue-200' : 'text-amber-600 bg-amber-50 border-amber-200'"
                                                        class="text-xs font-semibold px-2.5 py-1 rounded-lg border transition uppercase"
                                                        x-text="line.dk === 'debit' ? 'Debit' : 'Kredit'"></button>
                                            </td>
                                            <td class="table-td text-center">
                                                <button type="button" @click="removeLine(index)"
                                                        :disabled="lines.length <= 2"
                                                        :class="lines.length <= 2 ? 'text-surface-300 cursor-not-allowed' : 'text-red-400 hover:text-red-600 hover:bg-red-50'"
                                                        class="w-8 h-8 flex items-center justify-center rounded-xl transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-surface-50 border-t-2 border-surface-200 font-semibold text-sm">
                                        <td class="table-td" colspan="3"></td>
                                        <td class="table-td text-right text-blue-600 whitespace-nowrap">
                                            <span x-text="formatRupiah(totalDebit)"></span>
                                        </td>
                                        <td class="table-td text-center text-blue-600">Total Debit</td>
                                        <td class="table-td"></td>
                                    </tr>
                                    <tr class="bg-surface-50 font-semibold text-sm">
                                        <td class="table-td" colspan="3"></td>
                                        <td class="table-td text-right text-amber-600 whitespace-nowrap">
                                            <span x-text="formatRupiah(totalCredit)"></span>
                                        </td>
                                        <td class="table-td text-center text-amber-600">Total Kredit</td>
                                        <td class="table-td"></td>
                                    </tr>
                                    <tr class="bg-surface-50 border-t-2 border-surface-200 font-semibold text-sm"
                                        :class="balanceDiff === 0 ? 'text-emerald-600' : 'text-red-500'">
                                        <td class="table-td" colspan="3"></td>
                                        <td class="table-td text-right whitespace-nowrap">
                                            <span x-text="formatRupiah(Math.abs(balanceDiff))"></span>
                                        </td>
                                        <td class="table-td text-center" x-text="balanceDiff === 0 ? 'Seimbang' : 'Selisih'"></td>
                                        <td class="table-td">
                                            <template x-if="balanceDiff === 0">
                                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <x-input-error :messages="$errors->get('lines')" />
                    <x-input-error :messages="$errors->get('lines.*.account_id')" />

                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-surface-100">
                        <p class="text-xs text-surface-400">
                            Minimal 2 baris. Total Debit harus sama dengan Total Kredit.
                        </p>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('journals.index') }}" class="btn-secondary text-xs">Batal</a>
                            <button type="submit" class="btn-primary"
                                    :disabled="balanceDiff !== 0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Jurnal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function journalForm() {
        return {
            lines: [
                { account_id: '', description: '', amount: '', dk: 'debit' },
                { account_id: '', description: '', amount: '', dk: 'credit' },
            ],
            accountGroups: @json($accounts),
            totalDebit: 0,
            totalCredit: 0,
            balanceDiff: 0,

            init() {
                this.calcTotals();
            },

            addLine() {
                this.lines.push({ account_id: '', description: '', amount: '', dk: 'debit' });
            },

            removeLine(index) {
                if (this.lines.length <= 2) return;
                this.lines.splice(index, 1);
                this.calcTotals();
            },

            calcTotals() {
                this.totalDebit = this.lines.reduce((sum, l) => {
                    const val = parseFloat(l.amount) || 0;
                    return l.dk === 'debit' ? sum + val : sum;
                }, 0);
                this.totalCredit = this.lines.reduce((sum, l) => {
                    const val = parseFloat(l.amount) || 0;
                    return l.dk === 'credit' ? sum + val : sum;
                }, 0);
                this.balanceDiff = this.totalDebit - this.totalCredit;
            },

            formatRupiah(val) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
            },

            validateBalanced(e) {
                if (this.balanceDiff !== 0) {
                    e.preventDefault();
                    alert('Jurnal tidak seimbang! Debit dan Kredit harus sama.');
                }
            },
        };
    }
    </script>
</x-app-layout>
