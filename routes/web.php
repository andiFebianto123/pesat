<?php

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

//Route::get('/', 'App\Http\Controllers\ListChildController@index');
Route::group(['middleware' => ['assign.guard:sponsor']], function () {
    Route::group(['middleware' => ['auth:sponsor']], function () {
        Route::get('/my-account', 'App\Http\Controllers\Sponsor\MyAccountController@index');
        Route::get('/child-donation', 'App\Http\Controllers\Sponsor\MyAccountController@childDonation');
        Route::get('/project-donation', 'App\Http\Controllers\Sponsor\MyAccountController@projectDonation')->name('projectdonation');
        Route::get('/edit-account', 'App\Http\Controllers\Sponsor\MyAccountController@editaccount');
        Route::get('/child-donation-detail/{id}', 'App\Http\Controllers\Sponsor\MyAccountController@childdetaildonation');
        Route::get('/list-dlp/{id}', 'App\Http\Controllers\Sponsor\MyAccountController@listdlp');
        Route::get('/project-donation-detail/{id}', 'App\Http\Controllers\Sponsor\MyAccountController@projectdetaildonation');
        Route::post('/update-account', 'App\Http\Controllers\Sponsor\MyAccountController@updateaccount');
        Route::get('/order/{id}', 'App\Http\Controllers\OrderController@index');
        Route::post('/order/{id}', 'App\Http\Controllers\OrderController@postOrder');
        Route::get('/checkout-order/{id}', 'App\Http\Controllers\OrderController@orderdonation')->name('ordercheckout');
        Route::get('/project-order/{id}', 'App\Http\Controllers\ProjectOrderController@index');
        Route::post('/project-order/{id}', 'App\Http\Controllers\ProjectOrderController@postOrder');
        Route::get('/checkout-order-project/{code}', 'App\Http\Controllers\ProjectOrderController@orderproject')->name('orderprojectcheckout');

    });

    Route::group(['middleware' => ['isloggedin']], function () {

        // Bila user sudah login maka ketika akses halaman ini diredirect ke Halaman Akun Saya
        Route::get('/forgot-password', 'App\Http\Controllers\Sponsor\MyAccountController@forgotpassword');
        Route::post('/reset-password', 'App\Http\Controllers\Sponsor\MyAccountController@resetpassword');
        Route::get('/register', 'App\Http\Controllers\Sponsor\MyAccountController@register');
        Route::post('/create-account', 'App\Http\Controllers\Sponsor\MyAccountController@createaccount');

    });
    Route::get('/', 'App\Http\Controllers\HomeController@index');
    //Route::get('/send-mail/{dlp_id}', 'App\Http\Controllers\SendDlp@sendEmail');
    Route::get('/cek', 'App\Http\Controllers\OrderController@cekstatus');
    Route::get('/donate-goods', 'App\Http\Controllers\DonationGoodsController@index');
    // Route::get('reminder-invoice','App\Http\Controllers\OrderController@reminderinvoice');
    Route::get('/childdetail/{id}', 'App\Http\Controllers\ListChildController@childdetail');
    Route::get('/list-child', 'App\Http\Controllers\ListChildController@index');
    Route::get('/list-proyek', 'App\Http\Controllers\ListProyekController@index');
    Route::get('project-detail/{id}', 'App\Http\Controllers\ListProyekController@projectdetail');
    Route::post('/validate', 'App\Http\Controllers\LoginController@validatelogin')->name('validate');
    Route::prefix('sponsor')
        ->as('sponsor.')
        ->group(function () {
            Route::get('home', 'App\Http\Controllers\HomeController@index')->name('home');

            Route::namespace ('App\Http\Controllers\Auth\Login')
                ->group(function () {
                    Route::get('login', 'SponsorLoginController@showLoginForm')->name('login');
                    Route::post('login', 'SponsorLoginController@login')->name('login');
                    Route::post('logout', 'SponsorLoginController@logout')->name('logout');
                });
        });
});
