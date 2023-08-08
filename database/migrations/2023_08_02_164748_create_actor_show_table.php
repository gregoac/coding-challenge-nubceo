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
        Schema::create('actor_show', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->constrained()->onDelete('cascade');
            $table->foreignId('tv_show_id')->constrained('tv_shows')->onDelete('cascade');
            $table->foreignId('season_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actor_show', function (Blueprint $table) {
            $table->dropForeign(['tv_show_id']);  // Drop the foreign key constraint
        });
        
        Schema::dropIfExists('actor_show');
    }
};
