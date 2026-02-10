<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->string('name'); // Asset name (land parcel, stock, equipment)
            $table->string('category')->nullable(); // land, stock, equipment, other
            $table->decimal('acquisition_cost', 15, 2)->default(0);
            $table->date('date_acquired')->nullable();
            $table->decimal('current_value', 15, 2)->nullable();

            $table->text('notes')->nullable();

            // Simple supporting document fields (link to title deed, invoice, etc.)
            $table->string('supporting_document_path')->nullable();
            $table->string('supporting_document_name')->nullable();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_assets');
    }
};

