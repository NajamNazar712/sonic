<?php

namespace App\Providers;


use App\Http\Models\Admin\AdminsScreenList;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

use App\Http\Models\Admin\GlobalSettings;

use Auth;

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

        view()->composer('*', function ($view) {
            if (Auth::guard('admin')->check()) {
                $settings = GlobalSettings::where('type', 'admin_ticker');
                $search_sonic = AdminsScreenList::whereIn('permission_id', session('permissions'))->select('id','name', 'url');
            }
            else if (Auth::guard('web')->check() || Auth::guard('substitute_users')->check()) {
                $settings = GlobalSettings::where('type', 'shipper_ticker');
            }
            else {
                $settings = NULL;
            }

            if ($settings && $settings->exists()) {
                $settings = $settings->first();

                $ticker = $settings->text;
//                $search = $search_sonic->name;

                if (!empty($ticker)) {
                    $view->with('ticker', $ticker);
                }

            }

            if($search_sonic->exists()){
                $search_sonic = $search_sonic->get();
                if (!empty($search_sonic)) {
                    $view->with('search_sonic', $search_sonic);
                }
            }
        });
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
