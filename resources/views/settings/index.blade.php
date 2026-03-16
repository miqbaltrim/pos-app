<x-layouts.admin>
    <x-slot:header>Pengaturan Toko</x-slot:header>
    <x-slot:title>Settings</x-slot:title>
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko</label>
                        <input type="text" name="store_name" value="{{ $settings['store_name'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Toko</label>
                        <textarea name="store_address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ $settings['store_address'] ?? '' }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label><input type="text" name="store_phone" value="{{ $settings['store_phone'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="store_email" value="{{ $settings['store_email'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Pajak Default (%)</label><input type="number" name="tax_percent" value="{{ $settings['tax_percent'] ?? 11 }}" min="0" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Ambang Stok Rendah</label><input type="number" name="low_stock_threshold" value="{{ $settings['low_stock_threshold'] ?? 10 }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Footer Struk</label><input type="text" name="receipt_footer" value="{{ $settings['receipt_footer'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Printer Thermal (path/ip:port)</label><input type="text" name="receipt_printer" value="{{ $settings['receipt_printer'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono" placeholder="/dev/usb/lp0 atau 192.168.1.100:9100"></div>
                </div>
                <div class="mt-6 pt-4 border-t">
                    @can('settings.edit')<button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Simpan Pengaturan</button>@endcan
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>