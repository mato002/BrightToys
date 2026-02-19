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
        Schema::table('payment_plan_installments', function (Blueprint $table) {
            $table->timestamp('reminder_sent_at')->nullable()->after('notes');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('reminder_sent_at');
            $table->integer('reminder_count')->default(0)->after('last_reminder_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_plan_installments', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_at', 'last_reminder_sent_at', 'reminder_count']);
        });
    }
};
