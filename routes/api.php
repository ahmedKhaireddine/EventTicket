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

Auth::routes(['verify' => true]);

Route::post('login', 'Auth\LoginController@login')->name('login');

Route::middleware('auth:api')->group(function () {

    Route::post('create', 'Auth\RegisterController@register')->name('admin.create')->middleware('can:create-admin,App\User');

    Route::post('signout', function (Request $request) {
        $request->user()->token()->revoke();
        return response()->json([], 204);
    });

    Route::get('user', function (Request $request) {
        return new App\Http\Resources\UserResource($request->user());
    });

    Route::apiResource('addresses', AddressController::class)->only(['show', 'store', 'update'])->middleware('verified');
    Route::apiResource('conversations', ConversationController::class)->only(['index', 'store']);
    Route::apiResource('events', EventController::class)->only(['store', 'update'])->middleware('verified');
    Route::apiResource('tickets', TicketController::class)->only(['store', 'show', 'update', 'destroy'])->middleware('verified');
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'update', 'destroy']);
});

Route::apiResource('events', EventController::class)->only(['index', 'show']);
Route::apiResource('users', UserController::class)->only(['store']);
