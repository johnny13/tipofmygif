<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GiphyController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\CommentController;

Route::middleware('auth:sanctum')->get('/gifs/search', [GiphyController::class, 'search']);
Route::middleware('auth:sanctum')->apiResource('ratings', RatingController::class);
Route::middleware('auth:sanctum')->apiResource('comments', CommentController::class);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
