<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_wallet_id')->constrained('member_wallets')->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->string('fund_type', 50)->default('investment'); // welfare / investment
            $table->string('direction', 10); // credit / debit
            $table->string('type', 50); // contribution, withdrawal, profit_distribution, adjustment
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->timestamp('occurred_at');
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->morphs('source'); // source_type, source_id (e.g. PartnerContribution, FinancialRecord)
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

