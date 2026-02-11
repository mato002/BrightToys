<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_plan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_plan_id')->constrained()->cascadeOnDelete();
            $table->integer('installment_number'); // 1, 2, 3, etc.
            $table->decimal('amount', 15, 2); // Amount due for this installment
            $table->date('due_date'); // When this installment is due
            $table->decimal('paid_amount', 15, 2)->default(0); // Amount paid
            $table->date('paid_at')->nullable(); // When it was paid
            $table->enum('status', ['pending', 'paid', 'overdue', 'missed'])->default('pending');
            $table->integer('days_overdue')->default(0); // Days past due date
            $table->decimal('penalty_amount', 15, 2)->default(0); // Penalty applied
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plan_installments');
    }
};
