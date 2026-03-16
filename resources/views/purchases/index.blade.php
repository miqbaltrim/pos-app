<x-layouts.admin>
    <x-slot:header>Pembelian</x-slot:header>
    <x-slot:title>Pembelian</x-slot:title>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Daftar Purchase Order</h3>
            @can('purchases.create')
            <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                + Buat PO Baru
            </a>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-5 py-3 font-medium">No. PO</th>
                        <th class="px-5 py-3 font-medium">Tanggal</th>
                        <th class="px-5 py-3 font-medium">Supplier</th>
                        <th class="px-5 py-3 font-medium text-right">Total</th>
                        <th class="px-5 py-3 font-medium text-center">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $po)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs font-medium text-indigo-600">
                            <a href="{{ route('purchases.show', $po) }}">{{ $po->purchase_number }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $po->purchase_date->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $po->supplier->name }}</td>
                        <td class="px-5 py-3 text-right font-medium">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($po->status === 'received')
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Received</span>
                            @elseif($po->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs">Pending</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">Cancelled</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('purchases.show', $po) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Detail</a>
                                @can('purchases.receive')
                                @if($po->status === 'pending')
                                <form action="{{ route('purchases.receive', $po) }}" method="POST" onsubmit="return confirm('Terima barang dan update stok?')">
                                    @csrf
                                    <button class="text-green-600 hover:text-green-800 text-xs font-medium">Terima</button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Belum ada purchase order</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $purchases->links() }}</div>
    </div>
</x-layouts.admin>