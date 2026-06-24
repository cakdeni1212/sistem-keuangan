<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('hpp-products.index') }}" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
            <div>
                <h2 class="page-title">Import HPP dari Excel</h2>
                <p class="text-xs text-surface-400 mt-0.5">Upload file export produk dari Majo POS</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6 space-y-6">

                @if(session('success'))
                <div class="alert-success">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="alert-error">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
                @endif

                @if(isset($results))
                <div class="bg-brand-50 border border-brand-200 rounded-xl p-5 space-y-3">
                    <div class="flex items-center gap-2 text-brand-700 font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Hasil Import
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div class="bg-white rounded-xl p-3 text-center">
                            <p class="text-xs text-surface-400">Total di File</p>
                            <p class="text-xl font-bold text-surface-900">{{ $results['total'] }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-3 text-center">
                            <p class="text-xs text-surface-400">Baru Diimport</p>
                            <p class="text-xl font-bold text-emerald-600">{{ $results['new'] }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-3 text-center">
                            <p class="text-xs text-surface-400">Sudah Ada</p>
                            <p class="text-xl font-bold text-surface-400">{{ $results['existing'] }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('hpp-products.import.excel') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="file" value="Pilih File Excel" />
                        <div class="mt-1.5">
                            <label class="flex flex-col items-center justify-center h-36 rounded-xl border-2 border-dashed border-surface-300 bg-surface-50 cursor-pointer hover:bg-surface-100 hover:border-brand-300 transition">
                                <svg class="w-8 h-8 text-surface-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-medium text-surface-500">Klik untuk upload file Excel</p>
                                <p class="text-xs text-surface-400 mt-1">Format: .xlsx (export produk Majo)</p>
                                <input id="file" name="file" type="file" accept=".xlsx,.xls" class="hidden" required>
                                <p id="file-name" class="text-xs text-brand-600 mt-2 font-medium hidden"></p>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('file')" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-surface-100">
                        <a href="{{ route('hpp-products.index') }}" class="btn-secondary btn-sm">Batal</a>
                        <x-primary-button>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Import & Proses
                        </x-primary-button>
                    </div>
                </form>

                <div class="bg-surface-50 rounded-xl p-4 text-xs text-surface-500 space-y-1">
                    <p class="font-semibold text-surface-700">📋 Format File:</p>
                    <p>File Excel export dari Majo POS dengan kolom: <strong>Nama Produk, Kategori, Harga Beli, Harga Jual, SKU, Satuan</strong>.</p>
                    <p>Produk yang sudah ada di database akan <strong>dilewati</strong> (tidak diduplikasi).</p>
                </div>

            </div>
        </div>
    </div>

<script>
document.getElementById('file')?.addEventListener('change', function(e) {
    const name = e.target.files[0]?.name;
    const el = document.getElementById('file-name');
    if (name) { el.textContent = '📄 ' + name; el.classList.remove('hidden'); }
    else { el.classList.add('hidden'); }
});
</script>
</x-app-layout>
