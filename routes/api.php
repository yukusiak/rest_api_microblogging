<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



// Route::apiResource('posts',PostController::class)->middleware(('auth:sanctum'));

Route::apiResource('posts', PostController::class);
Route::post('posts/bulk', [PostController::class, 'bulkStore']);
Route::get('posts/by_user/{id}', [PostController::class, 'findByUserId']);
Route::get('posts/by_tag_ids/{ids}', [PostController::class, 'findByTagIds']);
Route::get('posts/by_tag_name/{names}', [PostController::class, 'findByTagNames']);
Route::get('posts/by_user_rating/{period}', [PostController::class, 'findByRating']);

Route::apiResource('tags', TagController::class);

Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
