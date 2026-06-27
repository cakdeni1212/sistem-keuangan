<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('cashbons.index') }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Edit Cashbon — {{ $cashbon->debtor_name }}</h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">

            @if($errors->any())
            <div class="alert-error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('cashbons.update', $cashbon) }}" method="POST" class="space-y-5"
                  enctype="multipart/form-data"
                  x-data="{ debtorType: '{{ old('debtor_type', $cashbon->debtor_type) }}' }">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="p-5 space-y-4">
                        <h3 class="text-sm font-bold text-surface-700 uppercase tracking-wide">Informasi Peminjam</h3>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Nama Peminjam <span class="text-red-500">*</span></label>
                            <input type="text" name="debtor_name" value="{{ old('debtor_name', $cashbon->debtor_name) }}" required
                                placeholder="Nama lengkap peminjam"
                                class="w-full input-field">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Tipe Peminjam <span class="text-red-500">*</span></label>
                            <select name="debtor_type" x-model="debtorType" required
                                class="w-full input-field">
                                <option value="karyawan" {{ old('debtor_type', $cashbon->debtor_type) === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                <option value="pelanggan" {{ old('debtor_type', $cashbon->debtor_type) === 'pelanggan' ? 'selected' : '' }}>Pelanggan</option>
                                <option value="supplier" {{ old('debtor_type', $cashbon->debtor_type) === 'supplier' ? 'selected' : '' }}>Supplier</option>
                                <option value="lainnya" {{ old('debtor_type', $cashbon->debtor_type) === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        <div x-show="debtorType === 'karyawan'" x-cloak>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Pilih Karyawan</label>
                            <select name="employee_id"
                                class="w-full input-field">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id', $cashbon->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <h3 class="text-sm font-bold text-surface-700 uppercase tracking-wide">Detail Cashbon</h3>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-surface-500 text-sm">Rp</span>
                                <input type="number" name="amount" value="{{ old('amount', $cashbon->amount) }}" required min="0" step="any"
                                    placeholder="0"
                                    class="w-full border border-surface-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Keterangan</label>
                            <input type="text" name="description" value="{{ old('description', $cashbon->description) }}" maxlength="255"
                                placeholder="Keperluan cashbon (opsional)"
                                class="w-full input-field">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Tanggal Hutang <span class="text-red-500">*</span></label>
                                <input type="date" name="debt_date" value="{{ old('debt_date', $cashbon->debt_date->format('Y-m-d')) }}" required
                                    class="w-full input-field">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Jatuh Tempo</label>
                                <input type="date" name="due_date" value="{{ old('due_date', $cashbon->due_date?->format('Y-m-d')) }}"
                                    class="w-full input-field">
                                <p class="text-xs text-surface-400 mt-1">Kosongkan jika tidak ada jatuh tempo.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Catatan</label>
                            <textarea name="notes" rows="3" maxlength="500"
                                placeholder="Catatan tambahan..."
                                class="w-full input-field">{{ old('notes', $cashbon->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                @if($cashbon->out_transaction_id)
                <div class="bg-brand-50 border border-brand-100 rounded-xl p-4">
                    <p class="text-xs text-brand-600">ℹ️ Perubahan jumlah dan tanggal hutang akan otomatis memperbarui transaksi pengeluaran terkait (ID #{{ $cashbon->out_transaction_id }}).</p>
                </div>
                @endif

                {{-- Receipt Upload --}}
                <div class="stat-card space-y-3" x-data="receiptUploader()">
                    <h3 class="text-sm font-bold text-surface-700 uppercase tracking-wide">📄 Bukti / Resi <span class="text-surface-400 font-normal normal-case">(opsional)</span></h3>

                    <input type="file" name="receipt" accept="image/*" class="hidden"
                           x-ref="fileInput" @change="onFileSelect($event)">

                    {{-- Show existing receipt --}}
                    @if($cashbon->receipt_path && !old('remove_receipt'))
                    <div x-show="!preview" class="space-y-2">
                        <p class="text-xs text-surface-500 font-medium">Bukti saat ini:</p>
                        <img src="{{ asset('storage/' . $cashbon->receipt_path) }}"
                            class="max-h-48 max-w-full object-contain rounded-lg border bg-surface-50 p-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remove_receipt" value="1"
                                {{ old('remove_receipt') ? 'checked' : '' }}
                                class="rounded border-surface-300 text-red-500">
                            <span class="text-xs text-red-500">Hapus bukti ini</span>
                        </label>
                        <p class="text-xs text-surface-400">Atau pilih gambar baru di bawah untuk menggantinya.</p>
                    </div>
                    @endif

                    <template x-if="preview">
                        <div class="relative">
                            <img :src="preview" class="w-full max-h-52 object-contain rounded-lg border bg-surface-50 p-1">
                            <button type="button" @click="clearFile()"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center font-bold shadow">
                                &#215;
                            </button>
                            <p class="text-xs text-surface-400 mt-1">Klik &#215; untuk membatalkan pilihan baru.</p>
                        </div>
                    </template>

                    <template x-if="!preview">
                        <div class="space-y-2">
                            <div class="flex gap-2">
                                <button type="button" @click="$refs.fileInput.click()"
                                    class="flex-1 flex flex-col items-center justify-center h-20 border-2 border-dashed border-surface-300 rounded-lg bg-surface-50 hover:bg-surface-100 transition text-surface-500">
                                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-xs font-medium">📁 Pilih dari Galeri</p>
                                </button>
                                <button type="button" @click="openCamera()"
                                    class="flex-1 flex flex-col items-center justify-center h-20 border-2 border-dashed border-brand-300 rounded-lg bg-brand-50 hover:bg-brand-100 transition text-brand-600">
                                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-xs font-medium">📷 Kamera</p>
                                    <p class="text-xs text-brand-400">Foto langsung</p>
                                </button>
                            </div>
                            <p class="text-xs text-surface-400">Opsional. Maks 5 MB. Format: JPG, PNG, dll.</p>
                        </div>
                    </template>

                    {{-- Camera Modal --}}
                    <div x-show="cameraOpen" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-2xl w-full max-w-sm">
                            <div class="flex items-center justify-between px-4 py-3 border-b">
                                <span class="font-semibold text-surface-700 text-sm">📷 Ambil Foto Bukti</span>
                                <button type="button" @click="closeCamera()" class="text-surface-400 hover:text-surface-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="relative bg-black" x-show="!snapPreview">
                                <video x-ref="video" autoplay playsinline muted class="w-full max-h-64 object-cover"></video>
                                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-3">
                                    <button type="button" @click="snap()"
                                        class="w-14 h-14 rounded-full bg-white border-4 border-brand-400 shadow-lg flex items-center justify-center hover:scale-105 transition">
                                        <div class="w-10 h-10 rounded-full bg-brand-500"></div>
                                    </button>
                                    <button type="button" @click="switchCamera()" title="Ganti kamera"
                                        class="w-10 h-10 rounded-full bg-white/80 flex items-center justify-center shadow text-surface-600 hover:bg-white transition self-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div x-show="snapPreview">
                                <img :src="snapPreview" class="w-full max-h-64 object-contain bg-surface-100">
                                <div class="flex gap-2 p-3">
                                    <button type="button" @click="retake()"
                                        class="flex-1 py-2 text-sm border border-surface-300 rounded-lg hover:bg-surface-50 transition">
                                        🔄 Ulangi
                                    </button>
                                    <button type="button" @click="useSnap()"
                                        class="flex-1 py-2 text-sm bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition font-medium">
                                        ✓ Gunakan Foto
                                    </button>
                                </div>
                            </div>
                            <p x-show="cameraError" class="px-4 pb-3 text-xs text-red-500 text-center" x-text="cameraError"></p>
                            <canvas x-ref="canvas" class="hidden"></canvas>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('cashbons.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
function receiptUploader() {
    return {
        preview: null,
        cameraOpen: false,
        snapPreview: null,
        cameraError: '',
        stream: null,
        facingMode: 'environment',

        onFileSelect(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = ev => this.preview = ev.target.result;
            reader.readAsDataURL(file);
        },

        clearFile() {
            this.preview = null;
            const input = this.$refs.fileInput;
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
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            this.snapPreview = canvas.toDataURL('image/jpeg', 0.85);
        },

        retake() { this.snapPreview = null; },

        useSnap() {
            this.$refs.canvas.toBlob(blob => {
                const file = new File([blob], 'foto-bukti.jpg', { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(file);
                this.$refs.fileInput.files = dt.files;
                this.preview = this.snapPreview;
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
