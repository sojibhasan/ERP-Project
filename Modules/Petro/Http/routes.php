<?php

Route::group(['middleware' => ['web', 'auth', 'language', 'SetSessionData', 'DayEnd'], 'prefix' => 'petro', 'namespace' => 'Modules\Petro\Http\Controllers'], function () {
    Route::get('/dashboard', 'PetroController@index');


    Route::get('/tank-management/get-tank-product', 'FuelTankController@getTankProduct');
    Route::resource('/tank-management', 'FuelTankController');
    Route::get('/tanks-transaction-summary', 'TanksTransactionDetailController@tankTransactionSummary');
    Route::resource('/tanks-transaction-details', 'TanksTransactionDetailController');
    Route::post('/pumps/save-import', 'PumpController@saveImport');
    Route::get('/pumps/import', 'PumpController@importPumps');
    Route::get('/pump-management/get-meter-readings', 'PumpController@getMeterReadings');
    Route::get('/pump-management/get-testing-details', 'PumpController@getTestingDetails');
    Route::resource('/pump-management', 'PumpController');


    Route::get('/pump-operators/get-pumpter-excess-shortage-payments', 'PumpOperatorController@getPumperExcessShortagePayments');
    Route::post('/pump-operators/save-import', 'PumpOperatorController@saveImport');
    Route::get('/pump-operators/import', 'PumpOperatorController@importPumps');
    Route::get('/pump-operators/ledger', 'PumpOperatorController@getLedger');
    Route::resource('/recover-shortage', 'RecoverShortageController');
    Route::resource('/excess-comission', 'ExcessComissionController');
    Route::get('/pump-operators/toggle-active/{id}', 'PumpOperatorController@toggleActivate');
    Route::get('/pump-operators/get-dashboard-data', 'PumpOperatorController@getDashboardData');

    Route::get('/pump-operators/dashboard', 'PumpOperatorController@dashboard');
    Route::get('/pump-operators/check-passcode', 'PumpOperatorController@checPasscode');
    Route::get('/pump-operators/check-username', 'PumpOperatorController@checUsername');
    Route::resource('/pump-operators/shift-summary', 'ShiftSummaryController');
    Route::get('/pump-operators/pumper-day-entries/add-settlement-no/{id}', 'PumperDayEntryController@getAddSettlementNo');
    Route::post('/pump-operators/pumper-day-entries/add-settlement-no/{id}', 'PumperDayEntryController@postAddSettlementNo');
    Route::get('/pump-operators/pumper-day-entries/view-settlement-no/{id}', 'PumperDayEntryController@viewAddSettlementNo');
    Route::get('/pump-operators/pumper-day-entries/get-daily-collection', 'PumperDayEntryController@getDailyCollection');
    Route::resource('/pump-operators/pumper-day-entries', 'PumperDayEntryController');
    Route::get('/pump-operators/set-main-system-session', 'PumpOperatorController@setMainSystemSession');
    Route::resource('/pump-operators', 'PumpOperatorController');
    Route::resource('/opening-meter', 'OpeningMeterController');

    Route::post('/pump-operator-actions/get-pumper-assignment/{pump_id}/{pump_operator_id}', 'PumpOperatorActionsController@postPumperAssignment');
    Route::post('/pump-operator-actions/get-colsing-meter/{pump_id}', 'PumpOperatorActionsController@postClosingMeter');
    Route::get('/pump-operator-actions/get-colsing-meter/{pump_id}', 'PumpOperatorActionsController@getClosingMeter');
    Route::get('/pump-operator-actions/get-colsing-meter-modal', 'PumpOperatorActionsController@getClosingMeterModal');
    Route::get('/pump-operator-actions/get-receive-pump', 'PumpOperatorActionsController@getReceivePump');
    Route::get('/pump-operator-payments/get-payment-modal', 'PumpOperatorPaymentController@getPaymentModal');
    Route::get('/pump-operator-payments/get-modal', 'PumpOperatorPaymentController@getPaymentSummaryModal');
    Route::get('/pump-operator-payments/balance-to-operator/{pump_operator}', 'PumpOperatorPaymentController@balanceToOperator');
    Route::resource('/pump-operator-payments', 'PumpOperatorPaymentController');
    Route::get('/closing-shift/close-shift/{pump_operator_id}', 'ClosingShiftController@closeShift');
    Route::resource('/closing-shift', 'ClosingShiftController');
    Route::get('/current-meter/get-modal', 'CurrentMeterController@getModal');
    Route::resource('/current-meter', 'CurrentMeterController');
    Route::get('/unload-stock/get-details', 'UnloadStockController@getDetails');
    Route::resource('/unload-stock', 'UnloadStockController');

    Route::get('/pump-operator-actions/get-pumper-assignment/{pump_id}/{pump_operator_id}', 'PumpOperatorAssignmentController@getPumperAssignment');
    Route::resource('/pump-operator-assignment', 'PumpOperatorAssignmentController');

    //common controller for document & note
    Route::get('get-document-note-page', 'PumperDocumentAndNoteController@getDocAndNoteIndexPage');
    Route::post('post-document-upload', 'PumperDocumentAndNoteController@postMedia');
    Route::resource('pumper-note-documents', 'PumperDocumentAndNoteController');

    Route::get('/daily-collection/print/{pump_operator_id}', 'DailyCollectionController@print');
    Route::get('/daily-collection/get-balance-collection/{pump_operator_id}', 'DailyCollectionController@getBalanceCollection');
    Route::resource('/daily-collection', 'DailyCollectionController');


    Route::get('/settlement/get_balance_stock/{id}', 'SettlementController@getBalanceStock');
    Route::get('/settlement/get_balance_stock_by_id/{id}', 'SettlementController@getBalanceStockById');
    Route::delete('/settlement/delete-customer-payment/{id}', 'SettlementController@deleteCustomerPayment');
    Route::delete('/settlement/delete-other-income/{id}', 'SettlementController@deleteOtherIncome');
    Route::delete('/settlement/delete-other-sale/{id}', 'SettlementController@deleteOtherSale');
    Route::delete('/settlement/delete-meter-sale/{id}', 'SettlementController@deleteMeterSale');
    Route::post('/settlement/save-customer-payment', 'SettlementController@saveCustomerPayment');
    Route::post('/settlement/save-other-income', 'SettlementController@saveOtherIncome');
    Route::post('/settlement/save-other-sale', 'SettlementController@saveOtherSale');
    Route::post('/settlement/save-meter-sale', 'SettlementController@saveMeterSale');
    Route::get('/settlement/get-pump-details/{pump_id}', 'SettlementController@getPumpDetails');
    Route::get('/settlement/print/{id}', 'SettlementController@print');
    Route::resource('/settlement', 'SettlementController');


    Route::delete('/settlement/payment/delete-excess-payment/{id}', 'AddPaymentController@deleteExcessPayment');
    Route::post('/settlement/payment/save-excess-payment', 'AddPaymentController@saveExcessPayment');
    Route::delete('/settlement/payment/delete-shortage-payment/{id}', 'AddPaymentController@deleteShortagePayment');
    Route::post('/settlement/payment/save-shortage-payment', 'AddPaymentController@saveShortagePayment');
    Route::delete('/settlement/payment/delete-expense-payment/{id}', 'AddPaymentController@deleteExpensePayment');
    Route::post('/settlement/payment/save-expense-payment', 'AddPaymentController@saveExpensePayment');
    Route::delete('/settlement/payment/delete-credit-sale-payment/{id}', 'AddPaymentController@deleteCreditSalePayment');
    Route::post('/settlement/payment/save-credit-sale-payment', 'AddPaymentController@saveCreditSalePayment');
    Route::delete('/settlement/payment/delete-cheque-payment/{id}', 'AddPaymentController@deleteChequePayment');
    Route::post('/settlement/payment/save-cheque-payment', 'AddPaymentController@saveChequePayment');
    Route::delete('/settlement/payment/delete-card-payment/{id}', 'AddPaymentController@deleteCardPayment');
    Route::post('/settlement/payment/save-card-payment', 'AddPaymentController@saveCardPayment');
    Route::delete('/settlement/payment/delete-cash-payment/{id}', 'AddPaymentController@deleteCashPayment');
    Route::post('/settlement/payment/save-cash-payment', 'AddPaymentController@saveCashPayment');
    Route::get('/settlement/payment/get-product-price', 'AddPaymentController@getProductPrice');
    Route::get('/settlement/payment/get-customer-details/{customer_id}', 'AddPaymentController@getCustomerDetails');
    Route::get('/settlement/payment/preview/{id}', 'AddPaymentController@preview');
    Route::get('/settlement/payment/preview/credit-sale-product/{id}', 'AddPaymentController@productPreview');
    Route::get('/settlement/payment', 'AddPaymentController@create');
    Route::resource('/settlement/payment', 'AddPaymentController');
    Route::get('/get-stores-by-id', 'SettlementController@getStoresById');
    Route::get('/get-products-by-store-id', 'SettlementController@getProductsByStoreId');


    Route::get('/get-dip-resetting', 'DipManagementController@getDipResetting');
    Route::get('/get-dip-report', 'DipManagementController@getDipReport');
    Route::get('/get-tank-balance-by-id/{tank_id}', 'DipManagementController@getTankBalanceById');
    Route::get('/get-tank-product/{tank_id}', 'DipManagementController@getTankProduct');
    Route::post('/save-resetting-dip', 'DipManagementController@saveResettingDip');
    Route::get('/add-resetting-dip', 'DipManagementController@addResettingDip');
    Route::post('/save-new-dip-reading', 'DipManagementController@saveNewDip');
    Route::get('/add-new-dip', 'DipManagementController@addNewDip');
    Route::resource('/dip-management', 'DipManagementController');


    Route::get('/meter-resetting/get-pump-details', 'MeterResettingController@getPumpDetails');
    Route::resource('/meter-resetting', 'MeterResettingController');


    Route::get('issue-customer-bill/get-customer-reference/{id}', 'IssueCustomerBillController@getCustomerReference');
    Route::get('issue-customer-bill/get-product-row', 'IssueCustomerBillController@getProductRow');
    Route::get('issue-customer-bill/get-product-price/{id}', 'IssueCustomerBillController@getProductPrice');
    Route::get('issue-customer-bill/print/{id}', 'IssueCustomerBillController@print');
    Route::resource('issue-customer-bill', 'IssueCustomerBillController');

    Route::get('daily-voucher/print/{id}', 'DailyVoucherController@print');
    Route::get('daily-voucher/get-product-row', 'DailyVoucherController@getProductRow');
    Route::resource('daily-voucher', 'DailyVoucherController');
});
