<x-layouts.admin>
    <x-slot:header>Detail Purchase Order</x-slot:header>
    <x-slot:title>{{ $purchase->purchase_number }}</x-slot:title>

    <div class="max-w-4xl space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $purchase->purchase_number }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $purchase->purchase_date->format('d F Y') }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $purchase->status === 'received' ? 'bg-green-100 text-green-700' : ($purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ ucfirst($purchase->status) }}
                </span>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-4 text-sm">
                <div><p class="text-gray-400">Supplier</p><p class="font-medium">{{ $purchase->supplier->name }}</p></div>
                <div><p class="text-gray-400">Dibuat oleh</p><p class="font-medium">{{ $purchase->user->name }}</p></div>
                <div><p class="text-gray-400">Catatan</p><p class="font-medium">{{ $purchase->notes ?? '-' }}</p></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Produk</th>
                        <th class="px-5 py-3 text-right">Harga Beli</th>
                        <th class="px-5 py-3 text-center">Qty</th>
                        <th class="px-5 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->details as $i => $d)
                    <tr class="border-b border-gray-50">
                        <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 font-medium">{{ $d->product_name }}</td>
                        <td class="px-5 py-3 text-right">Rp {{ number_format($d->unit_cost, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center">{{ $d->quantity }}</td>
                        <td class="px-5 py-3 text-right font-medium">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr class="border-t-2">
                        <td colspan="4" class="px-5 py-3 text-right font-bold">TOTAL</td>
                        <td class="px-5 py-3 text-right font-bold text-indigo-600">Rp {{ number_format($purchase->grand_total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">&larr; Kembali</a>
            @can('purchases.receive')
            @if($purchase->status === 'pending')
            <form action="{{ route('purchases.receive', $purchase) }}" method="POST" onsubmit="return confirm('Terima barang? Stok akan bertambah.')">
                @csrf
                <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">Terima Barang</button>
            </form>
            @endif
            @endcan
        </div>
    </div>
</x-layouts.admin>