<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="page-title">💳 Cashbon</h2>
                <p class="text-xs text-surface-500 mt-0.5">Manajemen piutang / pinjaman karyawan & pelanggan</p>
            </div>
            @can('create cashbon')
            <a href="{{ route('cashbons.create') }}" class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition">
                + Tambah Cashbon
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5"
         x-data="{ lightboxOpen: false, lightboxSrc: '' }"
         @open-lightbox.window="lightboxOpen=true; lightboxSrc=$event.detail.src"
         @keydown.escape.window="lightboxOpen=false; lightboxSrc=''">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card flex items-center gap-4">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center text-xl">💳</div>
                <div>
                    <p class="text-xs text-surface-500">Piutang Belum Bayar{{ $periodLabel ? ' ('.$periodLabel.')' : '' }}</p>
                    <p class="text-xl font-extrabold text-yellow-700">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-xl">✅</div>
                <div>
                    <p class="text-xs text-surface-500">Total Lunas{{ $periodLabel ? ' ('.$periodLabel.')' : '' }}</p>
                    <p class="text-xl font-extrabold text-green-700">Rp {{ number_format($totalLunas, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-4">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center text-xl">⚠️</div>
                <div>
                    <p class="text-xs text-surface-500">Jatuh Tempo Terlewat</p>
                    <p class="text-xl font-extrabold text-red-700">Rp {{ number_format($totalOverdue, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Flash message --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
        @endif

        {{-- Filter bar --}}
        <div class="stat-card">
            <form method="GET" action="{{ route('cashbons.index') }}" id="cashbon-filter-form" class="flex flex-wrap gap-3 items-end">

                {{-- Bulan & Tahun --}}
                <div>
                    <label class="block text-xs font-medium text-surface-600 mb-1">Bulan</label>
                    <select name="month" onchange="document.getElementById('cashbon-filter-form').submit()"
                            class="border border-surface-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Bulan</option>
                        @foreach($months as $n => $nm)
                        <option value="{{ $n }}" @selected($n == $filterMonth)>{{ $nm }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-surface-600 mb-1">Tahun</label>
                    <select name="year" onchange="document.getElementById('cashbon-filter-form').submit()"
                            class="border border-surface-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $y)
                        <option value="{{ $y }}" @selected($y == $filterYear)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-medium text-surface-600 mb-1">Cari Nama</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama peminjam..."
                        class="w-full border border-surface-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-surface-600 mb-1">Status</label>
                    <select name="status" class="border border-surface-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-surface-600 mb-1">Tipe</label>
                    <select name="debtor_type" class="border border-surface-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Tipe</option>
                        <option value="karyawan"  {{ request('debtor_type') === 'karyawan'  ? 'selected' : '' }}>Karyawan</option>
                        <option value="pelanggan" {{ request('debtor_type') === 'pelanggan' ? 'selected' : '' }}>Pelanggan</option>
                        <option value="supplier"  {{ request('debtor_type') === 'supplier'  ? 'selected' : '' }}>Supplier</option>
                        <option value="lainnya"   {{ request('debtor_type') === 'lainnya'   ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-1.5 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition">Filter</button>
                    @if(request()->hasAny(['search','status','debtor_type','month','year']))
                    <a href="{{ route('cashbons.index') }}" class="px-4 py-1.5 bg-white border border-surface-300 text-surface-700 text-sm font-medium rounded-lg hover:bg-surface-50 transition">Reset</a>
                    @endif
                </div>

                {{-- Shortcut bulan --}}
                <div class="w-full flex flex-wrap gap-1.5 pt-1 border-t border-surface-100">
                    <span class="text-xs text-surface-400 self-center mr-1">Cepat:</span>
                    <a href="{{ route('cashbons.index', ['month' => date('n'), 'year' => date('Y')] + request()->except(['month','year'])) }}"
                       class="px-2.5 py-1 text-xs rounded-full border transition {{ ($filterMonth == date('n') && $filterYear == date('Y')) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400' }}">
                        Bulan Ini
                    </a>
                    @php $prevM = date('n') == 1 ? 12 : date('n') - 1; $prevY = date('n') == 1 ? date('Y') - 1 : date('Y'); @endphp
                    <a href="{{ route('cashbons.index', ['month' => $prevM, 'year' => $prevY] + request()->except(['month','year'])) }}"
                       class="px-2.5 py-1 text-xs rounded-full border transition {{ ($filterMonth == $prevM && $filterYear == $prevY) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400' }}">
                        Bulan Lalu
                    </a>
                    <a href="{{ route('cashbons.index', ['year' => date('Y')] + request()->except(['month','year'])) }}"
                       class="px-2.5 py-1 text-xs rounded-full border transition {{ (!$filterMonth && $filterYear == date('Y')) ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-surface-600 border-surface-300 hover:border-brand-400' }}">
                        Tahun Ini
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="px-5 py-3.5 border-b border-surface-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-surface-700">Daftar Cashbon</h3>
                <span class="text-xs text-surface-400">{{ $cashbons->count() }} data</span>
            </div>

            @if($cashbons->isEmpty())
            <div class="py-16 text-center text-surface-400">
                <p class="text-4xl mb-3">💳</p>
                <p class="text-sm">Belum ada data cashbon.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="table-th">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-surface-600">Nama Peminjam</th>
                            <th class="px-4 py-3 text-left font-semibold text-surface-600">Tipe</th>
                            <th class="px-4 py-3 text-right font-semibold text-surface-600">Jumlah</th>
                            <th class="px-4 py-3 text-center font-semibold text-surface-600 hidden md:table-cell">Tgl Hutang</th>
                            <th class="px-4 py-3 text-center font-semibold text-surface-600 hidden lg:table-cell">Jatuh Tempo</th>
                            <th class="px-4 py-3 text-center font-semibold text-surface-600">Status</th>
                            <th class="px-4 py-3 text-center font-semibold text-surface-600 hidden md:table-cell">Tersinkron</th>
                            <th class="px-4 py-3 text-center font-semibold text-surface-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach($cashbons as $cashbon)
                        <tr class="hover:bg-surface-50 transition">
                            <td class="px-4 py-3">
                                <p class="font-medium text-surface-800">{{ $cashbon->debtor_name }}</p>
                                @if($cashbon->description)
                                <p class="text-xs text-surface-400 truncate max-w-[180px]">{{ $cashbon->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="capitalize text-surface-600 text-xs">{{ $cashbon->debtor_type }}</span>
                                @if($cashbon->employee)
                                <span class="block text-xs text-brand-500">{{ $cashbon->employee->name }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-surface-800">
                                Rp {{ number_format($cashbon->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center text-surface-600 hidden md:table-cell">
                                {{ $cashbon->debt_date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-center hidden lg:table-cell">
                                @if($cashbon->due_date)
                                    <span class="{{ $cashbon->is_overdue ? 'text-red-600 font-semibold' : 'text-surface-600' }}">
                                        {{ $cashbon->due_date->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-surface-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($cashbon->status === 'lunas')
                                    <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Lunas</span>
                                @elseif($cashbon->is_overdue)
                                    <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Terlambat</span>
                                @else
                                    <span class="inline-block px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center hidden md:table-cell">
                                <div class="flex items-center justify-center gap-1">
                                    @if($cashbon->out_transaction_id)
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-brand-50 text-brand-600 text-xs rounded" title="Transaksi pengeluaran terhubung">↑ Keluar</span>
                                    @endif
                                    @if($cashbon->in_transaction_id)
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-green-50 text-green-600 text-xs rounded" title="Transaksi pemasukan terhubung">↓ Masuk</span>
                                    @endif
                                    @if(!$cashbon->out_transaction_id && !$cashbon->in_transaction_id)
                                        <span class="text-surface-400 text-xs">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    {{-- Tandai Lunas --}}
                                    @can('edit cashbon')
                                    @if($cashbon->status === 'belum_bayar')
                                    <button type="button"
                                        x-data
                                        @click="$dispatch('open-modal', 'bayar-{{ $cashbon->id }}')"
                                        class="text-xs text-green-600 hover:underline font-medium">
                                        ✓ Lunas
                                    </button>
                                    @endif
                                    @endcan

                                    {{-- Edit --}}
                                    @can('edit cashbon')
                                    @if($cashbon->status === 'belum_bayar')
                                    <a href="{{ route('cashbons.edit', $cashbon) }}" class="text-xs text-yellow-600 hover:underline">Edit</a>
                                    @endif
                                    @endcan

                                    {{-- Hapus --}}
                                    @can('delete cashbon')
                                    <form method="POST" action="{{ route('cashbons.destroy', $cashbon) }}"
                                        onsubmit="return confirm('Hapus cashbon ini? Transaksi terkait juga akan dihapus.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                                    </form>
                                    @endcan
                                    {{-- Receipt viewer --}}
                                    @if($cashbon->receipt_path)
                                    <button type="button"
                                        data-src="{{ asset('storage/' . $cashbon->receipt_path) }}"
                                        @click="$dispatch('open-lightbox', { src: $el.dataset.src })"
                                        class="text-xs text-blue-500 hover:underline" title="Lihat Bukti Cashbon">
                                        &#128196; Bukti
                                    </button>
                                    @endif
                                    @if($cashbon->payment_receipt_path)
                                    <button type="button"
                                        data-src="{{ asset('storage/' . $cashbon->payment_receipt_path) }}"
                                        @click="$dispatch('open-lightbox', { src: $el.dataset.src })"
                                        class="text-xs text-emerald-600 hover:underline" title="Lihat Bukti Pembayaran">
                                        &#129534; Resi Bayar
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Tandai Lunas Modal --}}
                        @can('edit cashbon')
                        @if($cashbon->status === 'belum_bayar')
                        <x-modal name="bayar-{{ $cashbon->id }}" :show="false">
                            <form method="POST" action="{{ route('cashbons.mark-paid', $cashbon) }}" class="p-6"
                                  enctype="multipart/form-data"
                                  x-data="receiptUploader()">
                                @csrf
                                <h2 class="text-base font-bold text-surface-800 mb-1">✓ Tandai Lunas</h2>
                                <p class="text-sm text-surface-500 mb-4">
                                    Cashbon <span class="font-semibold text-surface-700">{{ $cashbon->debtor_name }}</span>
                                    sebesar <span class="font-semibold text-brand-700">Rp {{ number_format($cashbon->amount, 0, ',', '.') }}</span>
                                </p>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-surface-700 mb-1">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                                    <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" required
                                        class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>

                                {{-- Payment Receipt Upload --}}
                                <div class="mb-4 space-y-2">
                                    <label class="block text-sm font-medium text-surface-700">&#128196; Bukti Pembayaran <span class="text-surface-400 font-normal">(opsional)</span></label>

                                    <input type="file" name="payment_receipt" accept="image/*" class="hidden"
                                           x-ref="fileInput" @change="onFileSelect($event)">

                                    <template x-if="preview">
                                        <div class="relative">
                                            <img :src="preview" class="w-full max-h-40 object-contain rounded-lg border bg-surface-50 p-1">
                                            <button type="button" @click="clearFile()"
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center font-bold shadow">
                                                &#215;
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="!preview">
                                        <div class="flex gap-2">
                                            <button type="button" @click="$refs.fileInput.click()"
                                                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 border-2 border-dashed border-surface-300 rounded-lg text-xs text-surface-500 hover:border-brand-400 hover:text-brand-600 transition">
                                                &#128193; Galeri
                                            </button>
                                            <button type="button" @click="openCamera()"
                                                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 border-2 border-dashed border-brand-300 rounded-lg text-xs text-brand-600 bg-brand-50 hover:bg-brand-100 transition">
                                                &#128247; Kamera
                                            </button>
                                        </div>
                                    </template>

                                    {{-- Camera Modal --}}
                                    <div x-show="cameraOpen" x-cloak
                                         class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70 p-4">
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

                                <p class="text-xs text-surface-400 mb-4">Pembayaran ini akan otomatis tercatat sebagai pemasukan (Pembayaran Cashbon).</p>
                                <div class="flex gap-3 justify-end">
                                    <button type="button"
                                        x-data
                                        @click="$dispatch('close')"
                                        class="px-4 py-2 bg-white border border-surface-300 text-surface-700 text-sm font-medium rounded-lg hover:bg-surface-50 transition">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                        Tandai Lunas
                                    </button>
                                </div>
                            </form>
                        </x-modal>
                        @endif
                        @endcan

                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        {{-- Lightbox --}}
        <div x-show="lightboxOpen" @click="lightboxOpen=false; lightboxSrc=''"
             class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4"
             x-cloak>
            <div class="relative max-w-2xl w-full" @click.stop>
                <img :src="lightboxSrc" class="w-full max-h-[85vh] object-contain rounded-lg shadow-2xl bg-white">
                <button @click="lightboxOpen=false; lightboxSrc=''"
                    class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold shadow-lg">
                    &#215;
                </button>
            </div>
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
