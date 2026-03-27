<x-layouts.admin>
    <x-slot:header>{{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}</x-slot:header>
    <x-slot:title>{{ isset($product) ? 'Edit' : 'Tambah' }} Produk</x-slot:title>

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}"
                  method="POST" enctype="multipart/form-data"
                  x-data="productForm()">
                @csrf
                @if(isset($product)) @method('PUT') @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Barcode --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}"
                               placeholder="Scan atau ketik barcode"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('barcode') border-red-500 @enderror">
                        @error('barcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                        <select name="category_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('category_id') border-red-500 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Satuan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan *</label>
                        <input type="text" name="unit" value="{{ old('unit', $product->unit ?? 'pcs') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="pcs, kg, liter, box...">
                    </div>

                    {{-- Harga Beli --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli *</label>
                        <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price ?? 0) }}" required min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('cost_price') border-red-500 @enderror">
                        @error('cost_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Harga Jual --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual *</label>
                        <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price ?? 0) }}" required min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('selling_price') border-red-500 @enderror">
                        @error('selling_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Stok Awal (hanya saat create) --}}
                    @if(!isset($product))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal *</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" required min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    @endif

                    {{-- Min Stok --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Stok *</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 5) }}" required min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    {{-- Deskripsi --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                    {{-- ===== IMAGE UPLOAD ===== --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>

                        <div class="flex items-start gap-4">
                            {{-- Preview --}}
                            <div class="shrink-0">
                                {{-- Current image (edit mode) --}}
                                <div x-show="!previewUrl && currentImage" class="relative">
                                    <img :src="currentImage"
                                         class="w-28 h-28 rounded-lg object-cover border border-gray-200">
                                    <button type="button"
                                            @click="removeCurrentImage()"
                                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-sm transition"
                                            title="Hapus gambar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- New image preview --}}
                                <div x-show="previewUrl" class="relative">
                                    <img :src="previewUrl"
                                         class="w-28 h-28 rounded-lg object-cover border-2 border-indigo-300">
                                    <button type="button"
                                            @click="clearNewImage()"
                                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-sm transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    <span class="absolute bottom-1 left-1 bg-indigo-600 text-white text-[10px] px-1.5 py-0.5 rounded">Baru</span>
                                </div>

                                {{-- No image placeholder --}}
                                <div x-show="!previewUrl && !currentImage"
                                     class="w-28 h-28 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Upload area --}}
                            <div class="flex-1">
                                <label class="block cursor-pointer">
                                    <div class="border-2 border-dashed border-gray-300 hover:border-indigo-400 rounded-lg p-4 text-center transition">
                                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="text-sm text-gray-600">Klik untuk pilih gambar</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP. Maks 2MB</p>
                                    </div>
                                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                                           class="hidden"
                                           @change="handleFileSelect($event)"
                                           x-ref="fileInput">
                                </label>
                                @error('image') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Hidden input untuk remove image --}}
                        <input type="hidden" name="remove_image" :value="shouldRemove ? '1' : '0'">
                    </div>

                    {{-- Status (hanya edit) --}}
                    @if(isset($product))
                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700">Produk Aktif</span>
                        </label>
                    </div>
                    @endif
                </div>

                {{-- Margin Calculator --}}
                <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-600"
                     x-data="{
                        cost: {{ old('cost_price', $product->cost_price ?? 0) }},
                        sell: {{ old('selling_price', $product->selling_price ?? 0) }},
                        get margin() { return this.sell > 0 ? ((this.sell - this.cost) / this.sell * 100).toFixed(1) : 0 },
                        get profit() { return this.sell - this.cost }
                     }"
                     x-init="
                        $watch('cost', v => cost = parseFloat(v) || 0);
                        $watch('sell', v => sell = parseFloat(v) || 0);
                        document.querySelector('[name=cost_price]').addEventListener('input', e => cost = parseFloat(e.target.value) || 0);
                        document.querySelector('[name=selling_price]').addEventListener('input', e => sell = parseFloat(e.target.value) || 0);
                     ">
                    <span>Profit: <strong class="text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(profit)"></strong></span>
                    <span class="ml-4">Margin: <strong :class="margin >= 20 ? 'text-green-600' : 'text-red-600'" x-text="margin + '%'"></strong></span>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 mt-6 pt-4 border-t">
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        {{ isset($product) ? 'Update Produk' : 'Simpan Produk' }}
                    </button>
                    <a href="{{ route('products.index') }}"
                       class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function productForm() {
        return {
            previewUrl: null,
            currentImage: @json(isset($product) && $product->image ? asset('storage/' . $product->image) : null),
            shouldRemove: false,

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (!file) return;

                // Validasi ukuran (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maks 2MB!');
                    this.$refs.fileInput.value = '';
                    return;
                }

                // Validasi tipe
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    alert('Format harus JPG, PNG, atau WebP!');
                    this.$refs.fileInput.value = '';
                    return;
                }

                // Preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewUrl = e.target.result;
                    this.shouldRemove = false;
                };
                reader.readAsDataURL(file);
            },

            clearNewImage() {
                this.previewUrl = null;
                this.$refs.fileInput.value = '';
            },

            removeCurrentImage() {
                this.currentImage = null;
                this.shouldRemove = true;
            },
        };
    }
    </script>
    @endpush
</x-layouts.admin>