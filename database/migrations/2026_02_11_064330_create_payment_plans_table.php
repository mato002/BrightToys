<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_contribution_id')->constrained('entry_contributions')->cascadeOnDelete();
            $table->integer('total_installments'); // Number of installments
            $table->date('start_date'); // When payment plan starts
            $table->enum('frequency', ['weekly', 'monthly', 'quarterly', 'custom'])->default('monthly');
            $table->text('terms')->nullable(); // Payment terms description
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};
