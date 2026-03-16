<x-layouts.admin>
    <x-slot:header>Produk</x-slot:header>
    <x-slot:title>Produk</x-slot:title>

    <div class="bg-white rounded-xl border border-gray-200">
        {{-- Header + Search --}}
        <div class="p-5 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="font-semibold text-gray-900">Daftar Produk</h3>
                @can('products.create')
                <a href="{{ route('products.create') }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                    + Tambah Produk
                </a>
                @endcan
            </div>
            <form class="flex flex-wrap items-center gap-2 mt-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, SKU, barcode..."
                       class="flex-1 min-w-[200px] border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <label class="flex items-center gap-1.5 text-sm text-gray-600">
                    <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-red-600">
                    Stok rendah
                </label>
                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm rounded-lg transition">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-5 py-3 font-medium">SKU</th>
                        <th class="px-5 py-3 font-medium">Produk</th>
                        <th class="px-5 py-3 font-medium">Kategori</th>
                        <th class="px-5 py-3 font-medium text-right">Harga Beli</th>
                        <th class="px-5 py-3 font-medium text-right">Harga Jual</th>
                        <th class="px-5 py-3 font-medium text-center">Stok</th>
                        <th class="px-5 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-500 font-mono text-xs">{{ $product->sku }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-900">{{ $product->name }}</p>
                            @if($product->barcode)
                            <p class="text-xs text-gray-400">{{ $product->barcode }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $product->category->name }}</td>
                        <td class="px-5 py-3 text-right text-gray-500">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-medium">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $product->isLowStock() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $product->stock }} {{ $product->unit }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('products.edit')
                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                                @endcan
                                @can('products.delete')
                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                      onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">Belum ada produk</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $products->withQueryString()->links() }}</div>
    </div>
</x-layouts.admin>