<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectKpi extends Model
{
    protected $fillable = [
        'project_id',
        // Land / real estate KPIs
        'target_annual_value_growth_pct',
        'expected_holding_period_years',
        'minimum_acceptable_roi_pct',
        // Operating business / toy shop KPIs
        'monthly_revenue_target',
        'gross_margin_target_pct',
        'operating_expense_ratio_target_pct',
        'break_even_revenue',
        'loan_coverage_ratio_target',
    ];

    protected $casts = [
        'target_annual_value_growth_pct' => 'float',
        'expected_holding_period_years' => 'float',
        'minimum_acceptable_roi_pct' => 'float',
        'monthly_revenue_target' => 'float',
        'gross_margin_target_pct' => 'float',
        'operating_expense_ratio_target_pct' => 'float',
        'break_even_revenue' => 'float',
        'loan_coverage_ratio_target' => 'float',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

