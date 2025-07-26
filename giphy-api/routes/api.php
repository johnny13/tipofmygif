<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GiphyController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\GifsController;

Route::middleware('auth:sanctum')->get('/gifs/search', [GiphyController::class, 'search']);
Route::middleware('auth:sanctum')->apiResource('ratings', RatingController::class);
Route::middleware('auth:sanctum')->apiResource('comments', CommentController::class);

// GIF management routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/gifs/save', [GifsController::class, 'save']);
    Route::get('/gifs/my', [GifsController::class, 'myGifs']);
    Route::get('/gifs/stats', [GifsController::class, 'stats']);
    Route::get('/gifs/check/{giphy_id}', [GifsController::class, 'checkSaved']);
    Route::get('/gifs/{id}', [GifsController::class, 'show']);
    Route::delete('/gifs/{id}', [GifsController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
