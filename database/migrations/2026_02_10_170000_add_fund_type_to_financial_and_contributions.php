<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_records', function (Blueprint $table) {
            if (! Schema::hasColumn('financial_records', 'fund_type')) {
                $table->string('fund_type', 50)
                    ->default('general') // general, welfare, investment, operational
                    ->after('type');
            }
        });

        Schema::table('partner_contributions', function (Blueprint $table) {
            if (! Schema::hasColumn('partner_contributions', 'fund_type')) {
                $table->string('fund_type', 50)
                    ->default('investment') // investment or welfare
                    ->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('financial_records', function (Blueprint $table) {
            if (Schema::hasColumn('financial_records', 'fund_type')) {
                $table->dropColumn('fund_type');
            }
        });

        Schema::table('partner_contributions', function (Blueprint $table) {
            if (Schema::hasColumn('partner_contributions', 'fund_type')) {
                $table->dropColumn('fund_type');
            }
        });
    }
};

