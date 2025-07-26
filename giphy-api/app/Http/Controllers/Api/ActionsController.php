<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gif;
/**
 * @OA\Tag(
 *     name="Actions",
 *     description="GIF actions endpoints"
 * )
 */
class ActionsController extends Controller
{
    public function conversion(Request $request)
    {
        $request->validate([
            'gif_id' => 'required|string',
        ]);

        $gif = Gif::find($request->gif_id);
        
        
    }
}
