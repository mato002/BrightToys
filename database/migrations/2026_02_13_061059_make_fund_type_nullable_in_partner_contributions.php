<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('partner_contributions', function (Blueprint $table) {
            $table->string('fund_type', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_contributions', function (Blueprint $table) {
            $table->string('fund_type', 50)->nullable(false)->default('investment')->change();
        });
    }
};
