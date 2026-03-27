<x-layouts.admin>
    <x-slot:header>Buat Purchase Order</x-slot:header>
    <x-slot:title>Buat PO</x-slot:title>

    <div class="max-w-4xl" x-data="purchaseForm()">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form @submit.prevent="submitForm()">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                        <select x-model="form.supplier_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" x-model="form.purchase_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                {{-- Item Search + Add --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                    <div class="relative" x-on:click.outside="searchResults = []">
                        <input
                            type="text"
                            x-model="productSearch"
                            @input.debounce.300ms="searchProduct()"
                            @keydown.escape="searchResults = []"
                            placeholder="Ketik nama atau SKU produk..."
                            autocomplete="off"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                        >

                        {{-- Loading indicator --}}
                        <div x-show="searching" class="absolute right-3 top-2.5">
                            <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                        </div>

                        {{-- Dropdown hasil pencarian --}}
                        <div x-show="searchResults.length > 0"
                             x-transition
                             class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-56 overflow-y-auto">
                            <template x-for="p in searchResults" :key="p.id">
                                <button type="button" @click="addItem(p)"
                                        class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 text-sm border-b border-gray-50 flex items-center justify-between">
                                    <span>
                                        <span class="font-medium text-gray-800" x-text="p.name"></span>
                                        <span class="text-gray-400 text-xs ml-2" x-text="p.sku"></span>
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Stok: <span x-text="p.stock"></span>
                                        &nbsp;|&nbsp;
                                        <span x-text="formatRp(p.cost_price)"></span>
                                    </span>
                                </button>
                            </template>
                        </div>

                        {{-- Tidak ditemukan --}}
                        <div x-show="notFound"
                             class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 px-4 py-3 text-sm text-gray-400">
                            Produk tidak ditemukan.
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="overflow-x-auto border rounded-lg mb-4">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b text-gray-500">
                                <th class="px-4 py-2 text-left">Produk</th>
                                <th class="px-4 py-2 text-right w-36">Harga Beli</th>
                                <th class="px-4 py-2 text-center w-24">Qty</th>
                                <th class="px-4 py-2 text-right w-36">Subtotal</th>
                                <th class="px-4 py-2 w-12"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in items" :key="idx">
                                <tr class="border-b">
                                    <td class="px-4 py-2">
                                        <p class="font-medium text-gray-800" x-text="item.name"></p>
                                        <p class="text-xs text-gray-400" x-text="item.sku"></p>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" x-model.number="item.unit_cost" min="0" step="1"
                                               class="w-full text-right border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-300">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" x-model.number="item.quantity" min="1"
                                               class="w-full text-center border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-300">
                                    </td>
                                    <td class="px-4 py-2 text-right font-medium" x-text="formatRp(item.unit_cost * item.quantity)"></td>
                                    <td class="px-4 py-2 text-center">
                                        <button type="button" @click="items.splice(idx, 1)" class="text-red-400 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0">
                                <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                                    Belum ada item. Cari dan tambahkan produk di atas.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr class="border-t">
                                <td colspan="3" class="px-4 py-3 text-right font-semibold">Total</td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-600" x-text="formatRp(grandTotal)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea x-model="form.notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>

                <div class="flex items-center gap-3 mt-6 pt-4 border-t">
                    <button type="submit" :disabled="items.length === 0 || !form.supplier_id || submitting"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition">
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan PO'"></span>
                    </button>
                    <a href="{{ route('purchases.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function purchaseForm() {
        return {
            form: {
                supplier_id: '',
                purchase_date: new Date().toISOString().split('T')[0],
                notes: '',
            },
            items: [],
            productSearch: '',
            searchResults: [],
            searching: false,
            notFound: false,
            submitting: false,

            get grandTotal() {
                return this.items.reduce((sum, i) => sum + (i.unit_cost * i.quantity), 0);
            },

            async searchProduct() {
                this.notFound = false;
                if (this.productSearch.length < 2) {
                    this.searchResults = [];
                    return;
                }

                this.searching = true;
                try {
                    const res = await fetch(`/api/products/search?q=${encodeURIComponent(this.productSearch)}`, {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    });
                    const data = await res.json();
                    this.searchResults = data;
                    this.notFound = data.length === 0;
                } catch (e) {
                    console.error('Search error:', e);
                } finally {
                    this.searching = false;
                }
            },

            addItem(product) {
                const existing = this.items.find(i => i.product_id === product.id);
                if (existing) {
                    existing.quantity++;
                } else {
                    this.items.push({
                        product_id: product.id,
                        name: product.name,
                        sku: product.sku,
                        // Gunakan cost_price (harga beli), fallback ke 0 jika kosong
                        unit_cost: parseFloat(product.cost_price) || 0,
                        quantity: 1,
                    });
                }
                this.productSearch = '';
                this.searchResults = [];
                this.notFound = false;
            },

            async submitForm() {
                if (this.items.length === 0 || !this.form.supplier_id) return;
                this.submitting = true;
                try {
                    const res = await fetch('{{ route("purchases.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            ...this.form,
                            items: this.items.map(i => ({
                                product_id: i.product_id,
                                unit_cost: i.unit_cost,
                                quantity: i.quantity,
                            })),
                        }),
                    });

                    if (res.redirected) {
                        window.location.href = res.url;
                        return;
                    }

                    const data = await res.json();
                    if (data.errors) {
                        alert(Object.values(data.errors).flat().join('\n'));
                    } else {
                        window.location.href = '{{ route("purchases.index") }}';
                    }
                } catch (e) {
                    alert('Error: ' + e.message);
                } finally {
                    this.submitting = false;
                }
            },

            formatRp(n) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
            },
        };
    }
    </script>
    @endpush
</x-layouts.admin>