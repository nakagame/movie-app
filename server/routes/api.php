<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/reviews/{media_type}/{media_id}', [ReviewController::class, 'index']);
Route::post('/reviews', [ReviewController::class, 'store']);
Route::put('/review/{review}', [ReviewController::class, 'update']);
Route::delete('/review/{review}', [ReviewController::class, 'destroy']);
Route::get('/review/{review}', [ReviewController::class, 'show']);

// Comments
Route::post('comments', [CommentController::class, 'store']);
Route::put('/comments/{comment}', [CommentController::class, 'update']);
Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

// Favorites
Route::get('/favorites', [FavoriteController::class, 'index']);
Route::get('/favorites/status', [FavoriteController::class, 'checkFavoriteStatus']);
Route::post('/favorites', [FavoriteController::class, 'toggleFavorite']);