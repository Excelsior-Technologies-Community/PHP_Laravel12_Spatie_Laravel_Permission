<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <span>🛡️</span> Role Management
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Manage system security roles, permission bindings, role cloning, and JSON schema backup.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.schema.export-json') }}" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold uppercase tracking-wider rounded-lg shadow transition flex items-center gap-1.5" title="Export RBAC to JSON">
                    <span>📥</span> Export JSON
                </a>

                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="px-3 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold uppercase tracking-wider rounded-lg shadow transition flex items-center gap-1.5" title="Import RBAC from JSON">
                    <span>📤</span> Import JSON
                </button>

                <a href="{{ route('admin.roles.create') }}" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold uppercase tracking-wider rounded-lg shadow transition flex items-center gap-1.5">
                    <span>+</span> Create Role
                </a>

                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 bg-gray-700 hover:bg-gray-800 text-white text-xs font-semibold uppercase tracking-wider rounded-lg transition">
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <span class="text-lg">✅</span> {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <span class="text-lg">⚠️</span> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-xl p-6 border border-gray-100">

                {{-- Search / Filter --}}
                <form method="GET" action="{{ route('admin.roles.index') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="search" value="Search Role" />
                            <x-text-input id="search" name="search" type="text" value="{{ $search }}" placeholder="Search role name..." class="mt-1 block w-full text-sm" />
                        </div>

                        <div>
                            <x-input-label for="sort" value="Sort By" />
                            <select id="sort" name="sort" class="mt-1 block w-full text-sm border-gray-300 rounded-lg shadow-sm">
                                <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Role Name</option>
                                <option value="created_at" {{ $sort === 'created_at' ? 'selected' : '' }}>Created Date</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="direction" value="Order" />
                            <select id="direction" name="direction" class="mt-1 block w-full text-sm border-gray-300 rounded-lg shadow-sm">
                                <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>Ascending</option>
                                <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>Descending</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <x-primary-button class="h-10 text-xs">
                                Apply
                            </x-primary-button>
                            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold uppercase tracking-wider hover:bg-gray-300 flex items-center h-10 transition">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Roles Table --}}
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Role Name</th>
                                <th class="px-6 py-3.5 text-left">Assigned Users</th>
                                <th class="px-6 py-3.5 text-left">Permissions Count</th>
                                <th class="px-6 py-3.5 text-left">Created At</th>
                                <th class="px-6 py-3.5 text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($roles as $role)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-base">🛡️</span>
                                            <span class="font-bold text-gray-900">{{ ucfirst($role->name) }}</span>
                                            @if($role->name === 'admin')
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold rounded bg-amber-100 text-amber-800 uppercase">System</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            👥 {{ $role->users_count }} users
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                            🔑 {{ $role->permissions->count() }} abilities
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $role->created_at->format('d M Y') }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-end space-x-1">
                                        {{-- 1. Clone Role --}}
                                        <button type="button" onclick="openCloneModal('{{ $role->id }}', '{{ $role->name }}', '{{ route('admin.roles.clone', $role) }}')" class="inline-flex items-center px-2.5 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 rounded text-xs font-semibold transition" title="Clone this role and its permissions">
                                            📋 Clone
                                        </button>

                                        {{-- 2. Edit --}}
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-xs font-semibold transition">
                                            ✏️ Edit
                                        </a>

                                        {{-- 3. Delete --}}
                                        @if($role->name !== 'admin')
                                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete role {{ $role->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded text-xs font-semibold transition">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No roles found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Clone Role Modal -->
    <div id="cloneModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                    <span>📋</span> Clone Role
                </h3>
                <button type="button" onclick="document.getElementById('cloneModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
            </div>
            <form id="cloneForm" method="POST" action="">
                @csrf
                <p class="text-sm text-gray-600 mb-4">
                    Creating a clone of role <strong id="cloneOriginalName" class="text-indigo-600"></strong>. All assigned permissions will be copied to the new role instantly.
                </p>
                <div class="mb-4">
                    <x-input-label for="new_name" value="New Role Name" class="font-bold text-gray-700" />
                    <x-text-input id="new_name" name="new_name" type="text" class="mt-1 block w-full text-sm" required />
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('cloneModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-semibold uppercase tracking-wider transition">Cancel</button>
                    <x-primary-button class="text-xs">Clone Role</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import JSON Modal -->
    <div id="importModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                    <span>📤</span> Import RBAC JSON Schema
                </h3>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
            </div>
            <form method="POST" action="{{ route('admin.schema.import-json') }}" enctype="multipart/form-data">
                @csrf
                <p class="text-xs text-gray-500 mb-4">
                    Upload an exported RBAC JSON file. Roles and permissions will be created and bound automatically.
                </p>
                <div class="mb-4">
                    <input type="file" name="json_file" accept=".json,.txt" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-semibold uppercase tracking-wider transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-xs font-semibold uppercase tracking-wider shadow transition">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCloneModal(id, name, actionUrl) {
            document.getElementById('cloneOriginalName').textContent = name;
            document.getElementById('new_name').value = name + '_copy';
            document.getElementById('cloneForm').action = actionUrl;
            document.getElementById('cloneModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>