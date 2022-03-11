<?php

Route::group(['middleware' => 'web', 'prefix' => 'sms', 'namespace' => 'Modules\SMS\Http\Controllers'], function()
{
    Route::get('list/view-numbers/{id}', 'SMSController@showNumbers');
    Route::resource('list', 'SMSController');
});
