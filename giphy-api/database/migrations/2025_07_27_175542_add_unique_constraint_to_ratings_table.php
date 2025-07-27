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
        Schema::table('ratings', function (Blueprint $table) {
            // Ensure only one rating per user per GIF
            $table->unique(['user_id', 'gif_id'], 'ratings_user_gif_unique');
            
            // Add indexes for better performance if they don't exist
            if (!Schema::hasIndex('ratings', 'ratings_gif_id_index')) {
                $table->index('gif_id', 'ratings_gif_id_index');
            }
            if (!Schema::hasIndex('ratings', 'ratings_user_id_index')) {
                $table->index('user_id', 'ratings_user_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique('ratings_user_gif_unique');
            $table->dropIndexIfExists('ratings_gif_id_index');
            $table->dropIndexIfExists('ratings_user_id_index');
        });
    }
};
