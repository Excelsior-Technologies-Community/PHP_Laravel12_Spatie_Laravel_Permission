<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display users with search, role filtering, and expiration status.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $users = User::query()
            ->with(['roles', 'permissions'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query) use ($role) {
                $query->role($role);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact(
            'users',
            'roles',
            'search',
            'role'
        ));
    }

    /**
     * Show the role & permission assignment form.
     */
    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        // Get current role expiry if any
        $tableNames = config('permission.table_names');
        $rolePivot = DB::table($tableNames['model_has_roles'])
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->first();

        $currentExpiry = $rolePivot && $rolePivot->expires_at ? Carbon::parse($rolePivot->expires_at) : null;

        return view('admin.users.edit', compact(
            'user',
            'roles',
            'permissions',
            'currentExpiry'
        ));
    }

    /**
     * Update the user's role, direct permissions, and expiration.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'exists:roles,name'],
            'expiry_option' => ['nullable', 'string', 'in:none,1_hour,24_hours,7_days,30_days,custom'],
            'custom_expires_at' => ['nullable', 'date', 'after:now'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Prevent Admin From Removing Their Own Admin Role
        |--------------------------------------------------------------------------
        */
        if (
            auth()->id() === $user->id &&
            $user->hasRole('admin') &&
            $validated['role'] !== 'admin'
        ) {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        // Calculate Expiry Date
        $expiresAt = null;
        $option = $request->input('expiry_option', 'none');

        if ($option === '1_hour') {
            $expiresAt = now()->addHour();
        } elseif ($option === '24_hours') {
            $expiresAt = now()->addDay();
        } elseif ($option === '7_days') {
            $expiresAt = now()->addDays(7);
        } elseif ($option === '30_days') {
            $expiresAt = now()->addDays(30);
        } elseif ($option === 'custom' && $request->filled('custom_expires_at')) {
            $expiresAt = Carbon::parse($request->input('custom_expires_at'));
        }

        // Assign Role with Expiry
        $user->assignRoleWithExpiry($validated['role'], $expiresAt);

        // Sync Direct Permissions
        $directPermissions = $request->input('permissions', []);
        $user->syncPermissions($directPermissions);

        $expiryMsg = $expiresAt ? " (Access expires on {$expiresAt->format('d M Y H:i')})" : '';

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User roles and permissions updated successfully.{$expiryMsg}");
    }

    /**
     * Inspect and analyze effective permissions for a user.
     */
    public function permissionsAnalyzer(User $user): View
    {
        $allPermissions = Permission::orderBy('name')->get();
        $userRoles = $user->roles()->with('permissions')->get();
        $directPermissions = $user->permissions->pluck('name')->toArray();

        $rolePermissionMap = [];
        foreach ($userRoles as $role) {
            foreach ($role->permissions as $perm) {
                $rolePermissionMap[$perm->name][] = $role->name;
            }
        }

        // Group permissions by module (e.g. users, roles, orders, invoices)
        $analyzedPermissions = [];
        $totalAllowed = 0;
        $totalDirect = 0;
        $totalInherited = 0;
        $totalDenied = 0;

        foreach ($allPermissions as $permission) {
            $permName = $permission->name;
            $parts = explode('.', $permName);
            $module = count($parts) > 1 ? ucfirst($parts[0]) : 'General';

            $isDirect = in_array($permName, $directPermissions, true);
            $inheritedRoles = $rolePermissionMap[$permName] ?? [];
            $isInherited = !empty($inheritedRoles);
            $isAllowed = $isDirect || $isInherited;

            if ($isAllowed) {
                $totalAllowed++;
                if ($isDirect) $totalDirect++;
                if ($isInherited) $totalInherited++;
            } else {
                $totalDenied++;
            }

            $analyzedPermissions[$module][] = [
                'permission' => $permission,
                'name' => $permName,
                'is_allowed' => $isAllowed,
                'is_direct' => $isDirect,
                'is_inherited' => $isInherited,
                'inherited_roles' => $inheritedRoles,
            ];
        }

        return view('admin.users.permissions-analyzer', compact(
            'user',
            'userRoles',
            'analyzedPermissions',
            'totalAllowed',
            'totalDirect',
            'totalInherited',
            'totalDenied',
            'allPermissions'
        ));
    }

    /**
     * Test a specific ability/permission in real-time.
     */
    public function testPermission(Request $request, User $user)
    {
        $validated = $request->validate([
            'permission' => ['required', 'string'],
        ]);

        $permissionName = $validated['permission'];
        $can = $user->can($permissionName);

        $direct = $user->hasDirectPermission($permissionName);
        $viaRoles = $user->getPermissionsViaRoles()->pluck('name')->contains($permissionName);

        $reason = 'Access Denied: Neither assigned directly nor inherited through roles.';
        if ($can) {
            if ($direct && $viaRoles) {
                $reason = "Access Granted: Assigned directly AND inherited via roles.";
            } elseif ($direct) {
                $reason = "Access Granted: Assigned directly to this user.";
            } else {
                $roles = $user->roles()->whereHas('permissions', fn($q) => $q->where('name', $permissionName))->pluck('name')->implode(', ');
                $reason = "Access Granted: Inherited via role(s) [{$roles}].";
            }
        }

        return response()->json([
            'permission' => $permissionName,
            'can' => $can,
            'reason' => $reason,
        ]);
    }
}