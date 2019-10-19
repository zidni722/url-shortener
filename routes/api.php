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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', [
    'uses' => 'Linkshortener\\LinkShortenerController@index'
]);

Route::group(['prefix' => 'shortener-url','middleware' => ['auth.allowed-user']], function () {
    Route::post('/create',[
        'uses' => 'Linkshortener\\LinkShortenerController@create'
    ]);

    Route::delete('/{code}',[
        'uses' => 'Linkshortener\\LinkShortenerController@delete'
    ]);

    Route::put('/{code}',[
        'uses' => 'Linkshortener\\LinkShortenerController@update'
    ]);
});

Route::get('/{code}',[
    'uses' => 'Linkshortener\\LinkShortenerController@redirect'
]);
