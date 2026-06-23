<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Pengaturan Tampilan</h2>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">

            @if(session('success'))
            <div class="alert-success">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="divide-y divide-surface-100">

                        <div class="px-6 py-5">
                            <h3 class="text-sm font-bold text-surface-900 uppercase tracking-wide mb-4">Identitas Bisnis</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="label">Nama Bisnis</label>
                                    <input type="text" name="business_name"
                                           value="{{ old('business_name', $settings['business_name']) }}"
                                           placeholder="FORKA COFFEE & SPACE"
                                           class="input-field @error('business_name') !border-red-400 @enderror">
                                    <p class="text-xs text-surface-400 mt-1">Ditampilkan di slip gaji dan header sidebar.</p>
                                    @error('business_name')<p class="input-error">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="label">Tagline Sidebar</label>
                                    <input type="text" name="sidebar_tagline"
                                           value="{{ old('sidebar_tagline', $settings['sidebar_tagline']) }}"
                                           placeholder="Coffee Shop Manager"
                                           class="input-field">
                                    <p class="text-xs text-surface-400 mt-1">Teks kecil di bawah nama bisnis pada sidebar.</p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-5">
                            <h3 class="text-sm font-bold text-surface-900 uppercase tracking-wide mb-4">Slip Gaji</h3>
                            <div>
                                <label class="label">Subjudul Slip Gaji</label>
                                <input type="text" name="slip_subtitle"
                                       value="{{ old('slip_subtitle', $settings['slip_subtitle']) }}"
                                       placeholder="Slip Gaji Karyawan"
                                       class="input-field">
                                <p class="text-xs text-surface-400 mt-1">Teks di bawah nama bisnis pada header slip gaji.</p>
                            </div>
                        </div>

                        <div class="px-6 py-5">
                            <h3 class="text-sm font-bold text-surface-900 uppercase tracking-wide mb-4">Landing Page</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="label">WhatsApp Number</label>
                                    <input type="text" name="wa_number"
                                           value="{{ old('wa_number', $settings['wa_number'] ?? '6281234567890') }}"
                                           placeholder="6281234567890"
                                           class="input-field">
                                    <p class="text-xs text-surface-400 mt-1">Tombol WhatsApp floating di landing page.</p>
                                </div>
                                <div>
                                    <label class="label">Alamat / Jam Operasional</label>
                                    <textarea name="landing_address" rows="2" class="input-field !resize-y"
                                        placeholder="Jl. Contoh No. 123, Kota">{{ old('landing_address', $settings['landing_address'] ?? '') }}</textarea>
                                    <p class="text-xs text-surface-400 mt-1">Ditampilkan di bagian lokasi landing page.</p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-5 bg-surface-50">
                            <h3 class="text-sm font-bold text-surface-900 uppercase tracking-wide mb-3">Preview Slip Gaji</h3>
                            <div class="bg-brand-600 text-white rounded-lg px-5 py-4 flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-lg" id="preview-name">{{ $settings['business_name'] }}</div>
                                    <div class="text-xs opacity-70 mt-0.5" id="preview-subtitle">{{ $settings['slip_subtitle'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs opacity-70">Periode</div>
                                    <div class="font-semibold text-sm mt-0.5">April 2026</div>
                                </div>
                            </div>
                            <p class="text-xs text-surface-400 mt-2">Preview berubah setelah disimpan.</p>
                        </div>

                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
