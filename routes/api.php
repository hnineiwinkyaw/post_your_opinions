<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\ApiAuthController;
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

Route::middleware('auth:api')->group(function() {
	Route::post('/logout',[ApiAuthController::class, 'logout'])->name('logout.api');
});

Route::get('blogs',[BlogController::class, 'index'])->middleware('auth:api');
Route::get('blogs/{blog}',[BlogController::class, 'show']);
Route::post('blogs',[BlogController::class, 'store']);
Route::put('blogs/{blog}',[BlogController::class, 'update']);
Route::delete('blogs/{blog}',[BlogController::class, 'delete']);

Route::group(['middleware' => ['cors','json.response']], function() {
	Route::post('/login',[ApiAuthController::class, 'login'])->name('login.api');
	Route::post('/register',[ApiAuthController::class, 'register'])->name('register.api');
	
});
