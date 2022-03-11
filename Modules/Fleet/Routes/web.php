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

Route::group(['middleware' => ['web', 'auth', 'language'], 'prefix' => 'fleet-management'], function () {
    Route::get('/fleet/get-ledger/{id}', 'FleetController@getLedger');
    Route::resource('/fleet', 'FleetController');
    Route::get('/routes/get-details/{id}', 'RouteController@getDetails');
    Route::get('/routes/get-dropdown', 'RouteController@getRouteDropdown');
    Route::resource('/routes', 'RouteController');
    Route::resource('/drivers', 'DriverController');
    Route::resource('/helpers', 'HelperController');
    Route::resource('/settings', 'SettingController');
    Route::resource('/route-operation', 'RouteOperationController');
    Route::resource('/income', 'IncomeController');
    Route::resource('/route-invoice-number', 'RouteInvoiceNumberController');
    Route::resource('/route-products', 'RouteProductController');
   
});
