<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectAsset;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectAssetController extends Controller
{
    /**
     * Finance/leadership check (similar to other finance controllers).
     */
    protected function checkFinancePermission(): void
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return;
        }

        if (! $user->hasAdminRole('finance_admin') && ! $user->hasAdminRole('chairman') && ! $user->hasAdminRole('treasurer')) {
            abort(403, 'You do not have permission to manage project assets.');
        }
    }

    public function create(Request $request)
    {
        $this->checkFinancePermission();

        $projects = Project::orderBy('name')->get(['id', 'name']);
        $selectedProjectId = $request->get('project_id');

        return view('admin.project-assets.create', compact('projects', 'selectedProjectId'));
    }

    public function store(Request $request)
    {
        $this->checkFinancePermission();

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'acquisition_cost' => ['required', 'numeric', 'min:0'],
            'date_acquired' => ['nullable', 'date'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'supporting_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $data = [
            'project_id' => $validated['project_id'],
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'acquisition_cost' => $validated['acquisition_cost'],
            'date_acquired' => $validated['date_acquired'] ?? null,
            'current_value' => $validated['current_value'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ];

        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $path = $file->store('project-assets', 'public');
            $data['supporting_document_path'] = $path;
            $data['supporting_document_name'] = $file->getClientOriginalName();
        }

        $asset = ProjectAsset::create($data);

        ActivityLogService::log('project_asset_created', $asset, $data);

        return redirect()
            ->route('admin.projects.show', $asset->project_id)
            ->with('success', 'Project asset recorded successfully.');
    }

    public function edit(ProjectAsset $projectAsset)
    {
        $this->checkFinancePermission();

        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('admin.project-assets.edit', [
            'asset' => $projectAsset,
            'projects' => $projects,
        ]);
    }

    public function update(Request $request, ProjectAsset $projectAsset)
    {
        $this->checkFinancePermission();

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'acquisition_cost' => ['required', 'numeric', 'min:0'],
            'date_acquired' => ['nullable', 'date'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'supporting_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $data = [
            'project_id' => $validated['project_id'],
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'acquisition_cost' => $validated['acquisition_cost'],
            'date_acquired' => $validated['date_acquired'] ?? null,
            'current_value' => $validated['current_value'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        if ($request->hasFile('supporting_document')) {
            // Delete old file if exists
            if ($projectAsset->supporting_document_path) {
                Storage::disk('public')->delete($projectAsset->supporting_document_path);
            }

            $file = $request->file('supporting_document');
            $path = $file->store('project-assets', 'public');
            $data['supporting_document_path'] = $path;
            $data['supporting_document_name'] = $file->getClientOriginalName();
        }

        $projectAsset->update($data);

        ActivityLogService::log('project_asset_updated', $projectAsset, $data);

        return redirect()
            ->route('admin.projects.show', $projectAsset->project_id)
            ->with('success', 'Project asset updated successfully.');
    }

    public function destroy(ProjectAsset $projectAsset)
    {
        $this->checkFinancePermission();

        if ($projectAsset->supporting_document_path) {
            Storage::disk('public')->delete($projectAsset->supporting_document_path);
        }

        $projectId = $projectAsset->project_id;

        ActivityLogService::log('project_asset_deleted', $projectAsset);

        $projectAsset->delete();

        return redirect()
            ->route('admin.projects.show', $projectId)
            ->with('success', 'Project asset deleted successfully.');
    }
}

