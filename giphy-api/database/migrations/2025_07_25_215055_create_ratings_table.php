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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('gif_id'); // Giphy GIF ID
            $table->integer('rating')->between(1, 5); // Rating from 1 to 5
            $table->timestamps();
            
            // Ensure only one rating per user per GIF
            $table->unique(['user_id', 'gif_id']);
            
            // Add indexes for better performance
            $table->index(['gif_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
