<?php

namespace App\Providers;

//use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use App\Models\City;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
        Validator::extend('provinsikabupatenvalidation', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $province = isset($data['propinsi']) ? trim($data['propinsi']) : '';
            $kabupaten = isset($data['kabupaten']) ? trim($data['kabupaten']) : '';
            $cekData = City::whereHas('province', function($query) use($province){
                $query->where('province_name', $province);
            })->where('city_name', $kabupaten);
            return $cekData->exists();
        });
        
        Paginator::useBootstrap();
    }
    
}
