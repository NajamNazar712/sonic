<?php

namespace App\Providers;

use App\Http\Models\PackagingCharge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class CODViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->composeSidebar();
    }

    private function composeSidebar(){
        view()->composer('client.layout.sidebar',function($view){

            $packaging_charges_check = 10;
//            if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>1])->exists()){
//                $packaging_charges_check = true;
//            }else if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>2])->exists()){
//                $packaging_charges_check = true;
//            }else if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>3])->exists()){
//                $packaging_charges_check = true;
//            }else if(PackagingCharge::where(['user_id'=>Auth::id(),'shipping_mode_id'=>4])->exists()){
//                $packaging_charges_check = true;
//            }
           return $view->with('packaging_charges_check',$packaging_charges_check);
        });
    }
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
