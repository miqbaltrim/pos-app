<x-layouts.admin>
    <x-slot:header>Role & Hak Akses</x-slot:header>
    <x-slot:title>Roles</x-slot:title>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Daftar Role</h3>
            @can('roles.create')
            <a href="{{ route('roles.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Tambah Role</a>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 bg-gray-50 border-b">
                        <th class="px-5 py-3 font-medium">Role</th>
                        <th class="px-5 py-3 font-medium text-center">Users</th>
                        <th class="px-5 py-3 font-medium">Permissions</th>
                        <th class="px-5 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ ucfirst($role->name) }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="bg-gray-100 px-2 py-0.5 rounded-full text-xs">{{ $role->users_count }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($role->permissions->take(5) as $perm)
                                <span class="bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded text-xs">{{ $perm->name }}</span>
                                @endforeach
                                @if($role->permissions->count() > 5)
                                <span class="text-gray-400 text-xs">+{{ $role->permissions->count() - 5 }} lainnya</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('roles.edit')
                                <a href="{{ route('roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>