<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Sistem Keuangan') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @php $appFavicon = \App\Models\AppSetting::get('landing_favicon', ''); @endphp
        @if($appFavicon)
        <link rel="icon" type="image/x-icon" href="{{ $appFavicon }}">
        <link rel="shortcut icon" href="{{ $appFavicon }}">
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-surface-50">
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

            {{-- Overlay --}}
            <div x-show="sidebarOpen" x-cloak
                 class="fixed inset-0 z-20 bg-black/50 lg:hidden"
                 @click="sidebarOpen = false"></div>

            {{-- Sidebar --}}
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-30 w-60 bg-[#171717] flex flex-col transition-transform duration-300 lg:static lg:translate-x-0 lg:flex-shrink-0">

                {{-- Logo --}}
                <div class="flex items-center gap-3 px-4 h-14 border-b border-white/5">
                    @php $appLogo = \App\Models\AppSetting::get('landing_logo', ''); @endphp
                    @if($appLogo)
                    <img src="{{ $appLogo }}" class="h-7 w-auto rounded flex-shrink-0">
                    @else
                    <div class="w-7 h-7 rounded-lg bg-brand-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">F</div>
                    @endif
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-white leading-tight">{{ config('app.name') }}</span>
                        <span class="text-[10px] text-surface-400 font-medium">{{ \App\Models\AppSetting::get('sidebar_tagline', 'Coffee Shop Manager') }}</span>
                    </div>
                </div>

                {{-- Nav --}}
                <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-0.5">

                    <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="dashboard">
                        Dashboard
                    </x-sidebar-link>

                    <p class="px-3 pt-4 pb-1 text-[10px] font-semibold text-surface-500 uppercase tracking-widest">Operasional</p>

                    <x-sidebar-link href="{{ route('penjualan-harian.index') }}" :active="request()->routeIs('penjualan-harian.*')" icon="shopping-cart">
                        Penjualan Harian
                    </x-sidebar-link>

                    @can('view daily revenues')
                    <x-sidebar-link href="{{ route('daily-revenues.index') }}" :active="request()->routeIs('daily-revenues.*')" icon="chart-bar">
                        Omset Harian
                    </x-sidebar-link>
                    @endcan

                    @can('view cashbon')
                    <x-sidebar-link href="{{ route('cashbons.index') }}" :active="request()->routeIs('cashbons.*')" icon="credit-card">
                        Cashbon
                    </x-sidebar-link>
                    @endcan

                    @can('view transactions')
                    <x-sidebar-link href="{{ route('transactions.index') }}" :active="request()->routeIs('transactions.*')" icon="transfer">
                        Transaksi
                    </x-sidebar-link>
                    @endcan

                    @can('view transactions')
                    <x-sidebar-link href="{{ route('pengeluaran.index') }}" :active="request()->routeIs('pengeluaran.*')" icon="trending-down">
                        Pengeluaran
                    </x-sidebar-link>
                    @endcan

                    <p class="px-3 pt-4 pb-1 text-[10px] font-semibold text-surface-500 uppercase tracking-widest">Produk</p>

                    @can('view hpp')
                    <x-sidebar-link href="{{ route('hpp-products.index') }}" :active="request()->routeIs('hpp-products.*')" icon="calculator">
                        HPP Produk
                    </x-sidebar-link>
                    @endcan

                    @can('view raw-material')
                    <x-sidebar-link href="{{ route('raw-materials.index') }}" :active="request()->routeIs('raw-materials.*')" icon="package">
                        Stok Bahan Baku
                    </x-sidebar-link>
                    @endcan

                    <x-sidebar-link href="{{ route('calculator.index') }}" :active="request()->routeIs('calculator.*')" icon="lightbulb">
                        Kalkulator
                    </x-sidebar-link>

                    @can('view employee')
                    <x-sidebar-link href="{{ route('employees.index') }}" :active="request()->routeIs('employees.*') || request()->routeIs('employee-salaries.*')" icon="users">
                        Karyawan
                    </x-sidebar-link>
                    @endcan

                    <p class="px-3 pt-4 pb-1 text-[10px] font-semibold text-surface-500 uppercase tracking-widest">Pengaturan</p>

                    <x-sidebar-link href="{{ route('laporan.index') }}" :active="request()->routeIs('laporan.*')" icon="file-text">
                        Laporan & Export
                    </x-sidebar-link>

                    @role('admin|owner')
                    <x-sidebar-link href="{{ route('transaction-types.index') }}" :active="request()->routeIs('transaction-types.*')" icon="tags">
                        Jenis Transaksi
                    </x-sidebar-link>

                    <x-sidebar-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')" icon="user-cog">
                        Manajemen User
                    </x-sidebar-link>

                    <x-sidebar-link href="{{ route('settings.index') }}" :active="request()->routeIs('settings.index')" icon="palette">
                        Tampilan
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('settings.landing') }}" :active="request()->routeIs('settings.landing')" icon="file-text">
                        Landing Page
                    </x-sidebar-link>
                    @endrole
                </nav>

                {{-- User --}}
                <div class="border-t border-white/5 px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-600/20 text-brand-400 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-surface-400 truncate">{{ Auth::user()->getRoleNames()->first() }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Logout" class="btn-icon !text-surface-500 hover:!text-red-400 hover:!bg-red-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Main --}}
            <div class="flex-1 flex flex-col overflow-hidden">

                {{-- Top bar minimal --}}
                <header class="bg-white border-b border-surface-200 h-14 flex-shrink-0">
                    <div class="flex items-center justify-between px-4 h-full">
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="lg:hidden w-8 h-8 rounded-lg text-surface-400 hover:bg-surface-100 flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div class="hidden sm:flex items-center gap-2 text-xs text-surface-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            {{ now()->format('l, d F Y') }}
                        </div>
                        <div class="flex-1"></div>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2.5 text-sm text-surface-600 hover:text-surface-900 transition group">
                            <div class="w-7 h-7 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center text-xs font-bold group-hover:bg-brand-200 transition">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden sm:block text-sm font-medium">{{ Auth::user()->name }}</span>
                            <span class="hidden sm:block text-[10px] px-1.5 py-0.5 rounded bg-surface-100 text-surface-500">{{ Auth::user()->getRoleNames()->first() }}</span>
                        </a>
                    </div>
                </header>

                {{-- Page header --}}
                @if(isset($header) && trim($header) !== '')
                <div class="bg-white border-b border-surface-200 px-6 py-3">
                    {{ $header }}
                </div>
                @endif

                {{-- Content --}}
                <main class="flex-1 overflow-y-auto p-5">
                    {{ $slot }}
                </main>
            </div>

        </div>
    </body>
</html>
