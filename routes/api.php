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
    'prefix' => 'auth',
    'namespace' => 'User'
], function ($router) {
    Route::post('login', 'UserController@login');
    Route::post('login-with-provider', 'UserController@loginWithProvider');
    Route::post('register', 'UserController@register');

    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::get('logout', 'UserController@logout');
        Route::get('refresh', 'UserController@refresh');
    });
});

// ====================== Games ======================
Route::group(['prefix' => 'games'], function () {
    Route::get('', 'GameController@index');
    Route::get('list', 'GameController@list');
    Route::get('banner-list', 'GameController@listBanner');
    Route::get('hot-list', 'GameController@listHot');
    Route::get('featured-list', 'GameController@featuredList');
    Route::get('promo-feature', 'GameController@promoFeature');
    Route::get('promo-list', 'GameController@promoList');
    Route::get('category/{slug}', 'GameController@gamesByCategory');
});

Route::group(['prefix' => 'game'], function () {
    Route::get('{id}/play-times', 'GameController@increasePlayTimes');
    Route::get('{id}', 'GameController@detail');

    Route::get('{id}/comments', 'GameController@listComments');
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::post('{id}/comment', 'GameController@addComment');
        Route::post('{id}/comment/{commentId}', 'GameController@editComment');
    });
});

// ====================== Categories ======================
Route::group(['prefix' => 'categories'], function () {
    Route::get('', 'CategoryController@index');
    Route::get('list', 'CategoryController@list');
    Route::get('{slug}', 'CategoryController@detail');
});

// ====================== Tags ======================
Route::group(['prefix' => 'tags'], function () {
    Route::get('', 'TagController@index');
    Route::get('list', 'TagController@list');
    Route::get('{slug}', 'TagController@detail');
});

// ====================== Users ======================
Route::group(['prefix' => 'user', 'middleware' => 'jwt.verify', 'namespace' => 'User'], function () {
    Route::get('games', 'UserGameController@list');

    Route::prefix('game')->group(function () {
        Route::post('', 'UserGameController@store');
        Route::post('{gameId}', 'UserGameController@edit');
        Route::post('change-status/{gameId}', 'UserGameController@changeStatus');
        Route::post('upload-thumbnail/{gameId}', 'UserGameController@uploadThumbnail');
        Route::post('upload-game/{gameId}', 'UserGameController@uploadGame');
    });

    Route::get('profile', 'UserController@profile');
    Route::post('upload-avatar', 'UserController@uploadAvatar');
    Route::post('edit', 'UserController@edit');
    Route::post('change-password', 'UserController@changePassword');


});
