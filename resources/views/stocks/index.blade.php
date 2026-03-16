<x-layouts.admin>
    <x-slot:header>Stok & Opname</x-slot:header>
    <x-slot:title>Stok</x-slot:title>

    <div class="space-y-4">
        @can('stocks.adjust')
        <div class="flex justify-end">
            <a href="{{ route('stocks.adjust') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                + Penyesuaian Stok
            </a>
        </div>
        @endcan

        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b"><h3 class="font-semibold text-gray-900">Log Pergerakan Stok</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 bg-gray-50 border-b">
                            <th class="px-5 py-3 font-medium">Waktu</th>
                            <th class="px-5 py-3 font-medium">Produk</th>
                            <th class="px-5 py-3 font-medium text-center">Tipe</th>
                            <th class="px-5 py-3 font-medium text-center">Qty</th>
                            <th class="px-5 py-3 font-medium text-center">Sebelum</th>
                            <th class="px-5 py-3 font-medium text-center">Sesudah</th>
                            <th class="px-5 py-3 font-medium">Keterangan</th>
                            <th class="px-5 py-3 font-medium">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $mv)
                        <tr class="border-b border-gray-50">
                            <td class="px-5 py-3 text-xs text-gray-400">{{ $mv->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $mv->product->name }}</td>
                            <td class="px-5 py-3 text-center">
                                @if($mv->type === 'in')
                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Masuk</span>
                                @elseif($mv->type === 'out')
                                    <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">Keluar</span>
                                @elseif($mv->type === 'return')
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs">Return</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs">Adjust</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center font-medium">{{ $mv->quantity }}</td>
                            <td class="px-5 py-3 text-center text-gray-400">{{ $mv->stock_before }}</td>
                            <td class="px-5 py-3 text-center font-medium">{{ $mv->stock_after }}</td>
                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $mv->notes }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $mv->user->name }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">Belum ada pergerakan stok</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $movements->links() }}</div>
        </div>
    </div>
</x-layouts.admin>