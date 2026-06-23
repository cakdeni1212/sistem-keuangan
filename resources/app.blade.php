<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Sistem Keuangan') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

            {{-- Overlay mobile --}}
            <div x-show="sidebarOpen" x-cloak
                 class="fixed inset-0 z-20 bg-black/40 backdrop-blur-sm lg:hidden"
                 @click="sidebarOpen = false"></div>

            {{-- ===== SIDEBAR ===== --}}
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-surface-200 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:flex-shrink-0 shadow-sm">

                {{-- Logo --}}
                <div class="flex items-center gap-3 px-5 h-16 border-b border-surface-100">
                    <div class="w-8 h-8 rounded-xl gradient-brand flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        F
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-surface-900 leading-tight">{{ config('app.name') }}</span>
                        <span class="text-[10px] text-surface-400 font-medium">{{ \App\Models\AppSetting::get('sidebar_tagline', 'Coffee Shop Manager') }}</span>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 overflow-y-auto py-4 space-y-0.5 px-3">

                    {{-- Dashboard --}}
                    <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="dashboard">
                        Dashboard
                    </x-sidebar-link>

                    {{-- Operasional --}}
                    <p class="px-3 pt-5 pb-1 text-[10px] font-bold text-surface-400 uppercase tracking-[0.12em]">Operasional</p>

                    <x-sidebar-link href="{{ route('penjualan-harian.index') }}" :active="request()->routeIs('penjualan-harian.*')" icon="shopping-cart">
                        Penjualan Harian
                    </x-sidebar-link>

                    <x-sidebar-link href="{{ route('kasir.index') }}" :active="request()->routeIs('kasir.index')" icon="cash-register">
                        Kasir
                    </x-sidebar-link>

                    <x-sidebar-link href="{{ route('kasir.riwayat') }}" :active="request()->routeIs('kasir.riwayat')" icon="receipt">
                        Riwayat Kasir
                    </x-sidebar-link>

                    @can('view daily revenues')
                    <x-sidebar-link href="{{ route('daily-revenues.index') }}" :active="request()->routeIs('daily-revenues.*')" icon="chart-bar">
                        Omset Harian
                    </x-sidebar-link>
                    @endcan

                    <x-sidebar-link href="{{ route('kasir.data') }}" :active="request()->routeIs('kasir.data')" icon="database">
                        Data Kasir
                    </x-sidebar-link>

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

                    {{-- Produk --}}
                    <p class="px-3 pt-5 pb-1 text-[10px] font-bold text-surface-400 uppercase tracking-[0.12em]">Produk</p>

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

                    {{-- Pengaturan --}}
                    <p class="px-3 pt-5 pb-1 text-[10px] font-bold text-surface-400 uppercase tracking-[0.12em]">Pengaturan</p>

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

                    <x-sidebar-link href="{{ route('settings.index') }}" :active="request()->routeIs('settings.*')" icon="palette">
                        Tampilan
                    </x-sidebar-link>
                    @endrole

                </nav>

                {{-- User profile at bottom --}}
                <div class="border-t border-surface-100 px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl gradient-brand flex items-center justify-center text-sm font-bold text-white flex-shrink-0 shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-surface-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-surface-400 truncate">{{ Auth::user()->getRoleNames()->first() }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Logout"
                                    class="w-8 h-8 rounded-lg text-surface-400 hover:text-red-500 hover:bg-red-50 transition flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- ===== MAIN CONTENT ===== --}}
            <div class="flex-1 flex flex-col overflow-hidden">

                {{-- Top bar --}}
                <header class="bg-white border-b border-surface-200 flex-shrink-0 h-16">
                    <div class="flex items-center justify-between px-4 h-full">
                        <div class="flex items-center gap-3">
                            <button @click="sidebarOpen = !sidebarOpen"
                                    class="lg:hidden w-9 h-9 rounded-xl text-surface-400 hover:bg-surface-100 hover:text-surface-600 transition flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            <div class="hidden sm:flex items-center gap-2 text-sm text-surface-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>{{ now()->format('l, d F Y') }}</span>
                            </div>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-surface-50 transition group">
                            <div class="w-8 h-8 rounded-xl gradient-brand flex items-center justify-center text-xs font-bold text-white shadow-sm group-hover:shadow transition-shadow">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-semibold text-surface-900">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] font-medium text-surface-400">{{ Auth::user()->getRoleNames()->first() }}</p>
                            </div>
                        </a>
                    </div>
                </header>

                {{-- Page header --}}
                @if(isset($header) && trim($header) !== '')
                <div class="bg-white border-b border-surface-200 px-6 py-4">
                    {{ $header }}
                </div>
                @endif

                {{-- Content --}}
                <main class="flex-1 overflow-y-auto bg-surface-50">
                    {{ $slot }}
                </main>
            </div>

        </div>
    </body>
</html>
