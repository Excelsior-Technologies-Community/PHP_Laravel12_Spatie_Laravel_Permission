<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display all roles with search, sorting and pagination.
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

        $roles = Role::query()
            ->with('permissions')
            ->withCount('users')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.roles.index', compact(
            'roles',
            'search',
            'sort',
            'direction',
            'perPage'
        ));
    }

    /**
     * Show create role form.
     */
    public function create(): View
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a new role.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'exists:permissions,name',
            ],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show edit role form.
     */
    public function edit(Role $role): View
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact(
            'role',
            'permissions',
            'rolePermissions'
        ));
    }

    /**
     * Update a role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name,' . $role->id,
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'exists:permissions,name',
            ],
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions(
            $validated['permissions'] ?? []
        );

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Clone an existing role with all its permissions.
     */
    public function clone(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'new_name' => ['required', 'string', 'max:255', 'unique:roles,name'],
        ]);

        $newRole = Role::create([
            'name' => $validated['new_name'],
            'guard_name' => $role->guard_name ?? 'web',
        ]);

        $permissions = $role->permissions->pluck('name')->toArray();
        if (!empty($permissions)) {
            $newRole->syncPermissions($permissions);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' successfully cloned as '{$newRole->name}' with " . count($permissions) . " permissions.");
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'admin') {
            return back()->with('error', 'The admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Bulk delete roles.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:roles,id'],
        ]);

        $roles = Role::whereIn('id', $validated['ids'])
            ->where('name', '!=', 'admin')
            ->get();

        foreach ($roles as $role) {
            $role->delete();
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Selected roles deleted successfully.');
    }
}