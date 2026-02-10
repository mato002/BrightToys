<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_rules', function (Blueprint $table) {
            $table->id();
            $table->string('action')->unique(); // e.g. financial_record.approve
            $table->unsignedTinyInteger('min_approvals')->default(1);
            $table->json('required_roles')->nullable(); // ["chairman","treasurer"]
            $table->boolean('allow_initiator_approve')->default(false);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // must match approval_rules.action
            $table->morphs('subject'); // subject_type, subject_id
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        Schema::create('approval_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->constrained('approvals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role_used')->nullable();
            $table->enum('decision', ['approve', 'reject']);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['approval_id', 'user_id']); // one decision per user per approval
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_decisions');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('approval_rules');
    }
};

