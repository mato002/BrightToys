<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalty_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->enum('type', ['apply', 'waive', 'pause']); // manual penalty, waiver, or pause
            $table->string('scope')->default('monthly_contribution'); // future extension point
            $table->unsignedSmallInteger('target_year')->nullable(); // for apply/waive
            $table->unsignedTinyInteger('target_month')->nullable(); // 1-12
            $table->decimal('amount', 15, 2)->nullable(); // positive amount for apply/waive
            $table->date('paused_from')->nullable(); // for pause
            $table->date('paused_to')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalty_adjustments');
    }
};

