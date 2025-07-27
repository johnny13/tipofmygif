<?php

namespace Tests\Feature;

use App\Models\Gif;
use App\Models\User;
use App\Models\Comment;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GifCommentsAndRatingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_my_gifs_endpoint_includes_comments_and_ratings_info()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a GIF for the user
        $gif = Gif::createFromGiphyData([
            'id' => 'test_gif_123',
            'title' => 'Test GIF',
            'slug' => 'test-gif',
            'type' => 'gif',
            'rating' => 'g',
            'url' => 'https://giphy.com/gifs/test',
            'images' => [
                'original' => [
                    'url' => 'https://media.giphy.com/media/test/giphy.gif',
                    'width' => 480,
                    'height' => 360,
                    'size' => 1024000
                ],
                'downsized' => [
                    'url' => 'https://media.giphy.com/media/test/giphy-downsized.gif',
                    'width' => 200,
                    'height' => 150,
                    'size' => 256000
                ],
                '480w_still' => [
                    'url' => 'https://media.giphy.com/media/test/480w_still.jpg'
                ]
            ]
        ], $user->id);

        // Add a comment to the GIF
        Comment::create([
            'user_id' => $user->id,
            'gif_id' => $gif->giphy_id,
            'comment' => 'This is a test comment'
        ]);

        // Add a rating to the GIF
        Rating::create([
            'user_id' => $user->id,
            'gif_id' => $gif->giphy_id,
            'rating' => 5
        ]);

        // Make request to myGifs endpoint
        $response = $this->actingAs($user)
            ->getJson('/api/gifs/my');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        
        $gifData = $data[0];
        
        // Check that comments and ratings info is included
        $this->assertEquals(1, $gifData['comments_count']);
        $this->assertTrue($gifData['has_comments']);
        $this->assertEquals(5, $gifData['user_rating']);
        $this->assertTrue($gifData['has_user_rating']);
        $this->assertEquals(5.0, $gifData['average_rating']);
        $this->assertEquals(1, $gifData['ratings_count']);
        
        // Check that comments and ratings arrays are included
        $this->assertArrayHasKey('comments', $gifData);
        $this->assertArrayHasKey('ratings', $gifData);
        $this->assertCount(1, $gifData['comments']);
        $this->assertCount(1, $gifData['ratings']);
    }

    public function test_show_gif_endpoint_includes_comments_and_ratings_info()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a GIF for the user
        $gif = Gif::createFromGiphyData([
            'id' => 'test_gif_456',
            'title' => 'Test GIF 2',
            'slug' => 'test-gif-2',
            'type' => 'gif',
            'rating' => 'pg',
            'url' => 'https://giphy.com/gifs/test2',
            'images' => [
                'original' => [
                    'url' => 'https://media.giphy.com/media/test2/giphy.gif',
                    'width' => 480,
                    'height' => 360,
                    'size' => 1024000
                ],
                'downsized' => [
                    'url' => 'https://media.giphy.com/media/test2/giphy-downsized.gif',
                    'width' => 200,
                    'height' => 150,
                    'size' => 256000
                ],
                '480w_still' => [
                    'url' => 'https://media.giphy.com/media/test2/480w_still.jpg'
                ]
            ]
        ], $user->id);

        // Add multiple comments to the GIF
        Comment::create([
            'user_id' => $user->id,
            'gif_id' => $gif->giphy_id,
            'comment' => 'First comment'
        ]);
        
        Comment::create([
            'user_id' => $user->id,
            'gif_id' => $gif->giphy_id,
            'comment' => 'Second comment'
        ]);

        // Add a rating to the GIF
        Rating::create([
            'user_id' => $user->id,
            'gif_id' => $gif->giphy_id,
            'rating' => 4
        ]);

        // Make request to show endpoint
        $response = $this->actingAs($user)
            ->getJson("/api/gifs/{$gif->id}");

        $response->assertStatus(200);
        
        $gifData = $response->json();
        
        // Check that comments and ratings info is included
        $this->assertEquals(2, $gifData['comments_count']);
        $this->assertTrue($gifData['has_comments']);
        $this->assertEquals(4, $gifData['user_rating']);
        $this->assertTrue($gifData['has_user_rating']);
        $this->assertEquals(4.0, $gifData['average_rating']);
        $this->assertEquals(1, $gifData['ratings_count']);
        
        // Check that comments and ratings arrays are included
        $this->assertArrayHasKey('comments', $gifData);
        $this->assertArrayHasKey('ratings', $gifData);
        $this->assertCount(2, $gifData['comments']);
        $this->assertCount(1, $gifData['ratings']);
    }

    public function test_gif_without_comments_and_ratings()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a GIF for the user without comments or ratings
        $gif = Gif::createFromGiphyData([
            'id' => 'test_gif_789',
            'title' => 'Test GIF 3',
            'slug' => 'test-gif-3',
            'type' => 'gif',
            'rating' => 'g',
            'url' => 'https://giphy.com/gifs/test3',
            'images' => [
                'original' => [
                    'url' => 'https://media.giphy.com/media/test3/giphy.gif',
                    'width' => 480,
                    'height' => 360,
                    'size' => 1024000
                ],
                'downsized' => [
                    'url' => 'https://media.giphy.com/media/test3/giphy-downsized.gif',
                    'width' => 200,
                    'height' => 150,
                    'size' => 256000
                ],
                '480w_still' => [
                    'url' => 'https://media.giphy.com/media/test3/480w_still.jpg'
                ]
            ]
        ], $user->id);

        // Make request to myGifs endpoint
        $response = $this->actingAs($user)
            ->getJson('/api/gifs/my');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        
        $gifData = $data[0];
        
        // Check that comments and ratings info shows no activity
        $this->assertEquals(0, $gifData['comments_count']);
        $this->assertFalse($gifData['has_comments']);
        $this->assertNull($gifData['user_rating']);
        $this->assertFalse($gifData['has_user_rating']);
        $this->assertNull($gifData['average_rating']);
        $this->assertEquals(0, $gifData['ratings_count']);
        
        // Check that comments and ratings arrays are empty
        $this->assertArrayHasKey('comments', $gifData);
        $this->assertArrayHasKey('ratings', $gifData);
        $this->assertCount(0, $gifData['comments']);
        $this->assertCount(0, $gifData['ratings']);
    }
} 