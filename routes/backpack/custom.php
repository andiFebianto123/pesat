<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

use App\Http\Controllers\Admin\DlpCrudController;
use App\Http\Controllers\Admin\ProjectMasterDetailCrudController;

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
     Route::prefix('project-master-detail/{header_id}')->group(function () {
         Route::crud('image', 'ProjectMasterDetailCrudController');
         Route::get('detail/{id}/document', [ProjectMasterDetailCrudController::class, 'document']);
     });
    Route::crud('dlp', 'DlpCrudController');
    Route::prefix('dlp/{header_id}')->group(function () {
        Route::crud('detail', 'DlpCrudController');
        Route::get('detail/{id}/document', [DlpCrudController::class, 'document']);
    });
    Route::crud('user-role', 'UserRoleCrudController');
    Route::crud('users', 'UsersCrudController');
    Route::crud('user-attribute', 'UserAttributeCrudController');
    Route::crud('data-sponsor', 'DataSponsorCrudController');
    Route::crud('sponsor', 'SponsorCrudController');
}); // this should be the absolute last line of this file
Route::get('/api/provice', 'App\Http\Controllers\Admin\ProvinceCustomController@index');
Route::get('/api/city', 'App\Http\Controllers\Admin\ProvinceCustomController@getCity');
