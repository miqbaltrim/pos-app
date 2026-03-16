<x-layouts.admin>
    <x-slot:header>{{ isset($role) ? 'Edit Role' : 'Tambah Role' }}</x-slot:header>
    <x-slot:title>{{ isset($role) ? 'Edit' : 'Tambah' }} Role</x-slot:title>

    <div class="max-w-4xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form action="{{ isset($role) ? route('roles.update', $role) : route('roles.store') }}" method="POST">
                @csrf
                @if(isset($role)) @method('PUT') @endif

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role *</label>
                    <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required
                           {{ isset($role) && $role->name === 'super-admin' ? 'readonly' : '' }}
                           class="w-full max-w-sm border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Hak Akses *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($permissions as $group => $perms)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 text-sm mb-2 capitalize">{{ $group }}</h4>
                            @foreach($perms as $perm)
                            <label class="flex items-center gap-2 py-1">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                       {{ in_array($perm->name, old('permissions', $rolePermissions ?? [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-600">{{ $perm->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6 pt-4 border-t">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">{{ isset($role) ? 'Update' : 'Simpan' }}</button>
                    <a href="{{ route('roles.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>