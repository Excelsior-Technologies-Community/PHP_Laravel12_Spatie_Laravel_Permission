<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>

                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Authorization Dashboard
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Roles, permissions and user authorization overview.
                </p>

            </div>

            <div class="flex flex-wrap gap-2">

                @can('users.view')
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800"
                    >
                        Users
                    </a>
                @endcan

                @can('roles.view')
                    <a
                        href="{{ route('admin.roles.index') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Roles
                    </a>
                @endcan

                @can('permissions.view')
                    <a
                        href="{{ route('admin.permissions.index') }}"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                    >
                        Permissions
                    </a>
                @endcan

            </div>

        </div>

    </x-slot>


    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            {{-- Statistics --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Total Users
                    </p>

                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $totalUsers }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Total Roles
                    </p>

                    <p class="text-3xl font-bold text-indigo-600 mt-2">
                        {{ $totalRoles }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Permissions
                    </p>

                    <p class="text-3xl font-bold text-green-600 mt-2">
                        {{ $totalPermissions }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Admin Users
                    </p>

                    <p class="text-3xl font-bold text-purple-600 mt-2">
                        {{ $adminUsers }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Users Without Role
                    </p>

                    <p class="text-3xl font-bold text-red-600 mt-2">
                        {{ $usersWithoutRole }}
                    </p>

                </div>

            </div>


            {{-- Role Statistics --}}

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <div class="flex justify-between items-center mb-5">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Role Statistics
                        </h3>

                        @can('roles.view')
                            <a
                                href="{{ route('admin.roles.index') }}"
                                class="text-indigo-600 hover:text-indigo-800 text-sm"
                            >
                                Manage Roles
                            </a>
                        @endcan

                    </div>


                    <div class="space-y-4">

                        @forelse($rolesWithUsers as $role)

                            <div class="flex items-center justify-between border-b pb-3">

                                <div>

                                    <p class="font-medium text-gray-800">
                                        {{ ucfirst($role->name) }}
                                    </p>

                                    <p class="text-sm text-gray-500">
                                        {{ $role->permissions->count() }} permissions
                                    </p>

                                </div>

                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                                    {{ $role->users_count }} users
                                </span>

                            </div>

                        @empty

                            <p class="text-gray-500">
                                No roles found.
                            </p>

                        @endforelse

                    </div>

                </div>


                {{-- Permission Statistics --}}

                <div class="bg-white shadow-sm rounded-lg p-6">

                    <div class="flex justify-between items-center mb-5">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Permission Usage
                        </h3>

                        @can('permissions.view')
                            <a
                                href="{{ route('admin.permissions.index') }}"
                                class="text-green-600 hover:text-green-800 text-sm"
                            >
                                Manage Permissions
                            </a>
                        @endcan

                    </div>


                    <div class="space-y-4">

                        @forelse($permissionsWithRoles as $permission)

                            <div class="flex items-center justify-between border-b pb-3">

                                <div>

                                    <p class="font-medium text-gray-800">
                                        {{ $permission->name }}
                                    </p>

                                </div>

                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                    {{ $permission->roles_count }} roles
                                </span>

                            </div>

                        @empty

                            <p class="text-gray-500">
                                No permissions found.
                            </p>

                        @endforelse

                    </div>

                </div>

            </div>


        </div>

    </div>

</x-app-layout>