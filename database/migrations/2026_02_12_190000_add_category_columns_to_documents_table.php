<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // High-level business category (e.g. constitution, loan_agreement, title_deed)
            $table->string('category')->nullable()->after('type');
            // Optional sub-category or finer grouping (e.g. 'AGM 2025', 'Land Project A')
            $table->string('sub_category')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['category', 'sub_category']);
        });
    }
};

