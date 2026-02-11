<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entry_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->decimal('total_amount', 15, 2); // Total required entry contribution
            $table->decimal('initial_deposit', 15, 2)->default(0); // Initial deposit paid
            $table->decimal('paid_amount', 15, 2)->default(0); // Total paid so far
            $table->decimal('outstanding_balance', 15, 2); // Remaining balance
            $table->enum('payment_method', ['full', 'installments'])->default('full');
            $table->string('currency', 3)->default('KES');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_contributions');
    }
};
