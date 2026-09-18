<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <span>👥</span> User & Access Management
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Manage user roles, direct permissions, time-bound temporary access, and user impersonation.
                </p>
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="px-4 py-2 bg-gray-700 text-white text-xs font-semibold uppercase tracking-wider rounded-lg hover:bg-gray-800 shadow transition">
                ← Admin Dashboard
            </a>
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

                {{-- Search & Filter --}}
                <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="search" value="Search User" />
                            <x-text-input id="search" name="search" type="text" class="mt-1 block w-full text-sm" placeholder="Search by name or email..." :value="$search" />
                        </div>

                        <div>
                            <x-input-label for="role" value="Filter by Role" />
                            <select id="role" name="role" class="mt-1 block w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Roles</option>
                                @foreach($roles as $roleItem)
                                    <option value="{{ $roleItem->name }}" {{ $role === $roleItem->name ? 'selected' : '' }}>
                                        {{ ucfirst($roleItem->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <x-primary-button class="h-10 text-xs">
                                Search
                            </x-primary-button>
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold uppercase tracking-wider hover:bg-gray-300 flex items-center h-10 transition">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Users Table --}}
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5 text-left">User</th>
                                <th class="px-6 py-3.5 text-left">Role & Expiry</th>
                                <th class="px-6 py-3.5 text-left">Direct Permissions</th>
                                <th class="px-6 py-3.5 text-left">Registered</th>
                                <th class="px-6 py-3.5 text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1 items-start">
                                            @forelse($user->roles as $userRole)
                                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                    🛡️ {{ ucfirst($userRole->name) }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-400 italic">No Role</span>
                                            @endforelse

                                            @if($user->role_expiry)
                                                @if($user->role_expiry->isPast())
                                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-700">
                                                        ⚠️ Expired {{ $user->role_expiry->format('d M') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800" title="{{ $user->role_expiry->format('d M Y H:i') }}">
                                                        ⏱️ Expires {{ $user->role_expiry->diffForHumans() }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->permissions->count() > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                                👤 {{ $user->permissions->count() }} direct
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">0 direct</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-end space-x-2">
                                        {{-- 1. Impersonate ("Login As") --}}
                                        @if(auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('admin.impersonate', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded text-xs font-semibold shadow transition" title="Login as this user without password">
                                                    🎭 Login As
                                                </button>
                                            </form>
                                        @endif

                                        {{-- 2. Permissions Analyzer --}}
                                        <a href="{{ route('admin.users.analyzer', $user) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-xs font-semibold transition" title="Inspect effective permissions">
                                            🧬 Analyzer
                                        </a>

                                        {{-- 3. Edit Role & Direct Permissions --}}
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-semibold transition" title="Change role and permissions">
                                            ✏️ Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No users found matching your criteria.
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