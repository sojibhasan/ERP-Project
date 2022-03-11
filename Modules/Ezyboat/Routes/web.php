<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['web', 'auth', 'language'], 'prefix' => 'ezyboat'], function () {
    Route::get('/ezyboat/get-ledger/{id}', 'EzyboatController@getLedger');
    Route::resource('/list', 'EzyboatController');
    Route::get('/routes/get-details/{id}', 'RouteController@getDetails');
    Route::get('/routes/get-dropdown', 'RouteController@getRouteDropdown');
    Route::resource('/routes', 'RouteController');
    Route::resource('/crew', 'CrewController');
    Route::post('/income-setting/toggle-status/{id}', 'IncomeSettingController@toggleStatus');
    Route::resource('/income-setting', 'IncomeSettingController');
    Route::resource('/settings', 'SettingController');
    Route::resource('/boat-operation', 'BoatOperationController');
    Route::resource('/income', 'IncomeController');
    Route::resource('/route-invoice-number', 'RouteInvoiceNumberController');
    Route::resource('/boat-trips', 'BoatTripController');
    Route::resource('/route-product', 'RouteProductController');
   
});
