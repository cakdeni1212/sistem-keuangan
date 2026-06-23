<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="page-title">📦 Stok Bahan Baku</h2>
            @can('create raw-material')
            <a href="{{ route('raw-materials.create') }}"
               class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition">
                + Tambah Bahan Baku
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            {{ session('error') }}
        </div>
        @endif

        {{-- Summary cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="card p-5">
                <p class="text-sm text-surface-500">Total Item</p>
                <p class="text-2xl font-bold text-surface-900 mt-1">{{ $totalItems }}</p>
            </div>
            <div class="card p-5">
                <p class="text-sm text-surface-500">Total Aktif</p>
                <p class="text-2xl font-bold text-green-700 mt-1">{{ $totalActive }}</p>
            </div>
            <div class="card p-5">
                <p class="text-sm text-surface-500">Stok Rendah (&lt;100)</p>
                <p class="text-2xl font-bold {{ $lowStock > 0 ? 'text-red-600' : 'text-surface-900' }} mt-1">{{ $lowStock }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-50 text-left">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide">#</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide">Nama</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide">Satuan</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide">Kategori</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide text-right">Stok Sekarang</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide text-right">Harga/Satuan</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide text-center">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide text-center">Digunakan di</th>
                            <th class="px-4 py-3 text-xs font-semibold text-surface-500 uppercase tracking-wide text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @forelse($rawMaterials as $item)
                        <tr class="hover:bg-surface-50 transition">
                            <td class="px-4 py-3 text-surface-400">{{ $loop->iteration + ($rawMaterials->currentPage() - 1) * $rawMaterials->perPage() }}</td>
                            <td class="px-4 py-3 font-medium text-surface-900">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-surface-600">{{ $item->unit }}</td>
                            <td class="px-4 py-3 text-surface-500">{{ $item->category ?: '—' }}</td>
                            <td class="px-4 py-3 text-right font-medium {{ (float)$item->stock_quantity < 100 ? 'text-red-600' : 'text-surface-800' }}">
                                {{ number_format($item->stock_quantity, 3, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right text-surface-700">Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($item->is_active)
                                    <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-surface-500">
                                {{ $item->ingredients_count }} produk
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @can('edit raw-material')
                                    <a href="{{ route('raw-materials.edit', $item) }}"
                                       class="px-3 py-1 text-xs bg-brand-50 text-brand-700 hover:bg-brand-100 rounded-md transition">Edit</a>
                                    @endcan
                                    @can('delete raw-material')
                                    <form action="{{ route('raw-materials.destroy', $item) }}" method="POST"
                                          onsubmit="return confirm('Hapus bahan baku \'{{ $item->name }}\'?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1 text-xs bg-red-50 text-red-700 hover:bg-red-100 rounded-md transition">
                                            Hapus
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-surface-400">
                                Belum ada data bahan baku. <a href="{{ route('raw-materials.create') }}" class="text-brand-600 hover:underline">Tambah sekarang</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rawMaterials->hasPages())
            <div class="px-4 py-3 border-t bg-surface-50">
                {{ $rawMaterials->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
