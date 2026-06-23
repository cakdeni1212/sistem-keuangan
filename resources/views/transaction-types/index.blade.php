<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="page-title">Jenis Transaksi</h2>
                <p class="text-xs text-surface-500 mt-0.5">Kelola jenis pemasukan dan pengeluaran</p>
            </div>
            <a href="{{ route('transaction-types.create') }}"
               class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Jenis
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-lg">{{ session('error') }}</div>
            @endif

            @foreach(['pengeluaran' => 'Pengeluaran', 'pemasukan' => 'Pemasukan'] as $cat => $label)
            @php $group = $types->where('category', $cat); @endphp
            <div class="card mb-6">
                <div class="px-6 py-3 {{ $cat === 'pemasukan' ? 'bg-green-50 border-b border-green-200' : 'bg-red-50 border-b border-red-200' }}">
                    <h3 class="font-semibold text-sm {{ $cat === 'pemasukan' ? 'text-green-800' : 'text-red-800' }}">
                        {{ $label }} ({{ $group->count() }} jenis)
                    </h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="table-th">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Nama</th>
                            @if($cat === 'pengeluaran')
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Grup</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-surface-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @forelse($group as $type)
                        <tr class="hover:bg-surface-50">
                            <td class="px-6 py-3 text-sm font-medium text-surface-900">{{ $type->name }}</td>
                            @if($cat === 'pengeluaran')
                            <td class="px-6 py-3">
                                @if($type->grup === 'Dapur')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">🍳 Dapur</span>
                                @elseif($type->grup === 'BAR')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">☕ BAR</span>
                                @elseif($type->grup === 'Operasional')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-surface-100 text-surface-600">⚙️ Operasional</span>
                                @else
                                    <span class="text-xs text-surface-400">—</span>
                                @endif
                            </td>
                            @endif
                            <td class="px-6 py-3 text-sm text-surface-500">{{ $type->description ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 text-xs rounded-full font-medium {{ $type->is_active ? 'bg-green-100 text-green-700' : 'bg-surface-100 text-surface-500' }}">
                                    {{ $type->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('transaction-types.edit', $type) }}" title="Edit"
                                       class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('transaction-types.destroy', $type) }}" class="inline"
                                          onsubmit="return confirm('Hapus jenis transaksi {{ addslashes($type->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $cat === 'pengeluaran' ? 5 : 4 }}" class="px-6 py-6 text-center text-sm text-surface-400">Belum ada jenis transaksi {{ $label }}.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endforeach

            {{-- No pagination needed — all types loaded at once --}}
        </div>
</x-app-layout>
