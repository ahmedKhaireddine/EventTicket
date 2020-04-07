<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('addresses', AddressController::class)->only(['show', 'store', 'update']);
    Route::apiResource('conversations', ConversationController::class)->only(['index', 'store']);
    Route::apiResource('events', EventController::class)->only(['store', 'update']);
    Route::apiResource('tickets', TicketController::class)->only(['store', 'show', 'update', 'destroy']);
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'update', 'destroy']);
});

Route::apiResource('events', EventController::class)->only(['index', 'show']);
Route::apiResource('users', UserController::class)->only(['store']);
