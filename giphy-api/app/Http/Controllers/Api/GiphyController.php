<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GiphyService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Giphy",
 *     description="Giphy GIF search and management endpoints"
 * )
 */
class GiphyController extends Controller
{
    protected $giphyService;

    public function __construct(GiphyService $giphyService)
    {
        $this->giphyService = $giphyService;
    }

    /**
     * @OA\Get(
     *     path="/api/gifs/search",
     *     tags={"Giphy"},
     *     summary="Search Giphy GIFs",
     *     description="Search for GIFs on Giphy using a query string",
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search query for GIFs",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of results to return",
     *         required=false,
     *         @OA\Schema(type="integer", default=25)
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Offset for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=0)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
            'limit' => 'integer|min:1|max:100',
            'offset' => 'integer|min:0',
        ]);

        $result = $this->giphyService->search(
            $request->query('query'),
            $request->query('limit', 25),
            $request->query('offset', 0)
        );

        return response()->json($result);
    }
}