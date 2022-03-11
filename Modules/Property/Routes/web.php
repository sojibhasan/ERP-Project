<?php

Route::group(['middleware' => ['web', 'auth', 'language', 'SetSessionData'], 'prefix' => 'property'], function () {
    Route::get('/list-price-changes', 'PriceChangesController@index');
    Route::resource('/properties', 'PropertyController');
    Route::get('/properties/get-property-details/{id}', 'PropertyController@getPropertyDetails');
    Route::get('/properties/get-customer-details/{customer_id}', 'PropertyController@getCustomerDetails');
    Route::resource('/settings', 'SettingController');
    Route::resource('/property-taxes', 'PropertyTaxesController');
    Route::resource('/purchases', 'PurchaseController');
    Route::resource('/property-blocks', 'PropertyBlocksController');
    Route::get('/property-blocks/get-block-tr/{id}', 'PropertyBlocksController@getBlockTr');
    Route::post('/property-blocks/update-blocks', 'PropertyBlocksController@updateBlocks');
    Route::get('/property-blocks/get-block-details/{id}', 'PropertyBlocksController@getBlockDetails');
    Route::get('/property-blocks/import/{id}', 'PropertyBlocksController@getImport');
    Route::post('/property-blocks/import/{id}', 'PropertyBlocksController@postImport');
    Route::get('/property-blocks/get-block-dropdown/{id}', 'PropertyBlocksController@getBlocksDropdown');
    Route::get('/contacts/ledger', 'ContactController@getLedger');
    Route::resource('/contacts', 'ContactController');
    Route::post('contacts/{id}', 'ContactController@update');

    Route::resource('/payment-options', 'PaymentOptionController');
    Route::get('/finance-options/get-details/{id}', 'FinanceOptionController@getDetails');
    Route::resource('/finance-options', 'FinanceOptionController');
    Route::resource('/installment-cycle', 'InstallmentCycleController');
    Route::resource('/sell-land-blocks', 'SellLandBlockController');
    Route::resource('/sell-land-blocks/store', 'SellLandBlockController@store');
    Route::get('/customer-payment/get-property-dropdown-by-customer-id/{customer_id}', 'CustomerPaymentController@getPropertyDropdownByCustomer');
    Route::resource('/customer-payment', 'CustomerPaymentController');
    Route::resource('/close-current-sale', 'CloseCurrentSaleController');
    Route::resource('/property-finalize', 'PropertyFinalizeController');
    Route::resource('/property-account-setting', 'PropertyAccountSettingController');
    Route::get('/easy-payments/aging-report', 'EasyPaymentController@getAgingReport');
    Route::resource('/easy-payments', 'EasyPaymentController');
    Route::resource('/penalty', 'PenaltyController');
    Route::resource('/expenses', 'ExpenseController');
    Route::resource('/block-close-reason', 'BlockCloseReasonController');
    Route::resource('/sales-officer', 'SalesOfficerController');

    Route::get('/sale-and-customer-payment/access-main-system', 'SaleAndCustomerPaymentController@accessMainSystem');
    Route::get('/sale-and-customer-payment/get-totals', 'SaleAndCustomerPaymentController@getTotals');
    Route::get('/sale-and-customer-payment/dashboard', 'SaleAndCustomerPaymentController@dashboard');
    Route::resource('/sale-and-customer-payment', 'SaleAndCustomerPaymentController');
    Route::get('reports', 'ReportController@index');
    Route::get('reports/daily-report', 'ReportController@getDailyReport');
});
Route::group(['middleware' => ['web', 'auth', 'language', 'SetSessionData'], 'prefix' => 'ajax'], function () {
    Route::put('/credit_sub_account_type','AjaxController@credit_sub_account_type_ajax');

});
