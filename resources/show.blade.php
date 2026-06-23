<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">&larr; Kembali</a>
                <h2 class="text-lg font-bold text-gray-800">{{ $employee->name }}</h2>
                @if($employee->status === 'aktif')
                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Aktif</span>
                @else
                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Nonaktif</span>
                @endif
            </div>
            <div class="flex gap-2">
                @can('create salary')
                <a href="{{ route('employee-salaries.create', $employee) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    + Input Gaji
                </a>
                @endcan
                @can('edit employee')
                <a href="{{ route('employees.edit', $employee) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Edit Data
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-5">

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        {{-- Info Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Personal Info --}}
            <div class="bg-white rounded-xl border shadow-sm p-5 space-y-3">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Informasi Karyawan</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Jabatan</dt><dd class="font-medium text-gray-800">{{ $employee->position }}</dd></div>
                    @if($employee->department)
                    <div class="flex justify-between"><dt class="text-gray-500">Divisi</dt><dd class="font-medium text-gray-800">{{ $employee->department }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-gray-500">Gaji Pokok</dt><dd class="font-medium text-gray-800">Rp {{ number_format($employee->base_salary, 0, ',', '.') }}</dd></div>
                    @if($employee->phone)
                    <div class="flex justify-between"><dt class="text-gray-500">No HP</dt><dd class="font-medium text-gray-800">{{ $employee->phone }}</dd></div>
                    @endif
                    @if($employee->email)
                    <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="font-medium text-gray-800">{{ $employee->email }}</dd></div>
                    @endif
                    @if($employee->join_date)
                    <div class="flex justify-between"><dt class="text-gray-500">Bergabung</dt><dd class="font-medium text-gray-800">{{ $employee->join_date->format('d M Y') }}</dd></div>
                    @endif
                </dl>
            </div>

            {{-- Bank Info --}}
            <div class="bg-white rounded-xl border shadow-sm p-5 space-y-3">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Rekening Bank</h3>
                @if($employee->bank_name || $employee->account_number)
                <dl class="space-y-2 text-sm">
                    @if($employee->bank_name)
                    <div class="flex justify-between"><dt class="text-gray-500">Bank</dt><dd class="font-medium text-gray-800">{{ $employee->bank_name }}</dd></div>
                    @endif
                    @if($employee->account_number)
                    <div class="flex justify-between"><dt class="text-gray-500">No Rekening</dt><dd class="font-mono font-semibold text-gray-800 text-base">{{ $employee->account_number }}</dd></div>
                    @endif
                    @if($employee->account_name)
                    <div class="flex justify-between"><dt class="text-gray-500">Nama Pemilik</dt><dd class="font-medium text-gray-800">{{ $employee->account_name }}</dd></div>
                    @endif
                </dl>
                @else
                <p class="text-sm text-gray-400 italic">Belum ada info rekening.</p>
                @endif
                @if($employee->notes)
                <div class="pt-2 border-t">
                    <p class="text-xs text-gray-400">Catatan:</p>
                    <p class="text-sm text-gray-600">{{ $employee->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Salary History --}}
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-700">Riwayat Gaji</h3>
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span>Omset bulan ini: <span class="font-semibold text-gray-700">Rp {{ number_format($omsetBulanIni, 0, ',', '.') }}</span></span>
                    <span>|</span>
                    <span>Total gaji bulan ini: <span class="font-semibold text-red-600">Rp {{ number_format($totalGajiBulanIni, 0, ',', '.') }}</span></span>
                    <span>|</span>
                    <span>Total dibayar: <span class="font-semibold text-gray-700">Rp {{ number_format($totalGajiDibayar, 0, ',', '.') }}</span></span>
                </div>
            </div>
            @if($employee->salaries->isEmpty())
            <div class="py-12 text-center text-gray-400">
                <p class="text-3xl mb-2">💸</p>
                <p class="text-sm">Belum ada data gaji.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Periode</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Gaji Pokok</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Bonus</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Potongan</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Metode</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Sinkron</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($employee->salaries as $sal)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $sal->period_label }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($sal->base_salary, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-green-600">{{ $sal->bonus > 0 ? '+Rp '.number_format($sal->bonus, 0, ',', '.') : '—' }}</td>
                            <td class="px-4 py-3 text-right text-red-600">{{ $sal->deductions > 0 ? '-Rp '.number_format($sal->deductions, 0, ',', '.') : '—' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">Rp {{ number_format($sal->total_salary, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($sal->paid_at)
                                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full" title="{{ $sal->paid_at->format('d M Y') }}">Dibayar</span>
                                @else
                                <span class="inline-block px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500 text-xs">{{ $sal->payment_method ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($sal->transaction_id)
                                <span class="inline-block px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full" title="Tercatat sebagai transaksi #{{ $sal->transaction_id }}">✓ Tercatat</span>
                                @else
                                <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-400 text-xs rounded-full">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @can('view salary')
                                    <a href="{{ route('employee-salaries.slip', [$employee, $sal]) }}" target="_blank" class="text-xs text-indigo-600 hover:underline">🖨️ Slip</a>
                                    @endcan
                                    @can('edit salary')
                                    @if(!$sal->paid_at)
                                    <button type="button"
                                        onclick="document.getElementById('modal-bayar-{{ $sal->id }}').classList.remove('hidden')"
                                        class="text-xs text-green-600 font-semibold hover:underline">
                                        ✓ Bayar
                                    </button>
                                    @endif
                                    <a href="{{ route('employee-salaries.edit', [$employee, $sal]) }}" class="text-xs text-yellow-600 hover:underline">Edit</a>
                                    @endcan
                                    @can('delete salary')
                                    <form method="POST" action="{{ route('employee-salaries.destroy', [$employee, $sal]) }}" onsubmit="return confirm('Hapus data gaji ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Tandai Lunas --}}
                        @can('edit salary')
                        @if(!$sal->paid_at)
                        <div id="modal-bayar-{{ $sal->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
                                <h3 class="text-base font-bold text-gray-800 mb-1">Tandai Lunas</h3>
                                <p class="text-sm text-gray-500 mb-4">{{ $employee->name }} — <strong>{{ $sal->period_label }}</strong><br>Total: <strong class="text-indigo-600">Rp {{ number_format($sal->total_salary, 0, ',', '.') }}</strong></p>
                                <form method="POST" action="{{ route('employee-salaries.mark-paid', [$employee, $sal]) }}">
                                    @csrf
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar <span class="text-red-500">*</span></label>
                                            <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Bayar</label>
                                            <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                                <option value="Transfer">Transfer Bank</option>
                                                <option value="Tunai">Tunai</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 mt-4">
                                        <button type="submit" class="flex-1 py-2 bg-green-600 text-white font-semibold text-sm rounded-lg hover:bg-green-700 transition">
                                            ✓ Konfirmasi Lunas
                                        </button>
                                        <button type="button"
                                            onclick="document.getElementById('modal-bayar-{{ $sal->id }}').classList.add('hidden')"
                                            class="flex-1 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                        @endcan
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
