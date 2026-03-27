<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS — {{ \App\Models\StoreSetting::getValue('store_name', 'Kasir') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.75rem;
        }

        .img-ph { background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); }

        .thin-scroll::-webkit-scrollbar { width: 3px; }
        .thin-scroll::-webkit-scrollbar-track { background: transparent; }
        .thin-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

        .clamp2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Step indicator line */
        .step-line { background: linear-gradient(90deg, #6366f1, #8b5cf6); }

        /* Slide transitions */
        .slide-enter { animation: slideUp 0.25s ease; }
        .panel-enter { animation: fadeSlide 0.2s ease; }

        /* Mobile cart backdrop */
        .backdrop-enter { animation: fadeIn 0.2s ease; }
    </style>
</head>
<body class="h-full bg-gray-50" x-data="posApp()" x-cloak>

<div class="h-screen flex flex-col overflow-hidden">

    {{-- ══ TOP BAR ══ --}}
    <header class="h-14 bg-slate-900 flex items-center justify-between px-4 shrink-0 shadow-lg z-20">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}"
               class="text-slate-500 hover:text-white transition p-1 rounded-lg hover:bg-slate-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-sm tracking-wide">
                    {{ \App\Models\StoreSetting::getValue('store_name', 'POS KASIR') }}
                </span>
            </div>
        </div>

        {{-- Step indicator (desktop) --}}
        <div class="hidden md:flex items-center gap-1">
            {{-- Step 1 --}}
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition"
                 :class="step === 1 ? 'bg-indigo-600' : (step > 1 ? 'bg-slate-700' : 'bg-slate-800')">
                <div class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-bold"
                     :class="step > 1 ? 'bg-emerald-400 text-white' : 'bg-white/20 text-white'">
                    <span x-show="step <= 1">1</span>
                    <span x-show="step > 1">&#10003;</span>
                </div>
                <span class="text-xs font-medium text-white">Pilih Produk</span>
            </div>
            <div class="w-6 h-px bg-slate-700"></div>
            {{-- Step 2 --}}
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition"
                 :class="step === 2 ? 'bg-indigo-600' : (step > 2 ? 'bg-slate-700' : 'bg-slate-800')">
                <div class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-bold"
                     :class="step > 2 ? 'bg-emerald-400 text-white' : 'bg-white/20 text-white'">
                    <span x-show="step <= 2">2</span>
                    <span x-show="step > 2">&#10003;</span>
                </div>
                <span class="text-xs font-medium text-white">Keranjang</span>
            </div>
            <div class="w-6 h-px bg-slate-700"></div>
            {{-- Step 3 --}}
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition"
                 :class="step === 3 ? 'bg-indigo-600' : 'bg-slate-800'">
                <div class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-[9px] font-bold text-white">3</div>
                <span class="text-xs font-medium text-white">Pembayaran</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-slate-500 text-xs font-mono hidden sm:block" x-text="currentTime"></span>
            <div class="w-7 h-7 bg-slate-700 rounded-full flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>
    </header>

    {{-- Mobile step bar --}}
    <div class="md:hidden bg-white border-b border-gray-200 px-4 py-2 shrink-0">
        <div class="flex items-center gap-2">
            <template x-for="(label, i) in ['Pilih Produk','Keranjang','Bayar']" :key="i">
                <div class="flex items-center gap-1.5 flex-1" x-show="i < 3">
                    <div class="flex items-center gap-1">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition"
                             :class="step === i+1 ? 'bg-indigo-600 text-white' : (step > i+1 ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400')">
                            <span x-show="step <= i+1" x-text="i+1"></span>
                            <span x-show="step > i+1">&#10003;</span>
                        </div>
                        <span class="text-[10px] font-medium transition"
                              :class="step === i+1 ? 'text-indigo-600' : (step > i+1 ? 'text-emerald-600' : 'text-gray-400')"
                              x-text="label"></span>
                    </div>
                    <div x-show="i < 2" class="flex-1 h-px bg-gray-200"></div>
                </div>
            </template>
        </div>
    </div>

    {{-- ══ CONTENT ══ --}}
    <div class="flex-1 overflow-hidden flex flex-col">

        {{-- ════════ STEP 1: PILIH PRODUK ════════ --}}
        <div x-show="step === 1" class="flex-1 flex flex-col overflow-hidden">

            {{-- Search --}}
            <div class="px-4 pt-3 pb-2 bg-white border-b border-gray-100 shrink-0">
                <div class="relative max-w-lg">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="searchQuery"
                           x-on:input.debounce.300ms="searchProducts()"
                           x-on:keydown.enter.prevent="addFirstResult()"
                           placeholder="Cari produk, nama, atau scan barcode..."
                           class="w-full pl-9 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-gray-50"
                           autofocus>
                    <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="animate-spin w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Grid produk --}}
            <div class="flex-1 overflow-y-auto thin-scroll p-4">

                {{-- Skeleton --}}
                <div x-show="loading" class="product-grid">
                    <template x-for="i in 16" :key="i">
                        <div class="bg-white rounded-2xl overflow-hidden animate-pulse shadow-sm">
                            <div class="aspect-square bg-gray-100"></div>
                            <div class="p-3 space-y-2">
                                <div class="h-2.5 bg-gray-100 rounded-full w-4/5"></div>
                                <div class="h-2.5 bg-gray-100 rounded-full w-3/5"></div>
                                <div class="h-3.5 bg-gray-100 rounded-full w-2/5 mt-1"></div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="!loading" class="product-grid">
                    <template x-for="p in products" :key="p.id">
                        <button x-on:click="addToCart(p)"
                                :disabled="p.stock <= 0"
                                :class="p.stock <= 0
                                    ? 'opacity-40 cursor-not-allowed'
                                    : 'hover:shadow-lg hover:-translate-y-1 hover:border-indigo-300 active:scale-95'"
                                class="bg-white border-2 rounded-2xl text-left transition-all duration-200 overflow-hidden flex flex-col group shadow-sm"
                                :style="isInCart(p.id) ? 'border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15)' : 'border-color: #f1f5f9'">

                            <div class="w-full aspect-square relative overflow-hidden bg-gray-50">
                                <img x-show="p.image_url"
                                     :src="p.image_url" :alt="p.name"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                     loading="lazy"
                                     x-on:error="p.image_url = null">
                                <div x-show="!p.image_url"
                                     class="w-full h-full img-ph flex items-center justify-center">
                                    <svg class="w-10 h-10 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                {{-- Stok badge --}}
                                <span :class="p.stock <= 0 ? 'bg-rose-500' : (p.stock <= 5 ? 'bg-amber-400' : 'bg-emerald-500')"
                                      class="absolute top-2 right-2 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full leading-none shadow"
                                      x-text="p.stock <= 0 ? 'Habis' : p.stock"></span>
                                {{-- Qty badge --}}
                                <div x-show="isInCart(p.id)"
                                     class="absolute top-2 left-2 bg-indigo-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow flex items-center gap-0.5">
                                    <span>&#10003;</span>
                                    <span x-text="cartQty(p.id)"></span>
                                </div>
                            </div>

                            <div class="p-3 flex flex-col flex-1">
                                <p class="text-xs font-bold text-gray-800 leading-tight clamp2" x-text="p.name"></p>
                                <p class="text-[9px] text-gray-400 font-mono mt-0.5" x-text="p.sku"></p>
                                <p class="text-sm font-extrabold text-indigo-600 mt-auto pt-2" x-text="formatRp(p.selling_price)"></p>
                            </div>
                        </button>
                    </template>
                </div>

                <div x-show="!loading && products.length === 0"
                     class="flex flex-col items-center justify-center py-20 text-gray-400">
                    <svg class="w-16 h-16 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="text-sm font-medium"
                       x-text="searchQuery.length > 0 ? 'Produk tidak ditemukan' : 'Mulai ketik atau scan barcode'"></p>
                </div>
            </div>

            {{-- CTA Bar bawah --}}
            <div class="shrink-0 bg-white border-t border-gray-100 px-4 py-3 shadow-lg">
                <div class="flex items-center gap-3">
                    {{-- Summary cart --}}
                    <div class="flex-1" x-show="cart.length > 0">
                        <p class="text-xs text-gray-500"><span x-text="cart.length"></span> item dipilih</p>
                        <p class="text-sm font-bold text-gray-800" x-text="formatRp(subtotal)"></p>
                    </div>
                    <div class="flex-1" x-show="cart.length === 0">
                        <p class="text-xs text-gray-400">Belum ada item dipilih</p>
                    </div>

                    {{-- Lanjut button --}}
                    <button x-on:click="step = 2"
                            :disabled="cart.length === 0"
                            class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold rounded-xl transition text-sm shadow-md shadow-indigo-200">
                        <span>Lanjut Pesan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        {{-- END STEP 1 --}}

        {{-- ════════ STEP 2: REVIEW KERANJANG ════════ --}}
        <div x-show="step === 2" class="flex-1 flex flex-col overflow-hidden bg-gray-50">

            {{-- Back + title --}}
            <div class="px-4 py-3 bg-white border-b border-gray-100 flex items-center gap-3 shrink-0">
                <button x-on:click="step = 1"
                        class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Review Pesanan</h2>
                    <p class="text-xs text-gray-400" x-text="cart.length + ' item'"></p>
                </div>
                <button x-on:click="clearCart()" x-show="cart.length > 0"
                        class="ml-auto text-xs text-rose-500 hover:text-rose-700 font-medium transition">
                    Hapus Semua
                </button>
            </div>

            {{-- Cart items --}}
            <div class="flex-1 overflow-y-auto thin-scroll p-4 space-y-3">
                <template x-for="(item, idx) in cart" :key="item.product_id">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center gap-3 p-3">
                            {{-- Thumb --}}
                            <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-indigo-50 flex items-center justify-center border border-gray-100">
                                <img x-show="item.image_url" :src="item.image_url" :alt="item.name"
                                     class="w-full h-full object-cover">
                                <svg x-show="!item.image_url" class="w-7 h-7 text-indigo-200"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800 leading-tight" x-text="item.name"></p>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="formatRp(item.price) + ' / ' + item.unit"></p>
                                {{-- Qty control --}}
                                <div class="flex items-center gap-2 mt-2">
                                    <button x-on:click="updateQty(idx,-1)"
                                            class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold transition">&#8722;</button>
                                    <span class="text-sm font-bold text-gray-800 w-6 text-center" x-text="item.qty"></span>
                                    <button x-on:click="updateQty(idx,1)"
                                            class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold transition">&#43;</button>
                                </div>
                            </div>
                            {{-- Subtotal + hapus --}}
                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <p class="text-sm font-extrabold text-gray-900" x-text="formatRp(item.price * item.qty)"></p>
                                <button x-on:click="removeFromCart(idx)"
                                        class="w-6 h-6 rounded-lg bg-rose-50 hover:bg-rose-100 flex items-center justify-center transition">
                                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Customer --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Customer (opsional)</p>
                    <select x-model="customerId"
                            class="w-full text-sm border border-gray-200 rounded-xl py-2.5 px-3 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">— Tanpa Customer —</option>
                        @foreach($customers as $cust)
                        <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->code }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Summary --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 space-y-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ringkasan</p>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal (<span x-text="cart.length"></span> item)</span>
                        <span class="font-semibold" x-text="formatRp(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm items-center gap-3">
                        <span class="text-gray-500">Diskon (%)</span>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model.number="discountPercent" min="0" max="100"
                                   x-on:input="recalc()"
                                   class="w-20 text-right text-sm border border-gray-200 rounded-lg py-1.5 px-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                    </div>
                    <div x-show="discountPercent > 0" class="flex justify-between text-sm text-rose-500">
                        <span>Potongan</span>
                        <span x-text="'- ' + formatRp(discAmt)"></span>
                    </div>
                    <div class="flex justify-between text-sm items-center gap-3">
                        <span class="text-gray-500">Pajak (%)</span>
                        <input type="number" x-model.number="taxPercent" min="0" max="100"
                               x-on:input="recalc()"
                               class="w-20 text-right text-sm border border-gray-200 rounded-lg py-1.5 px-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                               value="{{ \App\Models\StoreSetting::getValue('tax_percent', 11) }}">
                    </div>
                    <div x-show="taxPercent > 0" class="flex justify-between text-sm text-gray-500">
                        <span>PPN</span>
                        <span x-text="formatRp(taxAmt)"></span>
                    </div>
                    <div class="pt-3 border-t border-dashed border-gray-200 flex justify-between items-center">
                        <span class="text-base font-bold text-gray-900">TOTAL</span>
                        <span class="text-2xl font-extrabold text-indigo-600" x-text="formatRp(grandTotal)"></span>
                    </div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="shrink-0 bg-white border-t border-gray-100 px-4 py-3 shadow-lg">
                <button x-on:click="step = 3"
                        :disabled="cart.length === 0"
                        class="w-full flex items-center justify-center gap-2 py-3.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 disabled:bg-gray-200 disabled:cursor-not-allowed text-white font-bold rounded-xl transition text-sm shadow-md shadow-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Lanjut ke Pembayaran</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
        {{-- END STEP 2 --}}

        {{-- ════════ STEP 3: PEMBAYARAN ════════ --}}
        <div x-show="step === 3" class="flex-1 flex flex-col overflow-hidden bg-gray-50">

            {{-- Back + title --}}
            <div class="px-4 py-3 bg-white border-b border-gray-100 flex items-center gap-3 shrink-0">
                <button x-on:click="step = 2"
                        class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Pembayaran</h2>
                    <p class="text-xs text-gray-400">Total: <span class="font-bold text-indigo-600" x-text="formatRp(grandTotal)"></span></p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto thin-scroll p-4 space-y-4">

                {{-- Metode Bayar --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-gray-500 mb-3 uppercase tracking-wide">Metode Pembayaran</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <template x-for="m in payMethods" :key="m.value">
                            <button x-on:click="paymentMethod = m.value; recalc()"
                                    :class="paymentMethod === m.value
                                        ? 'border-indigo-500 bg-indigo-50 text-indigo-700 shadow-sm'
                                        : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-gray-300'"
                                    class="flex items-center gap-2 px-3 py-2.5 border-2 rounded-xl transition text-sm font-semibold">
                                <span x-text="m.icon" class="text-base"></span>
                                <span x-text="m.label" class="text-xs"></span>
                                <svg x-show="paymentMethod === m.value" class="w-3.5 h-3.5 ml-auto text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Input bayar (cash) --}}
                <div x-show="paymentMethod === 'cash'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 space-y-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Jumlah Bayar</p>
                    <input type="number" x-model.number="paidAmount" x-on:input="recalc()"
                           placeholder="0"
                           class="w-full text-2xl font-bold text-right border-2 border-gray-200 rounded-xl py-3 px-4 bg-gray-50 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 transition">

                    {{-- Kembalian --}}
                    <div class="flex justify-between items-center p-3 rounded-xl"
                         :class="change >= 0 ? 'bg-emerald-50' : 'bg-rose-50'">
                        <span class="text-sm font-semibold"
                              :class="change >= 0 ? 'text-emerald-700' : 'text-rose-700'">Kembalian</span>
                        <span class="text-xl font-extrabold"
                              :class="change >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                              x-text="formatRp(Math.max(0, change))"></span>
                    </div>

                    {{-- Quick cash --}}
                    <div>
                        <p class="text-[10px] text-gray-400 mb-1.5 uppercase tracking-wide">Nominal cepat</p>
                        <div class="grid grid-cols-4 gap-1.5">
                            <template x-for="nom in quickCash" :key="nom">
                                <button x-on:click="paidAmount = nom; recalc()"
                                        :class="paidAmount === nom ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50'"
                                        class="py-2 text-xs font-bold border-2 rounded-xl transition"
                                        x-text="fmtShort(nom)"></button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Non-cash info --}}
                <div x-show="paymentMethod !== 'cash'"
                     class="bg-indigo-50 border-2 border-indigo-200 rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-indigo-800">Pembayaran Non-Tunai</p>
                        <p class="text-xs text-indigo-600 mt-0.5">Pastikan pembayaran sudah diterima sebelum klik Proses.</p>
                    </div>
                </div>

                {{-- Order summary mini --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs font-semibold text-gray-500 mb-3 uppercase tracking-wide">Rincian Tagihan</p>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Subtotal</span><span x-text="formatRp(subtotal)"></span>
                        </div>
                        <div x-show="discountPercent > 0" class="flex justify-between text-sm text-rose-500">
                            <span>Diskon <span x-text="discountPercent"></span>%</span>
                            <span x-text="'- ' + formatRp(discAmt)"></span>
                        </div>
                        <div x-show="taxPercent > 0" class="flex justify-between text-sm text-gray-500">
                            <span>PPN <span x-text="taxPercent"></span>%</span>
                            <span x-text="formatRp(taxAmt)"></span>
                        </div>
                        <div class="pt-2 border-t border-dashed border-gray-200 flex justify-between">
                            <span class="font-bold text-gray-900">Total</span>
                            <span class="text-lg font-extrabold text-indigo-600" x-text="formatRp(grandTotal)"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PROSES BAYAR --}}
            <div class="shrink-0 bg-white border-t border-gray-100 px-4 py-3 shadow-lg">
                <button x-on:click="checkout()"
                        :disabled="processing || (paymentMethod === 'cash' && paidAmount < grandTotal)"
                        class="w-full flex items-center justify-center gap-2 py-4 font-bold rounded-xl transition text-base shadow-lg disabled:cursor-not-allowed"
                        :class="paymentMethod === 'cash' && paidAmount < grandTotal && paidAmount > 0
                            ? 'bg-rose-500 text-white'
                            : processing
                                ? 'bg-indigo-400 text-white'
                                : 'bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white shadow-indigo-200'">
                    <svg x-show="processing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <svg x-show="!processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="processing ? 'Memproses Transaksi...' : (paymentMethod === \'cash\' && paidAmount < grandTotal && paidAmount > 0 ? \'Bayar Kurang!\' : \'Proses Pembayaran\')"></span>
                </button>
                <p class="text-center text-[10px] text-gray-400 mt-2">Tekan F12 untuk shortcut bayar</p>
            </div>
        </div>
        {{-- END STEP 3 --}}

    </div>
