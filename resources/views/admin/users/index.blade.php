<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="page-title">Manajemen User</h2>
                <p class="text-xs text-surface-500 mt-0.5">Kelola akun dan hak akses pengguna</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <table class="w-full text-sm">
                    <thead class="table-th">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-surface-600 uppercase tracking-wider">Terdaftar</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-surface-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-surface-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-sm mr-3">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-surface-900">{{ $user->name }}</span>
                                        @if($user->id === auth()->id())
                                            <span class="ml-2 text-xs text-surface-400">(Anda)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-surface-500">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @foreach($user->roles as $role)
                                        @php
                                            $colors = [
                                                'admin'            => 'bg-red-100 text-red-800',
                                                'manajer_keuangan' => 'bg-blue-100 text-blue-800',
                                                'akuntan'          => 'bg-green-100 text-green-800',
                                                'viewer'           => 'bg-surface-100 text-surface-800',
                                            ];
                                            $color = $colors[$role->name] ?? 'bg-surface-100 text-surface-800';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $color }}">
                                            {{ str_replace('_', ' ', ucfirst($role->name)) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-surface-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}" title="Edit"
                                           class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  class="inline"
                                                  onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Hapus"
                                                        class="p-1.5 text-red-600 hover:bg-red-50 rounded transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-surface-400">
                                    Belum ada user terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-surface-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
</x-app-layout>
