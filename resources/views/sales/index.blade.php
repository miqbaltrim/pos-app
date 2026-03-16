<x-layouts.admin>
    <x-slot:header>Riwayat Penjualan</x-slot:header>
    <x-slot:title>Penjualan</x-slot:title>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900 mb-3">Riwayat Transaksi</h3>
            <form class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. invoice..."
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <input type="date" name="to" value="{{ request('to') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm rounded-lg">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-5 py-3 font-medium">Invoice</th>
                        <th class="px-5 py-3 font-medium">Tanggal</th>
                        <th class="px-5 py-3 font-medium">Customer</th>
                        <th class="px-5 py-3 font-medium">Kasir</th>
                        <th class="px-5 py-3 font-medium text-right">Total</th>
                        <th class="px-5 py-3 font-medium text-center">Bayar</th>
                        <th class="px-5 py-3 font-medium text-center">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs font-medium text-indigo-600">
                            <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $sale->transaction_date->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $sale->customer?->name ?? 'Umum' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $sale->user->name }}</td>
                        <td class="px-5 py-3 text-right font-medium">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center text-xs text-gray-500">{{ ucfirst($sale->payment_method) }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($sale->status === 'completed')
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Completed</span>
                            @elseif($sale->status === 'cancelled')
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">Cancelled</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs">{{ ucfirst($sale->status) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">Belum ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $sales->withQueryString()->links() }}</div>
    </div>
</x-layouts.admin>