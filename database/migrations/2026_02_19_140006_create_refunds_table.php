<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_order_id')->constrained('pos_orders')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('reason', 500)->nullable();
            $table->foreignId('refunded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('pending')->comment('pending, completed, failed');
            $table->string('transaction_reference', 255)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->index('pos_order_id');
            $table->index('refunded_by');
            $table->index('status');
            $table->index('transaction_reference');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
