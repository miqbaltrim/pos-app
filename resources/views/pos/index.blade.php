<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Kasir — {{ \App\Models\StoreSetting::getValue('store_name', 'POS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem; }
    </style>
</head>
<body class="h-full bg-gray-100" x-data="posApp()" x-cloak>

<div class="h-screen flex flex-col">

    {{-- ===== TOP BAR ===== --}}
    <header class="bg-gray-900 text-white h-14 flex items-center justify-between px-4 shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-gray-300 hover:text-white transition text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Dashboard
            </a>
            <span class="text-lg font-bold text-indigo-400">POS KASIR</span>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <span class="text-gray-400">Kasir: <strong class="text-white">{{ auth()->user()->name }}</strong></span>
            <span class="text-gray-400" x-text="currentTime"></span>
        </div>
    </header>

    {{-- ===== MAIN AREA ===== --}}
    <div class="flex-1 flex overflow-hidden">

        {{-- LEFT: Product Search & Grid --}}
        <div class="flex-1 flex flex-col p-4 overflow-hidden">
            {{-- Search Bar --}}
            <div class="mb-3">
                <div class="relative">
                    <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           x-model="searchQuery"
                           @input.debounce.300ms="searchProducts()"
                           @keydown.enter.prevent="addFirstResult()"
                           placeholder="Scan barcode atau ketik nama produk..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           autofocus>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="flex-1 overflow-y-auto">
                <div class="product-grid">
                    <template x-for="product in products" :key="product.id">
                        <button @click="addToCart(product)"
                                class="bg-white border border-gray-200 rounded-lg p-3 text-left hover:border-indigo-300 hover:shadow-md transition group">
                            <p class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="product.sku"></p>
                            <p class="text-sm font-bold text-indigo-600 mt-2" x-text="formatRp(product.selling_price)"></p>
                            <p class="text-xs mt-1" :class="product.stock <= 5 ? 'text-red-500' : 'text-gray-400'">
                                Stok: <span x-text="product.stock"></span> <span x-text="product.unit"></span>
                            </p>
                        </button>
                    </template>
                </div>
                <p x-show="products.length === 0 && searchQuery.length > 0" class="text-center text-gray-400 py-12 text-sm">
                    Produk tidak ditemukan
                </p>
                <p x-show="products.length === 0 && searchQuery.length === 0" class="text-center text-gray-400 py-12 text-sm">
                    Ketik nama produk atau scan barcode untuk mulai
                </p>
            </div>
        </div>

        {{-- RIGHT: Cart --}}
        <div class="w-96 bg-white border-l border-gray-200 flex flex-col shrink-0">
            {{-- Cart Header --}}
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">
                    Keranjang
                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full ml-1" x-text="cart.length"></span>
                </h2>
                <button @click="clearCart()" x-show="cart.length > 0"
                        class="text-xs text-red-500 hover:text-red-700 transition">
                    Hapus Semua
                </button>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                <template x-for="(item, index) in cart" :key="item.product_id">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="item.name"></p>
                                <p class="text-xs text-gray-500" x-text="formatRp(item.price) + ' / ' + item.unit"></p>
                            </div>
                            <button @click="removeFromCart(index)" class="text-gray-400 hover:text-red-500 ml-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center gap-2">
                                <button @click="updateQty(index, -1)"
                                        class="w-7 h-7 rounded bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold">-</button>
                                <input type="number" x-model.number="item.qty" min="1" :max="item.max_stock"
                                       @change="recalculate()"
                                       class="w-12 text-center text-sm border border-gray-300 rounded py-1">
                                <button @click="updateQty(index, 1)"
                                        class="w-7 h-7 rounded bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold">+</button>
                            </div>
                            <p class="text-sm font-semibold text-gray-900" x-text="formatRp(item.price * item.qty)"></p>
                        </div>
                    </div>
                </template>
                <p x-show="cart.length === 0" class="text-center text-gray-400 py-12 text-sm">Keranjang kosong</p>
            </div>

            {{-- Customer Select --}}
            <div class="px-4 py-2 border-t border-gray-100">
                <select x-model="customerId" class="w-full text-sm border border-gray-300 rounded-lg py-2 px-3">
                    <option value="">-- Tanpa Customer --</option>
                    @foreach($customers as $cust)
                    <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->code }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Totals --}}
            <div class="p-4 border-t border-gray-200 space-y-2 bg-gray-50">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-medium" x-text="formatRp(subtotal)"></span>
                </div>
                <div class="flex justify-between text-sm items-center gap-2">
                    <span class="text-gray-500">Diskon (%)</span>
                    <input type="number" x-model.number="discountPercent" min="0" max="100"
                           @input="recalculate()"
                           class="w-20 text-right text-sm border border-gray-300 rounded py-1 px-2">
                </div>
                <div class="flex justify-between text-sm items-center gap-2">
                    <span class="text-gray-500">Pajak (%)</span>
                    <input type="number" x-model.number="taxPercent" min="0" max="100"
                           @input="recalculate()"
                           class="w-20 text-right text-sm border border-gray-300 rounded py-1 px-2"
                           value="{{ \App\Models\StoreSetting::getValue('tax_percent', 11) }}">
                </div>
                <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-300">
                    <span>TOTAL</span>
                    <span class="text-indigo-600" x-text="formatRp(grandTotal)"></span>
                </div>
            </div>

            {{-- Payment --}}
            <div class="p-4 border-t border-gray-200 space-y-3">
                <select x-model="paymentMethod" class="w-full text-sm border border-gray-300 rounded-lg py-2 px-3">
                    <option value="cash">Tunai (Cash)</option>
                    <option value="debit">Debit</option>
                    <option value="credit">Kartu Kredit</option>
                    <option value="ewallet">E-Wallet</option>
                    <option value="transfer">Transfer Bank</option>
                </select>

                <div x-show="paymentMethod === 'cash'">
                    <label class="text-xs text-gray-500">Bayar</label>
                    <input type="number" x-model.number="paidAmount"
                           @input="recalculate()"
                           class="w-full text-sm border border-gray-300 rounded-lg py-2 px-3 font-mono text-right"
                           placeholder="0">
                    <p class="text-right mt-1 text-sm" :class="changeAmount >= 0 ? 'text-green-600' : 'text-red-600'">
                        Kembali: <strong x-text="formatRp(Math.max(0, changeAmount))"></strong>
                    </p>
                </div>

                {{-- Quick Cash Buttons --}}
                <div x-show="paymentMethod === 'cash'" class="grid grid-cols-4 gap-1.5">
                    <template x-for="nom in quickCash" :key="nom">
                        <button @click="paidAmount = nom; recalculate()"
                                class="py-1.5 text-xs bg-gray-100 hover:bg-indigo-100 hover:text-indigo-700 rounded transition font-medium"
                                x-text="formatRpShort(nom)">
                        </button>
                    </template>
                </div>

                <button @click="checkout()"
                        :disabled="cart.length === 0 || processing"
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <svg x-show="processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="processing ? 'Memproses...' : 'BAYAR (F12)'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== RECEIPT MODAL ===== --}}
<div x-show="showReceipt" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/70"
     @keydown.escape.window="showReceipt = false">
    <div class="bg-white rounded-xl shadow-2xl w-96 max-h-[90vh] flex flex-col">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="font-semibold">Struk Pembayaran</h3>
            <button @click="showReceipt = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-4">
            <pre class="font-mono text-xs whitespace-pre-wrap bg-gray-50 p-4 rounded-lg border" x-text="receiptText"></pre>
        </div>
        <div class="p-4 border-t flex gap-2">
            <button @click="printReceipt()"
                    class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                Cetak Thermal
            </button>
            <button @click="showReceipt = false; resetTransaction()"
                    class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                Transaksi Baru
            </button>
        </div>
    </div>
