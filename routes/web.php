<?php

use App\Http\Controllers\SendMail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', 'App\Http\Controllers\HomeController@index');
Route::get('/send-mail/{dlp_id}', 'App\Http\Controllers\SendDlp@sendEmail');
Route::get('/childdetail/{id}', 'App\Http\Controllers\HomeController@childdetail');
Route::get('/transaction', 'App\Http\Controllers\TransactionController@index');
//Route::get('/login', 'App\Http\Controllers\LoginController@index');
Route::post('/validate', 'App\Http\Controllers\LoginController@validatelogin')->name('validate');
Route::prefix('sponsor')
    ->as('sponsor.')
    ->group(function() {
        Route::get('home', 'App\Http\Controllers\HomeController@index')->name('home');

    	Route::namespace('App\Http\Controllers\Auth\Login')
    		->group(function() {
    			Route::get('login', 'SponsorLoginController@showLoginForm')->name('login');
				Route::post('login', 'SponsorLoginController@login')->name('login');
				Route::post('logout', 'SponsorLoginController@logout')->name('logout');
    		});
	});