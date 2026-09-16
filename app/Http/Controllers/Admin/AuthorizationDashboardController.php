<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthorizationDashboardController extends Controller
{
    /**
     * Display authorization statistics dashboard.
     */
    public function index(): View
    {
        $totalUsers = User::count();

        $totalRoles = Role::count();

        $totalPermissions = Permission::count();

        $adminUsers = User::role('admin')->count();

        $usersWithoutRole = User::doesntHave('roles')->count();

        $rolesWithUsers = Role::withCount('users')
            ->orderByDesc('users_count')
            ->orderBy('name')
            ->get();

        $permissionsWithRoles = Permission::withCount('roles')
            ->orderByDesc('roles_count')
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalRoles',
            'totalPermissions',
            'adminUsers',
            'usersWithoutRole',
            'rolesWithUsers',
            'permissionsWithRoles'
        ));
    }
}