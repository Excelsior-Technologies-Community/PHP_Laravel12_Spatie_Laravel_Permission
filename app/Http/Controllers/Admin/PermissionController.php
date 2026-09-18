<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display all permissions with search, sorting and pagination.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $perPage = (int) $request->input('per_page', 10);

        $allowedSorts = ['name', 'created_at'];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        if (!in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = 10;
        }

        $permissions = Permission::query()
            ->with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.permissions.index', compact(
            'permissions',
            'search',
            'sort',
            'direction',
            'perPage'
        ));
    }

    /**
     * Show create permission form.
     */
    public function create(): View
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a new permission.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name',
            ],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * 1-Click CRUD Permission Generator for a Module.
     */
    public function generateCrud(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'module' => ['required', 'string', 'max:50'],
            'actions' => ['nullable', 'array'],
        ]);

        $module = Str::slug($validated['module'], '.');
        $actions = $request->input('actions', ['view', 'create', 'edit', 'delete']);

        $created = 0;
        foreach ($actions as $action) {
            $permName = "{$module}.{$action}";
            if (!Permission::where('name', $permName)->exists()) {
                Permission::create([
                    'name' => $permName,
                    'guard_name' => 'web',
                ]);
                $created++;
            }
        }

        // Automatically assign new permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(Permission::all());
        }

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', "CRUD permissions for module '{$module}' generated ({$created} new permissions created and granted to Admin).");
    }

    /**
     * Export all roles and permissions to JSON format.
     */
    public function exportJson()
    {
        $roles = Role::with('permissions')->get()->map(function ($role) {
            return [
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ];
        });

        $permissions = Permission::all()->map(function ($perm) {
            return [
                'name' => $perm->name,
                'guard_name' => $perm->guard_name,
            ];
        });

        $exportData = [
            'exported_at' => now()->toIso8601String(),
            'total_roles' => $roles->count(),
            'total_permissions' => $permissions->count(),
            'roles' => $roles,
            'permissions' => $permissions,
        ];

        $json = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="rbac_roles_permissions_' . date('Y_m_d_His') . '.json"',
        ]);
    }

    /**
     * Import roles and permissions from uploaded JSON file.
     */
    public function importJson(Request $request): RedirectResponse
    {
        $request->validate([
            'json_file' => ['required', 'file', 'mimes:json,txt', 'max:5120'],
        ]);

        $content = file_get_contents($request->file('json_file')->getRealPath());
        $data = json_decode($content, true);

        if (!$data || !isset($data['permissions']) || !isset($data['roles'])) {
            return back()->with('error', 'Invalid RBAC JSON schema format.');
        }

        // 1. Import Permissions
        $newPerms = 0;
        foreach ($data['permissions'] as $p) {
            if (!Permission::where('name', $p['name'])->exists()) {
                Permission::create([
                    'name' => $p['name'],
                    'guard_name' => $p['guard_name'] ?? 'web',
                ]);
                $newPerms++;
            }
        }

        // 2. Import Roles & Sync Permissions
        $newRoles = 0;
        foreach ($data['roles'] as $r) {
            $role = Role::firstOrCreate(
                ['name' => $r['name']],
                ['guard_name' => $r['guard_name'] ?? 'web']
            );
            if ($role->wasRecentlyCreated) {
                $newRoles++;
            }

            if (!empty($r['permissions'])) {
                $existingPerms = Permission::whereIn('name', $r['permissions'])->pluck('name')->toArray();
                $role->syncPermissions($existingPerms);
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "RBAC Schema successfully imported ({$newRoles} new roles, {$newPerms} new permissions).");
    }

    /**
     * Delete a permission.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Bulk delete permissions.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:permissions,id'],
        ]);

        Permission::whereIn('id', $validated['ids'])->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Selected permissions deleted successfully.');
    }
}