<?php

use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MatchOrdersApiController;


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

// Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'show']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Add other protected routes here later, e.g., for orders
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::post('/orders', [OrderApiController::class, 'store']);
    Route::post('/orders/{order}/cancel', [OrderApiController::class, 'cancel']);
    
    Route::get('/match-orders', [MatchOrdersApiController::class, 'index'])->name('orders.match');
    Route::post('/match-orders/{buyOrder}/{sellOrder}', [MatchOrdersApiController::class, 'execute'])->name('match.execute');
    
    Broadcast::routes();
});
