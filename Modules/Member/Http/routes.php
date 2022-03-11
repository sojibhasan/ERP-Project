<?php

Route::group(['middleware' => 'web', 'prefix' => 'member-module', 'namespace' => 'Modules\Member\Http\Controllers'], function()
{
    Route::resource('/members', 'MemberController');
    Route::resource('/member-settings', 'MemberSettingController');

    Route::resource('/gramaseva-vasama', 'GramasevaVasamaController');
    Route::resource('/balamandalaya', 'BalamandalayaController');
    Route::resource('/member-groups', 'MemberGroupController');
    Route::resource('/service-areas', 'ServiceAreasController');
 
});
