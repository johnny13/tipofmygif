<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Rating",
 *     title="Rating",
 *     description="A user's rating for a GIF",
 *     @OA\Property(property="id", type="integer", description="Rating ID"),
 *     @OA\Property(property="user_id", type="integer", description="User ID"),
 *     @OA\Property(property="gif_id", type="string", description="GIF identifier"),
 *     @OA\Property(property="rating", type="integer", minimum=1, maximum=5, description="Rating value (1-5)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
 * )
 */
class Rating extends Model
{
    protected $fillable = [
        'user_id',
        'gif_id',
        'rating'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
