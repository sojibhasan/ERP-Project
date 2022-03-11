<?php

Route::group(['middleware' => 'web', 'prefix' => 'leads', 'namespace' => 'Modules\Leads\Http\Controllers'], function()
{
    Route::post('/leads/toggle-valid/{id}', 'LeadsController@toggleStatus');
    Route::post('/leads/bulk-valid', 'LeadsController@massValid');
    Route::post('/leads/bulk-invalid', 'LeadsController@massInvalid');
    Route::resource('/leads', 'LeadsController');
    Route::resource('/import', 'ImportLeadsController');
    Route::resource('/day-count', 'DayCountController');
    Route::resource('/district', 'DistrictController');
    Route::resource('/town', 'TownController');
    Route::resource('/settings', 'SettingController');
    Route::resource('/category', 'CategoryController');


});
