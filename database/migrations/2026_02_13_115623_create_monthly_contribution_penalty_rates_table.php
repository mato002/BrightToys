<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('monthly_contribution_penalty_rates')) {
            Schema::create('monthly_contribution_penalty_rates', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // e.g., "Standard Monthly Contribution Penalty"
                $table->decimal('rate', 5, 4); // Rate as decimal (e.g., 0.1000 for 10%)
                $table->date('effective_from'); // Date from which this rate takes effect
                $table->date('effective_to')->nullable(); // Date until which this rate is effective (null = current)
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                
                // Index for efficient queries
                $table->index(['effective_from', 'effective_to', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_contribution_penalty_rates');
    }
};
