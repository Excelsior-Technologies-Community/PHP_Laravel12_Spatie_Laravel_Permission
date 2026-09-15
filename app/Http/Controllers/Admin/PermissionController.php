<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display all permissions.
     */
    public function index(): View
    {
        $permissions = Permission::withCount('roles')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.permissions.index', compact('permissions'));
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
     * Delete a permission.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        if ($permission->roles()->exists()) {
            return back()->with(
                'error',
                'This permission cannot be deleted because it is assigned to a role.'
            );
        }

        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}