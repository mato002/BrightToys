<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalty_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Standard Late Payment"
            $table->decimal('rate', 5, 2); // Percentage rate (e.g., 5.00 for 5%)
            $table->enum('calculation_method', ['percentage_of_installment', 'percentage_per_day', 'fixed_amount'])->default('percentage_per_day');
            $table->integer('grace_period_days')->default(0); // Days before penalty applies
            $table->decimal('max_penalty_amount', 15, 2)->nullable(); // Maximum penalty cap
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalty_rates');
    }
};
