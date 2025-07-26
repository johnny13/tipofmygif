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
        Schema::create('gifs', function (Blueprint $table) {
            $table->id();
            
            // Giphy identifier and basic info
            $table->string('giphy_id')->unique();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('type')->default('gif');
            $table->string('rating')->default('g');
            
            // URLs
            $table->text('url')->nullable();
            $table->text('bitly_url')->nullable();
            $table->text('embed_url')->nullable();
            
            // Original image data
            $table->integer('original_width')->nullable();
            $table->integer('original_height')->nullable();
            $table->bigInteger('original_size')->nullable();
            $table->text('original_url')->nullable();
            $table->text('original_webp')->nullable();
            $table->integer('original_frames')->nullable();
            $table->string('original_hash')->nullable();
            
            // Downsized data for thumbnails
            $table->text('downsized_url')->nullable();
            $table->integer('downsized_width')->nullable();
            $table->integer('downsized_height')->nullable();
            $table->bigInteger('downsized_size')->nullable();
            
            // Still image
            $table->text('still_480w_url')->nullable();
            
            // Source information
            $table->text('source_post_url')->nullable();
            $table->string('source_tld')->nullable();
            
            // Author information (only stored if verified)
            $table->string('author_username')->nullable();
            $table->text('author_avatar_url')->nullable();
            $table->text('author_profile_url')->nullable();
            $table->string('author_display_name')->nullable();
            $table->boolean('author_is_verified')->default(false);
            
            // Giphy timestamps
            $table->timestamp('import_datetime')->nullable();
            $table->timestamp('trending_datetime')->nullable();
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['giphy_id']);
            $table->index(['rating']);
            $table->index(['author_is_verified']);
            $table->index(['import_datetime']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifs');
    }
};
