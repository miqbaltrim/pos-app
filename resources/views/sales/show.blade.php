<x-layouts.admin>
    <x-slot:header>Detail Penjualan</x-slot:header>
    <x-slot:title>{{ $sale->invoice_number }}</x-slot:title>

    <div class="max-w-4xl space-y-6">
        {{-- Info Header --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $sale->invoice_number }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $sale->transaction_date->format('d F Y, H:i') }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $sale->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ ucfirst($sale->status) }}
                </span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 text-sm">
                <div>
                    <p class="text-gray-400">Kasir</p>
                    <p class="font-medium text-gray-900">{{ $sale->user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Customer</p>
                    <p class="font-medium text-gray-900">{{ $sale->customer?->name ?? 'Umum' }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Pembayaran</p>
                    <p class="font-medium text-gray-900">{{ ucfirst($sale->payment_method) }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Referensi</p>
                    <p class="font-medium text-gray-900">{{ $sale->payment_reference ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Item Detail --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b"><h3 class="font-semibold">Detail Item</h3></div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Produk</th>
                        <th class="px-5 py-3 text-right">Harga</th>
                        <th class="px-5 py-3 text-center">Qty</th>
                        <th class="px-5 py-3 text-right">Diskon</th>
                        <th class="px-5 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->details as $i => $detail)
                    <tr class="border-b border-gray-50">
                        <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-900">{{ $detail->product_name }}</p>
                            <p class="text-xs text-gray-400">{{ $detail->product_sku }}</p>
                        </td>
                        <td class="px-5 py-3 text-right">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center">{{ $detail->quantity }}</td>
                        <td class="px-5 py-3 text-right text-red-500">
                            {{ $detail->discount_amount > 0 ? '-Rp ' . number_format($detail->discount_amount, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-5 py-3 text-right font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr class="border-t"><td colspan="5" class="px-5 py-2 text-right text-gray-500">Subtotal</td><td class="px-5 py-2 text-right font-medium">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td></tr>
                    @if($sale->discount_amount > 0)
                    <tr><td colspan="5" class="px-5 py-2 text-right text-gray-500">Diskon ({{ $sale->discount_percent }}%)</td><td class="px-5 py-2 text-right text-red-500">-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</td></tr>
                    @endif
                    @if($sale->tax_amount > 0)
                    <tr><td colspan="5" class="px-5 py-2 text-right text-gray-500">Pajak ({{ $sale->tax_percent }}%)</td><td class="px-5 py-2 text-right">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</td></tr>
                    @endif
                    <tr class="border-t-2 border-gray-300"><td colspan="5" class="px-5 py-3 text-right font-bold text-base">TOTAL</td><td class="px-5 py-3 text-right font-bold text-base text-indigo-600">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td></tr>
                    <tr><td colspan="5" class="px-5 py-2 text-right text-gray-500">Dibayar</td><td class="px-5 py-2 text-right font-medium">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td></tr>
                    <tr><td colspan="5" class="px-5 py-2 text-right text-gray-500">Kembali</td><td class="px-5 py-2 text-right font-medium text-green-600">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</td></tr>
                </tfoot>
            </table>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">&larr; Kembali</a>
            @can('sales.cancel')
            @if($sale->status === 'completed')
            <form action="{{ route('sales.cancel', $sale) }}" method="POST" onsubmit="return confirm('Yakin cancel transaksi ini? Stok akan dikembalikan.')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Cancel Transaksi</button>
            </form>
            @endif
            @endcan
        </div>
    </div>
</x-layouts.admin>