<?php

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


// ====================== Admin Authentication ======================

Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::get('test', 'AdminController@test');
    Route::post('login', 'AdminController@login');
    Route::post('register', 'AdminController@register');

    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::get('logout', 'AdminController@logout');
        Route::get('refresh', 'AdminController@refresh');
        Route::get('profile', 'AdminController@profile');
    });
});

// ====================== Admin Games ======================
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::group(['prefix' => 'games'], function () {
        Route::get('/list', 'AdminGameController@list');
    });

    Route::group(['prefix' => 'game'], function () {
        Route::post('/change-status/{id}', 'AdminGameController@changeStatus');
    });
});
