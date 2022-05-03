<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
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
    Route::post('login', 'App\Http\Controllers\UserController@login');
    Route::post('register', 'App\Http\Controllers\UserController@register');
    Route::get('/products', 'App\Http\Controllers\ProductController@index');
    Route::get('/products/flashsale', 'App\Http\Controllers\ProductController@flashsale');
    Route::post('/upload-file', 'App\Http\Controllers\ProductController@uploadFile');
    Route::get('/products/{product}', 'App\Http\Controllers\ProductController@show');

    Route::group(['middleware' => 'auth:api'], function(){
        Route::get('/users','App\Http\Controllers\UserController@index');
        Route::get('users/{user}','App\Http\Controllers\UserController@show');
        Route::patch('users/{user}','App\Http\Controllers\UserController@update');
        Route::get('users/{user}/orders','App\Http\Controllers\UserController@showOrders');
        Route::patch('products/{product}/units/add','App\Http\Controllers\ProductController@updateUnits');
        Route::patch('products/{product}/flashsale/change','App\Http\Controllers\ProductController@updateFlashsale');
        Route::patch('orders/{order}/deliver','App\Http\Controllers\OrderController@deliverOrder');
        Route::resource('/orders', 'App\Http\Controllers\OrderController');
        Route::resource('/products', 'App\Http\Controllers\ProductController')->except(['index','show']);
    });
