@if(session()->has('impersonator_id'))
    <div class="bg-amber-600 text-white px-4 py-2.5 text-sm font-semibold shadow-md flex flex-wrap items-center justify-between sticky top-0 z-50">
        <div class="flex items-center space-x-2">
            <span class="text-base">🎭</span>
            <span>You are currently impersonating <strong class="underline decoration-white">{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }}). Experiencing the app with their assigned roles & permissions.</span>
        </div>
        <form method="POST" action="{{ route('impersonate.leave') }}" class="inline">
            @csrf
            <button type="submit" class="bg-white text-amber-900 hover:bg-amber-100 font-bold px-3 py-1 rounded shadow text-xs uppercase tracking-wider transition">
                ← Revert to Admin
            </button>
        </form>
    </div>
@endif

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @role('admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            Admin
                        </x-nav-link>
                    @endrole

                    @can('users.view')
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            Users
                        </x-nav-link>
                    @endcan

                    @can('roles.view')
                        <x-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                            Roles
                        </x-nav-link>
                    @endcan

                    @can('permissions.view')
                        <x-nav-link :href="route('admin.permissions.index')" :active="request()->routeIs('admin.permissions.*')">
                            Permissions
                        </x-nav-link>
                    @endcan

                    @can('edit orders')
                        <x-nav-link :href="route('orders.edit')" :active="request()->routeIs('orders.edit')">
                            Orders
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @role('admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Admin Dashboard
                </x-responsive-nav-link>
            @endrole

            @can('users.view')
                <x-responsive-nav-link :href="route('admin.users.index')">
                    Users
                </x-responsive-nav-link>
            @endcan

            @can('roles.view')
                <x-responsive-nav-link :href="route('admin.roles.index')">
                    Roles
                </x-responsive-nav-link>
            @endcan

            @can('permissions.view')
                <x-responsive-nav-link :href="route('admin.permissions.index')">
                    Permissions
                </x-responsive-nav-link>
            @endcan

            @can('edit orders')
                <x-responsive-nav-link :href="route('orders.edit')">
                    Orders
                </x-responsive-nav-link>
            @endcan
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profile
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>