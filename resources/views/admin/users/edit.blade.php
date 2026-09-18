<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>✏️</span> Edit User Roles & Permissions
            </h2>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold uppercase tracking-wider rounded-lg transition">
                ← Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-xl p-6 border border-gray-100">
                <!-- User Profile Header -->
                <div class="flex items-center space-x-4 pb-6 border-b border-gray-100 mb-6">
                    <div class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- 1. Primary Role Selection -->
                    <div>
                        <x-input-label for="role" value="Primary Role" class="font-bold text-gray-700" />
                        <select id="role" name="role" class="mt-2 block w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    🛡️ {{ ucfirst($role->name) }} ({{ $role->permissions->count() }} permissions)
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <!-- 2. Time-Bound Access / Role Expiry -->
                    <div class="p-4 bg-amber-50/60 rounded-xl border border-amber-200/80">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg">⏱️</span>
                            <x-input-label for="expiry_option" value="Time-Bound Role Access (Expiry Duration)" class="font-bold text-amber-900" />
                        </div>
                        <p class="text-xs text-amber-700 mb-3">
                            Set an optional expiration limit. When expired, the system will automatically revoke the assigned role.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <select id="expiry_option" name="expiry_option" class="block w-full text-sm border-amber-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white" onchange="toggleCustomDate(this.value)">
                                    <option value="none" {{ !$currentExpiry ? 'selected' : '' }}>♾️ Permanent Access (No Expiry)</option>
                                    <option value="1_hour">⚡ 1 Hour (Temporary Test)</option>
                                    <option value="24_hours">📅 24 Hours (1 Day Access)</option>
                                    <option value="7_days">🗓️ 7 Days (1 Week Access)</option>
                                    <option value="30_days">📆 30 Days (1 Month Access)</option>
                                    <option value="custom" {{ $currentExpiry ? 'selected' : '' }}>🎯 Custom Expiration Date/Time</option>
                                </select>
                            </div>

                            <div id="customDateWrapper" class="{{ $currentExpiry ? 'block' : 'hidden' }}">
                                <input type="datetime-local" id="custom_expires_at" name="custom_expires_at" value="{{ $currentExpiry ? $currentExpiry->format('Y-m-d\TH:i') : '' }}" class="block w-full text-sm border-amber-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                            </div>
                        </div>

                        @if($currentExpiry)
                            <div class="mt-2 text-xs font-semibold text-amber-800">
                                Current Expiry: {{ $currentExpiry->format('d M Y, H:i') }} ({{ $currentExpiry->diffForHumans() }})
                            </div>
                        @endif
                    </div>

                    <!-- 3. Direct Permissions Selection -->
                    <div>
                        <x-input-label value="Direct User Permissions (In addition to Role)" class="font-bold text-gray-700 mb-2" />
                        <p class="text-xs text-gray-500 mb-4">
                            Direct permissions are granted specifically to this user regardless of their role.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200 max-h-72 overflow-y-auto">
                            @foreach($permissions as $permission)
                                <label class="flex items-center space-x-2 text-sm text-gray-700 hover:text-indigo-600 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ $user->hasDirectPermission($permission->name) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="font-mono text-xs">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex gap-3">
                        <x-primary-button class="h-10 text-xs">
                            💾 Save Changes
                        </x-primary-button>

                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold uppercase tracking-wider hover:bg-gray-300 flex items-center h-10 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCustomDate(val) {
            const wrapper = document.getElementById('customDateWrapper');
            if (val === 'custom') {
                wrapper.classList.remove('hidden');
                wrapper.classList.add('block');
            } else {
                wrapper.classList.remove('block');
                wrapper.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>