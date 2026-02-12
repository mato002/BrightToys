<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chart of Accounts
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->timestamps();
        });

        // Accounting Rules (for automatic journal entries)
        Schema::create('accounting_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger_event'); // e.g., 'salary_advance', 'loan_ledger', 'loan_interest'
            $table->foreignId('debit_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->foreignId('credit_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Wallet Account Mappings
        Schema::create('wallet_account_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_type'); // savings, transactional, investment, etc.
            $table->foreignId('account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['wallet_type', 'account_id']);
        });

        // Journal Entries
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // Format: YYYYMMDD + sequence
            $table->date('transaction_date');
            $table->string('reference_number')->nullable();
            $table->text('transaction_details')->nullable();
            $table->text('comments')->nullable();
            $table->foreignId('branch_id')->nullable(); // For multi-branch support
            $table->string('branch_name')->default('Corporate (HQ)');
            $table->enum('status', ['draft', 'posted'])->default('draft');
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('posted_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index('transaction_date');
            $table->index('status');
        });

        // Journal Entry Lines (debit/credit entries)
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->enum('entry_type', ['debit', 'credit']);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('journal_entry_id');
            $table->index('account_id');
        });

        // Budgets
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->string('branch_name')->default('Corporate');
            $table->year('year');
            $table->unsignedTinyInteger('month'); // 1-12
            $table->string('expense_book')->nullable();
            $table->decimal('budget_amount', 15, 2);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['account_id', 'branch_name', 'year', 'month', 'expense_book']);
            $table->index(['year', 'month']);
        });

        // Company Assets
        Schema::create('company_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('asset_code')->unique();
            $table->enum('asset_type', ['fixed', 'current', 'intangible', 'other']);
            $table->foreignId('account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->decimal('purchase_value', 15, 2);
            $table->decimal('current_value', 15, 2)->nullable();
            $table->date('purchase_date');
            $table->date('depreciation_start_date')->nullable();
            $table->string('depreciation_method')->nullable(); // straight_line, declining_balance, etc.
            $table->decimal('depreciation_rate', 5, 2)->nullable(); // percentage
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Account Reconciliation
        Schema::create('account_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->date('reconciliation_date');
            $table->decimal('opening_balance', 15, 2);
            $table->decimal('closing_balance', 15, 2);
            $table->decimal('reconciled_amount', 15, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'reconciled', 'discrepancy'])->default('pending');
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reconciled_at')->nullable();
            $table->timestamps();
            
            $table->index('account_id');
            $table->index('reconciliation_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_reconciliations');
        Schema::dropIfExists('company_assets');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('wallet_account_mappings');
        Schema::dropIfExists('accounting_rules');
        Schema::dropIfExists('chart_of_accounts');
    }
};
