<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// ====================== User Authentication ======================
Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'UserController@login');
    Route::post('register', 'UserController@register');

    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::get('logout', 'UserController@logout');
        Route::get('refresh', 'UserController@refresh');
        Route::get('profile', 'UserController@profile');
    });
});

// ====================== Games ======================
Route::group(['prefix' => 'games'], function () {
    Route::get('', 'GameController@index');
    Route::get('/list', 'GameController@list');
    Route::get('/featured-list', 'GameController@featuredList');
    Route::get('/promo-feature', 'GameController@promoFeature');
    Route::get('/promo-list', 'GameController@promoList');

    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::get('/user/{id}', 'GameController@listByUser');
    });
});

Route::group(['prefix' => 'game'], function () {
    Route::get('/{id}', 'GameController@detail');

    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::post('/store', 'GameController@store');
        Route::post('/edit/{id}', 'GameController@edit');
        Route::post('/status/{id}', 'GameController@changeStatus');
    });
});

// ====================== Categories ======================
Route::group(['prefix' => 'categories'], function () {
    Route::get('', 'CategoryController@index');
    Route::get('/list', 'CategoryController@list');
    Route::get('/{slug}', 'CategoryController@detail');
});
