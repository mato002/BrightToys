<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welfare_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_type'); // medical, education, emergency, funeral, etc.
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->integer('max_per_year')->nullable();
            $table->integer('min_months_membership')->default(0);
            $table->boolean('requires_approval')->default(true);
            $table->json('approval_levels')->nullable(); // Array of required approval levels
            $table->json('required_documents')->nullable(); // Array of required document types
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // For ordering
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_rules');
    }
};
