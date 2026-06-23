<x-kasir-layout>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5" x-data="riwayatApp()">

        {{-- Flash message --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
        @endif

        {{-- Filter bar --}}
        <div class="bg-white rounded-xl border shadow-sm px-5 py-4">
            <form method="GET" action="{{ route('kasir.riwayat') }}" id="riwayat-filter" class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-gray-600 mr-1">🗓 Periode:</span>
                <select name="month" onchange="document.getElementById('riwayat-filter').submit()"
                        class="border-gray-300 rounded-lg text-sm py-1.5 pr-8">
                    @foreach($months as $n => $name)
                        <option value="{{ $n }}" @selected($n == $month)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="year" onchange="document.getElementById('riwayat-filter').submit()"
                        class="border-gray-300 rounded-lg text-sm py-1.5 pr-8">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>

                @php $nowM = now()->month; $nowY = now()->year; $prevM = now()->subMonth()->month; $prevY = now()->subMonth()->year; @endphp
                <a href="{{ route('kasir.riwayat', ['month' => $nowM, 'year' => $nowY]) }}"
                   class="px-3 py-1 text-xs rounded-full border transition {{ ($month==$nowM && $year==$nowY) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('kasir.riwayat', ['month' => $prevM, 'year' => $prevY]) }}"
                   class="px-3 py-1 text-xs rounded-full border transition {{ ($month==$prevM && $year==$prevY) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400' }}">
                    Bulan Lalu
                </a>
            </form>
        </div>

        {{-- Summary cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border shadow-sm p-4 text-center">
                <p class="text-xs text-gray-400">Total Transaksi</p>
                <p class="text-2xl font-extrabold text-gray-800">{{ $totalTx }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $periodLabel }}</p>
            </div>
            <div class="bg-white rounded-xl border shadow-sm p-4 text-center">
                <p class="text-xs text-gray-400">Total Omset</p>
                <p class="text-lg font-extrabold text-indigo-700">Rp {{ number_format($totalOmset, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $periodLabel }}</p>
            </div>
            <div class="bg-white rounded-xl border shadow-sm p-4 text-center">
                <p class="text-xs text-purple-500">📱 QRIS</p>
                <p class="text-lg font-extrabold text-purple-700">Rp {{ number_format($totalQris, 0, ',', '.') }}</p>
                @if($totalOmset > 0)
                <p class="text-xs text-gray-400 mt-0.5">{{ round($totalQris/$totalOmset*100) }}%</p>
                @endif
            </div>
            <div class="bg-white rounded-xl border shadow-sm p-4 text-center">
                <p class="text-xs text-green-500">💵 Tunai</p>
                <p class="text-lg font-extrabold text-green-700">Rp {{ number_format($totalTunai, 0, ',', '.') }}</p>
                @if($totalOmset > 0)
                <p class="text-xs text-gray-400 mt-0.5">{{ round($totalTunai/$totalOmset*100) }}%</p>
                @endif
            </div>
        </div>

        {{-- Best Sellers --}}
        @if($bestSellers->count() > 0)
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b">
                <h3 class="text-sm font-bold text-gray-700">🏆 Produk Terlaris — {{ $periodLabel }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">#</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Produk</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500">Terjual</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($bestSellers as $i => $bs)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 text-gray-400 font-medium">
                                {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : $i+1)) }}
                            </td>
                            <td class="px-4 py-2.5 font-medium text-gray-800">{{ $bs->product_name }}</td>
                            <td class="px-4 py-2.5 text-right font-bold text-gray-700">{{ number_format($bs->total_qty) }} unit</td>
                            <td class="px-4 py-2.5 text-right font-bold text-indigo-700">Rp {{ number_format($bs->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Transaksi list (grouped by date) --}}
        @if($sessions->isEmpty())
        <div class="bg-white rounded-xl border shadow-sm py-16 text-center text-gray-400">
            <p class="text-4xl mb-3">📋</p>
            <p class="text-sm">Belum ada transaksi kasir untuk {{ $periodLabel }}.</p>
        </div>
        @else
        @foreach($sessions->groupBy(fn($s) => $s->date->format('Y-m-d')) as $dateStr => $daySessions)
        @php $dateObj = \Carbon\Carbon::parse($dateStr); @endphp
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b bg-gray-50 flex items-center justify-between">
                <div>
                    <span class="text-sm font-bold text-gray-700">{{ $dateObj->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <span class="text-sm font-extrabold text-indigo-700">
                    Rp {{ number_format($daySessions->sum('total_amount'), 0, ',', '.') }}
                    <span class="text-xs text-gray-400 font-normal">({{ $daySessions->count() }} tx)</span>
                </span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($daySessions as $session)
                <div x-data="{ open: false }" class="px-5 py-3">
                    <div class="flex items-center justify-between cursor-pointer">
                        <div class="flex items-center gap-3 flex-1" @click="open = !open">
                            <span class="text-lg">{{ $session->payment_method === 'qris' ? '📱' : '💵' }}</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ $session->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}
                                    <span class="text-xs text-gray-400 ml-1">— {{ $session->items->count() }} item</span>
                                    <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full font-medium {{ $session->shift === 'pagi' ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ $session->shift === 'pagi' ? '🌤 Pagi' : '🌆 Sore' }}
                                    </span>
                                </p>
                                @if($session->notes)
                                <p class="text-xs text-gray-400">{{ $session->notes }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-800">Rp {{ number_format($session->total_amount, 0, ',', '.') }}</span>
                            {{-- Edit button --}}
                            <button type="button"
                                @click.stop="openEdit({{ $session->id }}, '{{ $session->date->format('Y-m-d') }}', '{{ $session->shift }}', '{{ $session->payment_method }}', @js($session->notes ?? ''))"
                                class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            {{-- Delete button --}}
                            <button type="button"
                                @click.stop="confirmDelete({{ $session->id }}, 'Rp {{ number_format($session->total_amount, 0, ',', '.') }}')"
                                class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            <svg class="w-4 h-4 text-gray-400 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" @click="open = !open">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    {{-- Detail items --}}
                    <div x-show="open" x-collapse class="mt-2 pl-9 space-y-1">
                        @foreach($session->items as $item)
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                            <span class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        <p class="text-xs text-gray-300 mt-1">oleh {{ $session->creator->name ?? '-' }} · {{ $session->created_at->format('H:i') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @endif

        {{-- ===== EDIT MODAL ===== --}}
        <div x-show="editOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm" @click.outside="editOpen = false">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">✏️ Edit Transaksi</h3>
                    <button type="button" @click="editOpen = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <form :action="editAction" method="POST" class="px-5 py-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">📅 Tanggal Transaksi</label>
                        <input type="date" name="date" x-model="editDate"
                               :max="todayDate"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">🕐 Shift</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="editShift = 'pagi'"
                                :class="editShift === 'pagi' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-600 border-gray-300 hover:border-amber-400'"
                                class="py-2 rounded-xl border-2 font-semibold text-sm transition">
                                🌤 Pagi
                            </button>
                            <button type="button" @click="editShift = 'sore'"
                                :class="editShift === 'sore' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400'"
                                class="py-2 rounded-xl border-2 font-semibold text-sm transition">
                                🌆 Sore
                            </button>
                        </div>
                        <input type="hidden" name="shift" :value="editShift">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="editPayment = 'tunai'"
                                :class="editPayment === 'tunai' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-300 hover:border-green-400'"
                                class="py-2.5 rounded-xl border-2 font-semibold text-sm transition">
                                💵 Tunai
                            </button>
                            <button type="button" @click="editPayment = 'qris'"
                                :class="editPayment === 'qris' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-600 border-gray-300 hover:border-purple-400'"
                                class="py-2.5 rounded-xl border-2 font-semibold text-sm transition">
                                📱 QRIS
                            </button>
                        </div>
                        <input type="hidden" name="payment_method" :value="editPayment">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Catatan</label>
                        <input type="text" name="notes" x-model="editNotes"
                               placeholder="opsional..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button type="button" @click="editOpen = false"
                                class="flex-1 py-2.5 border border-gray-300 text-gray-600 font-semibold text-sm rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== DELETE CONFIRM MODAL ===== --}}
        <div x-show="deleteOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs text-center p-6" @click.outside="deleteOpen = false">
                <div class="text-5xl mb-3">🗑️</div>
                <h3 class="font-bold text-gray-800 mb-1">Hapus Transaksi?</h3>
                <p class="text-sm text-gray-500 mb-5">Transaksi <span class="font-semibold text-gray-700" x-text="deleteLabel"></span> akan dihapus permanen.</p>
                <div class="flex gap-2">
                    <button type="button" @click="deleteOpen = false"
                            class="flex-1 py-2.5 border border-gray-300 text-gray-600 font-semibold text-sm rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <form :action="deleteAction" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full py-2.5 bg-red-600 text-white font-bold text-sm rounded-xl hover:bg-red-700 transition">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>{{-- end x-data="riwayatApp()" --}}
</x-kasir-layout>

<script>
function riwayatApp() {
    const today = new Date().toISOString().split('T')[0];
    return {
        todayDate: today,
        editOpen: false,
        editAction: '',
        editDate: today,
        editShift: 'pagi',
        editPayment: 'tunai',
        editNotes: '',
        deleteOpen: false,
        deleteAction: '',
        deleteLabel: '',

        openEdit(id, date, shift, payment, notes) {
            this.editAction = `/kasir/riwayat/${id}`;
            this.editDate = date;
            this.editShift = shift;
            this.editPayment = payment;
            this.editNotes = notes ?? '';
            this.editOpen = true;
        },

        confirmDelete(id, label) {
            this.deleteAction = `/kasir/riwayat/${id}`;
            this.deleteLabel = label;
            this.deleteOpen = true;
        },
    };
}
</script>
