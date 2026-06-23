<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.show', $employee) }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="page-title">Edit Karyawan — {{ $employee->name }}</h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="card">
                    {{-- Info Pribadi --}}
                    <div class="p-5">
                        <h3 class="text-sm font-bold text-surface-700 uppercase tracking-wide mb-4">Informasi Pribadi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-surface-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                                    placeholder="Contoh: Budi Santoso"
                                    class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">No HP / WhatsApp</label>
                                <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                                    placeholder="Contoh: 0812xxxxxxxx"
                                    class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                                    placeholder="Contoh: budi@email.com"
                                    class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>
                    </div>

                    {{-- Info Jabatan --}}
                    <div class="p-5">
                        <h3 class="text-sm font-bold text-surface-700 uppercase tracking-wide mb-4">Jabatan & Status</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                                <select name="position" required class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($positions as $pos)
                                    <option value="{{ $pos }}" {{ old('position', $employee->position) == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Divisi</label>
                                <select name="department" class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($departments as $dep)
                                    <option value="{{ $dep }}" {{ old('department', $employee->department) == $dep ? 'selected' : '' }}>{{ $dep }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Status Karyawan <span class="text-red-500">*</span></label>
                                <select name="status" required class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="aktif" {{ old('status', $employee->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status', $employee->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Tanggal Bergabung</label>
                                <input type="date" name="join_date" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}"
                                    class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-surface-700 mb-1">Gaji Pokok <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-surface-500 text-sm">Rp</span>
                                    <input type="number" name="base_salary" value="{{ old('base_salary', $employee->base_salary) }}"
                                        min="0" step="50000" required
                                        class="w-full border border-surface-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Rekening --}}
                    <div class="p-5">
                        <h3 class="text-sm font-bold text-surface-700 uppercase tracking-wide mb-4">Info Rekening Bank (untuk Gajian)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Nama Bank</label>
                                <select name="bank_name" class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($banks as $bank)
                                    <option value="{{ $bank }}" {{ old('bank_name', $employee->bank_name) == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Nomor Rekening</label>
                                <input type="text" name="account_number" value="{{ old('account_number', $employee->account_number) }}"
                                    placeholder="Contoh: 1234567890"
                                    class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Nama Pemilik Rekening</label>
                                <input type="text" name="account_name" value="{{ old('account_name', $employee->account_name) }}"
                                    placeholder="Sesuai nama di buku tabungan"
                                    class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="p-5">
                        <label class="block text-sm font-medium text-surface-700 mb-1">Catatan</label>
                        <textarea name="notes" rows="2" placeholder="Catatan tambahan..."
                            class="w-full border border-surface-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('notes', $employee->notes) }}</textarea>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 font-medium text-sm transition">Simpan Perubahan</button>
                    <a href="{{ route('employees.show', $employee) }}" class="px-6 py-2 bg-white border border-surface-300 text-surface-700 rounded-lg hover:bg-surface-50 font-medium text-sm transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
