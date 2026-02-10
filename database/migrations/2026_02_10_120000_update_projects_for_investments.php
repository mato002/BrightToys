<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'objective')) {
                $table->text('objective')->nullable()->after('description');
            }

            if (!Schema::hasColumn('projects', 'status')) {
                $table->string('status')->default('planning')->after('type');
            }

            if (!Schema::hasColumn('projects', 'created_by_user_id')) {
                $table->foreignId('created_by_user_id')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('projects', 'activated_by_user_id')) {
                $table->foreignId('activated_by_user_id')
                    ->nullable()
                    ->after('created_by_user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('projects', 'activated_at')) {
                $table->timestamp('activated_at')->nullable()->after('activated_by_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'objective')) {
                $table->dropColumn('objective');
            }

            if (Schema::hasColumn('projects', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('projects', 'activated_at')) {
                $table->dropColumn('activated_at');
            }

            if (Schema::hasColumn('projects', 'activated_by_user_id')) {
                $table->dropConstrainedForeignId('activated_by_user_id');
            }

            if (Schema::hasColumn('projects', 'created_by_user_id')) {
                $table->dropConstrainedForeignId('created_by_user_id');
            }
        });
    }
};

