<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Comments",
 *     description="GIF comment management endpoints"
 * )
 */
class CommentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/comments",
     *     tags={"Comments"},
     *     summary="Get comments for a GIF",
     *     description="Retrieve all comments for a specific GIF",
     *     @OA\Parameter(
     *         name="gif_id",
     *         in="query",
     *         description="GIF identifier to get comments for",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - missing gif_id parameter"
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
        return Comment::where('gif_id', request('gif_id'))->with('user')->get();
    }

    /**
     * @OA\Post(
     *     path="/api/comments",
     *     tags={"Comments"},
     *     summary="Create a new comment",
     *     description="Create a new comment for a GIF",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"gif_id","comment"},
     *             @OA\Property(property="gif_id", type="string", description="GIF identifier"),
     *             @OA\Property(property="comment", type="string", description="Comment text", minLength=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
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
            'comment' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'gif_id' => $request->gif_id,
            'comment' => $request->comment,
        ]);

        return response()->json($comment->load('user'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/comments/{comment}",
     *     tags={"Comments"},
     *     summary="Get a specific comment",
     *     description="Retrieve a specific comment by ID",
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         description="Comment ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
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
     *         description="Comment not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function show(Comment $comment)
    {
        $this->authorize('view', $comment);
        return $comment->load('user');
    }

    /**
     * @OA\Put(
     *     path="/api/comments/{comment}",
     *     tags={"Comments"},
     *     summary="Update a comment",
     *     description="Update an existing comment",
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         description="Comment ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"comment"},
     *             @OA\Property(property="comment", type="string", description="Updated comment text", minLength=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
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
     *         description="Comment not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        $request->validate(['comment' => 'required|string']);
        $comment->update(['comment' => $request->comment]);
        return $comment->load('user');
    }

    /**
     * @OA\Delete(
     *     path="/api/comments/{comment}",
     *     tags={"Comments"},
     *     summary="Delete a comment",
     *     description="Delete a specific comment",
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         description="Comment ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Comment deleted successfully"
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
     *         description="Comment not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->json(null, 204);
    }
}