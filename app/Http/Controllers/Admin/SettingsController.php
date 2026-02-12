<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminRole;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Check if user can manage roles
        $canManageRoles = $user->isSuperAdmin() || $user->hasAdminRole('finance_admin');
        
        // Get all admin users and roles if user can manage roles
        $adminUsers = null;
        $roles = null;
        
        if ($canManageRoles) {
            $adminUsers = User::where('is_admin', true)
                ->with('adminRoles')
                ->orderBy('name')
                ->get();
            $roles = AdminRole::with('permissions')->orderBy('display_name')->get();
        }

        return view('admin.settings.index', compact('user', 'canManageRoles', 'adminUsers', 'roles'));
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.settings')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Assign roles to a user.
     */
    public function assignRoles(Request $request, User $user)
    {
        // Check permissions
        $currentUser = auth()->user();
        if (!$currentUser->isSuperAdmin() && !$currentUser->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        // Ensure we're only managing admin users
        if (!$user->is_admin) {
            abort(404, 'User is not an admin.');
        }

        // Prevent removing super admin role from yourself
        if ($user->id === $currentUser->id && $currentUser->isSuperAdmin()) {
            $superAdminRole = AdminRole::where('name', 'super_admin')->first();
            if ($superAdminRole && !in_array($superAdminRole->id, $request->input('roles', []))) {
                return redirect()->back()
                    ->with('error', 'You cannot remove the super admin role from your own account.');
            }
        }

        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:admin_roles,id'],
        ]);

        // Sync roles
        $user->adminRoles()->sync($validated['roles'] ?? []);

        ActivityLogService::logAdmin('roles_updated', $user, [
            'roles' => $validated['roles'] ?? [],
            'updated_by' => $currentUser->name,
        ]);

        return redirect()->route('admin.settings')
            ->with('success', 'Roles updated successfully for ' . $user->name . '.');
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(Request $request, User $user, AdminRole $role)
    {
        // Check permissions
        $currentUser = auth()->user();
        if (!$currentUser->isSuperAdmin() && !$currentUser->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to manage roles.');
        }

        // Ensure we're only managing admin users
        if (!$user->is_admin) {
            abort(404, 'User is not an admin.');
        }

        // Prevent removing super admin role from yourself
        if ($user->id === $currentUser->id && $role->name === 'super_admin' && $currentUser->isSuperAdmin()) {
            return redirect()->back()
                ->with('error', 'You cannot remove the super admin role from your own account.');
        }

        $user->adminRoles()->detach($role->id);

        ActivityLogService::logAdmin('role_removed', $user, [
            'role' => $role->name,
            'removed_by' => $currentUser->name,
        ]);

        return redirect()->route('admin.settings')
            ->with('success', $role->display_name . ' role removed from ' . $user->name . '.');
    }
}
