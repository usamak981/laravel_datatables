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


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function () {

    Route::post('login', 'Api\AuthController@login');
    Route::post('logout', 'Api\AuthController@logout');
    Route::post('refresh', 'Api\AuthController@refresh');
    Route::post('me', 'Api\AuthController@me');
    Route::post('payload', 'Api\AuthController@payload');
    Route::post('create', 'Api\AuthController@create');
    Route::post('list', 'Api\AuthController@list');
    Route::post('show', 'Api\AuthController@show');
    Route::post('update', 'Api\AuthController@update');
    Route::post('destroy', 'Api\AuthController@destroy');

});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
