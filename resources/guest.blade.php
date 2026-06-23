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
    <body class="font-sans antialiased bg-surface-50">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8">
            <div class="w-full max-w-sm">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 rounded-2xl gradient-brand flex items-center justify-center text-white text-2xl font-bold mx-auto shadow-lg shadow-brand-200 mb-4">
                        F
                    </div>
                    <h1 class="text-xl font-bold text-surface-900">{{ config('app.name') }}</h1>
                    <p class="text-sm text-surface-400 mt-1">{{ \App\Models\AppSetting::get('sidebar_tagline', 'Coffee Shop Manager') }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6">
                    {{ $slot }}
                </div>

                <p class="text-center text-xs text-surface-400 mt-6">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
