<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Comment",
 *     title="Comment",
 *     description="A user's comment on a GIF",
 *     @OA\Property(property="id", type="integer", description="Comment ID"),
 *     @OA\Property(property="user_id", type="integer", description="User ID"),
 *     @OA\Property(property="gif_id", type="string", description="GIF identifier"),
 *     @OA\Property(property="comment", type="string", description="Comment text"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp"),
 *     @OA\Property(property="user", type="object", description="User who made the comment", ref="#/components/schemas/User")
 * )
 */
class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'gif_id',
        'comment'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 