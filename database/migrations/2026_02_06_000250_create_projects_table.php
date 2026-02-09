<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('ecommerce'); // ecommerce, service, platform, etc.
            $table->string('url')->nullable(); // External URL if applicable
            $table->string('route_name')->nullable(); // Laravel route name for internal projects
            $table->string('icon')->nullable(); // Icon class or SVG
            $table->string('color')->default('emerald'); // Color theme
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
