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
                @if(request()->hasAny(['search', 'category_id', 'low_stock']))
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm rounded-lg transition">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-5 py-3 font-medium">Produk</th>
                        <th class="px-5 py-3 font-medium">Kategori</th>
                        <th class="px-5 py-3 font-medium text-right">Harga Beli</th>
                        <th class="px-5 py-3 font-medium text-right">Harga Jual</th>
                        <th class="px-5 py-3 font-medium text-center">Stok</th>
                        <th class="px-5 py-3 font-medium text-center">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 group">
                        <td class="px-5 py-2.5">
                            <div class="flex items-center gap-3">
                                {{-- Thumbnail: 36x36px pas untuk table row --}}
                                @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     style="width:36px; height:36px; min-width:36px;"
                                     class="rounded-md object-cover border border-gray-200">
                                @else
                                <div style="width:36px; height:36px; min-width:36px;"
                                     class="rounded-md bg-gray-100 border border-gray-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate leading-tight">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400 font-mono truncate leading-tight mt-0.5">{{ $product->sku }}@if($product->barcode) · {{ $product->barcode }}@endif</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-2.5 text-gray-500">{{ $product->category->name }}</td>
                        <td class="px-5 py-2.5 text-right text-gray-500 tabular-nums">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-2.5 text-right font-medium tabular-nums">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-2.5 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $product->isLowStock() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $product->stock }} {{ $product->unit }}
                            </span>
                        </td>
                        <td class="px-5 py-2.5 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-2.5 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                                @can('products.edit')
                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                                @endcan
                                @can('products.delete')
                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                      onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Belum ada produk
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $products->withQueryString()->links() }}</div>
    </div>
</x-layouts.admin>