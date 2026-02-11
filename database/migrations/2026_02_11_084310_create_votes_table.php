<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_topic_id')->constrained('voting_topics')->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->enum('choice', ['yes', 'no', 'abstain']);
            $table->decimal('weight_percentage', 5, 2); // snapshot of ownership % at time of vote
            $table->decimal('weight_value', 15, 6); // numeric weight for tally
            $table->timestamp('cast_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['voting_topic_id', 'partner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
