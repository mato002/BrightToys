<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. financial.records.approve
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_role_id')->constrained('admin_roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['admin_role_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_role_permission');
        Schema::dropIfExists('permissions');
    }
};

