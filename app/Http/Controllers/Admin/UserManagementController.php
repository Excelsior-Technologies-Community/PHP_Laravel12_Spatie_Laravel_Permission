<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display users with search and role filtering.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $users = User::query()
            ->with('roles')
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
     * Show the role assignment form.
     */
    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact(
            'user',
            'roles'
        ));
    }

    /**
     * Update the user's role.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'exists:roles,name'],
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

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User role updated successfully.');
    }
}