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
});

// ====================== Middleware ======================
Route::group(['middleware' => ['jwt.verify']], function () {
    // ====================== Auth ======================
    Route::group(['prefix' => 'auth'], function () {
        Route::get('logout', 'AdminController@logout');
        Route::get('refresh', 'AdminController@refresh');
    });

    // ====================== Games ======================
    Route::group(['prefix' => 'games'], function () {
        Route::get('/list', 'AdminGameController@list');
    });

    Route::group(['prefix' => 'game'], function () {
        Route::post('/change-status/{id}', 'AdminGameController@changeStatus');
    });

    // ====================== Categories ======================

    Route::group(['prefix' => 'category'], function () {
        Route::post('/store', 'AdminCategoryController@store');
        Route::post('/edit/{id}', 'AdminCategoryController@edit');
        Route::post('/change-status/{id}', 'AdminCategoryController@changeStatus');
        Route::get('/{id}', 'AdminCategoryController@show');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/list', 'AdminCategoryController@list');
    });

    Route::get('profile', 'AdminController@profile');
    Route::post('upload-avatar', 'AdminController@uploadAvatar');
    Route::post('edit', 'AdminController@edit');
    Route::post('change-password', 'AdminController@changePassword');
});
