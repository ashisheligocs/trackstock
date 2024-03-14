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

// Route::middleware('auth:api')->get('/accounts', function (Request $request) {
//     return $request->user();
// });




Route::prefix('account')->group(function () {
    Route::prefix('coa')->group(function () {
        Route::get('/', 'Api\LedgerController@index');
    });
    Route::prefix('ledger')->group(function () {
        Route::prefix('group')->group(function () {
            Route::get('/', 'Api\LedgerController@listLedgerType');
            Route::post('/add', 'Api\LedgerController@ledgerGroupCreate');
            Route::get('/view/{id?}', 'Api\LedgerController@showLedgerTypehow');
            Route::get('/delete/{id?}', 'Api\LedgerController@destroyLedgerTypehow');
        });
        Route::get('/', 'Api\LedgerController@listLedger');
        Route::post('/add', 'Api\LedgerController@ledgerCreate');
        Route::get('/view/{id?}', 'Api\LedgerController@showLedger');
        Route::get('/delete/{id?}', 'Api\LedgerController@destroyLedger');
        Route::post('/list', 'Api\LedgerController@getIdWise');
        Route::post('/revenue', 'Api\LedgerController@revenueCalculation');
        Route::post('/expense', 'Api\LedgerController@expenseCalculation');
        Route::post('/asset', 'Api\LedgerController@assetCalculation');
        Route::post('/liability', 'Api\LedgerController@liabilityCalculation');
    });

    Route::prefix('journal')->group(function () {
        Route::get('/', 'Api\JournalEntryController@getAllAccountGroups');
        Route::get('/get-journal','Api\JournalEntryController@getJournalData');
        Route::get('/transaction/{id?}','Api\JournalEntryController@getJournalDetail');
        Route::Post('/add','Api\JournalEntryController@journalCreate');
        Route::Post('/edit','Api\JournalEntryController@journalUpdate');
        Route::delete('/delete/{id?}', 'Api\JournalEntryController@deleteJournalEntry');
    });
});
