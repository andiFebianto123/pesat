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


Route::get('/', 'App\Http\Controllers\Home@index');
Route::get('/send-mail/{dlp_id}', 'App\Http\Controllers\SendDlp@sendEmail');
Route::get('/childdetail/{id}', 'App\Http\Controllers\Home@childdetail');
//Route::get('/test', 'App\Http\Controllers\Home@index');