<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('members', 'national_id_number')) {
                $table->string('national_id_number')->nullable()->after('date_of_birth');
            }
            if (! Schema::hasColumn('members', 'address')) {
                $table->string('address')->nullable()->after('national_id_number');
            }
            if (! Schema::hasColumn('members', 'id_document_path')) {
                $table->string('id_document_path')->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'id_document_path')) {
                $table->dropColumn('id_document_path');
            }
            if (Schema::hasColumn('members', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('members', 'national_id_number')) {
                $table->dropColumn('national_id_number');
            }
            if (Schema::hasColumn('members', 'date_of_birth')) {
                $table->dropColumn('date_of_birth');
            }
        });
    }
};

