<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('daily-revenues.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">&larr; Kembali</a>
            <h2 class="text-lg font-bold text-gray-800">Upload Omset dari Excel</h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto space-y-6">

            {{-- Info card --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 flex gap-4">
                <div class="text-indigo-500 text-2xl">📊</div>
                <div>
                    <h3 class="font-semibold text-indigo-800 mb-1">Upload Data Omset via Excel</h3>
                    <p class="text-sm text-indigo-700">Import data omset harian (Tanggal, QRIS, Cash) dari file Excel sekaligus.
                       Jika tanggal sudah ada di sistem, data lama akan <strong>diperbarui</strong>.</p>
                    <a href="{{ route('daily-revenues.template') }}"
                       class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Template Excel
                    </a>
                </div>
            </div>

            {{-- Format info --}}
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-semibold text-gray-700 text-sm mb-3 uppercase tracking-wide">Format Kolom Excel</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-indigo-600 text-white">
                                <th class="px-4 py-2 text-left rounded-tl-lg">Kolom</th>
                                <th class="px-4 py-2 text-left">Nama</th>
                                <th class="px-4 py-2 text-left rounded-tr-lg">Format / Contoh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="bg-gray-50">
                                <td class="px-4 py-2 font-mono font-bold text-indigo-700">A</td>
                                <td class="px-4 py-2 font-medium">Tanggal</td>
                                <td class="px-4 py-2 text-gray-600">DD/MM/YYYY — contoh: <code class="bg-gray-200 px-1 rounded">01/04/2026</code></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-mono font-bold text-indigo-700">B</td>
                                <td class="px-4 py-2 font-medium">QRIS</td>
                                <td class="px-4 py-2 text-gray-600">Angka tanpa titik/koma — contoh: <code class="bg-gray-200 px-1 rounded">1500000</code></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="px-4 py-2 font-mono font-bold text-indigo-700">C</td>
                                <td class="px-4 py-2 font-medium">Cash</td>
                                <td class="px-4 py-2 text-gray-600">Angka tanpa titik/koma — contoh: <code class="bg-gray-200 px-1 rounded">800000</code></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-mono font-bold text-gray-400">D</td>
                                <td class="px-4 py-2 text-gray-400">Total</td>
                                <td class="px-4 py-2 text-gray-400">Opsional — dihitung otomatis (tidak perlu diisi)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Upload form --}}
            <form action="{{ route('daily-revenues.upload') }}" method="POST" enctype="multipart/form-data"
                  class="bg-white rounded-xl shadow-sm border p-6 space-y-5" x-data="{ fileName: '', dragging: false }">
                @csrf

                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Pilih File</h3>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
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
                         :class="dragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-indigo-400 hover:bg-gray-50'">
                        <svg class="mx-auto w-10 h-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-gray-600" x-text="fileName ? '✅ ' + fileName : 'Drag & drop file Excel di sini atau klik untuk pilih'"></p>
                        <p class="text-xs text-gray-400 mt-1">Format: .xlsx, .xls — Maksimal 5MB</p>
                    </div>
                    <input type="file" name="file" x-ref="fileInput" accept=".xlsx,.xls,.csv" class="hidden"
                           @change="fileName = $event.target.files[0]?.name || ''">
                </label>

                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition text-center">
                        🚀 Upload & Import
                    </button>
                    <a href="{{ route('daily-revenues.index') }}"
                       class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </a>
                </div>
            </form>

            {{-- Tips --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
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
