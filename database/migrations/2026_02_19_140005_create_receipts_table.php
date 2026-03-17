<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_order_id')->constrained('pos_orders')->cascadeOnDelete();
            $table->string('receipt_number', 50)->unique()->comment('e.g. RCP-2024-00001');
            $table->unsignedTinyInteger('copy_number')->default(1)->comment('1=customer, 2=merchant, etc.');
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->index('pos_order_id');
            $table->index('receipt_number');
            $table->index('printed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
