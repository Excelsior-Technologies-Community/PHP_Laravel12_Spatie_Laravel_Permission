<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Permission
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST"
                      action="{{ route('admin.permissions.store') }}">

                    @csrf

                    <div>

                        <x-input-label
                            for="name"
                            value="Permission Name"
                        />

                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Example: reports.view"
                            :value="old('name')"
                            required
                        />

                        <p class="mt-2 text-sm text-gray-500">
                            Use a descriptive permission name such as
                            reports.view or reports.export.
                        </p>

                        <x-input-error
                            :messages="$errors->get('name')"
                            class="mt-2"
                        />

                    </div>

                    <div class="mt-6 flex gap-3">

                        <x-primary-button>
                            Create Permission
                        </x-primary-button>

                        <a href="{{ route('admin.permissions.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>