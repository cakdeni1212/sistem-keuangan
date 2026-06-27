<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('journals.index') }}" class="btn-secondary text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
            <div>
                <h2 class="page-title">Detail Jurnal {{ $journal->journal_number }}</h2>
                <p class="text-xs text-surface-400 mt-0.5">{{ $journal->journal_date->format('d F Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 bg-surface-50 flex items-center justify-between">
                    <span class="badge {{ $journal->is_posted ? 'badge-green' : 'badge-gray' }}">
                        {{ $journal->is_posted ? 'Posted' : 'Draft' }}
                    </span>
                    <div class="flex gap-2">
                        @if($journal->is_posted)
                        <form method="POST" action="{{ route('journals.unpost', $journal) }}">
                            @csrf
                            <button type="submit"
                                    class="btn-secondary text-xs"
                                    onclick="return confirm('Unpost jurnal {{ $journal->journal_number }}?')">
                                Unpost
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('journals.post', $journal) }}">
                            @csrf
                            <button type="submit" class="btn-primary text-xs">
                                Posting Jurnal
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <div class="p-6 grid grid-cols-2 gap-y-4 gap-x-8">
                    <div>
                        <dt class="text-xs font-semibold text-surface-500">No. Jurnal</dt>
                        <dd class="text-sm text-surface-900 font-medium">{{ $journal->journal_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-surface-500">Tanggal</dt>
                        <dd class="text-sm text-surface-900">{{ $journal->journal_date->format('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-surface-500">Tipe Jurnal</dt>
                        <dd class="text-sm text-surface-900 capitalize">{{ str_replace('_', ' ', $journal->journal_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-surface-500">Referensi</dt>
                        <dd class="text-sm text-surface-900">{{ $journal->reference ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs font-semibold text-surface-500">Deskripsi</dt>
                        <dd class="text-sm text-surface-900">{{ $journal->description ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-surface-500">Dibuat oleh</dt>
                        <dd class="text-sm text-surface-900">{{ $journal->creator->name }}</dd>
                    </div>
                    @if($journal->is_posted)
                    <div>
                        <dt class="text-xs font-semibold text-surface-500">Diposting pada</dt>
                        <dd class="text-sm text-surface-900">{{ $journal->posted_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Lines --}}
            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 bg-surface-50">
                    <h3 class="text-sm font-semibold text-surface-700">Ayat Jurnal</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-wrap">
                        <thead>
                            <tr class="bg-surface-50">
                                <th class="table-th table-head text-center w-12">No</th>
                                <th class="table-th table-head">Kode Akun</th>
                                <th class="table-th table-head">Nama Akun</th>
                                <th class="table-th table-head">Deskripsi</th>
                                <th class="table-th table-head text-right">Debit</th>
                                <th class="table-th table-head text-right">Kredit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100">
                            @foreach($journal->lines as $i => $line)
                            <tr class="hover:bg-surface-50 transition-colors">
                                <td class="table-td text-center text-surface-400">{{ $i + 1 }}</td>
                                <td class="table-td font-medium text-surface-900 whitespace-nowrap">{{ $line->account?->code ?? '—' }}</td>
                                <td class="table-td text-surface-700">{{ $line->account?->name ?? '—' }}</td>
                                <td class="table-td text-surface-400 max-w-xs truncate">{{ $line->description ?? '—' }}</td>
                                <td class="table-td text-right font-semibold text-blue-600 whitespace-nowrap">
                                    {{ $line->debit > 0 ? 'Rp ' . number_format($line->debit, 0, ',', '.') : '—' }}
                                </td>
                                <td class="table-td text-right font-semibold text-amber-600 whitespace-nowrap">
                                    {{ $line->credit > 0 ? 'Rp ' . number_format($line->credit, 0, ',', '.') : '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-surface-50 font-bold border-t-2 border-surface-200">
                                <td class="table-td text-center" colspan="4">Total</td>
                                <td class="table-td text-right text-blue-600 whitespace-nowrap">Rp {{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                                <td class="table-td text-right text-amber-600 whitespace-nowrap">Rp {{ number_format($journal->total_credit, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
