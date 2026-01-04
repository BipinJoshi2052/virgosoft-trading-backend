<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\MatchOrdersController;
use App\Http\Controllers\Web\OrderBookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Route::get('/', function () {
    //     return view('welcome');
    // });
    
    Route::get('/', function () {
        // return view('app'); // to load vue files
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile');
});

Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/symbol/{symbol}', [OrderController::class, 'bySymbol'])
        ->name('orders.bySymbol');

    // Orders UI
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // Temporary matching preview
    Route::get('/match-orders', [MatchOrdersController::class, 'index'])->name('orders.match');
    Route::post('/match-orders/{buyOrder}/{sellOrder}', [MatchOrdersController::class, 'execute'])->name('match.execute');

    // Cancel order
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])
    ->middleware('auth')
    ->name('orders.cancel');
    
    Broadcast::routes();
});

// Route::middleware(['auth'])->group(function () {
//     Route::get('/orderbook', [OrderBookController::class, 'index'])
//         ->name('orderbook.index');

//     Route::get('/orderbook/{symbol}', [OrderBookController::class, 'show'])
//         ->name('orderbook.show');
// });