<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique()->comment('e.g. POS-2024-00001');
            $table->foreignId('register_id')->nullable()->constrained('registers')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Cashier');
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete()->comment('Optional walk-in customer');
            $table->string('status', 30)->default('pending')->comment('pending, completed, cancelled, refunded');
            $table->string('payment_status', 30)->default('pending')->comment('pending, partial, paid, refunded');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('pos_orders', function (Blueprint $table) {
            $table->index('order_number');
            $table->index('register_id');
            $table->index('user_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_orders');
    }
};
