<?php

Route::group(['middleware' => 'web', 'prefix' => 'hr', 'namespace' => 'Modules\HR\Http\Controllers'], function () {
    Route::get('employee/terminated', 'EmployeeController@teminatedEmployee');
    Route::resource('employee/application', 'ApplicationController');
    Route::resource('employee/award', 'EmployeeAwardController');
    Route::resource('employee/application', 'ApplicationController');
    Route::get('employee/import', 'EmployeeController@getImportEmployee');
    Route::post('employee/import', 'EmployeeController@postImportEmployee');
    Route::post('employee/toggle-active/{id}', 'EmployeeController@toggleActive');
    Route::resource('employee', 'EmployeeController');
    Route::resource('reimbursement', 'ReimbursementController');
    Route::get('attendance/import', 'AttendanceController@getImportAttendance');
    Route::post('attendance/import', 'AttendanceController@postImportAttendance');
    Route::get('attendance/report', 'AttendanceController@getAttendanceReport');
    Route::post('attendance/get-approved-late-and-overtime/{id}', 'AttendanceController@postApproveLateOverTime');
    Route::get('attendance/get-approved-late-and-overtime/{id}', 'AttendanceController@getApproveLateOverTime');
    Route::get('attendance/get-late-and-overtime', 'AttendanceController@getLateOvertime');
    Route::resource('attendance', 'AttendanceController');
    Route::get('payroll/salary/get-salary-range-by-employee-id/{id}', 'SalaryController@getSalaryRangeByEmployeeId');
    Route::resource('payroll/salary', 'SalaryController');
    Route::get('payroll/get-employee-by-department', 'BasicSalaryController@getEmployeeByDepartment');
    Route::resource('payroll/basic-salary', 'BasicSalaryController');
    Route::get('payroll/payment/make-payment', 'PayrollPaymentController@getMakePayment');
    Route::get('payroll/payment/print/{id}', 'PayrollPaymentController@printPayment');
    Route::resource('payroll/payment', 'PayrollPaymentController');
    Route::resource('notice-board', 'NoticeBoardController');
    Route::get('report/get-payroll-report', 'ReportController@getPayrollReport');
    Route::get('report/get-employee-report', 'ReportController@getEmployeeReport');
    Route::get('report/get-attendance-report', 'ReportController@getAttendanceReport');
    Route::get('report/get-employee-by-department', 'ReportController@getEmployeeByDerpartment');
    Route::resource('leave-request', 'LeaveRequestController');
    Route::resource('report', 'ReportController');

    Route::group(['prefix' => 'settings'], function () {
        Route::resource('department', 'DepartmentController');
        Route::resource('jobtitle', 'JobtitleController');
        Route::resource('jobcategory', 'JobCategoryController');
        Route::resource('workingdays', 'WorkingDayController');
        Route::resource('workshift', 'WorkShiftController');
        Route::resource('holidays', 'HolidayController');
        Route::resource('leave-application-type', 'LeaveApplicationTypeController');
        Route::resource('salary-grade', 'SalrayGradeController');
        Route::resource('employment-status', 'EmploymentStatusController');
        Route::resource('religion', 'ReligionController');
        Route::resource('salary-component', 'SalaryComponentController');
        Route::resource('tax', 'TaxController');
        Route::resource('prefix', 'PrefixController');
        Route::resource('hr-settings', 'HrSettingsController');
    });
});
