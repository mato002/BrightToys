<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminRole;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of admins.
     */
    public function index()
    {
        $query = User::where('is_admin', true);

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->latest()->paginate(20)->withQueryString();

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        // Check if current user can create admins
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to create admins.');
        }

        $roles = AdminRole::all();
        return view('admin.admins.create', compact('roles'));
    }

    /**
     * Store a newly created admin.
     */
    public function store(Request $request)
    {
        // Check permissions
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to create admins.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:admin_roles,id'],
        ]);

        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => true,
        ]);

        // Assign roles
        if (!empty($validated['roles'])) {
            $admin->adminRoles()->attach($validated['roles']);
        }

        ActivityLogService::logAdmin('created', $admin, [
            'name' => $admin->name,
            'email' => $admin->email,
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully.');
    }

    /**
     * Display the specified admin.
     */
    public function show(User $admin)
    {
        // Ensure we're only showing admins
        if (!$admin->is_admin) {
            abort(404);
        }

        $admin->load(['adminRoles'])->loadCount('orders');

        return view('admin.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $admin)
    {
        // Ensure we're only editing admins
        if (!$admin->is_admin) {
            abort(404);
        }

        // Check permissions
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to edit admins.');
        }

        $admin->load('adminRoles');
        $roles = AdminRole::all();

        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified admin.
     */
    public function update(Request $request, User $admin)
    {
        // Ensure we're only updating admins
        if (!$admin->is_admin) {
            abort(404);
        }

        // Check permissions
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to update admins.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:admin_roles,id'],
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];

        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        // Update roles
        if (isset($validated['roles'])) {
            $admin->adminRoles()->sync($validated['roles']);
        }

        ActivityLogService::logAdmin('updated', $admin, $validated);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified admin.
     */
    public function destroy(User $admin)
    {
        // Prevent deleting yourself
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Ensure we're only deleting admins
        if (!$admin->is_admin) {
            abort(404);
        }

        // Check permissions - only super admin can delete
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only super admins can delete admin accounts.');
        }

        ActivityLogService::logAdmin('deleted', $admin);

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted successfully.');
    }
}
