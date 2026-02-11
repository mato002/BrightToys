<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectKpi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalProjectMetricsService
{
    /**
     * Fetch latest performance/financial metrics for a given project
     * from an external system (e.g. toy shop e-commerce) via HTTP API.
     *
     * This is a pluggable integration. Configure the base URL and token in config/services.php:
     *
     * 'external_projects' => [
     *     'base_url' => env('EXTERNAL_PROJECTS_BASE_URL'),
     *     'token' => env('EXTERNAL_PROJECTS_TOKEN'),
     * ],
     *
     * The external API is expected to expose an endpoint like:
     *   GET {base_url}/projects/{slug}/metrics
     *
     * returning JSON, for example:
     * {
     *   "monthly_revenue": 123456.78,
     *   "gross_margin_pct": 32.5,
     *   "operating_expense_ratio_pct": 18.4,
     *   "break_even_revenue": 95000,
     *   "loan_coverage_ratio": 1.8
     * }
     */
    public function fetchMetrics(Project $project): ?array
    {
        $baseUrl = config('services.external_projects.base_url');
        $token   = config('services.external_projects.token');

        if (! $baseUrl) {
            Log::warning('ExternalProjectMetricsService: base URL not configured (services.external_projects.base_url)');
            return null;
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(rtrim($baseUrl, '/') . '/projects/' . $project->slug . '/metrics');

            if (! $response->successful()) {
                Log::warning('ExternalProjectMetricsService: non-success response', [
                    'project_id' => $project->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('ExternalProjectMetricsService: error fetching metrics', [
                'project_id' => $project->id,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch metrics from the external system and persist them
     * into the project's KPI record for use in dashboards.
     */
    public function syncProjectMetrics(Project $project): bool
    {
        $data = $this->fetchMetrics($project);

        if (! $data) {
            return false;
        }

        // Map external fields into our ProjectKpi model.
        // Adjust keys below to match your external API response.
        $kpiPayload = [
            // Targets remain as configured in the project; we update actual performance-related fields.
            'monthly_revenue_target'          => $data['monthly_revenue']      ?? null,
            'gross_margin_target_pct'        => $data['gross_margin_pct']     ?? null,
            'operating_expense_ratio_target_pct' => $data['operating_expense_ratio_pct'] ?? null,
            'break_even_revenue'             => $data['break_even_revenue']   ?? null,
            'loan_coverage_ratio_target'     => $data['loan_coverage_ratio']  ?? null,
        ];

        /** @var \App\Models\ProjectKpi $kpi */
        $kpi = $project->kpi ?: new ProjectKpi(['project_id' => $project->id]);

        $kpi->fill(array_filter($kpiPayload, fn ($value) => ! is_null($value)));
        $kpi->project()->associate($project);
        $kpi->save();

        return true;
    }
}

