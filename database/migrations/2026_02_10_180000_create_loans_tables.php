<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('lender_name'); // Bank / SACCO
            $table->decimal('amount', 18, 2);
            $table->decimal('interest_rate', 8, 4); // e.g. 0.12 for 12% p.a.
            $table->unsignedInteger('tenure_months');
            $table->string('repayment_frequency')->default('monthly');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->enum('status', ['active', 'repaid', 'in_arrears'])->default('active');
            $table->timestamps();
        });

        Schema::create('loan_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->unsignedInteger('period_number');
            $table->date('due_date');
            $table->decimal('principal_due', 18, 2);
            $table->decimal('interest_due', 18, 2);
            $table->decimal('total_due', 18, 2);
            $table->timestamps();

            $table->unique(['loan_id', 'period_number']);
        });

        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->foreignId('loan_schedule_id')->nullable()->constrained('loan_schedules')->nullOnDelete();
            $table->date('date_paid');
            $table->decimal('amount_paid', 18, 2);
            $table->string('bank_reference')->nullable();
            $table->string('document_path')->nullable();
            $table->string('document_name')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('reconciliation_status', ['pending', 'green', 'red'])->default('pending');
            $table->text('reconciliation_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
        Schema::dropIfExists('loan_schedules');
        Schema::dropIfExists('loans');
    }
};

