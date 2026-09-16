<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permission Management
            </h2>

            <div class="flex gap-2">

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800"
                >
                    Admin Dashboard
                </a>

                @can('permissions.create')

                    <a
                        href="{{ route('admin.permissions.create') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        + Create Permission
                    </a>

                @endcan

            </div>

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


                {{-- Search / Sort / Per Page --}}

                <form
                    method="GET"
                    action="{{ route('admin.permissions.index') }}"
                    class="mb-6"
                >

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">


                        <div>

                            <label
                                for="search"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Search Permission
                            </label>

                            <input
                                id="search"
                                name="search"
                                type="text"
                                value="{{ $search }}"
                                placeholder="Search permission..."
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            >

                        </div>


                        <div>

                            <label
                                for="sort"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Sort By
                            </label>

                            <select
                                id="sort"
                                name="sort"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            >

                                <option
                                    value="name"
                                    {{ $sort === 'name' ? 'selected' : '' }}
                                >
                                    Name
                                </option>

                                <option
                                    value="created_at"
                                    {{ $sort === 'created_at' ? 'selected' : '' }}
                                >
                                    Created Date
                                </option>

                            </select>

                        </div>


                        <div>

                            <label
                                for="direction"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Direction
                            </label>

                            <select
                                id="direction"
                                name="direction"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            >

                                <option
                                    value="asc"
                                    {{ $direction === 'asc' ? 'selected' : '' }}
                                >
                                    Ascending
                                </option>

                                <option
                                    value="desc"
                                    {{ $direction === 'desc' ? 'selected' : '' }}
                                >
                                    Descending
                                </option>

                            </select>

                        </div>


                        <div>

                            <label
                                for="per_page"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Per Page
                            </label>

                            <select
                                id="per_page"
                                name="per_page"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            >

                                @foreach([5, 10, 25, 50] as $size)

                                    <option
                                        value="{{ $size }}"
                                        {{ $perPage == $size ? 'selected' : '' }}
                                    >
                                        {{ $size }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    <div class="flex gap-2 mt-4">

                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                        >
                            Apply
                        </button>

                        <a
                            href="{{ route('admin.permissions.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                        >
                            Reset
                        </a>

                    </div>

                </form>


                @can('permissions.delete')

                    <form
                        method="POST"
                        action="{{ route('admin.permissions.bulk-delete') }}"
                        onsubmit="return confirm('Are you sure you want to delete the selected permissions? Permissions assigned to roles will be skipped.');"
                    >

                        @csrf


                        <div class="flex items-center justify-between mb-4">

                            <span
                                id="selectedPermissionCount"
                                class="text-sm text-gray-600"
                            >
                                0 selected
                            </span>


                            <button
                                type="submit"
                                id="bulkPermissionDeleteButton"
                                disabled
                                class="px-4 py-2 bg-red-600 text-white rounded-md disabled:opacity-40 disabled:cursor-not-allowed hover:bg-red-700"
                            >
                                Delete Selected
                            </button>

                        </div>


                        <div class="overflow-x-auto">

                            <table class="min-w-full divide-y divide-gray-200">

                                <thead class="bg-gray-50">

                                    <tr>

                                        <th class="px-6 py-3">

                                            <input
                                                type="checkbox"
                                                id="selectAllPermissions"
                                                class="rounded border-gray-300"
                                            >

                                        </th>

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

                                            <td class="px-6 py-4">

                                                @if($permission->roles_count === 0)

                                                    <input
                                                        type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission->id }}"
                                                        class="permission-checkbox rounded border-gray-300"
                                                    >

                                                @else

                                                    <span class="text-xs text-gray-400">
                                                        —
                                                    </span>

                                                @endif

                                            </td>


                                            <td class="px-6 py-4 font-medium">

                                                {{ $permission->name }}

                                            </td>


                                            <td class="px-6 py-4">

                                                @if($permission->roles_count > 0)

                                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">

                                                        {{ $permission->roles_count }}

                                                        {{ Str::plural('Role', $permission->roles_count) }}

                                                    </span>

                                                @else

                                                    <span class="text-gray-500">
                                                        Not assigned
                                                    </span>

                                                @endif

                                            </td>


                                            <td class="px-6 py-4">

                                                @if($permission->roles_count === 0)

                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.permissions.destroy', $permission) }}"
                                                        class="inline"
                                                        onsubmit="return confirm('Delete this permission?');"
                                                    >

                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="text-red-600 hover:text-red-900"
                                                        >
                                                            Delete
                                                        </button>

                                                    </form>

                                                @else

                                                    <span class="text-gray-400 text-sm">
                                                        Assigned to role
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td
                                                colspan="4"
                                                class="px-6 py-6 text-center text-gray-500"
                                            >
                                                No permissions found.
                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </form>

                @else

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

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($permissions as $permission)

                                    <tr>

                                        <td class="px-6 py-4">
                                            {{ $permission->name }}
                                        </td>

                                        <td class="px-6 py-4">
                                            {{ $permission->roles_count }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="2"
                                            class="px-6 py-6 text-center text-gray-500"
                                        >
                                            No permissions found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                @endcan


                <div class="mt-6">
                    {{ $permissions->links() }}
                </div>


            </div>

        </div>

    </div>


    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const selectAll =
                document.getElementById(
                    'selectAllPermissions'
                );

            const checkboxes =
                document.querySelectorAll(
                    '.permission-checkbox'
                );

            const deleteButton =
                document.getElementById(
                    'bulkPermissionDeleteButton'
                );

            const selectedCount =
                document.getElementById(
                    'selectedPermissionCount'
                );


            function updateSelection() {

                const selected =
                    document.querySelectorAll(
                        '.permission-checkbox:checked'
                    ).length;

                selectedCount.textContent =
                    selected + ' selected';

                deleteButton.disabled =
                    selected === 0;

                if (checkboxes.length > 0) {

                    selectAll.checked =
                        selected === checkboxes.length;

                }

            }


            if (selectAll) {

                selectAll.addEventListener(
                    'change',
                    function () {

                        checkboxes.forEach(
                            checkbox => {
                                checkbox.checked =
                                    selectAll.checked;
                            }
                        );

                        updateSelection();

                    }
                );

            }


            checkboxes.forEach(
                checkbox => {

                    checkbox.addEventListener(
                        'change',
                        updateSelection
                    );

                }
            );


            updateSelection();

        });

    </script>

</x-app-layout>