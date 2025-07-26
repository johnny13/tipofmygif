<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Gif",
 *     title="Gif",
 *     description="A GIF retrieved from the Giphy API",
 *     @OA\Property(property="id", type="integer", description="Database ID"),
 *     @OA\Property(property="giphy_id", type="string", description="Giphy's unique identifier"),
 *     @OA\Property(property="created_by", type="integer", description="User ID who saved this GIF"),
 *     @OA\Property(property="title", type="string", description="GIF title"),
 *     @OA\Property(property="slug", type="string", description="GIF slug"),
 *     @OA\Property(property="type", type="string", description="Content type (gif, sticker, etc.)"),
 *     @OA\Property(property="rating", type="string", description="Content rating (g, pg, pg-13, r)"),
 *     @OA\Property(property="url", type="string", description="Giphy page URL"),
 *     @OA\Property(property="bitly_url", type="string", description="Bitly shortened URL"),
 *     @OA\Property(property="embed_url", type="string", description="Embed URL for the GIF"),
 *     @OA\Property(property="original_width", type="integer", description="Original image width"),
 *     @OA\Property(property="original_height", type="integer", description="Original image height"),
 *     @OA\Property(property="original_size", type="integer", description="Original file size in bytes"),
 *     @OA\Property(property="original_url", type="string", description="Original GIF URL"),
 *     @OA\Property(property="original_webp", type="string", description="Original WebP URL"),
 *     @OA\Property(property="original_frames", type="integer", description="Number of frames in original"),
 *     @OA\Property(property="original_hash", type="string", description="Original image hash"),
 *     @OA\Property(property="downsized_url", type="string", description="Downsized GIF URL for thumbnails"),
 *     @OA\Property(property="downsized_width", type="integer", description="Downsized image width"),
 *     @OA\Property(property="downsized_height", type="integer", description="Downsized image height"),
 *     @OA\Property(property="downsized_size", type="integer", description="Downsized file size"),
 *     @OA\Property(property="still_480w_url", type="string", description="480w still image URL"),
 *     @OA\Property(property="source_post_url", type="string", description="Source post URL"),
 *     @OA\Property(property="source_tld", type="string", description="Source top-level domain"),
 *     @OA\Property(property="author_username", type="string", description="Author's username"),
 *     @OA\Property(property="author_avatar_url", type="string", description="Author's avatar URL"),
 *     @OA\Property(property="author_profile_url", type="string", description="Author's profile URL"),
 *     @OA\Property(property="author_display_name", type="string", description="Author's display name"),
 *     @OA\Property(property="author_is_verified", type="boolean", description="Whether author is verified"),
 *     @OA\Property(property="import_datetime", type="string", format="date-time", description="Import date from Giphy"),
 *     @OA\Property(property="trending_datetime", type="string", format="date-time", description="Trending date from Giphy"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Record creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Record update timestamp")
 * )
 */
class Gif extends Model
{
    protected $fillable = [
        'giphy_id',
        'created_by',
        'title',
        'slug',
        'type',
        'rating',
        'url',
        'bitly_url',
        'embed_url',
        'original_width',
        'original_height',
        'original_size',
        'original_url',
        'original_webp',
        'original_frames',
        'original_hash',
        'downsized_url',
        'downsized_width',
        'downsized_height',
        'downsized_size',
        'still_480w_url',
        'source_post_url',
        'source_tld',
        'author_username',
        'author_avatar_url',
        'author_profile_url',
        'author_display_name',
        'author_is_verified',
        'import_datetime',
        'trending_datetime',
    ];

    protected $casts = [
        'created_by' => 'integer',
        'original_width' => 'integer',
        'original_height' => 'integer',
        'original_size' => 'integer',
        'original_frames' => 'integer',
        'downsized_width' => 'integer',
        'downsized_height' => 'integer',
        'downsized_size' => 'integer',
        'author_is_verified' => 'boolean',
        'import_datetime' => 'datetime',
        'trending_datetime' => 'datetime',
    ];

    /**
     * Create a Gif from Giphy API response data
     */
    public static function createFromGiphyData(array $giphyData, ?int $userId = null): self
    {
        $images = $giphyData['images'] ?? [];
        $user = $giphyData['user'] ?? null;

        return self::create([
            'giphy_id' => $giphyData['id'],
            'created_by' => $userId,
            'title' => $giphyData['title'] ?? '',
            'slug' => $giphyData['slug'] ?? '',
            'type' => $giphyData['type'] ?? 'gif',
            'rating' => $giphyData['rating'] ?? 'g',
            'url' => $giphyData['url'] ?? '',
            'bitly_url' => $giphyData['bitly_url'] ?? '',
            'embed_url' => $giphyData['embed_url'] ?? '',
            
            // Original image data
            'original_width' => $images['original']['width'] ?? null,
            'original_height' => $images['original']['height'] ?? null,
            'original_size' => $images['original']['size'] ?? null,
            'original_url' => $images['original']['url'] ?? '',
            'original_webp' => $images['original']['webp'] ?? '',
            'original_frames' => $images['original']['frames'] ?? null,
            'original_hash' => $images['original']['hash'] ?? '',
            
            // Downsized data for thumbnails
            'downsized_url' => $images['downsized']['url'] ?? '',
            'downsized_width' => $images['downsized']['width'] ?? null,
            'downsized_height' => $images['downsized']['height'] ?? null,
            'downsized_size' => $images['downsized']['size'] ?? null,
            
            // Still image
            'still_480w_url' => $images['480w_still']['url'] ?? '',
            
            // Source information
            'source_post_url' => $giphyData['source_post_url'] ?? '',
            'source_tld' => $giphyData['source_tld'] ?? '',
            
            // Author information (only if verified)
            'author_username' => $user && ($user['is_verified'] ?? false) ? ($user['username'] ?? '') : '',
            'author_avatar_url' => $user && ($user['is_verified'] ?? false) ? ($user['avatar_url'] ?? '') : '',
            'author_profile_url' => $user && ($user['is_verified'] ?? false) ? ($user['profile_url'] ?? '') : '',
            'author_display_name' => $user && ($user['is_verified'] ?? false) ? ($user['display_name'] ?? '') : '',
            'author_is_verified' => $user ? ($user['is_verified'] ?? false) : false,
            
            // Timestamps
            'import_datetime' => $giphyData['import_datetime'] ?? null,
            'trending_datetime' => $giphyData['trending_datetime'] ?? null,
        ]);
    }

