<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\SwapController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Topic routes:
Route::get('/topics', [TopicController::class, 'index']);
Route::post('/topics', [TopicController::class, 'store']);
Route::put('/topics/{id}', [TopicController::class, 'update']);
Route::delete('/topics/{id}', [TopicController::class, 'destroy']);

// Reply routes:
Route::get('/topics/{topicId}/replies', [ReplyController::class, 'index']);
Route::post('/topics/{topicId}/replies', [ReplyController::class, 'store']);
Route::put('/replies/{id}', [ReplyController::class, 'update']);
Route::delete('/replies/{id}', [ReplyController::class, 'destroy']);

// Collection routes:
Route::get('/collections', [CollectionController::class, 'index']);
Route::post('/collections', [CollectionController::class, 'store']);
Route::patch('/collections/{id}/rating', [CollectionController::class, 'updateRating']);

// Swap routes:
Route::post('/swaps', [SwapController::class, 'store']);
Route::get('/swaps', [SwapController::class, 'index']);
Route::post('/swaps/{swap}/accept', [SwapController::class, 'accept']);
Route::post('/swaps/{swap}/reject', [SwapController::class, 'reject']);

// Listing, Message, and Review routes:
Route::resource('listings', ListingController::class);
Route::resource('messages', MessageController::class);
Route::resource('reviews', ReviewController::class);

// Chat routes:
Route::get('/chats', [ChatController::class, 'index']);
Route::post('/chats', [ChatController::class, 'store']);