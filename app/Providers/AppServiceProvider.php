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
            $search_sonic = NULL;
            if (Auth::guard('admin')->check()) {
                $settings = GlobalSettings::where('type', 'admin_ticker');
                if(session('role_id') !== 1){
                    $search_sonic = AdminsScreenList::whereIn('permission_id', session('permissions'))->select('id','name', 'url');
                }
                else{
                    $search_sonic = AdminsScreenList::select('id','name', 'url');
                }

            }
            else if (Auth::guard('web')->check() || Auth::guard('substitute_users')->check()) {
                $settings = GlobalSettings::where('type', 'shipper_ticker');
            }
            else {
                $settings = NULL;
                $search_sonic = NULL;
            }

            if ($settings && $settings->exists()) {
                $settings = $settings->first();

                $ticker = $settings->text;

                if (!empty($ticker)) {
                    $view->with('ticker', $ticker);
                }

            }

            if($search_sonic && $search_sonic->exists()){
                $search_sonic = $search_sonic->get();
                if (!empty($search_sonic)) {
                    $pages_list = array();
                    foreach ($search_sonic as $search){
                        $url = route("$search->url");
                        $pages_list[] = ['name' => $search->name, 'url' => $url];
                    }
                    $view->with('search_sonic', $pages_list);
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
