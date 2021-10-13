<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('province', 'ProvinceCrudController');
    Route::crud('city', 'CityCrudController');
    Route::crud('religion', 'ReligionCrudController');
    Route::crud('sponsor-type', 'SponsorTypeCrudController');
    Route::crud('child-master', 'ChildMasterCrudController');
    Route::crud('project-master', 'ProjectMasterCrudController');
    Route::crud('project-master-detail', 'ProjectMasterDetailCrudController');
    Route::crud('dlp', 'DlpCrudController');
}); // this should be the absolute last line of this file
Route::get('/api/provice', 'App\Http\Controllers\Admin\ProvinceCustomController@index');
Route::get('/api/city', 'App\Http\Controllers\Admin\ProvinceCustomController@getCity');
