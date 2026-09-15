<x-app-layout>

    <x-slot name="header">

        <div class="flex justify-between items-center">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Role Management
            </h2>

            @can('roles.create')

                <a href="{{ route('admin.roles.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    + Create Role
                </a>

            @endcan

        </div>

    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))

                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>

            @endif

            @if(session('error'))

                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md">
                    {{ session('error') }}
                </div>

            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Role
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Users
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Permissions
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($roles as $role)

                                <tr>

                                    <td class="px-6 py-4 font-semibold">
                                        {{ ucfirst($role->name) }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ $role->users_count }}
                                    </td>

                                    <td class="px-6 py-4">

                                        @forelse($role->permissions as $permission)

                                            <span class="inline-flex px-2 py-1 mr-1 mb-1 text-xs rounded bg-green-100 text-green-800">
                                                {{ $permission->name }}
                                            </span>

                                        @empty

                                            <span class="text-gray-500">
                                                No permissions
                                            </span>

                                        @endforelse

                                    </td>

                                    <td class="px-6 py-4">

                                        @can('roles.edit')

                                            <a href="{{ route('admin.roles.edit', $role) }}"
                                               class="text-indigo-600 hover:text-indigo-900 mr-4">
                                                Edit
                                            </a>

                                        @endcan

                                        @can('roles.delete')

                                            @if($role->name !== 'admin')

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.roles.destroy', $role) }}"
                                                    class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this role?');">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="text-red-600 hover:text-red-900">
                                                        Delete
                                                    </button>

                                                </form>

                                            @endif

                                        @endcan

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4"
                                        class="px-6 py-6 text-center text-gray-500">

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

</x-app-layout>