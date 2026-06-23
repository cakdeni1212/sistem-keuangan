<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir &mdash; {{ config('app.name', 'Sistem Keuangan') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-surface-50">

    {{-- Top nav bar --}}
    <header class="bg-white border-b border-surface-200 h-14 flex items-center px-4 shadow-sm">
        <div class="flex items-center justify-between w-full max-w-screen-2xl mx-auto">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg gradient-brand flex items-center justify-center text-white text-xs font-bold">
                    F
                </div>
                <span class="text-sm font-bold text-surface-900">{{ config('app.name', 'Sistem Keuangan') }}</span>
                <span class="text-surface-300 text-xs">|</span>
                <span class="text-brand-600 text-xs font-bold">POS</span>
            </div>

            <div class="flex items-center gap-1">
                <a href="{{ route('kasir.index') }}"
                   class="text-xs px-3 py-1.5 rounded-lg font-semibold transition
                          {{ request()->routeIs('kasir.index') ? 'bg-brand-100 text-brand-700' : 'text-surface-500 hover:bg-surface-100' }}">
                    <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2zM9 7h6v2H9V7z"/></svg>
                    Kasir
                </a>
                <a href="{{ route('kasir.riwayat') }}"
                   class="text-xs px-3 py-1.5 rounded-lg font-semibold transition
                          {{ request()->routeIs('kasir.riwayat') ? 'bg-brand-100 text-brand-700' : 'text-surface-500 hover:bg-surface-100' }}">
                    <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Riwayat
                </a>
                @role('admin|owner')
                <span class="text-surface-300 mx-1">|</span>
                <a href="{{ route('dashboard') }}"
                   class="text-xs px-3 py-1.5 rounded-lg text-surface-400 hover:bg-surface-100 hover:text-surface-600 transition font-medium">
                    <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                @endrole
            </div>

            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg gradient-brand flex items-center justify-center text-white text-xs font-bold shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="hidden sm:block leading-none">
                    <p class="text-xs font-semibold text-surface-900">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-surface-400">{{ Auth::user()->getRoleNames()->first() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                            class="w-7 h-7 rounded-lg text-surface-400 hover:text-red-500 hover:bg-red-50 transition flex items-center justify-center ml-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="min-h-[calc(100vh-3.5rem)]">
        {{ $slot }}
    </main>

</body>
</html>
