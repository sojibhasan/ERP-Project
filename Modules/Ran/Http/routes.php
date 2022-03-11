<?php

Route::group(['middleware' => 'web', 'prefix' => 'ran', 'namespace' => 'Modules\Ran\Http\Controllers'], function()
{
    Route::get('/', 'RanController@index');
    Route::resource('gold-grade', 'GoldGradeController');
    Route::get('gold-prices/get-gold-price-by-grade', 'GoldPriceController@getGoldPriceByGrade');
    Route::resource('gold-prices', 'GoldPriceController');
    Route::resource('production', 'ProductionController');
    Route::resource('goldsmith', 'GoldSmithController');
    Route::get('wastages/get-details', 'WastageController@getDetails');
    Route::resource('wastages', 'WastageController');
    Route::delete('work-order/delete-work-order-item', 'WorkOrderController@deleteWorkItem');
    Route::get('work-order/get-work-order-item-details', 'WorkOrderController@getWorkOrderItemDetails');
    Route::get('work-order/get-work-order-items', 'WorkOrderController@getWorkOrderItems');
    Route::resource('work-order', 'WorkOrderController');
    Route::resource('receive-work-order', 'ReceiveWorkOrderController');
    Route::resource('gold', 'GoldController');
});
