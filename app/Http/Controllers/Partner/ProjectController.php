<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $projects = Project::where('is_active', true)
            ->with('creator')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('partner.projects.index', compact('projects'));
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        if (!$project->is_active) {
            abort(404);
        }

        $project->load(['creator', 'assignedUsers']);

        // Partners can only view projects, not manage them
        // Project management is handled by admins only
        return view('partner.projects.show', compact('project'));
    }

    /**
     * Redirect to project (for quick access)
     */
    public function redirect(Project $project)
    {
        if (!$project->is_active) {
            abort(404);
        }

        // If project has no URL yet, redirect to project details page
        if (!$project->url && !$project->route_name) {
            return redirect()->route('partner.projects.show', $project)
                ->with('info', 'This project is not yet developed. Contact management when it\'s ready.');
        }

        return redirect($project->project_url);
    }
}
