<?php

Route::group(['middleware' => 'web', 'prefix' => 'mpcs', 'namespace' => 'Modules\MPCS\Http\Controllers'], function()
{
    Route::get('/get-last-verified-form-f22', 'F22FormController@getLastVerifiedF22Form');
    Route::get('/get-form-f22-list', 'F22FormController@getF22FormList');
    Route::get('/get-form-f22', 'F22FormController@getF22Form');
    Route::post('/save-form-f22', 'F22FormController@saveF22Form');
    Route::get('/print-form-f22-by-id/{header_id}', 'F22FormController@printF22FormById');
    Route::put('/update-form-f22/{id}', 'F22FormController@update');
    Route::get('/edit-form-f22/{id}', 'F22FormController@edit');
    Route::post('/print-form-f22', 'F22FormController@printF22Form');
    Route::get('/F22_stock_taking', 'F22FormController@F22StockTaking');
    Route::get('/form-set-1', 'MPCSController@FromSet1');
    Route::get('/get-form-16a', 'MPCSController@get16AForm');
    Route::get('/get_previous_value_16a', 'MPCSController@getPreviousValue16AForm');
    Route::get('/F14', 'MPCSController@F14');
    Route::get('/get_previous_value_9c', 'MPCSController@getPreviousValue9CForm');
    Route::get('/get-9c-form', 'MPCSController@get9CForm');
    Route::get('/F15-9ABC', 'MPCSController@F159ABC');
    Route::get('/F21', 'MPCSController@F21');

    Route::get('/get-form-14b', 'F20F14bFormController@getFrom14B');
    Route::get('/get-form-20', 'F20F14bFormController@getFrom20');
    Route::resource('/F14B_F20_Forms', 'F20F14bFormController');
    
    Route::get('/list-F17', 'F17FormController@list');
    Route::resource('/F17', 'F17FormController');

    Route::get('/form-opening-value/print/{id}', 'FormOpeningValueController@print');
    Route::resource('/form-opening-value', 'FormOpeningValueController');

    Route::post('/forms-setting/formf33', 'FormsSettingController@postFormF22Setting');
    Route::get('/forms-setting/formf33', 'FormsSettingController@getFormF22Setting');
    Route::post('/forms-setting/form21C', 'FormsSettingController@postForm21CSetting');
    Route::get('/forms-setting/form21C', 'FormsSettingController@getForm21CSetting');
    Route::post('/forms-setting/form159abc', 'FormsSettingController@saveForm159ABCSetting');
    Route::get('/forms-setting/form159abc', 'FormsSettingController@getForm159ABCSetting');
    Route::post('/forms-setting/form16a', 'FormsSettingController@postForm16ASetting');
    Route::get('/forms-setting/form16a', 'FormsSettingController@getForm16ASetting');
    Route::post('/forms-setting/form9c', 'FormsSettingController@postForm9CSetting');
    Route::get('/forms-setting/form9c', 'FormsSettingController@getForm9CSetting');
    Route::resource('/forms-setting', 'FormsSettingController');




});
