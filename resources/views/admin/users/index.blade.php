<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Management
            </h2>

            <a href="{{ route('admin.dashboard') }}"
               class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800">
                Admin Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                {{-- Search & Filter --}}
                <form method="GET"
                      action="{{ route('admin.users.index') }}"
                      class="mb-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>
                            <x-input-label for="search" value="Search User" />

                            <x-text-input
                                id="search"
                                name="search"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Name or email"
                                :value="$search"
                            />
                        </div>

                        <div>
                            <x-input-label for="role" value="Filter by Role" />

                            <select
                                id="role"
                                name="role"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                                <option value="">All Roles</option>

                                @foreach($roles as $roleItem)
                                    <option value="{{ $roleItem->name }}"
                                        {{ $role === $roleItem->name ? 'selected' : '' }}>
                                        {{ ucfirst($roleItem->name) }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="flex items-end gap-2">

                            <x-primary-button>
                                Search
                            </x-primary-button>

                            <a href="{{ route('admin.users.index') }}"
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">
                                Reset
                            </a>

                        </div>

                    </div>

                </form>

                {{-- Users Table --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Name
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Email
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Role
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Registered
                                </th>

                                @can('users.edit')
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Action
                                    </th>
                                @endcan

                            </tr>

                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse($users as $user)

                                <tr>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $user->name }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $user->email }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @forelse($user->roles as $userRole)

                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($userRole->name) }}
                                            </span>

                                        @empty

                                            <span class="text-gray-500">
                                                No Role
                                            </span>

                                        @endforelse

                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>

                                    @can('users.edit')

                                        <td class="px-6 py-4 whitespace-nowrap">

                                            <a href="{{ route('admin.users.edit', $user) }}"
                                               class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                Change Role
                                            </a>

                                        </td>

                                    @endcan

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-6 text-center text-gray-500">
                                        No users found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>

            </div>

        </div>

    </div>

</x-app-layout>