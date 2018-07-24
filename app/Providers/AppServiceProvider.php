<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
//        view()->composer('client.layout.sidebar',function($view){

//            $packaging_charges_check = false;
//            if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>1])->exists()){
//                $packaging_charges_check = true;
//            }else if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>2])->exists()){
//                $packaging_charges_check = true;
//            }else if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>3])->exists()){
//                $packaging_charges_check = true;
//            }else if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>4])->exists()){
//                $packaging_charges_check = true;
//            }
//            return $view->with('packaging_charges_check',$packaging_charges_check);
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
