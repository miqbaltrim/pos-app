<x-layouts.admin>
    <x-slot:header>{{ isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier' }}</x-slot:header>
    <x-slot:title>{{ isset($supplier) ? 'Edit' : 'Tambah' }} Supplier</x-slot:title>
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ isset($supplier) ? route('suppliers.update', $supplier) : route('suppliers.store') }}" method="POST">
                @csrf
                @if(isset($supplier)) @method('PUT') @endif
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier *</label>
                        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('phone') border-red-500 @enderror">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('email') border-red-500 @enderror">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('contact_person') border-red-500 @enderror">
                        @error('contact_person') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('address', $supplier->address ?? '') }}</textarea>
                    </div>
                    @if(isset($supplier))
                    <label class="flex items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"><span class="text-sm text-gray-700">Aktif</span></label>
                    @endif
                </div>
                <div class="flex items-center gap-3 mt-6 pt-4 border-t">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">{{ isset($supplier) ? 'Update' : 'Simpan' }}</button>
                    <a href="{{ route('suppliers.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>