<x-layouts.admin>
    <x-slot:header>Supplier</x-slot:header>
    <x-slot:title>Supplier</x-slot:title>
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Daftar Supplier</h3>
            @can('suppliers.create')
            <a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Tambah Supplier</a>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 bg-gray-50 border-b"><th class="px-5 py-3 font-medium">Kode</th><th class="px-5 py-3 font-medium">Nama</th><th class="px-5 py-3 font-medium">Telepon</th><th class="px-5 py-3 font-medium">Contact Person</th><th class="px-5 py-3 font-medium text-center">Status</th><th class="px-5 py-3 font-medium text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($suppliers as $item)
                    <tr class="border-b border-gray-50 hover:bg-gray-50"><td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $item->code }}</td><td class="px-5 py-3 font-medium text-gray-900">{{ $item->name }}</td><td class="px-5 py-3 text-gray-500">{{ $item->phone ?? '-' }}</td><td class="px-5 py-3 text-gray-500">{{ $item->contact_person ?? '-' }}</td><td class="px-5 py-3 text-center "><span class="px-2 py-0.5 rounded-full text-xs {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('suppliers.edit')<a href="{{ route('suppliers.edit', $item) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>@endcan
                                @can('suppliers.delete')<form action="{{ route('suppliers.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button></form>@endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $suppliers->links() }}</div>
    </div>
</x-layouts.admin>