    /**
     * Update Gif from Giphy API response data
     */
    public function updateFromGiphyData(array $giphyData): bool
    {
        $images = $giphyData['images'] ?? [];
        $user = $giphyData['user'] ?? null;

        return $this->update([
            'title' => $giphyData['title'] ?? $this->title,
            'slug' => $giphyData['slug'] ?? $this->slug,
            'type' => $giphyData['type'] ?? $this->type,
            'rating' => $giphyData['rating'] ?? $this->rating,
            'url' => $giphyData['url'] ?? $this->url,
            'bitly_url' => $giphyData['bitly_url'] ?? $this->bitly_url,
            'embed_url' => $giphyData['embed_url'] ?? $this->embed_url,
            
            // Original image data
            'original_width' => $images['original']['width'] ?? $this->original_width,
            'original_height' => $images['original']['height'] ?? $this->original_height,
            'original_size' => $images['original']['size'] ?? $this->original_size,
            'original_url' => $images['original']['url'] ?? $this->original_url,
            'original_webp' => $images['original']['webp'] ?? $this->original_webp,
            'original_frames' => $images['original']['frames'] ?? $this->original_frames,
            'original_hash' => $images['original']['hash'] ?? $this->original_hash,
            
            // Downsized data
            'downsized_url' => $images['downsized']['url'] ?? $this->downsized_url,
            'downsized_width' => $images['downsized']['width'] ?? $this->downsized_width,
            'downsized_height' => $images['downsized']['height'] ?? $this->downsized_height,
            'downsized_size' => $images['downsized']['size'] ?? $this->downsized_size,
            
            // Still image
            'still_480w_url' => $images['480w_still']['url'] ?? $this->still_480w_url,
            
            // Source information
            'source_post_url' => $giphyData['source_post_url'] ?? $this->source_post_url,
            'source_tld' => $giphyData['source_tld'] ?? $this->source_tld,
            
            // Author information (only if verified)
            'author_username' => $user && ($user['is_verified'] ?? false) ? ($user['username'] ?? '') : $this->author_username,
            'author_avatar_url' => $user && ($user['is_verified'] ?? false) ? ($user['avatar_url'] ?? '') : $this->author_avatar_url,
            'author_profile_url' => $user && ($user['is_verified'] ?? false) ? ($user['profile_url'] ?? '') : $this->author_profile_url,
            'author_display_name' => $user && ($user['is_verified'] ?? false) ? ($user['display_name'] ?? '') : $this->author_display_name,
            'author_is_verified' => $user ? ($user['is_verified'] ?? false) : $this->author_is_verified,
            
            // Timestamps
            'import_datetime' => $giphyData['import_datetime'] ?? $this->import_datetime,
            'trending_datetime' => $giphyData['trending_datetime'] ?? $this->trending_datetime,
        ]);
    }

    /**
     * Get the thumbnail URL (prefer downsized, fallback to still image)
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->downsized_url ?: $this->still_480w_url ?: $this->original_url;
    }

    /**
     * Get the best quality URL for display
     */
    public function getDisplayUrlAttribute(): string
    {
        return $this->original_url ?: $this->downsized_url ?: $this->still_480w_url;
    }

    /**
     * Check if the GIF has author information
     */
    public function hasAuthor(): bool
    {
        return $this->author_is_verified && !empty($this->author_username);
    }

    /**
     * Get the aspect ratio of the original image
     */
    public function getAspectRatioAttribute(): ?float
    {
        if ($this->original_width && $this->original_height) {
            return $this->original_width / $this->original_height;
        }
        return null;
    }

    /**
     * Get file size in human readable format
     */
    public function getFormattedSizeAttribute(): string
    {
        if (!$this->original_size) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->original_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'gif_id', 'giphy_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'gif_id', 'giphy_id');
    }

    /**
     * Scope to find by Giphy ID
     */
    public function scopeByGiphyId($query, string $giphyId)
    {
        return $query->where('giphy_id', $giphyId);
    }

    /**
     * Scope to find by rating
     */
    public function scopeByRating($query, string $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to find verified author GIFs
     */
    public function scopeWithVerifiedAuthor($query)
    {
        return $query->where('author_is_verified', true);
    }

    /**
     * Scope to find GIFs by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope to find saved GIFs (has a user)
     */
    public function scopeSaved($query)
    {
        return $query->whereNotNull('created_by');
    }
} 