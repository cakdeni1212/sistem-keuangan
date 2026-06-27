<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="page-title">Chart of Accounts</h2>
                <p class="text-xs text-surface-500 mt-0.5">Daftar akun keuangan</p>
            </div>
            <a href="{{ route('accounts.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Akun
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        @php
            $typeLabels = [
                'asset' => 'Aset',
                'liability' => 'Liabilitas',
                'equity' => 'Ekuitas',
                'revenue' => 'Pendapatan',
                'expense' => 'Beban',
            ];
            $typeColors = [
                'asset' => 'border-b-blue-200',
                'liability' => 'border-b-red-200',
                'equity' => 'border-b-purple-200',
                'revenue' => 'border-b-green-200',
                'expense' => 'border-b-orange-200',
            ];
            $typeBg = [
                'asset' => 'bg-blue-100 text-blue-800',
                'liability' => 'bg-red-100 text-red-800',
                'equity' => 'bg-purple-100 text-purple-800',
                'revenue' => 'bg-green-100 text-green-800',
                'expense' => 'bg-orange-100 text-orange-800',
            ];
        @endphp

        @foreach($typeLabels as $type => $label)
            @php
                $group = $accounts->get($type, collect());
                $parents = $group->whereNull('parent_id');
                $childrenByParent = $group->whereNotNull('parent_id')->groupBy('parent_id');
            @endphp
            <div class="card mb-6">
                <div class="px-6 py-3 bg-surface-50 border-b {{ $typeColors[$type] ?? '' }}">
                    <h3 class="font-semibold text-sm text-surface-800">
                        {{ $label }} ({{ $group->count() }} akun)
                    </h3>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-50/50">
                            <th class="table-th">Kode</th>
                            <th class="table-th">Nama Akun</th>
                            <th class="table-th">Saldo Normal</th>
                            <th class="table-th">Status</th>
                            <th class="table-th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @forelse($parents as $parent)
                            @include('accounts._account_row', ['account' => $parent, 'depth' => 0])
                            @if(isset($childrenByParent[$parent->id]))
                                @foreach($childrenByParent[$parent->id] as $child)
                                    @include('accounts._account_row', ['account' => $child, 'depth' => 1])
                                @endforeach
                            @endif
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-surface-400">Belum ada akun {{ $label }}.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach

    </div>
</x-app-layout>
