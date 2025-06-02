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
        //
        Schema::create('exhibition_participant_passes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('exhibition_participants')->onDelete('cascade');
            $table->foreignId('ticket_category_id')->constrained()->onDelete('cascade');
            $table->integer('badge_count')->default(0);
            $table->timestamps();

            $table->unique(['participant_id', 'ticket_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('exhibition_participant_passes');
    }
};
