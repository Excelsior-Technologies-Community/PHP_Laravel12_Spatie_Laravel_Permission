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
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $sort = $request->input('sort', 'name');

        $direction = $request->input('direction', 'asc');

        $perPage = (int) $request->input('per_page', 10);

        $allowedSorts = [
            'name',
            'created_at',
        ];

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
            ->withCount('roles')
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'name',
                    'like',
                    "%{$search}%"
                );
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view(
            'admin.permissions.index',
            compact(
                'permissions',
                'search',
                'sort',
                'direction',
                'perPage'
            )
        );
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
            ->with(
                'success',
                'Permission created successfully.'
            );
    }

    /**
     * Delete a permission.
     */
    public function destroy(
        Permission $permission
    ): RedirectResponse {
        if ($permission->roles()->exists()) {
            return back()->with(
                'error',
                'This permission cannot be deleted because it is assigned to a role.'
            );
        }

        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with(
                'success',
                'Permission deleted successfully.'
            );
    }

    /**
     * Bulk delete permissions.
     */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'permissions' => [
                'required',
                'array',
                'min:1',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        $deleted = 0;
        $skipped = 0;

        $permissions = Permission::whereIn(
            'id',
            $validated['permissions']
        )->get();

        foreach ($permissions as $permission) {

            // Never delete permissions assigned to roles.
            if ($permission->roles()->exists()) {
                $skipped++;
                continue;
            }

            $permission->delete();

            $deleted++;
        }

        $message =
            "{$deleted} permission(s) deleted successfully.";

        if ($skipped > 0) {
            $message .=
                " {$skipped} permission(s) skipped because they are assigned to roles.";
        }

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', $message);
    }
}