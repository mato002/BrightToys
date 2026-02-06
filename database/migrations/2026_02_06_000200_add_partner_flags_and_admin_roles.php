<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend users with is_partner flag
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_partner')) {
                $table->boolean('is_partner')->default(false)->after('is_admin');
            }
        });

        // Admin roles table
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. super_admin, finance_admin, store_admin
            $table->string('display_name');
            $table->timestamps();
        });

        // Pivot between users and admin_roles
        Schema::create('admin_role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_role_id')->constrained('admin_roles')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'admin_role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_role_user');
        Schema::dropIfExists('admin_roles');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_partner')) {
                $table->dropColumn('is_partner');
            }
        });
    }
};

