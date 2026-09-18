<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <span class="text-indigo-600">🧬</span> Effective Permissions Analyzer
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Deep inspection of effective, inherited, and direct permissions for <strong>{{ $user->name }}</strong> ({{ $user->email }}).
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold uppercase tracking-wider rounded-lg shadow transition">
                    ✏️ Edit Roles & Permissions
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold uppercase tracking-wider rounded-lg transition">
                    ← Back to Users
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- User Overview & Expiry Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                @forelse($userRoles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        🛡️ {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        No Roles Assigned
                                    </span>
                                @endforelse

                                @if($user->role_expiry)
                                    @if($user->role_expiry->isPast())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                            ⚠️ Role Expired on {{ $user->role_expiry->format('d M Y H:i') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            ⏱️ Access Expires {{ $user->role_expiry->diffForHumans() }} ({{ $user->role_expiry->format('d M Y') }})
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        ♾️ Permanent Access
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Impersonate Shortcut -->
                    <div>
                        <form method="POST" action="{{ route('admin.impersonate', $user) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow transition">
                                🎭 Login As {{ $user->name }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- KPI Metric Badges -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm text-center">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total System</span>
                    <span class="text-2xl font-black text-gray-800">{{ $allPermissions->count() }}</span>
                </div>
                <div class="bg-emerald-50 p-5 rounded-xl border border-emerald-100 shadow-sm text-center">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Effective Allowed</span>
                    <span class="text-2xl font-black text-emerald-700">{{ $totalAllowed }}</span>
                </div>
                <div class="bg-indigo-50 p-5 rounded-xl border border-indigo-100 shadow-sm text-center">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider block">Via Roles</span>
                    <span class="text-2xl font-black text-indigo-700">{{ $totalInherited }}</span>
                </div>
                <div class="bg-blue-50 p-5 rounded-xl border border-blue-100 shadow-sm text-center">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block">Direct Assigned</span>
                    <span class="text-2xl font-black text-blue-700">{{ $totalDirect }}</span>
                </div>
                <div class="bg-red-50 p-5 rounded-xl border border-red-100 shadow-sm text-center">
                    <span class="text-xs font-bold text-red-600 uppercase tracking-wider block">Denied / Missing</span>
                    <span class="text-2xl font-black text-red-700">{{ $totalDenied }}</span>
                </div>
            </div>

            <!-- Real-time Permission Check Simulator -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                    <span>⚡</span> Interactive Permission Check Simulator
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                    Type or select any permission to immediately test if <strong>{{ $user->name }}</strong> has access to execute that ability.
                </p>

                <div class="flex flex-wrap gap-3 items-center">
                    <div class="flex-grow max-w-md">
                        <input type="text" id="simPermissionInput" list="permissionsList" placeholder="e.g. users.edit, orders.create, articles.delete" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <datalist id="permissionsList">
                            @foreach($allPermissions as $perm)
                                <option value="{{ $perm->name }}">
                            @endforeach
                        </datalist>
                    </div>
                    <button type="button" id="btnTestPermission" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow transition">
                        🔍 Test Permission
                    </button>
                </div>

                <!-- Simulation Result Box -->
                <div id="simResultBox" class="mt-4 hidden p-4 rounded-xl border text-sm"></div>
            </div>

            <!-- Permission Analysis Breakdown by Module -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                        <span>📋</span> Detailed Module Permission Breakdown
                    </h3>
                    <div class="flex items-center space-x-4 text-xs font-medium">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Inherited via Role</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Direct Permission</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Denied</span>
                    </div>
                </div>

                <div class="p-6 space-y-8">
                    @foreach($analyzedPermissions as $module => $perms)
                        <div>
                            <div class="flex items-center justify-between pb-2 border-b border-gray-200 mb-3">
                                <h4 class="font-bold text-base text-gray-800 flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-gray-100 rounded text-gray-600 text-xs uppercase font-mono">{{ $module }}</span>
                                    <span>Module Permissions</span>
                                </h4>
                                <span class="text-xs text-gray-400">{{ count($perms) }} abilities</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($perms as $p)
                                    <div class="p-3.5 rounded-xl border transition {{ $p['is_allowed'] ? 'bg-emerald-50/40 border-emerald-200' : 'bg-gray-50/60 border-gray-200 opacity-70' }}">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="font-mono text-xs font-bold {{ $p['is_allowed'] ? 'text-gray-900' : 'text-gray-500' }}">
                                                {{ $p['name'] }}
                                            </span>
                                            @if($p['is_allowed'])
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                    ✓ ALLOWED
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-800">
                                                    ✕ DENIED
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap gap-1.5 mt-2">
                                            @if($p['is_inherited'])
                                                @foreach($p['inherited_roles'] as $rName)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-100 text-indigo-700" title="Granted via Role">
                                                        🛡️ via {{ $rName }}
                                                    </span>
                                                @endforeach
                                            @endif

                                            @if($p['is_direct'])
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-100 text-blue-700" title="Directly Assigned to User">
                                                    👤 Direct
                                                </span>
                                            @endif

                                            @if(!$p['is_allowed'])
                                                <span class="text-xs text-gray-400 italic">No access path</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- Simulator AJAX Script -->
    <script>
        document.getElementById('btnTestPermission').addEventListener('click', async function() {
            const input = document.getElementById('simPermissionInput');
            const permName = input.value.trim();
            const resultBox = document.getElementById('simResultBox');

            if (!permName) {
                alert('Please enter or select a permission name to test.');
                return;
            }

            resultBox.className = 'mt-4 p-4 rounded-xl border text-sm bg-gray-50 text-gray-600 block';
            resultBox.innerHTML = 'Testing permission...';

            try {
                const response = await fetch("{{ route('admin.users.test-permission', $user) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ permission: permName })
                });

                const data = await response.json();
                if (data.can) {
                    resultBox.className = 'mt-4 p-4 rounded-xl border text-sm bg-emerald-50 border-emerald-200 text-emerald-900 block';
                    resultBox.innerHTML = `<div class="font-bold flex items-center gap-2"><span class="text-lg">✅</span> Access GRANTED for "${data.permission}"</div><div class="mt-1 text-xs opacity-90">${data.reason}</div>`;
                } else {
                    resultBox.className = 'mt-4 p-4 rounded-xl border text-sm bg-red-50 border-red-200 text-red-900 block';
                    resultBox.innerHTML = `<div class="font-bold flex items-center gap-2"><span class="text-lg">❌</span> Access DENIED for "${data.permission}"</div><div class="mt-1 text-xs opacity-90">${data.reason}</div>`;
                }
            } catch (err) {
                resultBox.className = 'mt-4 p-4 rounded-xl border text-sm bg-red-50 border-red-200 text-red-900 block';
                resultBox.innerHTML = 'Error testing permission: ' + err.message;
            }
        });
    </script>
</x-app-layout>