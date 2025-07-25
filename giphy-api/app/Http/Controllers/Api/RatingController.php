<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Ratings",
 *     description="GIF rating management endpoints"
 * )
 */
class RatingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ratings",
     *     tags={"Ratings"},
     *     summary="Get user's ratings",
     *     description="Retrieve all ratings for the authenticated user",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Rating"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function index()
    {
        return Auth::user()->ratings;
    }

    /**
     * @OA\Post(
     *     path="/api/ratings",
     *     tags={"Ratings"},
     *     summary="Create or update a rating",
     *     description="Create a new rating or update existing one for a GIF",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"gif_id","rating"},
     *             @OA\Property(property="gif_id", type="string", description="GIF identifier"),
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, description="Rating value (1-5)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rating created/updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Rating")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'gif_id' => 'required|string',
            'rating' => 'required|integer|between:1,5',
        ]);

        $rating = Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'gif_id' => $request->gif_id],
            ['rating' => $request->rating]
        );

        return response()->json($rating, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ratings/{rating}",
     *     tags={"Ratings"},
     *     summary="Get a specific rating",
     *     description="Retrieve a specific rating by ID",
     *     @OA\Parameter(
     *         name="rating",
     *         in="path",
     *         description="Rating ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Rating")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rating not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function show(Rating $rating)
    {
        $this->authorize('view', $rating);
        return $rating;
    }

    /**
     * @OA\Put(
     *     path="/api/ratings/{rating}",
     *     tags={"Ratings"},
     *     summary="Update a rating",
     *     description="Update an existing rating",
     *     @OA\Parameter(
     *         name="rating",
     *         in="path",
     *         description="Rating ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating"},
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, description="Rating value (1-5)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rating updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Rating")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rating not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function update(Request $request, Rating $rating)
    {
        $this->authorize('update', $rating);
        $request->validate(['rating' => 'required|integer|between:1,5']);
        $rating->update(['rating' => $request->rating]);
        return $rating;
    }

    /**
     * @OA\Delete(
     *     path="/api/ratings/{rating}",
     *     tags={"Ratings"},
     *     summary="Delete a rating",
     *     description="Delete a specific rating",
     *     @OA\Parameter(
     *         name="rating",
     *         in="path",
     *         description="Rating ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Rating deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rating not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function destroy(Rating $rating)
    {
        $this->authorize('delete', $rating);
        $rating->delete();
        return response()->json(null, 204);
    }
}