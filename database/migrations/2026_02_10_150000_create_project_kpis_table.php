<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained()->cascadeOnDelete();

            // Land / real estate KPIs
            $table->decimal('target_annual_value_growth_pct', 5, 2)->nullable();
            $table->decimal('expected_holding_period_years', 5, 2)->nullable();
            $table->decimal('minimum_acceptable_roi_pct', 5, 2)->nullable();

            // Operating business / toy shop KPIs
            $table->decimal('monthly_revenue_target', 15, 2)->nullable();
            $table->decimal('gross_margin_target_pct', 5, 2)->nullable();
            $table->decimal('operating_expense_ratio_target_pct', 5, 2)->nullable();
            $table->decimal('break_even_revenue', 15, 2)->nullable();
            $table->decimal('loan_coverage_ratio_target', 5, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_kpis');
    }
};

