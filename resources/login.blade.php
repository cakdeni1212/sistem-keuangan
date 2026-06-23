<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-surface-700 mb-1.5">Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                   class="block w-full rounded-xl border-surface-300 bg-surface-50 px-4 py-2.5 text-sm text-surface-900 placeholder-surface-400 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 transition" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-surface-700 mb-1.5">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="block w-full rounded-xl border-surface-300 bg-surface-50 px-4 py-2.5 text-sm text-surface-900 placeholder-surface-400 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 transition" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded-lg border-surface-300 text-brand-600 focus:ring-brand-500" name="remember">
                <span class="text-sm text-surface-600">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-brand-600 hover:text-brand-700 font-semibold" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 gradient-brand text-white text-sm font-bold rounded-xl shadow-sm shadow-brand-200 hover:shadow-md transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Masuk
        </button>
    </form>
</x-guest-layout>
