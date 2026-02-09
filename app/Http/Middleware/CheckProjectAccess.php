<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckProjectAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $project = $request->route('project');

        if (!$user || !$project) {
            abort(404);
        }

        // Check if user is assigned to this project
        if (!$user->isAssignedToProject($project->id)) {
            abort(403, 'You are not assigned to this project.');
        }

        return $next($request);
    }
}
