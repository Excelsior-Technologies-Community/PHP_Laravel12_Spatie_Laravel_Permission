<x-app-layout>

    <x-slot name="header">

        <div class="flex justify-between items-center">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permission Management
            </h2>

            @can('permissions.create')

                <a href="{{ route('admin.permissions.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    + Create Permission
                </a>

            @endcan

        </div>

    </x-slot>

    <div class="py-12">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

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
                                    Permission
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Assigned Roles
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($permissions as $permission)

                                <tr>

                                    <td class="px-6 py-4 font-medium">
                                        {{ $permission->name }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ $permission->roles_count }}
                                    </td>

                                    <td class="px-6 py-4">

                                        @can('permissions.delete')

                                            @if($permission->roles_count === 0)

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.permissions.destroy', $permission) }}"
                                                    class="inline"
                                                    onsubmit="return confirm('Delete this permission?');">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="text-red-600 hover:text-red-900">
                                                        Delete
                                                    </button>

                                                </form>

                                            @else

                                                <span class="text-gray-400 text-sm">
                                                    Assigned to role
                                                </span>

                                            @endif

                                        @endcan

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="3"
                                        class="px-6 py-6 text-center text-gray-500">

                                        No permissions found.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-6">
                    {{ $permissions->links() }}
                </div>

            </div>

        </div>

    </div>

</x-app-layout>