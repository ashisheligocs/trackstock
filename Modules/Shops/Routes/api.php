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
Route::prefix('hotel')->group(function () {

    Route::get('/all', 'API\ShopController@lists');
    Route::get('/list-all', 'API\ShopController@listWithoutScope');
    Route::prefix('category')->group(function () {
        Route::get('/', 'API\HotelCategoryController@index');
        Route::post('/add', 'API\HotelCategoryController@store');
        Route::get('/edit/{id?}', 'API\HotelCategoryController@show');
        Route::post('/update', 'API\HotelCategoryController@store');
        Route::get('/view/{id?}', 'API\HotelCategoryController@show');
        Route::get('/list', 'API\HotelCategoryController@list');
        Route::resource('/delete', 'API\HotelCategoryController', [
            'as' => 'category'
        ]);
    });
    Route::prefix('facility')->group(function () {
        Route::get('/', 'API\HotelFacilityController@index');
        Route::post('/add', 'API\HotelFacilityController@store');
        Route::get('/edit/{id?}', 'API\HotelFacilityController@show');
        Route::post('/update', 'API\HotelFacilityController@store');
        Route::get('/view/{id?}', 'API\HotelFacilityController@show');
        Route::get('/list', 'API\HotelFacilityController@list');
        Route::resource('/delete', 'API\HotelFacilityController', [
            'as' => 'facility.'
        ]);
    });
    Route::get('/', 'API\HotelController@index');
    Route::post('/add', 'API\HotelController@store');
    Route::post('/edit', 'API\HotelController@store');
    Route::get('/view/{id?}', 'API\HotelController@show');
    Route::get('/list', 'API\HotelController@list');
    Route::post('/delete/{id?}', 'API\HotelController@destroy');
    Route::get('/get-floor/{id?}', 'API\HotelController@getFloor');
    Route::get('/get-rooms/{id?}', 'API\HotelController@getNoOfRooms');
    Route::post('/get-hotel-cat', 'API\HotelController@getHotelCategoryWise');
    Route::post('/get-single-room', 'API\HotelController@getRoomsDetails');
    Route::post('/get-single-room-price', 'API\HotelController@getRoomsDetailsprice');
    // api
    Route::get('/get-room-price', 'API\HotelController@getRoomsprice_api');

    /*** add room facility ****/
    Route::prefix('room')->group(function () {
        Route::prefix('meal')->group(function () {
            Route::post('/', 'API\RoomsController@getHotelMeals');
            Route::post('hotel/', 'API\RoomsController@getHotelMealslist');
            Route::post('/avalable', 'API\RoomsController@getAvailableHotelMeals');
            Route::post('/add', 'API\RoomsController@addHotelMeals');
            Route::post('/view', 'API\RoomsController@viewHotelMeals');
            Route::post('/view/price', 'API\RoomsController@viewHotelMealsPrice');
            Route::post('/view/all', 'API\RoomsController@getHotelMealslist');
            Route::post('/view/all/name', 'API\RoomsController@getHotelMealslistOnlyMealName');
            Route::post('/status', 'API\RoomsController@updateStatus');
            // api
            Route::post('/view/meal_price', 'API\RoomsController@meal_mealprice');
        });
        Route::post('/getRoomsDetailsAppprices', 'API\RoomsController@getRoomsDetailsAppprices');
    });
});

/*** room api ****/
Route::prefix('room')->group(function () {
    Route::prefix('category')->group(function () {
        Route::get('/', 'API\RoomCategoryController@index');
        // api
        Route::get('get_hotel_category/', 'API\RoomCategoryController@get_category');

        Route::get('/hotel', 'API\RoomCategoryController@hotelRoom');
        Route::get('/hotels', 'API\RoomCategoryController@hotelRoomCategory');
        Route::post('/add_category', 'API\RoomCategoryController@addHotelRoomCategory');
        Route::post('/edit_category', 'API\RoomCategoryController@editHotelRoomCategory');
        Route::post('/add', 'API\RoomCategoryController@store');
        Route::post('/add/facility', 'API\RoomCategoryController@saveFacility');
        Route::post('/lsit/facility/price', 'API\RoomCategoryController@listWithPrice');
        Route::post('/lsit/facility/price/hotel', 'API\RoomCategoryController@listWithPriceHotel');
        Route::post('/lsit/facility/price/hotel/singel', 'API\RoomCategoryController@listWithPriceHotelsingel');
        Route::post('/lsit/facility/price/hotel/singel/price', 'API\RoomCategoryController@listWithPriceHotelsingelWith');
        Route::post('/lsit/facility/price/hotel/service', 'API\RoomCategoryController@getAdditionalServiceWithPrice');
        Route::post('/edit', 'API\RoomCategoryController@store');
        Route::get('/view/{id?}', 'API\RoomCategoryController@show');
        Route::get('/list', 'API\RoomCategoryController@list');
        Route::post('/delete/{id?}', 'API\RoomCategoryController@destroy');
    });
    Route::prefix('facility')->group(function () {
        Route::get('/', 'API\RoomFacilityController@index');
        Route::post('/add', 'API\RoomFacilityController@store');
        Route::post('/edit', 'API\RoomFacilityController@store');
        Route::get('/view/{id?}', 'API\RoomFacilityController@show');
        Route::get('/list', 'API\RoomFacilityController@list');
        Route::post('/delete/{id?}', 'API\RoomFacilityController@destroy');
    });
    Route::get('/', 'API\RoomsController@index');
    Route::get('/occupied', 'API\RoomsController@occupiedRooms');
    Route::get('/roomcheckin', 'API\RoomsController@checkInRooms');
    Route::post('/add', 'API\RoomsController@store');
    Route::post('/edit', 'API\RoomsController@store');
    Route::get('/view/{id?}', 'API\RoomsController@show');
    Route::get('/list', 'API\RoomsController@list');
    Route::get('/list-with-status', 'API\RoomsController@listWithStatus');
    Route::post('/delete/{id?}', 'API\RoomsController@destroy');
});

