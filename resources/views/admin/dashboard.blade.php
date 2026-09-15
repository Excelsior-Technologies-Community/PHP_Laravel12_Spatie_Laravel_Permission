<x-app-layout>

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>

    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))

                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>

            @endif

            <div class="mb-8">

                <h1 class="text-3xl font-bold text-gray-800">
                    Welcome Admin: {{ auth()->user()->name }}
                </h1>

                @role('admin')

                    <p class="mt-2 text-green-700 font-semibold">
                        You have full admin access.
                    </p>

                @endrole

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- User Management --}}

                @can('users.view')

                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <h3 class="text-xl font-bold text-gray-800">
                            User Management
                        </h3>

                        <p class="mt-2 text-gray-600">
                            View users, search users and manage user roles.
                        </p>

                        <a href="{{ route('admin.users.index') }}"
                           class="inline-block mt-5 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Manage Users
                        </a>

                    </div>

                @endcan

                {{-- Role Management --}}

                @can('roles.view')

                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <h3 class="text-xl font-bold text-gray-800">
                            Role Management
                        </h3>

                        <p class="mt-2 text-gray-600">
                            Create roles and assign permissions.
                        </p>

                        <a href="{{ route('admin.roles.index') }}"
                           class="inline-block mt-5 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Manage Roles
                        </a>

                    </div>

                @endcan

                {{-- Permission Management --}}

                @can('permissions.view')

                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <h3 class="text-xl font-bold text-gray-800">
                            Permission Management
                        </h3>

                        <p class="mt-2 text-gray-600">
                            Create and manage application permissions.
                        </p>

                        <a href="{{ route('admin.permissions.index') }}"
                           class="inline-block mt-5 px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                            Manage Permissions
                        </a>

                    </div>

                @endcan

            </div>

            {{-- Existing Permission Example --}}

            @can('edit orders')

                <div class="mt-8 bg-white shadow-sm rounded-lg p-6">

                    <h3 class="text-xl font-bold text-gray-800">
                        Order Management
                    </h3>

                    <p class="mt-2 text-gray-600">
                        You have permission to edit orders.
                    </p>

                    <a href="{{ route('orders.edit') }}"
                       class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded-md">
                        Manage Orders
                    </a>

                </div>

            @endcan

        </div>

    </div>

</x-app-layout>