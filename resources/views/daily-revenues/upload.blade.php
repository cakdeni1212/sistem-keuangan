<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('daily-revenues.index') }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Upload Omset dari Excel</h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto space-y-6">

            {{-- Info card --}}
            <div class="bg-brand-50 border border-brand-200 rounded-xl p-5 flex gap-4">
                <div class="text-brand-500 text-2xl">📊</div>
                <div>
                    <h3 class="font-semibold text-brand-800 mb-1">Upload Data Omset via Excel</h3>
                    <p class="text-sm text-brand-700">Import data omset harian (Tanggal, QRIS, Cash) dari file Excel sekaligus.
                       Jika tanggal sudah ada di sistem, data lama akan <strong>diperbarui</strong>.</p>
                    <a href="{{ route('daily-revenues.template') }}"
                       class="btn-primary">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Template Excel
                    </a>
                </div>
            </div>

            {{-- Format info --}}
            <div class="card p-5">
                <h3 class="font-semibold text-surface-700 text-sm mb-3 uppercase tracking-wide">Format Kolom Excel</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="gradient-brand text-white">
                                <th class="px-4 py-2 text-left rounded-tl-xl">Kolom</th>
                                <th class="px-4 py-2 text-left">Nama</th>
                                <th class="px-4 py-2 text-left rounded-tr-xl">Format / Contoh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100">
                            <tr class="bg-surface-50">
                                <td class="px-4 py-2 font-mono font-bold text-brand-700">A</td>
                                <td class="px-4 py-2 font-medium">Tanggal</td>
                                <td class="px-4 py-2 text-surface-600">DD/MM/YYYY — contoh: <code class="bg-surface-200 px-1 rounded">01/04/2026</code></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-mono font-bold text-brand-700">B</td>
                                <td class="px-4 py-2 font-medium">QRIS</td>
                                <td class="px-4 py-2 text-surface-600">Angka tanpa titik/koma — contoh: <code class="bg-surface-200 px-1 rounded">1500000</code></td>
                            </tr>
                            <tr class="bg-surface-50">
                                <td class="px-4 py-2 font-mono font-bold text-brand-700">C</td>
                                <td class="px-4 py-2 font-medium">Cash</td>
                                <td class="px-4 py-2 text-surface-600">Angka tanpa titik/koma — contoh: <code class="bg-surface-200 px-1 rounded">800000</code></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-mono font-bold text-surface-400">D</td>
                                <td class="px-4 py-2 text-surface-400">Total</td>
                                <td class="px-4 py-2 text-surface-400">Opsional — dihitung otomatis (tidak perlu diisi)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Upload form --}}
            <form action="{{ route('daily-revenues.upload') }}" method="POST" enctype="multipart/form-data"
                  class="card p-6 space-y-5" x-data="{ fileName: '', dragging: false }">
                @csrf

                <h3 class="font-semibold text-surface-700 text-sm uppercase tracking-wide">Pilih File</h3>

                @if($errors->any())
                    <div class="alert-error">
                        <p class="text-sm font-medium text-red-700 mb-1">Gagal mengupload:</p>
                        <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Drag & drop zone --}}
                <label class="block cursor-pointer"
                       @dragover.prevent="dragging = true"
                       @dragleave.prevent="dragging = false"
                       @drop.prevent="
                           dragging = false;
                           let f = $event.dataTransfer.files[0];
                           if(f){ fileName = f.name; $refs.fileInput.files = $event.dataTransfer.files; }
                       ">
                    <div class="border-2 border-dashed rounded-xl p-10 text-center transition"
                         :class="dragging ? 'border-brand-500 bg-brand-50' : 'border-surface-300 hover:border-brand-400 hover:bg-surface-50'">
                        <svg class="mx-auto w-10 h-10 text-surface-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-surface-600" x-text="fileName ? '✅ ' + fileName : 'Drag & drop file Excel di sini atau klik untuk pilih'"></p>
                        <p class="text-xs text-surface-400 mt-1">Format: .xlsx, .xls — Maksimal 5MB</p>
                    </div>
                    <input type="file" name="file" x-ref="fileInput" accept=".xlsx,.xls,.csv" class="hidden"
                           @change="fileName = $event.target.files[0]?.name || ''">
                </label>

                <div class="flex gap-3">
                    <button type="submit"
                            class="btn-primary">
                        🚀 Upload & Import
                    </button>
                    <a href="{{ route('daily-revenues.index') }}"
                        class="btn-secondary">
                        Batal
                    </a>
                </div>
            </form>

            {{-- Tips --}}
            <div class="alert-warning text-sm text-amber-800">
                <p class="font-semibold mb-1">💡 Tips:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Download template terlebih dahulu, isi data, lalu upload</li>
                    <li>Baris header (Tanggal, QRIS, Cash, Total) harus tetap ada di baris pertama</li>
                    <li>Jika tanggal sudah ada, data QRIS dan Cash akan <strong>diperbarui</strong></li>
                    <li>Kolom Total tidak perlu diisi — dihitung otomatis</li>
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
