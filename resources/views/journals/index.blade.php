<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="page-title">Jurnal Umum</h2>
                <p class="text-xs text-surface-400 mt-0.5">Pencatatan ayat jurnal akuntansi</p>
            </div>
            @can('view transactions')
            <a href="{{ route('journals.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Jurnal
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

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

        {{-- Filter --}}
        <div class="filter-card">
            <form method="GET" action="{{ route('journals.index') }}" id="jv-filter-form"
                  class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">

                <div class="col-span-1 sm:col-span-3 lg:col-span-6">
                    <label class="block text-xs font-semibold text-surface-600 mb-1.5">Cari No. Jurnal</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-surface-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                        </span>
                        <input type="text" name="search" id="jv-search"
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="JV-202501-0001..."
                               class="block w-full pl-11 rounded-xl border-surface-300 bg-white px-4 py-2.5 text-sm text-surface-900 placeholder-surface-400 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 transition shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-surface-600 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input-field">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-surface-600 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input-field">
                </div>

                <div class="flex items-center gap-2 self-end">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('journals.index') }}" class="btn-secondary text-xs">Reset</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-surface-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-wrap">
                    <thead>
                        <tr class="bg-surface-50">
                            <th class="table-th table-head">No. Jurnal</th>
                            <th class="table-th table-head">Tanggal</th>
                            <th class="table-th table-head">Tipe</th>
                            <th class="table-th table-head">Deskripsi</th>
                            <th class="table-th table-head text-right">Debit</th>
                            <th class="table-th table-head text-right">Kredit</th>
                            <th class="table-th table-head text-center">Status</th>
                            <th class="table-th table-head text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @forelse($journals as $jv)
                        <tr class="hover:bg-surface-50 transition-colors">
                            <td class="table-td font-medium text-surface-900 whitespace-nowrap">{{ $jv->journal_number }}</td>
                            <td class="table-td whitespace-nowrap">{{ $jv->journal_date->format('d/m/Y') }}</td>
                            <td class="table-td">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-surface-100 text-surface-600 font-medium capitalize">
                                    {{ str_replace('_', ' ', $jv->journal_type) }}
                                </span>
                            </td>
                            <td class="table-td text-surface-400 max-w-xs truncate">{{ $jv->description ?? '—' }}</td>
                            <td class="table-td text-right font-semibold text-blue-600 whitespace-nowrap">
                                Rp {{ number_format($jv->total_debit, 0, ',', '.') }}
                            </td>
                            <td class="table-td text-right font-semibold text-amber-600 whitespace-nowrap">
                                Rp {{ number_format($jv->total_credit, 0, ',', '.') }}
                            </td>
                            <td class="table-td text-center">
                                @if($jv->is_posted)
                                    <span class="badge badge-green">Posted</span>
                                @else
                                    <span class="badge badge-gray">Draft</span>
                                @endif
                            </td>
                            <td class="table-td text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('journals.show', $jv) }}" title="Detail"
                                       class="w-8 h-8 flex items-center justify-center text-surface-400 hover:text-surface-600 hover:bg-surface-100 rounded-xl transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if(!$jv->is_posted)
                                    <form method="POST" action="{{ route('journals.post', $jv) }}" class="inline">
                                        @csrf
                                        <button type="submit" title="Posting"
                                                class="w-8 h-8 flex items-center justify-center text-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-surface-400">Belum ada jurnal.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($journals->hasPages())
                <div class="px-6 py-4 border-t border-surface-100">{{ $journals->links() }}</div>
            @endif
        </div>
    </div>

    <script>
    (function () {
        let timer;
        document.getElementById('jv-search').addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(() => {
                document.getElementById('jv-filter-form').submit();
            }, 400);
        });
    })();
    </script>
</x-app-layout>
