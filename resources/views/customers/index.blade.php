<x-layouts.admin>
    <x-slot:header>Customer</x-slot:header>
    <x-slot:title>Customer</x-slot:title>
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Daftar Customer</h3>
            @can('customers.create')
            <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Tambah Customer</a>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 bg-gray-50 border-b"><th class="px-5 py-3 font-medium">Kode</th><th class="px-5 py-3 font-medium">Nama</th><th class="px-5 py-3 font-medium">Telepon</th><th class="px-5 py-3 font-medium text-right">Total Belanja</th><th class="px-5 py-3 font-medium text-center">Poin</th><th class="px-5 py-3 font-medium text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($customers as $item)
                    <tr class="border-b border-gray-50 hover:bg-gray-50"><td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $item->code }}</td><td class="px-5 py-3 font-medium text-gray-900">{{ $item->name }}</td><td class="px-5 py-3 text-gray-500">{{ $item->phone ?? '-' }}</td><td class="px-5 py-3 text-right font-medium">Rp {{ number_format($item->total_purchases, 0, ',', '.') }}</td><td class="px-5 py-3 text-center "><span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $item->loyalty_points }}</span></td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('customers.edit')<a href="{{ route('customers.edit', $item) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>@endcan
                                @can('customers.delete')<form action="{{ route('customers.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button></form>@endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $customers->links() }}</div>
    </div>
</x-layouts.admin>