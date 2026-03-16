<x-layouts.admin>
    <x-slot:header>{{ isset($customer) ? 'Edit Customer' : 'Tambah Customer' }}</x-slot:header>
    <x-slot:title>{{ isset($customer) ? 'Edit' : 'Tambah' }} Customer</x-slot:title>
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ isset($customer) ? route('customers.update', $customer) : route('customers.store') }}" method="POST">
                @csrf
                @if(isset($customer)) @method('PUT') @endif
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Customer *</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('phone') border-red-500 @enderror">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('email') border-red-500 @enderror">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('address', $customer->address ?? '') }}</textarea>
                    </div>
                    @if(isset($customer))
                    <label class="flex items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"><span class="text-sm text-gray-700">Aktif</span></label>
                    @endif
                </div>
                <div class="flex items-center gap-3 mt-6 pt-4 border-t">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">{{ isset($customer) ? 'Update' : 'Simpan' }}</button>
                    <a href="{{ route('customers.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>