</div>

{{-- ══════════ RECEIPT MODAL ══════════ --}}
<div x-show="showReceipt" x-cloak
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/70 px-0 sm:px-4"
     x-on:keydown.escape.window="showReceipt = false">
    <div class="bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl w-full sm:w-96 flex flex-col" style="max-height:92vh;">
        {{-- Success header --}}
        <div class="p-6 border-b border-gray-100 text-center shrink-0">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-extrabold text-gray-900">Transaksi Berhasil!</h3>
            <p class="text-sm text-gray-500 mt-1">Pembayaran telah diterima</p>
        </div>
        <div class="flex-1 overflow-y-auto thin-scroll p-4">
            <pre class="font-mono text-xs whitespace-pre-wrap bg-gray-50 p-4 rounded-2xl border border-gray-200 text-gray-700" x-text="receiptText"></pre>
        </div>
        <div class="p-4 border-t border-gray-100 flex gap-2 shrink-0">
            <button x-on:click="printReceipt()"
                    class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-2xl transition flex items-center justify-center gap-1.5 shadow-md shadow-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Struk
            </button>
            <button x-on:click="showReceipt = false; resetTransaction()"
                    class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-2xl transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Transaksi Baru
            </button>
        </div>
    </div>
</div>

<script>
function posApp() {
    return {
        step: 1,
        searchQuery: '', products: [], cart: [], customerId: '',
        discountPercent: 0,
        taxPercent: {{ \App\Models\StoreSetting::getValue('tax_percent', 11) }},
        paymentMethod: 'cash', paidAmount: 0, processing: false,
        showReceipt: false, receiptText: '', lastSaleId: null,
        currentTime: '', loading: false,

        payMethods: [
            { value: 'cash',     label: 'Tunai',    icon: '💵' },
            { value: 'debit',    label: 'Debit',    icon: '💳' },
            { value: 'ewallet',  label: 'E-Wallet', icon: '📱' },
            { value: 'transfer', label: 'Transfer', icon: '🏦' },
            { value: 'credit',   label: 'Kredit',   icon: '💳' },
            { value: 'qris',     label: 'QRIS',     icon: '📷' },
        ],

        get subtotal()   { return this.cart.reduce((s,i) => s + i.price * i.qty, 0); },
        get discAmt()    { return this.discountPercent / 100 * this.subtotal; },
        get afterDisc()  { return this.subtotal - this.discAmt; },
        get taxAmt()     { return this.taxPercent / 100 * this.afterDisc; },
        get grandTotal() { return this.afterDisc + this.taxAmt; },
        get change()     { return this.paymentMethod === 'cash' ? this.paidAmount - this.grandTotal : 0; },
        get quickCash() {
            const gt = this.grandTotal;
            if (gt <= 0) return [];
            const r = [1000,5000,10000,20000,50000,100000].map(d => Math.ceil(gt/d)*d);
            return [...new Set(r)].filter(n => n >= gt).slice(0,4);
        },

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            document.addEventListener('keydown', e => {
                if (e.key === 'F12') { e.preventDefault(); this.checkout(); }
            });
            this.searchProducts();
        },

        updateClock() {
            this.currentTime = new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit',
            });
        },

        isInCart(id)  { return this.cart.some(i => i.product_id === id); },
        cartQty(id)   { return (this.cart.find(i => i.product_id === id) || {qty:0}).qty; },

        async searchProducts() {
            this.loading = true;
            try {
                const r = await fetch('/api/products/search?q=' + encodeURIComponent(this.searchQuery));
                this.products = await r.json();
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        addFirstResult() {
            if (this.products.length > 0) {
                this.addToCart(this.products[0]);
                this.searchQuery = '';
                this.searchProducts();
            }
        },

        addToCart(p) {
            if (p.stock <= 0) return;
            const ex = this.cart.find(i => i.product_id === p.id);
            if (ex) { if (ex.qty < p.stock) { ex.qty++; this.recalc(); } return; }
            this.cart.push({
                product_id: p.id, name: p.name,
                price: parseFloat(p.selling_price), qty: 1,
                unit: p.unit, max_stock: p.stock, discount_percent: 0,
                image_url: p.image_url || null,
            });
            this.recalc();
        },

        updateQty(idx, d) {
            const item = this.cart[idx];
            const q = item.qty + d;
            if (q < 1) { this.removeFromCart(idx); return; }
            if (q > item.max_stock) return;
            item.qty = q;
            this.recalc();
        },

        removeFromCart(idx) {
            this.cart.splice(idx, 1);
            this.recalc();
            if (this.cart.length === 0) this.step = 1;
        },

        clearCart() {
            if (confirm('Hapus semua item?')) { this.cart = []; this.recalc(); this.step = 1; }
        },

        recalc() {
            if (this.paymentMethod !== 'cash') this.paidAmount = this.grandTotal;
        },

        async checkout() {
            if (!this.cart.length) return;
            if (this.paymentMethod === 'cash' && this.paidAmount < this.grandTotal) {
                alert('Jumlah bayar kurang dari total!'); return;
            }
            if (this.paymentMethod !== 'cash') this.paidAmount = this.grandTotal;
            this.processing = true;
            try {
                const r = await fetch('{{ route("pos.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
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
                const d = await r.json();
                if (d.success) {
                    this.lastSaleId = d.data.id;
                    await this.loadReceipt(d.data.id);
                    this.showReceipt = true;
                } else { alert('Error: ' + d.message); }
            } catch(e) { alert('Terjadi kesalahan: ' + e.message); }
            finally { this.processing = false; }
        },

        async loadReceipt(id) {
            try {
                const r = await fetch('/pos/' + id + '/receipt');
                const d = await r.json();
                this.receiptText = d.receipt;
            } catch(e) { this.receiptText = 'Gagal memuat struk'; }
        },

        async printReceipt() {
            if (!this.lastSaleId) return;
            try {
                const r = await fetch('/pos/' + this.lastSaleId + '/print', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                });
                const d = await r.json();
                if (d.success) { alert('Struk berhasil dicetak!'); }
                else {
                    const w = window.open('', '_blank');
                    w.document.write('<pre style="font-family:monospace;font-size:12px">' + this.receiptText + '</pre>');
                    w.document.close();
                    w.print();
                }
            } catch(e) { alert('Error cetak: ' + e.message); }
        },

        resetTransaction() {
            this.cart = []; this.customerId = ''; this.discountPercent = 0;
            this.paidAmount = 0; this.paymentMethod = 'cash';
            this.lastSaleId = null; this.receiptText = '';
            this.searchQuery = ''; this.step = 1;
            this.searchProducts();
        },

        formatRp(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0)); },
        fmtShort(n) {
            if (n >= 1000000) return (n/1000000) + 'jt';
            if (n >= 1000) return (n/1000) + 'rb';
            return n;
        },
    };
}
</script>
</body>
</html>