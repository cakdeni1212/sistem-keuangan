<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary btn-sm">&larr; Kembali</a>
            <h2 class="font-semibold text-xl text-surface-800 leading-tight">
                Edit User: {{ $user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')

                    {{-- Nama --}}
                    <div class="mb-4">
                        <x-input-label for="name" value="Nama Lengkap" />
                        <x-text-input id="name" name="name" type="text"
                            class="mt-1 block w-full"
                            value="{{ old('name', $user->name) }}"
                            required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email"
                            class="mt-1 block w-full"
                            value="{{ old('email', $user->email) }}"
                            required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    {{-- Role --}}
                    <div class="mb-4">
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role"
                            class="mt-1 block w-full border-surface-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ (old('role', $userRole) === $role->name) ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', ucfirst($role->name)) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    {{-- Password (opsional) --}}
                    <div class="mb-4">
                        <x-input-label for="password" value="Password Baru (kosongkan jika tidak diubah)" />
                        <x-text-input id="password" name="password" type="password"
                            class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                        <x-text-input id="password_confirmation" name="password_confirmation"
                            type="password" class="mt-1 block w-full" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}"
                           class="px-4 py-2 text-sm text-surface-700 bg-surface-100 rounded-md hover:bg-surface-200">
                            Batal
                        </a>
                        <x-primary-button>Perbarui User</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