</div>

<script>
function posApp() {
    return {
        // State
        searchQuery: '',
        products: [],
        cart: [],
        customerId: '',
        discountPercent: 0,
        taxPercent: {{ \App\Models\StoreSetting::getValue('tax_percent', 11) }},
        paymentMethod: 'cash',
        paidAmount: 0,
        processing: false,
        showReceipt: false,
        receiptText: '',
        lastSaleId: null,
        currentTime: '',

        // Computed
        get subtotal() { return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0); },
        get discountAmount() { return this.discountPercent / 100 * this.subtotal; },
        get afterDiscount() { return this.subtotal - this.discountAmount; },
        get taxAmount() { return this.taxPercent / 100 * this.afterDiscount; },
        get grandTotal() { return this.afterDiscount + this.taxAmount; },
        get changeAmount() { return this.paymentMethod === 'cash' ? this.paidAmount - this.grandTotal : 0; },
        get quickCash() {
            const gt = this.grandTotal;
            if (gt <= 0) return [];
            const rounded = [
                Math.ceil(gt / 1000) * 1000,
                Math.ceil(gt / 5000) * 5000,
                Math.ceil(gt / 10000) * 10000,
                Math.ceil(gt / 50000) * 50000,
                Math.ceil(gt / 100000) * 100000,
            ];
            return [...new Set(rounded)].filter(n => n >= gt).slice(0, 4);
        },

        // Init
        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.key === 'F12') { e.preventDefault(); this.checkout(); }
            });

            // Load initial products
            this.searchProducts();
        },

        updateClock() {
            this.currentTime = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        },

        // Search
        async searchProducts() {
            try {
                const res = await fetch(`/api/products/search?q=${encodeURIComponent(this.searchQuery)}`);
                this.products = await res.json();
            } catch (e) {
                console.error('Search error:', e);
            }
        },

        addFirstResult() {
            if (this.products.length > 0) {
                this.addToCart(this.products[0]);
                this.searchQuery = '';
                this.searchProducts();
            }
        },

        // Cart
        addToCart(product) {
            const existing = this.cart.find(i => i.product_id === product.id);
            if (existing) {
                if (existing.qty < product.stock) {
                    existing.qty++;
                    this.recalculate();
                }
                return;
            }
            this.cart.push({
                product_id: product.id,
                name: product.name,
                price: parseFloat(product.selling_price),
                qty: 1,
                unit: product.unit,
                max_stock: product.stock,
                discount_percent: 0,
            });
            this.recalculate();
        },

        updateQty(index, delta) {
            const item = this.cart[index];
            const newQty = item.qty + delta;
            if (newQty < 1) return;
            if (newQty > item.max_stock) return;
            item.qty = newQty;
            this.recalculate();
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.recalculate();
        },

        clearCart() {
            if (confirm('Hapus semua item di keranjang?')) {
                this.cart = [];
                this.recalculate();
            }
        },

        recalculate() {
            if (this.paymentMethod !== 'cash') {
                this.paidAmount = this.grandTotal;
            }
        },

        // Checkout
        async checkout() {
            if (this.cart.length === 0) return;
            if (this.paymentMethod === 'cash' && this.paidAmount < this.grandTotal) {
                alert('Jumlah bayar kurang!');
                return;
            }
            if (this.paymentMethod !== 'cash') {
                this.paidAmount = this.grandTotal;
            }

            this.processing = true;

            try {
                const res = await fetch('{{ route("pos.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        customer_id: this.customerId || null,
                        payment_method: this.paymentMethod,
                        paid_amount: this.paidAmount,
                        discount_percent: this.discountPercent,
                        tax_percent: this.taxPercent,
                        items: this.cart.map(i => ({
                            product_id: i.product_id,
                            quantity: i.qty,
                            discount_percent: i.discount_percent,
                        })),
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    this.lastSaleId = data.data.id;
                    await this.loadReceipt(data.data.id);
                    this.showReceipt = true;
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (e) {
                alert('Terjadi kesalahan: ' + e.message);
            } finally {
                this.processing = false;
            }
        },

        async loadReceipt(saleId) {
            try {
                const res = await fetch(`/pos/${saleId}/receipt`);
                const data = await res.json();
                this.receiptText = data.receipt;
            } catch (e) {
                this.receiptText = 'Gagal memuat struk';
            }
        },

        async printReceipt() {
            if (!this.lastSaleId) return;
            try {
                const res = await fetch(`/pos/${this.lastSaleId}/print`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    alert('Struk berhasil dicetak!');
                } else {
                    // Fallback: print via browser
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`<pre style="font-family:monospace;font-size:12px">${this.receiptText}</pre>`);
                    printWindow.document.close();
                    printWindow.print();
                }
            } catch (e) {
                alert('Error cetak: ' + e.message);
            }
        },

        resetTransaction() {
            this.cart = [];
            this.customerId = '';
            this.discountPercent = 0;
            this.paidAmount = 0;
            this.paymentMethod = 'cash';
            this.lastSaleId = null;
            this.receiptText = '';
            this.searchQuery = '';
            this.searchProducts();
        },

        // Helpers
        formatRp(n) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
        },
        formatRpShort(n) {
            if (n >= 1000000) return (n/1000000) + 'jt';
            if (n >= 1000) return (n/1000) + 'rb';
            return n;
        },
    };
}
</script>

</body>
</html>