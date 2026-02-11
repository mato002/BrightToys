<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFunding;
use App\Models\ProjectKpi;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\ExternalProjectMetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Check if user has permission to access finance/partnership management (view only for partners).
     */
    protected function checkFinancePermission($allowPartners = false)
    {
        $user = auth()->user();
        if ($allowPartners && $user->is_partner) {
            return; // Partners can view
        }
        // Allow Super Admin, Finance Admin and Chairman to access projects area
        if (! $user->isSuperAdmin() && ! $user->hasAdminRole('finance_admin') && ! $user->hasAdminRole('chairman')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    /**
     * Ensure only leadership (Chairman + Treasurer) can create or activate projects.
     */
    protected function ensureLeadership(): void
    {
        $user = Auth::user();

        // Only Chairman or Treasurer can perform this action (no Super Admin bypass)
        if (! $user->hasAdminRole('chairman') && ! $user->hasAdminRole('treasurer')) {
            abort(403, 'Only leadership (Chairman or Treasurer) can perform this action.');
        }
    }

    public function index()
    {
        $this->checkFinancePermission(true); // Allow partners to view

        $query = Project::with(['creator', 'assignedUsers']);

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if (request('status') === 'active') {
            $query->where('is_active', true);
        } elseif (request('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $projects = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $this->ensureLeadership();

        $types = Project::TYPES;
        $statuses = Project::STATUSES;
        $officers = User::where('is_admin', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.projects.create', compact('types', 'statuses', 'officers'));
    }

    public function store(Request $request)
    {
        $this->ensureLeadership();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug'],
            'description' => ['nullable', 'string'],
            'objective' => ['required', 'string'],
            'type' => ['required', 'string', 'in:ecommerce,land,business,trading,other'],
            'status' => ['required', 'string', 'in:planning,active,completed,suspended'],
            'url' => ['nullable', 'url', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'in:emerald,blue,amber,purple,red,indigo'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            // Capital & funding structure (optional at creation but encouraged)
            'member_capital_amount' => ['nullable', 'numeric', 'min:0'],
            'member_capital_date' => ['nullable', 'date'],
            'member_capital_source' => ['nullable', 'string', 'max:255'],
            'has_loan' => ['nullable', 'boolean'],
            'lender_name' => ['nullable', 'string', 'max:255'],
            'loan_amount' => ['nullable', 'numeric', 'min:0'],
            'interest_rate' => ['nullable', 'numeric', 'min:0'],
            'tenure_months' => ['nullable', 'integer', 'min:0'],
            'monthly_repayment' => ['nullable', 'numeric', 'min:0'],
            'outstanding_balance' => ['nullable', 'numeric', 'min:0'],

            // Initial loan requirements (if a loan is involved)
            'requirements' => ['nullable', 'array'],
            'requirements.*.name' => ['nullable', 'string', 'max:255'],
            'requirements.*.responsible_user_id' => ['nullable', 'exists:users,id'],
            'requirements.*.due_date' => ['nullable', 'date'],
            'requirements.*.notes' => ['nullable', 'string'],

            // KPI targets (optional)
            'target_annual_value_growth_pct' => ['nullable', 'numeric'],
            'expected_holding_period_years' => ['nullable', 'numeric'],
            'minimum_acceptable_roi_pct' => ['nullable', 'numeric'],
            'monthly_revenue_target' => ['nullable', 'numeric'],
            'gross_margin_target_pct' => ['nullable', 'numeric'],
            'operating_expense_ratio_target_pct' => ['nullable', 'numeric'],
            'break_even_revenue' => ['nullable', 'numeric'],
            'loan_coverage_ratio_target' => ['nullable', 'numeric'],
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Project::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['color'] = $validated['color'] ?? 'emerald';
        $validated['created_by_user_id'] = Auth::id();

        if ($validated['status'] === 'active') {
            $validated['activated_by_user_id'] = Auth::id();
            $validated['activated_at'] = now();
            $validated['is_active'] = true;
        } else {
            $validated['is_active'] = false;
        }

        $project = Project::create($validated);

        // Create KPI record if any KPI-related fields were provided
        $kpiInput = $request->only([
            'target_annual_value_growth_pct',
            'expected_holding_period_years',
            'minimum_acceptable_roi_pct',
            'monthly_revenue_target',
            'gross_margin_target_pct',
            'operating_expense_ratio_target_pct',
            'break_even_revenue',
            'loan_coverage_ratio_target',
        ]);

        if (collect($kpiInput)->filter(fn ($v) => ! is_null($v) && $v !== '')->isNotEmpty()) {
            $project->kpi()->create($kpiInput);
        }

        // Create capital & funding structure record if provided
        $fundingData = [
            'member_capital_amount' => $validated['member_capital_amount'] ?? 0,
            'member_capital_date' => $validated['member_capital_date'] ?? null,
            'member_capital_source' => $validated['member_capital_source'] ?? null,
            'has_loan' => $request->boolean('has_loan'),
            'lender_name' => $validated['lender_name'] ?? null,
            'loan_amount' => $validated['loan_amount'] ?? null,
            'interest_rate' => $validated['interest_rate'] ?? null,
            'tenure_months' => $validated['tenure_months'] ?? null,
            'monthly_repayment' => $validated['monthly_repayment'] ?? null,
            'outstanding_balance' => $validated['outstanding_balance'] ?? null,
        ];

        $hasMeaningfulFunding =
            ($fundingData['member_capital_amount'] > 0) ||
            $fundingData['has_loan'] ||
            ! empty($fundingData['member_capital_source']) ||
            ! empty($fundingData['lender_name']);

        $funding = null;
        if ($hasMeaningfulFunding) {
            $funding = $project->funding()->create($fundingData);
        }

        // Seed initial loan requirements if a loan exists and requirements were provided
        if ($funding && $funding->has_loan && $request->filled('requirements')) {
            foreach ($request->input('requirements') as $requirement) {
                $name = $requirement['name'] ?? null;
                if (! $name) {
                    continue;
                }

                $funding->loanRequirements()->create([
                    'name' => $name,
                    'responsible_user_id' => $requirement['responsible_user_id'] ?? null,
                    'due_date' => $requirement['due_date'] ?? null,
                    'status' => \App\Models\ProjectLoanRequirement::STATUS_PENDING,
                    'notes' => $requirement['notes'] ?? null,
                ]);
            }
        }

        ActivityLogService::log('project_created', $project, $validated + ($fundingData ?? []));

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $this->checkFinancePermission(true); // Allow partners to view
        $project->load(['funding.loanRequirements.responsibleOfficer']);

        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->checkFinancePermission();

        $types = Project::TYPES;
        $statuses = Project::STATUSES;
        $project->load(['funding.loanRequirements']);

        return view('admin.projects.edit', compact('project', 'types', 'statuses'));
    }

    public function update(Request $request, Project $project)
    {
        $this->checkFinancePermission();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug,' . $project->id],
            'description' => ['nullable', 'string'],
            'objective' => ['required', 'string'],
            'type' => ['required', 'string', 'in:ecommerce,land,business,trading,other'],
            'status' => ['required', 'string', 'in:planning,active,completed,suspended'],
            'url' => ['nullable', 'url', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'in:emerald,blue,amber,purple,red,indigo'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            // Capital & funding structure
            'member_capital_amount' => ['nullable', 'numeric', 'min:0'],
            'member_capital_date' => ['nullable', 'date'],
            'member_capital_source' => ['nullable', 'string', 'max:255'],
            'has_loan' => ['nullable', 'boolean'],
            'lender_name' => ['nullable', 'string', 'max:255'],
            'loan_amount' => ['nullable', 'numeric', 'min:0'],
            'interest_rate' => ['nullable', 'numeric', 'min:0'],
            'tenure_months' => ['nullable', 'integer', 'min:0'],
            'monthly_repayment' => ['nullable', 'numeric', 'min:0'],
            'outstanding_balance' => ['nullable', 'numeric', 'min:0'],

            // KPI targets (optional)
            'target_annual_value_growth_pct' => ['nullable', 'numeric'],
            'expected_holding_period_years' => ['nullable', 'numeric'],
            'minimum_acceptable_roi_pct' => ['nullable', 'numeric'],
            'monthly_revenue_target' => ['nullable', 'numeric'],
            'gross_margin_target_pct' => ['nullable', 'numeric'],
            'operating_expense_ratio_target_pct' => ['nullable', 'numeric'],
            'break_even_revenue' => ['nullable', 'numeric'],
            'loan_coverage_ratio_target' => ['nullable', 'numeric'],
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($validated['status'] === 'active' && ! $project->activated_at) {
            $validated['activated_by_user_id'] = Auth::id();
            $validated['activated_at'] = now();
            $validated['is_active'] = true;
        } elseif ($validated['status'] !== 'active') {
            $validated['is_active'] = false;
        }

        // Ensure either url or route_name is provided
        if (empty($validated['url']) && empty($validated['route_name'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['url' => 'Either URL or Route Name must be provided.']);
        }

        $project->update($validated);

        // Upsert KPI targets
        $kpiInput = $request->only([
            'target_annual_value_growth_pct',
            'expected_holding_period_years',
            'minimum_acceptable_roi_pct',
            'monthly_revenue_target',
            'gross_margin_target_pct',
            'operating_expense_ratio_target_pct',
            'break_even_revenue',
            'loan_coverage_ratio_target',
        ]);

        if (collect($kpiInput)->filter(fn ($v) => ! is_null($v) && $v !== '')->isNotEmpty()) {
            if ($project->kpi) {
                $project->kpi->update($kpiInput);
            } else {
                $project->kpi()->create($kpiInput);
            }
        }

        // Upsert capital & funding details
        $fundingData = [
            'member_capital_amount' => $validated['member_capital_amount'] ?? 0,
            'member_capital_date' => $validated['member_capital_date'] ?? null,
            'member_capital_source' => $validated['member_capital_source'] ?? null,
            'has_loan' => $request->boolean('has_loan'),
            'lender_name' => $validated['lender_name'] ?? null,
            'loan_amount' => $validated['loan_amount'] ?? null,
            'interest_rate' => $validated['interest_rate'] ?? null,
            'tenure_months' => $validated['tenure_months'] ?? null,
            'monthly_repayment' => $validated['monthly_repayment'] ?? null,
            'outstanding_balance' => $validated['outstanding_balance'] ?? null,
        ];

        if ($project->funding) {
            $project->funding->update($fundingData);
        } else {
            $project->funding()->create($fundingData);
        }

        ActivityLogService::log('project_updated', $project, $validated + $fundingData);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->checkFinancePermission();

        ActivityLogService::log('project_deleted', $project);

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Activate a project (leadership only).
     */
    public function activate(Project $project)
    {
        $this->ensureLeadership();

        $project->update([
            'status' => 'active',
            'is_active' => true,
            'activated_by_user_id' => Auth::id(),
            'activated_at' => now(),
        ]);

        ActivityLogService::log('project_activated', $project);

        return redirect()->route('admin.projects.edit', $project)
            ->with('success', 'Project activated successfully.');
    }

    /**
     * Sync project KPIs and financial summaries from an external system (e.g. toy shop e-commerce).
     * This does not change permissions; only users with project access (finance/leadership) can do this.
     */
    public function syncMetrics(Project $project, ExternalProjectMetricsService $service)
    {
        $this->checkFinancePermission();

        $ok = $service->syncProjectMetrics($project);

        if (! $ok) {
            return redirect()->route('admin.projects.show', $project)
                ->with('error', 'Failed to sync metrics from external system. Check API configuration and logs.');
        }

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project metrics synced from external system.');
    }
}
