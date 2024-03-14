<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "Api" middleware group. Enjoy building your Api!
|
*/

// Route::middleware('auth:Api')->get('/restaurant', function (Request $request) {
//     return $request->user();
// });



Route::prefix('food')->group(function () {
    Route::prefix('category')->group(function () {
        Route::get('/', 'Api\FoodCategoryController@index');
        Route::post('/add', 'Api\FoodCategoryController@store');
        Route::post('/update', 'Api\FoodCategoryController@store');
        Route::get('/view/{id?}', 'Api\FoodCategoryController@show');
        Route::get('/list', 'Api\FoodCategoryController@list');
        Route::resource('/delete', 'Api\FoodCategoryController', [
            'as' => 'category.'
        ]);
    });

    Route::prefix('varient')->group(function () {
        Route::get('/', 'Api\VarientController@index');
        Route::post('/add', 'Api\VarientController@store');
        Route::post('/update', 'Api\VarientController@store');
        Route::get('/view/{id?}', 'Api\VarientController@show');
        Route::get('/list', 'Api\VarientController@list');
        Route::resource('/delete', 'Api\VarientController', [
            'as' => 'varient.'
        ]);
    });

    Route::prefix('optional-items')->group(function () {
        Route::get('/', 'Api\OptionalItemController@index');
        Route::post('/add', 'Api\optionalItemController@store');
        Route::post('/update', 'Api\optionalItemController@store');
    });

    Route::prefix('item')->group(function () {
        Route::get('/', 'Api\ItemController@index');
        Route::post('/add', 'Api\ItemController@store');
        Route::post('/update', 'Api\ItemController@store');
        Route::get('/view/{id?}', 'Api\ItemController@show');
        Route::get('/list', 'Api\ItemController@list');
        Route::post('/price', 'Api\ItemController@priceUpdated');
        Route::post('/list/price', 'Api\ItemController@listWithPrice');
        Route::get('/list/pos', 'Api\ItemController@listForPos');
        Route::resource('/delete', 'Api\ItemController', [
            'as' => 'item.'
        ]);
    });
    Route::prefix('order')->group(function () {
        Route::get('/', 'Api\OrderController@index');
        Route::post('/add', 'Api\OrderController@store');
        Route::post('/payment', 'Api\OrderController@makePaymentRequest');
        Route::post('/cancel/{id}', 'Api\OrderController@cancelOrder');
        Route::post('/update-status/{id}', 'Api\OrderController@updateOrderStatus');
        Route::get('/view/{id?}', 'Api\OrderController@show');
        Route::post('/invoice', 'Api\OrderController@createInvoice');
        Route::post('/invoice/update/{id}', 'Api\OrderController@updateInvoice');
        Route::post('/invoice/pay', 'Api\OrderController@payInvoice');
        Route::resource('/delete', 'Api\OrderController', [
            'as' => 'order.'
        ]);
    });
});


Route::prefix('restaurant')->group(function () {
    Route::get('/', 'Api\RestaurantController@index');
    Route::post('/price', 'Api\RestaurantController@price');
    Route::post('/order', 'Api\OrderController@store');
    Route::post('/order/update/{id}', 'Api\OrderController@update');
    Route::get('/{restaurant}/taxes', 'Api\RestaurantController@taxes');
    Route::post('/{restaurant}/tax', 'Api\RestaurantController@updateTax');
    Route::post('/{restaurant}/update-item-status', 'Api\RestaurantController@updateItemStatus');
    Route::post('/{restaurant}/variant-options', 'Api\RestaurantController@storeVariantsOptions');
});