// floor type api
Route::prefix('floor')->group(function () {
    Route::get('/', 'API\FloorController@index');
    Route::post('/add', 'API\FloorController@store');
    Route::post('/edit', 'API\FloorController@store');
    Route::get('/view/{id?}', 'API\FloorController@show');
    Route::get('/list', 'API\FloorController@list');
    Route::post('/delete/{id?}', 'API\FloorController@destroy');
});

// meal plan api
Route::prefix('meal')->group(function () {
    Route::get('/', 'API\MealController@index');
    Route::post('/add', 'API\MealController@store');
    Route::post('/edit', 'API\MealController@store');
    Route::get('/view/{id?}', 'API\MealController@show');
    Route::get('/list', 'API\MealController@list');
    Route::post('/delete/{id?}', 'API\MealController@destroy');
});

/*** bed type apis */
Route::prefix('bed')->group(function () {
    Route::get('/', 'API\BedController@index');
    Route::post('/add', 'API\BedController@store');
    Route::post('/edit', 'API\BedController@store');
    Route::get('/view/{id?}', 'API\BedController@show');
    Route::get('/list', 'API\BedController@list');
    Route::post('/delete/{id?}', 'API\BedController@destroy');
});

Route::prefix('booking')->group(function () {
    Route::prefix('type')->group(function () {
        Route::get('/', 'API\BookingTypeController@index');
        Route::post('/add', 'API\BookingTypeController@store');
        Route::post('/edit', 'API\BookingTypeController@store');
        Route::get('/view/{id?}', 'API\BookingTypeController@show');
        Route::get('/list', 'API\BookingTypeController@list');
        // new api
        Route::get('/agent-booking-list', 'API\BookingTypeController@booking_sourcecount');
        Route::get('/booking_hold_status', 'API\BookingTypeController@booking_hold_status');
        //finshed
        Route::post('/delete/{id?}', 'API\BookingTypeController@destroy');
    });

    Route::post('/', 'API\BookingController@index');
    Route::post('/add', 'API\BookingController@store');
    Route::post('/app/save_booking',  'API\BookingController@save_booking_app');
    Route::post('/check-in', 'API\BookingController@store');
    Route::get('/check-out/view/{booking?}', 'API\BookingController@checkoutView');
    Route::get('/restaurant-orders/{booking?}', 'API\BookingController@getRestaurantOrderDetails');
    Route::get('/check-out/room/{id?}', 'API\BookingController@checkOutRoomView');
    Route::get('/check-out/list/{id?}', 'API\BookingController@checkOutList');
    Route::post('/check-out/{booking?}', 'API\BookingController@checkOut');
    Route::post('/check-out', 'API\BookingController@store');
    Route::post('/edit', 'API\BookingController@store');
    Route::post('/advance-payment', 'API\BookingController@advancePayment');
    Route::post('/amend-stay', 'API\BookingController@amendStay');
    Route::post('/additional-service', 'API\BookingController@additionalService');
    Route::post('/room-move', 'API\BookingController@roomMove');
    Route::get('/view/{booking?}', 'API\BookingController@show');
    Route::get('/list', 'API\BookingController@list');
    Route::post('/delete/{booking?}', 'API\BookingController@destroy');
    Route::post('/cancel/{booking?}', 'API\BookingController@cancel');
    Route::get('/booking-status', 'API\BookingController@Booking_status_get');



    Route::get('/all-booking-list', 'API\BookingController@bookingList');
    Route::post('/room-booking-status', 'API\BookingController@roomCheckInOutDetails');
});


// });
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::prefix('category')->group(function() {
//     Route::get('/', 'API\HotelCategoryController@index');
// });



// Route::middleware('auth:sanctum')->group( function () {
// });
