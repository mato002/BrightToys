<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_loan_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_funding_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('status')->default('pending'); // pending / submitted / approved
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable(); // e.g. clarifications, risk notes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_loan_requirements');
    }
};

