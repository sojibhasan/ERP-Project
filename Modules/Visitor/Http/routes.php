<?php
Route::group(['middleware' => 'web', 'prefix' => 'visitor-module', 'namespace' => 'Modules\Visitor\Http\Controllers'], function()
{
    Route::resource('/visitor', 'VisitorController');
    Route::get('/registration', 'VisitorController@registration');
    
    Route::resource('/settings', 'VisitorSettingController');
    Route::get('/qr-visitor-reg', 'VisitorController@generateQr');
    Route::post('/qr-visitor-save', 'VisitorController@saveQr');
    Route::get('/registration', 'VisitorRegistrationController@create');
    Route::post('/registration', 'VisitorRegistrationController@store');
 
});