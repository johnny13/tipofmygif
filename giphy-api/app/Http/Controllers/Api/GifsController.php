<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gif;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="GIFs",
 *     description="API Endpoints for managing saved GIFs"
 * )
 */
class GifsController extends Controller
{
    /**
     * Save a GIF from Giphy API response
     * 
     * @OA\Post(
     *     path="/api/gifs/save",
     *     summary="Save a GIF to user's collection",
     *     tags={"GIFs"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"giphy_id", "giphy_data"},
     *             @OA\Property(property="giphy_id", type="string", description="Giphy's unique identifier"),
     *             @OA\Property(property="giphy_data", type="object", description="Complete Giphy API response object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="GIF saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="GIF saved successfully"),
     *             @OA\Property(property="gif", ref="#/components/schemas/Gif")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="GIF already saved",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="GIF already saved"),
     *             @OA\Property(property="gif", ref="#/components/schemas/Gif")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'giphy_id' => 'required|string|max:255',
            'giphy_data' => 'required|array'
        ]);

        $userId = auth()->id();

        // Check if user already saved this GIF
        $existingGif = Gif::byGiphyId($request->giphy_id)
            ->byUser($userId)
            ->first();

        if ($existingGif) {
            return response()->json([
                'message' => 'GIF already saved',
                'gif' => $existingGif
            ], 409);
        }

        // Save the GIF
        $gif = Gif::createFromGiphyData($request->giphy_data, $userId);

        return response()->json([
            'message' => 'GIF saved successfully',
            'gif' => $gif->load(['user', 'ratings', 'comments'])
        ], 201);
    }

    /**
     * Get user's saved GIFs
     * 
     * @OA\Get(
     *     path="/api/gifs/my",
     *     summary="Get user's saved GIFs",
     *     tags={"GIFs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         description="Filter by content rating",
     *         required=false,
     *         @OA\Schema(type="string", enum={"g", "pg", "pg-13", "r"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User's saved GIFs",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Gif")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function myGifs(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        $query = Gif::byUser($userId)
            ->with(['user', 'ratings', 'comments'])
            ->orderBy('created_at', 'desc');

        // Filter by rating if provided
        if ($request->has('rating')) {
            $query->byRating($request->rating);
        }

        $gifs = $query->paginate($request->get('per_page', 20));

        return response()->json($gifs);
    }

    /**
     * Get a specific saved GIF
     * 
     * @OA\Get(
     *     path="/api/gifs/{id}",
     *     summary="Get a specific saved GIF",
     *     tags={"GIFs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="GIF ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="GIF details",
     *         @OA\JsonContent(ref="#/components/schemas/Gif")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="GIF not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $gif = Gif::byUser(auth()->id())
            ->with(['user', 'ratings', 'comments'])
            ->findOrFail($id);

        return response()->json($gif);
    }

    /**
     * Delete a saved GIF
     * 
     * @OA\Delete(
     *     path="/api/gifs/{id}",
     *     summary="Delete a saved GIF",
     *     tags={"GIFs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="GIF ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="GIF deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="GIF removed from your collection")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="GIF not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $gif = Gif::byUser(auth()->id())
            ->findOrFail($id);

        $gif->delete();

        return response()->json([
            'message' => 'GIF removed from your collection'
        ]);
    }

    /**
     * Check if a GIF is saved by the user
     * 
     * @OA\Get(
     *     path="/api/gifs/check/{giphy_id}",
     *     summary="Check if a GIF is saved by the user",
     *     tags={"GIFs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="giphy_id",
     *         in="path",
     *         description="Giphy's unique identifier",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Check result",
     *         @OA\JsonContent(
     *             @OA\Property(property="saved", type="boolean"),
     *             @OA\Property(property="gif", ref="#/components/schemas/Gif", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function checkSaved(string $giphyId): JsonResponse
    {
        $gif = Gif::byGiphyId($giphyId)
            ->byUser(auth()->id())
            ->first();

        return response()->json([
            'saved' => $gif !== null,
            'gif' => $gif
        ]);
    }

    /**
     * Get statistics about user's saved GIFs
     * 
     * @OA\Get(
     *     path="/api/gifs/stats",
     *     summary="Get statistics about user's saved GIFs",
     *     tags={"GIFs"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="GIF statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_saved", type="integer"),
     *             @OA\Property(property="by_rating", type="object"),
     *             @OA\Property(property="with_verified_authors", type="integer"),
     *             @OA\Property(property="total_size", type="string"),
     *             @OA\Property(property="average_size", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function stats(): JsonResponse
    {
        $userId = auth()->id();
        
        $userGifs = Gif::byUser($userId);
        
        $stats = [
            'total_saved' => $userGifs->count(),
            'by_rating' => $userGifs->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray(),
            'with_verified_authors' => $userGifs->withVerifiedAuthor()->count(),
            'total_size' => $this->formatBytes($userGifs->sum('original_size')),
            'average_size' => $this->formatBytes($userGifs->avg('original_size'))
        ];

        return response()->json($stats);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes): string
    {
        if ($bytes === null || $bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $unit = 0;

        while ($bytes >= 1024 && $unit < count($units) - 1) {
            $bytes /= 1024;
            $unit++;
        }

        return round($bytes, 2) . ' ' . $units[$unit];
    }
}
