<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'POS App' }} — {{ \App\Models\StoreSetting::getValue('store_name', 'POS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: false }">

<div class="min-h-full">

    {{-- ===== MOBILE SIDEBAR OVERLAY ===== --}}
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden" @click="sidebarOpen = false">
    </div>

    {{-- ===== SIDEBAR ===== --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-300 lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- Logo --}}
        <div class="flex items-center gap-3 h-16 px-6 bg-gray-950">
            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
            <span class="text-white font-bold text-lg tracking-tight">
                {{ \App\Models\StoreSetting::getValue('store_name', 'POS App') }}
            </span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto h-[calc(100vh-8rem)]">

            {{-- Dashboard --}}
            @can('dashboard.view')
            <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
                Dashboard
            </x-sidebar-link>
            @endcan

            {{-- POS --}}
            @can('pos.access')
            <x-sidebar-link href="{{ route('pos.index') }}" :active="request()->routeIs('pos.*')" icon="cash-register">
                POS Kasir
            </x-sidebar-link>
            @endcan

            {{-- Master Data --}}
            @canany(['categories.view', 'products.view', 'suppliers.view', 'customers.view'])
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Master Data</p>
            </div>
            @endcanany

            @can('categories.view')
            <x-sidebar-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.*')" icon="tag">
                Kategori
            </x-sidebar-link>
            @endcan

            @can('products.view')
            <x-sidebar-link href="{{ route('products.index') }}" :active="request()->routeIs('products.*')" icon="cube">
                Produk
            </x-sidebar-link>
            @endcan

            @can('suppliers.view')
            <x-sidebar-link href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.*')" icon="truck">
                Supplier
            </x-sidebar-link>
            @endcan

            @can('customers.view')
            <x-sidebar-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.*')" icon="users">
                Customer
            </x-sidebar-link>
            @endcan

            {{-- Transaksi --}}
            @canany(['sales.view', 'purchases.view', 'stocks.view'])
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Transaksi</p>
            </div>
            @endcanany

            @can('sales.view')
            <x-sidebar-link href="{{ route('sales.index') }}" :active="request()->routeIs('sales.*')" icon="receipt">
                Riwayat Penjualan
            </x-sidebar-link>
            @endcan

            @can('purchases.view')
            <x-sidebar-link href="{{ route('purchases.index') }}" :active="request()->routeIs('purchases.*')" icon="shopping-bag">
                Pembelian
            </x-sidebar-link>
            @endcan

            @can('stocks.view')
            <x-sidebar-link href="{{ route('stocks.index') }}" :active="request()->routeIs('stocks.*')" icon="archive">
                Stok & Opname
            </x-sidebar-link>
            @endcan

            {{-- Laporan --}}
            @canany(['reports.sales', 'reports.profit', 'reports.stock', 'reports.purchases'])
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Laporan</p>
            </div>
            @endcanany

            @can('reports.sales')
            <x-sidebar-link href="{{ route('reports.sales') }}" :active="request()->routeIs('reports.sales*')" icon="chart-bar">
                Lap. Penjualan
            </x-sidebar-link>
            @endcan

            @can('reports.profit')
            <x-sidebar-link href="{{ route('reports.profit') }}" :active="request()->routeIs('reports.profit')" icon="trending-up">
                Lap. Laba/Rugi
            </x-sidebar-link>
            @endcan

            @can('reports.stock')
            <x-sidebar-link href="{{ route('reports.stock') }}" :active="request()->routeIs('reports.stock')" icon="clipboard-list">
                Lap. Stok
            </x-sidebar-link>
            @endcan

            {{-- Pengaturan --}}
            @canany(['users.view', 'roles.view', 'settings.view'])
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengaturan</p>
            </div>
            @endcanany

            @can('users.view')
            <x-sidebar-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')" icon="user-group">
                Manajemen User
            </x-sidebar-link>
            @endcan

            @can('roles.view')
            <x-sidebar-link href="{{ route('roles.index') }}" :active="request()->routeIs('roles.*')" icon="shield-check">
                Role & Hak Akses
            </x-sidebar-link>
            @endcan

            @can('settings.view')
            <x-sidebar-link href="{{ route('settings.index') }}" :active="request()->routeIs('settings.*')" icon="cog">
                Pengaturan Toko
            </x-sidebar-link>
            @endcan
        </nav>

        {{-- User info di bawah --}}
        <div class="border-t border-gray-800 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-medium">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->roles->first()?->name ?? '-' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-400 transition" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="lg:pl-64">
        {{-- Top Bar --}}
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                <div class="flex items-center gap-3">
                    {{-- Mobile menu button --}}
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900">{{ $header ?? '' }}</h1>
                </div>

                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mx-4 sm:mx-6 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="rounded-lg bg-green-50 border border-green-200 p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mx-4 sm:mx-6 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)">
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- Page Content --}}
        <main class="p-4 sm:p-6">
            {{ $slot }}
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>