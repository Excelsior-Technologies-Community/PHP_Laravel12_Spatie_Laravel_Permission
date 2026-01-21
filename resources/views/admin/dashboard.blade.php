<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded shadow">
                <h1 class="text-2xl font-bold mb-4">
                     Welcome Admin: {{ auth()->user()->name }}
                </h1>

                @role('admin')
                    <p class="text-green-700 font-semibold">
                        You have full admin access.
                    </p>
                @endrole

                <div class="mt-4">
                    <a href="/orders/edit"
                       class="px-4 py-2 bg-blue-600 text-white rounded">
                        Manage Orders
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
