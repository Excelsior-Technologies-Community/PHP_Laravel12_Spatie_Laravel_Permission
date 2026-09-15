<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Role: {{ ucfirst($role->name) }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST"
                      action="{{ route('admin.roles.update', $role) }}">

                    @csrf
                    @method('PUT')

                    <div>

                        <x-input-label
                            for="name"
                            value="Role Name"
                        />

                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('name', $role->name)"
                            required
                        />

                        <x-input-error
                            :messages="$errors->get('name')"
                            class="mt-2"
                        />

                    </div>

                    <div class="mt-6">

                        <h3 class="font-semibold text-gray-800 mb-4">
                            Manage Permissions
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                            @foreach($permissions as $permission)

                                <label class="flex items-center gap-3 p-3 border rounded-md hover:bg-gray-50">

                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->name }}"
                                        class="rounded border-gray-300 text-indigo-600"

                                        {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                    >

                                    <span>
                                        {{ $permission->name }}
                                    </span>

                                </label>

                            @endforeach

                        </div>

                    </div>

                    <div class="mt-6 flex gap-3">

                        <x-primary-button>
                            Update Role
                        </x-primary-button>

                        <a href="{{ route('admin.roles.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>