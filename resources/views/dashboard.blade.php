<x-layouts.admin>
    <x-slot:header>Dashboard</x-slot:header>
    <x-slot:title>Dashboard</x-slot:title>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Penjualan Hari Ini --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Penjualan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($summary['today_sales'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $summary['today_transactions'] }} transaksi</p>
        </div>

        {{-- Penjualan Bulan Ini --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Penjualan Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($summary['month_sales'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $summary['month_transactions'] }} transaksi</p>
        </div>

        {{-- Total Produk --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Produk Aktif</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['total_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Produk tersedia</p>
        </div>

        {{-- Stok Rendah --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Stok Rendah</p>
                    <p class="text-2xl font-bold {{ $summary['low_stock_products'] > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">
                        {{ $summary['low_stock_products'] }}
                    </p>
                </div>
                <div class="w-12 h-12 {{ $summary['low_stock_products'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $summary['low_stock_products'] > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.27 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Perlu restock</p>
        </div>
    </div>

    {{-- Top Produk Hari Ini --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Produk Terlaris Hari Ini</h3>
        </div>
        <div class="p-5">
            @if(count($summary['top_products_today']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="pb-3 font-medium">#</th>
                            <th class="pb-3 font-medium">Produk</th>
                            <th class="pb-3 font-medium text-right">Terjual</th>
                            <th class="pb-3 font-medium text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summary['top_products_today'] as $i => $product)
                        <tr class="border-b border-gray-50">
                            <td class="py-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="py-3 font-medium text-gray-900">{{ $product['product_name'] }}</td>
                            <td class="py-3 text-right text-gray-600">{{ $product['total_qty'] }}</td>
                            <td class="py-3 text-right font-medium text-gray-900">Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-400 text-center py-8">Belum ada penjualan hari ini</p>
            @endif
        </div>
    </div>
</x-layouts.admin>