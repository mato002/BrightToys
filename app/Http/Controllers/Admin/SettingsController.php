<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminRole;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use App\Models\WelfareRule;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'profile');
        
        // Check if user can manage roles and system settings
        $canManageRoles = $user->isSuperAdmin() || $user->hasAdminRole('finance_admin');
        $canManageSettings = $user->isSuperAdmin() || $user->hasAdminRole('finance_admin');
        
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

        // Get system settings
        $systemSettings = [
            'general' => [],
            'financial' => [],
            'approval' => [],
            'notification' => [],
            'email' => [],
        ];
        if ($canManageSettings) {
            $systemSettings = [
                'general' => SystemSetting::getByGroup('general') ?: [],
                'financial' => SystemSetting::getByGroup('financial') ?: [],
                'approval' => SystemSetting::getByGroup('approval') ?: [],
                'notification' => SystemSetting::getByGroup('notification') ?: [],
                'email' => SystemSetting::getByGroup('email') ?: [],
            ];
        }

        // Get welfare rules
        $welfareRules = null;
        if ($canManageSettings) {
            $welfareRules = WelfareRule::orderBy('priority')->orderBy('name')->get();
        }

        // Get recent activity logs
        $recentActivity = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        // Get account stats
        $accountStats = [
            'member_since' => $user->created_at->diffForHumans(),
            'last_login' => $user->updated_at->diffForHumans(),
            'total_activity' => ActivityLog::where('user_id', $user->id)->count(),
            'email_verified' => $user->email_verified_at !== null,
        ];

        return view('admin.settings.index', compact(
            'user', 
            'canManageRoles', 
            'canManageSettings',
            'adminUsers', 
            'roles', 
            'recentActivity', 
            'accountStats',
            'systemSettings',
            'welfareRules',
            'tab'
        ));
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

    /**
     * Update profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        ActivityLogService::logAdmin('profile_updated', $user, [
            'updated_fields' => array_keys($validated),
        ]);

        return redirect()->route('admin.settings')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to manage system settings.');
        }

        $group = $request->input('group', 'general');
        $settings = $request->except(['_token', 'group']);

        foreach ($settings as $key => $value) {
            SystemSetting::set($key, $value, 'string', $group);
        }

        ActivityLogService::logAdmin('system_settings_updated', $user, [
            'group' => $group,
            'settings' => array_keys($settings),
        ]);

        return redirect()->route('admin.settings', ['tab' => $group])
            ->with('success', ucfirst($group) . ' settings updated successfully.');
    }

    /**
     * Store or update welfare rule
     */
    public function storeWelfareRule(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to manage welfare rules.');
        }

        $validated = $request->validate([
            'rule_type' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_amount' => ['nullable', 'numeric', 'min:0'],
            'max_per_year' => ['nullable', 'integer', 'min:0'],
            'min_months_membership' => ['nullable', 'integer', 'min:0'],
            'requires_approval' => ['nullable', 'boolean'],
            'approval_levels' => ['nullable', 'array'],
            'required_documents' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'integer'],
        ]);

        if ($request->has('id')) {
            $rule = WelfareRule::findOrFail($request->id);
            $rule->update($validated);
            $message = 'Welfare rule updated successfully.';
        } else {
            $rule = WelfareRule::create($validated);
            $message = 'Welfare rule created successfully.';
        }

        ActivityLogService::logAdmin('welfare_rule_updated', $user, [
            'rule_id' => $rule->id,
            'rule_name' => $rule->name,
        ]);

        return redirect()->route('admin.settings', ['tab' => 'welfare'])
            ->with('success', $message);
    }

    /**
     * Delete welfare rule
     */
    public function deleteWelfareRule(WelfareRule $welfareRule)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to manage welfare rules.');
        }

        $name = $welfareRule->name;
        $welfareRule->delete();

        ActivityLogService::logAdmin('welfare_rule_deleted', $user, [
            'rule_name' => $name,
        ]);

        return redirect()->route('admin.settings', ['tab' => 'welfare'])
            ->with('success', 'Welfare rule deleted successfully.');
    }
}
