<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user.
     */
    public function impersonate(Request $request, User $user): RedirectResponse
    {
        $currentAdmin = Auth::user();

        if ($currentAdmin->id === $user->id) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        if (session()->has('impersonator_id')) {
            return back()->with('error', 'You are already impersonating another user. Please revert first.');
        }

        // Store original admin ID in session
        session(['impersonator_id' => $currentAdmin->id]);

        // Login as target user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "🎭 You are now logged in as {$user->name}. You can experience the application with their assigned roles and permissions.");
    }

    /**
     * Stop impersonating and return to administrator account.
     */
    public function leave(Request $request): RedirectResponse
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard')->with('error', 'You are not currently impersonating any user.');
        }

        $adminId = session()->pull('impersonator_id');
        $admin = User::findOrFail($adminId);

        Auth::login($admin);

        return redirect()->route('admin.users.index')->with('success', '✅ Successfully returned to Administrator account.');
    }
}