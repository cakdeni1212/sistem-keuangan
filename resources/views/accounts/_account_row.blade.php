@php
    $indent = $depth > 0 ? 'pl-' . ($depth * 6) : '';
    $textStyle = $depth > 0 ? 'text-surface-500' : 'font-medium text-surface-900';
@endphp
<tr class="hover:bg-surface-50 {{ !$account->is_active ? 'opacity-60' : '' }}">
    <td class="table-td {{ $indent }}">
        <span class="{{ $textStyle }}">{{ $account->code }}</span>
    </td>
    <td class="table-td">
        @if($depth > 0)
            <span class="text-surface-300 mr-1">&#9492;</span>
        @endif
        <span class="{{ $textStyle }}">{{ $account->name }}</span>
    </td>
    <td class="table-td">
        <span class="badge {{ $account->normal_balance === 'debit' ? 'badge-blue' : 'badge-purple' }}">
            {{ $account->normal_balance === 'debit' ? 'Debit' : 'Kredit' }}
        </span>
    </td>
    <td class="table-td">
        <span class="px-2 py-1 text-xs rounded-full font-medium {{ $account->is_active ? 'bg-green-100 text-green-700' : 'bg-surface-100 text-surface-500' }}">
            {{ $account->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
    </td>
    <td class="table-td text-right">
        <div class="flex items-center justify-end gap-1">
            <a href="{{ route('accounts.edit', $account) }}" title="Edit"
               class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            @if($account->is_active)
            <form method="POST" action="{{ route('accounts.destroy', $account) }}" class="inline"
                  onsubmit="return confirm('Nonaktifkan akun {{ addslashes($account->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" title="Nonaktifkan"
                        class="p-1.5 text-red-600 hover:bg-red-50 rounded transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636a9 9 0 11-12.728 0M12 3v4"/>
                    </svg>
                </button>
            </form>
            @endif
        </div>
    </td>
</tr>
