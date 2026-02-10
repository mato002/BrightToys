<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_records', function (Blueprint $table) {
            if (!Schema::hasColumn('financial_records', 'project_id')) {
                $table->foreignId('project_id')
                    ->nullable()
                    ->after('partner_id')
                    ->constrained('projects')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('financial_records', 'paid_from')) {
                $table->string('paid_from')->nullable()->after('currency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('financial_records', function (Blueprint $table) {
            if (Schema::hasColumn('financial_records', 'project_id')) {
                $table->dropConstrainedForeignId('project_id');
            }

            if (Schema::hasColumn('financial_records', 'paid_from')) {
                $table->dropColumn('paid_from');
            }
        });
    }
};

