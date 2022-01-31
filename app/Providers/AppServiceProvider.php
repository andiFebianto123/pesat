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
            $index = array_keys($data)[0];
            $province = isset($data[$index]['propinsi']) ? trim($data[$index]['propinsi']) : '';
            $kabupaten = isset($data[$index]['kabupaten']) ? trim($data[$index]['kabupaten']) : '';
            $cekData = City::whereHas('province', function($query) use($province){
                $query->where('province_name', $province);
            })->where('city_name', $kabupaten)
            ->exists();
            return $cekData;
        });
        
        Paginator::useBootstrap();
    }
    
}
