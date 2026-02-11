<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration merges member functionality into partners table.
     * Members and Partners are the same entity - removing duplication.
     */
    public function up(): void
    {
        // Add member-specific fields to partners table
        Schema::table('partners', function (Blueprint $table) {
            // Onboarding fields
            $table->string('onboarding_token')->nullable()->unique()->after('notes');
            $table->timestamp('onboarding_token_expires_at')->nullable()->after('onboarding_token');
            $table->timestamp('biodata_completed_at')->nullable()->after('onboarding_token_expires_at');
            $table->timestamp('id_verified_at')->nullable()->after('biodata_completed_at');
            
            // Biodata fields
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->string('national_id_number')->nullable()->after('date_of_birth');
            $table->string('address')->nullable()->after('national_id_number');
            $table->string('id_document_path')->nullable()->after('address');
            
            // Approval document link
            $table->foreignId('approval_document_id')->nullable()->after('user_id')->constrained('documents')->nullOnDelete();
        });

        // Update member_wallets to reference partners instead of members
        // First, drop the old foreign key
        Schema::table('member_wallets', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
        });

        // Add new partner_id column
        Schema::table('member_wallets', function (Blueprint $table) {
            $table->foreignId('partner_id')->nullable()->after('id');
        });

        // Migrate data: For each member_wallet, find the corresponding partner
        // If member has a partner_id, use that. Otherwise, we'll need to create partners from members.
        // For now, we'll handle this in a data migration script if needed.
        // Note: This assumes members table still exists during migration
        if (Schema::hasTable('members')) {
            DB::statement('
                UPDATE member_wallets mw
                INNER JOIN members m ON mw.member_id = m.id
                SET mw.partner_id = COALESCE(m.partner_id, m.id)
                WHERE mw.member_id IS NOT NULL
            ');
        }

        // Make partner_id required and add foreign key
        Schema::table('member_wallets', function (Blueprint $table) {
            $table->foreignId('partner_id')->nullable(false)->change();
            $table->foreign('partner_id')->references('id')->on('partners')->cascadeOnDelete();
        });

        // Drop old member_id column
        Schema::table('member_wallets', function (Blueprint $table) {
            $table->dropColumn('member_id');
        });

        // Migrate member data to partners (if any exists)
        // This will be handled in a separate data migration if needed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert member_wallets changes
        Schema::table('member_wallets', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
        });

        Schema::table('member_wallets', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->after('id');
        });

        DB::statement('UPDATE member_wallets SET member_id = partner_id WHERE partner_id IS NOT NULL');

        Schema::table('member_wallets', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable(false)->change();
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            $table->dropColumn('partner_id');
        });

        // Remove member fields from partners
        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['approval_document_id']);
            $table->dropColumn([
                'approval_document_id',
                'onboarding_token',
                'onboarding_token_expires_at',
                'biodata_completed_at',
                'id_verified_at',
                'date_of_birth',
                'national_id_number',
                'address',
                'id_document_path',
            ]);
        });
    }
};
