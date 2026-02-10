<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_fundings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            // Member equity / capital
            $table->decimal('member_capital_amount', 15, 2)->default(0);
            $table->date('member_capital_date')->nullable();
            $table->string('member_capital_source')->nullable(); // e.g. Investment Pool A

            // Debt / loan details
            $table->boolean('has_loan')->default(false);
            $table->string('lender_name')->nullable(); // Bank / SACCO name
            $table->decimal('loan_amount', 15, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable(); // percentage per year
            $table->integer('tenure_months')->nullable();
            $table->decimal('monthly_repayment', 15, 2)->nullable();
            $table->decimal('outstanding_balance', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_fundings');
    }
};

