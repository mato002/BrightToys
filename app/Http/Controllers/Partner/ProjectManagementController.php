<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProjectManagementController extends Controller
{
    /**
     * Restrict access to admins only.
     * Partners can no longer create/manage projects - this is handled by admins only.
     */
    protected function ensureAdmin()
    {
        $user = Auth::user();
        if (!$user->is_admin || (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin') && !$user->hasAdminRole('chairman'))) {
            abort(403, 'Only administrators can manage projects. Partners can view projects only.');
        }
    }

    /**
     * Display a listing of projects created by the partner.
     * NOTE: This controller is deprecated for partners. Project management is now admin-only.
     */
    public function index()
    {
        $this->ensureAdmin();
        
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        $projects = Project::where('created_by', $partner->id)
            ->with(['assignedUsers', 'creator'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('partner.projects.manage', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     * NOTE: This is now admin-only. Partners cannot create projects.
     */
    public function create()
    {
        $this->ensureAdmin();
        
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        $users = User::where('is_partner', false)
            ->where('is_admin', false)
            ->orderBy('name')
            ->get();

        return view('partner.projects.create', compact('users'));
    }

    /**
     * Store a newly created project.
     * NOTE: This is now admin-only. Partners cannot create projects.
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();
        
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'in:emerald,blue,amber,purple,red,indigo'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'assigned_users' => ['nullable', 'array'],
            'assigned_users.*' => ['exists:users,id'],
            'user_roles' => ['nullable', 'array'],
            'user_roles.*' => ['in:manager,editor,viewer'],
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Project::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['created_by'] = $partner->id;

        // URL and route_name are optional - projects can be created before development
        // They can be added later when the project is ready

        $project = Project::create($validated);

        // Assign users to the project
        if (!empty($validated['assigned_users'])) {
            $assignments = [];
            $userRoles = $validated['user_roles'] ?? [];
            foreach ($validated['assigned_users'] as $index => $userId) {
                if (!empty($userId)) { // Only assign if user is selected
                    $assignments[$userId] = [
                        'role' => $userRoles[$index] ?? 'manager',
                    ];
                }
            }
            if (!empty($assignments)) {
                $project->assignedUsers()->sync($assignments);
            }
        }

        ActivityLogService::log('project_created', $project, [
            'created_by_partner' => $partner->name,
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for editing the specified project.
     * NOTE: This is now admin-only. Partners cannot edit projects.
     */
    public function edit(Project $project)
    {
        $this->ensureAdmin();
        
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        // Only the creator can edit
        if ($project->created_by !== $partner->id) {
            abort(403, 'You can only edit projects you created.');
        }

        $users = User::where('is_partner', false)
            ->where('is_admin', false)
            ->orderBy('name')
            ->get();

        $project->load('assignedUsers');

        return view('partner.projects.edit', compact('project', 'users'));
    }

    /**
     * Update the specified project.
     * NOTE: This is now admin-only. Partners cannot update projects.
     */
    public function update(Request $request, Project $project)
    {
        $this->ensureAdmin();
        
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        // Only the creator can update
        if ($project->created_by !== $partner->id) {
            abort(403, 'You can only update projects you created.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug,' . $project->id],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'in:emerald,blue,amber,purple,red,indigo'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'assigned_users' => ['nullable', 'array'],
            'assigned_users.*' => ['exists:users,id'],
            'user_roles' => ['nullable', 'array'],
            'user_roles.*' => ['in:manager,editor,viewer'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // URL and route_name are optional - can be added later when project is developed

        $project->update($validated);

        // Update user assignments
        if (isset($validated['assigned_users'])) {
            $assignments = [];
            $userRoles = $validated['user_roles'] ?? [];
            foreach ($validated['assigned_users'] as $index => $userId) {
                if (!empty($userId)) { // Only assign if user is selected
                    $assignments[$userId] = [
                        'role' => $userRoles[$index] ?? 'manager',
                    ];
                }
            }
            $project->assignedUsers()->sync($assignments);
        }

        ActivityLogService::log('project_updated', $project, [
            'updated_by_partner' => $partner->name,
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project.
     * NOTE: This is now admin-only. Partners cannot delete projects.
     */
    public function destroy(Project $project)
    {
        $this->ensureAdmin();
        
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        // Only the creator can delete
        if ($project->created_by !== $partner->id) {
            abort(403, 'You can only delete projects you created.');
        }

        ActivityLogService::log('project_deleted', $project, [
            'deleted_by_partner' => $partner->name,
        ]);

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
