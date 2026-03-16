<x-layouts.admin>
    <x-slot:header>Penyesuaian Stok</x-slot:header>
    <x-slot:title>Adjust Stok</x-slot:title>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ route('stocks.adjust.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produk *</label>
                        <select name="product_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">Pilih Produk</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Stok: {{ $p->stock }} {{ $p->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                        <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="in">Masuk (Tambah Stok)</option>
                            <option value="out">Keluar (Kurangi Stok)</option>
                            <option value="adjustment">Penyesuaian (Set ke Angka Baru)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah *</label>
                        <input type="number" name="quantity" min="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan / Alasan *</label>
                        <textarea name="notes" rows="2" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                  placeholder="Contoh: Hasil stock opname, barang rusak, dll."></textarea>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-6 pt-4 border-t">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Simpan</button>
                    <a href="{{ route('stocks.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>