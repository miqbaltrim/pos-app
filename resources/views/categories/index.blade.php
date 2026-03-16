<x-layouts.admin>
    <x-slot:header>Kategori</x-slot:header>
    <x-slot:title>Kategori</x-slot:title>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Daftar Kategori</h3>
            @can('categories.create')
            <a href="{{ route('categories.create') }}"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                + Tambah Kategori
            </a>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-5 py-3 font-medium">Nama</th>
                        <th class="px-5 py-3 font-medium">Slug</th>
                        <th class="px-5 py-3 font-medium text-center">Produk</th>
                        <th class="px-5 py-3 font-medium text-center">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $category->name }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $category->slug }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs">{{ $category->products_count }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($category->is_active)
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Aktif</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('categories.edit')
                                <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                                @endcan
                                @can('categories.delete')
                                <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">Belum ada kategori</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $categories->links() }}</div>
    </div>
</x-layouts.admin>