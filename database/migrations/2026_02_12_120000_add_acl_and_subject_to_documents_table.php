<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Related entity (member, loan, project, meeting, etc.)
            $table->string('subject_type')->nullable()->after('uploaded_by');
            $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');

            // Fine-grained access control
            $table->json('view_roles')->nullable()->after('subject_id');
            $table->json('view_users')->nullable()->after('view_roles');
            $table->json('blocked_users')->nullable()->after('view_users');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'subject_type',
                'subject_id',
                'view_roles',
                'view_users',
                'blocked_users',
            ]);
        });
    }
};

