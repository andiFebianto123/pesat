<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

use App\Http\Controllers\Admin\DlpCrudController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProvinceCustomController;
use App\Http\Controllers\Admin\ProjectMasterDetailCrudController;
use App\Http\Controllers\Admin\ReportController;

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes

    Route::get('dashboard', [DashboardController::class,'index']);
    Route::get('report', [ReportController::class,'index']);
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

    Route::prefix('detail-sponsor/{header_id}')->group(function () {
        Route::crud('detail', 'DataDetailOrderCrudController');
        Route::get('detail/{id}/document', [DataDetailOrderCrudController::class, 'document']);
    });

    Route::prefix('api')->group(function () {
        Route::get('province-select', 'ProvinceCustomController@index');
        Route::get('city-select', 'ProvinceCustomController@getCity');
    });
//    Route::get('/api/provice', 'App\Http\Controllers\Admin\ProvinceCustomController@index');
    Route::crud('user-role', 'UserRoleCrudController');
    Route::crud('users', 'UsersCrudController');
    Route::crud('user-attribute', 'UserAttributeCrudController');
    Route::crud('data-sponsor', 'DataSponsorCrudController');
    Route::crud('sponsor', 'SponsorCrudController');
    Route::crud('data-order', 'DataOrderCrudController');
    Route::crud('data-detail-order', 'DataDetailOrderCrudController');
}); // this should be the absolute last line of this file