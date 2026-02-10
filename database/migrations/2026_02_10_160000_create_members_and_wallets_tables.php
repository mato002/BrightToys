<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('pending'); // pending, active, suspended, exited
            $table->string('onboarding_token')->nullable()->unique();
            $table->timestamp('onboarding_token_expires_at')->nullable();
            $table->timestamp('biodata_completed_at')->nullable();
            $table->timestamp('id_verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('member_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // welfare, investment
            $table->decimal('balance', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_wallets');
        Schema::dropIfExists('members');
    }
};

