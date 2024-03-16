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
// Route::group(['middleware' => 'auth:api'], function () {
/****** hotel api ******/
Route::prefix('shop')->group(function () {

    Route::get('/all', 'API\ShopController@lists');
    Route::get('/list-all', 'API\ShopController@listWithoutScope');
  
    Route::get('/', 'API\ShopController@index');
    Route::post('/add', 'API\ShopController@store');
    Route::post('/edit', 'API\ShopController@store');
    Route::get('/view/{id?}', 'API\ShopController@show');
    Route::get('/list', 'API\ShopController@list');
    Route::post('/delete/{id?}', 'API\ShopController@destroy');

});
