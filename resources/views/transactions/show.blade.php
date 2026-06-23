<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.index') }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Detail Transaksi #{{ $transaction->id }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 bg-surface-50 flex items-center justify-between">
                    @php
                        $statusColors = ['draft'=>'gray','pending'=>'yellow','approved'=>'green','rejected'=>'red'];
                        $c = $statusColors[$transaction->status];
                    @endphp
                    <span class="px-3 py-1 text-sm rounded-full font-medium bg-{{ $c }}-100 text-{{ $c }}-700">
                        {{ $transaction->status_label }}
                    </span>
                    <div class="flex gap-2">
                        @can('edit transactions')
                        @if(!in_array($transaction->status, ['approved','rejected']))
                        <a href="{{ route('transactions.edit', $transaction) }}"
                           class="px-3 py-1 text-sm bg-brand-600 text-white rounded-xl hover:bg-brand-700">Edit</a>
                        @endif
                        @endcan
                        @can('approve transactions')
                        @if($transaction->status === 'pending')
                        <form method="POST" action="{{ route('transactions.approve', $transaction) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1 text-sm bg-green-600 text-white rounded-xl hover:bg-green-700">✓ Setujui</button>
                        </form>
                        @endif
                        @endcan
                    </div>
                </div>

                <dl class="space-y-0">
                    <div class="px-6 py-4 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-surface-500">Tanggal</dt>
                        <dd class="text-sm text-surface-900 col-span-2">{{ $transaction->transaction_date->format('d F Y') }}</dd>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-surface-500">Jenis Transaksi</dt>
                        <dd class="text-sm text-surface-900 col-span-2">{{ $transaction->transactionType->name }}</dd>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-surface-500">Kategori</dt>
                        <dd class="col-span-2">
                            @php $cat = $transaction->transactionType->category; @endphp
                            <span class="px-2 py-1 text-xs rounded-full font-medium {{ $cat === 'pemasukan' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($cat) }}
                            </span>
                        </dd>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-surface-500">Jumlah</dt>
                        <dd class="text-lg font-bold col-span-2 {{ $cat === 'pemasukan' ? 'text-green-700' : 'text-red-700' }}">
                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-surface-500">Keterangan</dt>
                        <dd class="text-sm text-surface-900 col-span-2">{{ $transaction->description ?? '-' }}</dd>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-surface-500">Nota</dt>
                        <dd class="col-span-2">
                            @if($transaction->nota_path)
                                @php $ext = pathinfo($transaction->nota_path, PATHINFO_EXTENSION); @endphp
                                @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                                    <div x-data="{ open: false }">
                                        <img src="{{ $transaction->nota_url }}" alt="Nota"
                                             class="max-w-xs rounded-xl border shadow cursor-zoom-in hover:opacity-90 transition"
                                             @click="open = true">
                                        <div x-show="open" x-cloak
                                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
                                             @click.self="open = false"
                                             @keydown.escape.window="open = false">
                                            <div class="relative max-w-3xl w-full">
                                                <button @click="open = false"
                                                        class="absolute -top-10 right-0 text-white text-3xl leading-none hover:text-surface-300">&times;</button>
                                                <img src="{{ $transaction->nota_url }}" alt="Nota"
                                                     class="w-full max-h-[85vh] object-contain rounded-xl shadow-2xl">
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $transaction->nota_url }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-sm text-brand-600 hover:underline">
                                        📄 Buka PDF Nota
                                    </a>
                                @endif
                            @else
                                <span class="text-sm text-surface-400">Tidak ada nota</span>
                            @endif
                        </dd>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-surface-500">Dibuat oleh</dt>
                        <dd class="text-sm text-surface-900 col-span-2">{{ $transaction->creator->name }}</dd>
                    </div>
                    @if($transaction->approver)
                    <div class="px-6 py-4 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-surface-500">{{ $transaction->status === 'approved' ? 'Disetujui' : 'Ditolak' }} oleh</dt>
                        <dd class="text-sm text-surface-900 col-span-2">
                            {{ $transaction->approver->name }}
                            <span class="text-surface-400">— {{ $transaction->approved_at->format('d M Y H:i') }}</span>
                        </dd>
                    </div>
                    @endif
                    @if($transaction->rejection_reason)
                    <div class="px-6 py-4 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-surface-500">Alasan Penolakan</dt>
                        <dd class="text-sm text-red-600 col-span-2">{{ $transaction->rejection_reason }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Reject form --}}
            @can('approve transactions')
            @if($transaction->status === 'pending')
            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="text-sm font-semibold text-surface-700 mb-3">Tolak Transaksi</h3>
                <form method="POST" action="{{ route('transactions.reject', $transaction) }}">
                    @csrf
                    <div class="flex gap-3">
                        <input type="text" name="rejection_reason" required
                            class="flex-1 input-field !resize-y"
                            placeholder="Masukkan alasan penolakan...">
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white text-sm rounded-xl hover:bg-red-700">
                            ✗ Tolak
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('rejection_reason')" class="mt-1" />
                </form>
            </div>
            @endif
            @endcan

        </div>
    </div>
</x-app-layout>
