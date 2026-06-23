<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('transactions.index') }}" class="btn-secondary text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
            <div>
                <h2 class="page-title">Input Transaksi Baru</h2>
                <p class="text-xs text-surface-400 mt-0.5">Catat pemasukan atau pengeluaran keuangan</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6 space-y-5">
                <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-5">

                        {{-- Tanggal --}}
                        <div>
                            <x-input-label for="transaction_date" value="Tanggal Transaksi" />
                            <x-text-input id="transaction_date" name="transaction_date" type="date"
                                value="{{ old('transaction_date', date('Y-m-d')) }}" required />
                            <x-input-error :messages="$errors->get('transaction_date')" />
                        </div>

                        {{-- Jenis Transaksi --}}
                        <div>
                            <x-input-label for="transaction_type_id" value="Jenis Transaksi" />
                            <select id="transaction_type_id" name="transaction_type_id" class="input-field" required>
                                <option value="">-- Pilih Jenis Transaksi --</option>
                                @foreach(['pengeluaran' => 'Pengeluaran', 'pemasukan' => 'Pemasukan'] as $cat => $label)
                                    @if(isset($types[$cat]) && $types[$cat]->count())
                                    <optgroup label="{{ $label }}">
                                        @foreach($types[$cat] as $type)
                                            <option value="{{ $type->id }}" {{ old('transaction_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('transaction_type_id')" />
                        </div>

                        {{-- Jumlah --}}
                        <div>
                            <x-input-label for="amount" value="Jumlah" />
                            <div class="flex rounded-xl border border-surface-300 overflow-hidden focus-within:ring-2 focus-within:ring-brand-500/20 focus-within:border-brand-500 transition">
                                <span class="flex items-center px-4 py-2.5 bg-surface-50 text-surface-500 text-sm font-semibold border-r border-surface-300">Rp</span>
                                <input type="number" name="amount" min="1" step="1" value="{{ old('amount') }}" placeholder="0"
                                       class="flex-1 px-4 py-2.5 text-sm text-surface-900 border-0 focus:outline-none bg-white" required>
                            </div>
                            <x-input-error :messages="$errors->get('amount')" />
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <x-input-label for="description" value="Keterangan (opsional)" />
                            <textarea id="description" name="description" rows="3" class="input-field !resize-y"
                                placeholder="Catatan tambahan mengenai transaksi ini...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" />
                        </div>

                        {{-- Upload Nota --}}
                        <div>
                            <x-input-label value="Upload Nota / Bukti Transaksi (opsional)" />
                            <div class="mt-1.5 space-y-3" x-data="notaUploader()">

                                {{-- Preview --}}
                                <div x-show="preview" class="relative rounded-xl overflow-hidden border border-surface-200 bg-surface-50">
                                    <img :src="preview" alt="Preview nota" class="w-full max-h-48 object-contain" x-show="isImage">
                                    <div x-show="!isImage" class="flex items-center gap-2 p-4">
                                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="text-sm text-surface-600" x-text="fileName"></span>
                                    </div>
                                    <button type="button" @click="clearFile()"
                                        class="absolute top-2 right-2 w-8 h-8 bg-white rounded-xl shadow flex items-center justify-center text-red-400 hover:text-red-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                {{-- Upload area --}}
                                <div x-show="!preview" class="grid grid-cols-2 gap-3">
                                    <label class="flex flex-col items-center justify-center h-28 rounded-xl border-2 border-dashed border-surface-300 bg-surface-50 cursor-pointer hover:bg-surface-100 hover:border-brand-300 transition">
                                        <svg class="w-6 h-6 text-surface-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <p class="text-xs font-medium text-surface-500">Pilih File</p>
                                        <p class="text-[10px] text-surface-400 mt-0.5">JPG, PNG, PDF</p>
                                        <input id="nota" name="nota" type="file" accept=".jpg,.jpeg,.png,.pdf" class="hidden"
                                            @change="onFileSelect($event)">
                                    </label>
                                    <button type="button" @click="openCamera()"
                                        class="flex flex-col items-center justify-center h-28 rounded-xl border-2 border-dashed border-brand-300 bg-brand-50 hover:bg-brand-100 transition text-brand-600">
                                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <p class="text-xs font-medium">Kamera</p>
                                        <p class="text-[10px] text-brand-400">Foto langsung</p>
                                    </button>
                                </div>

                                {{-- Camera Modal --}}
                                <div x-show="cameraOpen" x-cloak
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                                    <div class="bg-white rounded-2xl overflow-hidden shadow-2xl w-full max-w-sm">
                                        <div class="flex items-center justify-between px-5 py-3.5 border-b border-surface-100">
                                            <span class="font-semibold text-surface-900 text-sm">Ambil Foto Nota</span>
                                            <button type="button" @click="closeCamera()" class="text-surface-400 hover:text-surface-600 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <div class="relative bg-black" x-show="!snapPreview">
                                            <video x-ref="video" autoplay playsinline muted class="w-full max-h-64 object-cover"></video>
                                            <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-4">
                                                <button type="button" @click="snap()"
                                                    class="w-14 h-14 rounded-full bg-white border-4 border-brand-400 shadow-lg flex items-center justify-center hover:scale-105 transition">
                                                    <div class="w-10 h-10 rounded-full gradient-brand"></div>
                                                </button>
                                                <button type="button" @click="switchCamera()" title="Ganti kamera"
                                                    class="w-10 h-10 rounded-full bg-white/80 flex items-center justify-center shadow text-surface-600 hover:bg-white transition self-center">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div x-show="snapPreview">
                                            <img :src="snapPreview" class="w-full max-h-64 object-contain bg-surface-50">
                                            <div class="flex gap-2 p-4">
                                                <button type="button" @click="retake()" class="flex-1 py-2.5 text-sm border border-surface-300 rounded-xl hover:bg-surface-50 transition font-medium text-surface-600">
                                                    Ulangi
                                                </button>
                                                <button type="button" @click="useSnap()" class="btn-primary">
                                                    Gunakan Foto
                                                </button>
                                            </div>
                                        </div>
                                        <p x-show="cameraError" class="px-5 pb-4 text-xs text-red-500 text-center" x-text="cameraError"></p>
                                        <canvas x-ref="canvas" class="hidden"></canvas>
                                    </div>
                                </div>

                            </div>
                            <x-input-error :messages="$errors->get('nota')" />
                        </div>

                        {{-- Status --}}
                        <div>
                            <x-input-label value="Simpan sebagai" />
                            <div class="mt-2 flex gap-6">
                                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border-2 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 border-surface-200 hover:bg-surface-50 transition">
                                    <input type="radio" name="status" value="draft"
                                        {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}
                                        class="text-brand-600 focus:ring-brand-500 rounded-full">
                                    <div>
                                        <span class="text-sm font-semibold text-surface-900">Draft</span>
                                        <p class="text-xs text-surface-400">Belum dikirim untuk approval</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border-2 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 border-surface-200 hover:bg-surface-50 transition">
                                    <input type="radio" name="status" value="pending"
                                        {{ old('status') === 'pending' ? 'checked' : '' }}
                                        class="text-brand-600 focus:ring-brand-500 rounded-full">
                                    <div>
                                        <span class="text-sm font-semibold text-surface-900">Kirim Approval</span>
                                        <p class="text-xs text-surface-400">Langgan dikirim ke atasan</p>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('status')" />
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-surface-100">
                        <a href="{{ route('transactions.index') }}" class="btn-secondary text-xs">Batal</a>
                        <x-primary-button>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Transaksi
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
function notaUploader() {
    return {
        preview: null,
        fileName: '',
        isImage: false,
        cameraOpen: false,
        snapPreview: null,
        cameraError: '',
        stream: null,
        facingMode: 'environment',

        onFileSelect(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.fileName = file.name;
            this.isImage = file.type.startsWith('image/');
            if (this.isImage) {
                const reader = new FileReader();
                reader.onload = ev => this.preview = ev.target.result;
                reader.readAsDataURL(file);
            } else {
                this.preview = 'pdf';
            }
        },

        clearFile() {
            this.preview = null;
            this.fileName = '';
            this.isImage = false;
            const input = document.getElementById('nota');
            if (input) { input.value = ''; const dt = new DataTransfer(); input.files = dt.files; }
        },

        async openCamera() {
            this.cameraError = '';
            this.cameraOpen = true;
            this.snapPreview = null;
            await this.$nextTick();
            await this.startStream();
        },

        async startStream() {
            try {
                if (this.stream) { this.stream.getTracks().forEach(t => t.stop()); }
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: this.facingMode }, audio: false
                });
                this.$refs.video.srcObject = this.stream;
            } catch (err) {
                this.cameraError = 'Kamera tidak dapat diakses. Pastikan izin kamera diberikan.';
            }
        },

        async switchCamera() {
            this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
            await this.startStream();
        },

        snap() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            this.snapPreview = canvas.toDataURL('image/jpeg', 0.85);
        },

        retake() {
            this.snapPreview = null;
        },

        useSnap() {
            const canvas = this.$refs.canvas;
            canvas.toBlob(blob => {
                const file = new File([blob], 'nota-foto.jpg', { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(file);
                const input = document.getElementById('nota');
                input.files = dt.files;
                this.preview = this.snapPreview;
                this.isImage = true;
                this.fileName = 'nota-foto.jpg';
                this.closeCamera();
            }, 'image/jpeg', 0.85);
        },

        closeCamera() {
            if (this.stream) { this.stream.getTracks().forEach(t => t.stop()); this.stream = null; }
            this.cameraOpen = false;
            this.snapPreview = null;
        },
    };
}
</script>
</x-app-layout>
