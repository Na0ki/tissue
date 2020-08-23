<?php

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/checkin/card', 'Api\\CardController@show');

Route::middleware('auth')->group(function () {
    Route::post('/likes', 'Api\\LikeController@store');
    Route::delete('/likes/{id}', 'Api\\LikeController@destroy');
});
