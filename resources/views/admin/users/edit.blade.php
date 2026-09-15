<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Change User Role
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <div class="mb-6">

                    <h3 class="text-xl font-semibold text-gray-800">
                        {{ $user->name }}
                    </h3>

                    <p class="text-gray-500">
                        {{ $user->email }}
                    </p>

                </div>

                <form method="POST"
                      action="{{ route('admin.users.update', $user) }}">

                    @csrf
                    @method('PUT')

                    <div>

                        <x-input-label
                            for="role"
                            value="Select Role"
                        />

                        <select
                            id="role"
                            name="role"
                            class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">

                            @foreach($roles as $role)

                                <option
                                    value="{{ $role->name }}"
                                    {{ $user->hasRole($role->name) ? 'selected' : '' }}>

                                    {{ ucfirst($role->name) }}

                                </option>

                            @endforeach

                        </select>

                        <x-input-error
                            :messages="$errors->get('role')"
                            class="mt-2"
                        />

                    </div>

                    <div class="mt-6 flex gap-3">

                        <x-primary-button>
                            Update Role
                        </x-primary-button>

                        <a href="{{ route('admin.users.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>