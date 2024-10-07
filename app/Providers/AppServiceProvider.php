<?php

namespace App\Providers;


use Auth;
use Carbon\Carbon;
use App\DailyVisit;
use App\Http\Models\Shipper\User;
use App\ReturnDeliveredToShipperSms;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\ReturnDeliveredToShipperTicker;
use Illuminate\Support\ServiceProvider;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\AdminsScreenList;
use App\Http\Models\Admin\Settings\GeneralSetting;
use App\Http\Models\Admin\NotificationReturnedDeliveredToShipper;
use App\Observers\GenericObserver;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminRole;

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
        Admin::observe(GenericObserver::class);
        AdminRole::observe(GenericObserver::class);


        view()->composer('*', function ($view) {
            $search_sonic = NULL;
            $visit = NULL;
            $shipper_return_note_ticker = NULL;

            $return_notes = array();

            if (Auth::guard('admin')->check()) {
                $date = Carbon::now()->toDateString();
                $settings = GeneralSetting::where('type', 'admin_ticker');
                if (session('role_id') !== 1) {
                    $search_sonic = AdminsScreenList::whereIn('permission_id', session('permissions'))->select('id', 'name', 'url');
                } else {
                    $search_sonic = AdminsScreenList::select('id', 'name', 'url');
                }
            } else if (Auth::guard('web')->check() || Auth::guard('substitute_users')->check()) {

                $settings = GeneralSetting::where('type', 'shipper_ticker');
              
                $visit = DailyVisit::where('shipper_id', session('user_id'))->where('rated', 0);

                $lead_logged_user = User::find(session('user_id'));
                
                $from =  Carbon::now()->startOfDay()->toDateTimeString();
                $to = Carbon::parse($from)->endOfDay()->toDateTimeString();

                $shipper_return_notes = ReturnDeliveredToShipperTicker::where('user_id', session('user_id'))->where('status', 1)
                    ->whereBetween('return_delivered_to_shipper_tickers.created_at', [$from, $to]);
                if ($shipper_return_notes->exists()) {
                    $shipper_return_notes = $shipper_return_notes->get();

                    foreach ($shipper_return_notes as $value) {

                        if (!isset($return_notes[$value->return_note_id]['count'])) {
                            $return_notes[$value->return_note_id]['count'] = 1;
                        } else {
                            $return_notes[$value->return_note_id]['count'] += 1;
                        }
                    }
                }

                if ($lead_logged_user) {
                    $view->with('lead_logged_user', $lead_logged_user);
                }

            } else {
                $settings = NULL;
                $search_sonic = NULL;
            }

            if ($settings && $settings->exists()) {
                 $settings = $settings->first();
                 $date = Carbon::now();
                
                 $ticker = $settings->description;
                    //    dd($settings->start_date,$date);
               
                if(!is_null($settings->start_date))
                {
                  
                    if($settings->start_date <= $date)
                    {
                         
                        $ticker = $settings->description;

                    } else{
                        $ticker = null;
                    }
                   
                }
             
                if(!is_null($settings->end_date))
                {
                      
                    if( $settings->start_date <=$date && $settings->end_date >= $date)
                    { 
                        $ticker = $settings->description;

                    } else{
                        $ticker = null;
                    }
                   
                }
               
                if (!empty($ticker) || !is_null($ticker)) {
                    $view->with('ticker', $ticker);
                }
            }

            if ($search_sonic && $search_sonic->exists()) {
                $search_sonic = $search_sonic->get();
                if (!empty($search_sonic)) {
                    $pages_list = array();
                    foreach ($search_sonic as $search) {
                        $url = route("$search->url");
                        $pages_list[] = ['name' => $search->name, 'url' => $url];
                    }
                    $view->with('search_sonic', $pages_list);
                }
            }
            if (Auth::guard('web')->check() || Auth::guard('substitute_users')->check()) {
                if ($visit && $visit->exists()) {
                    $visit = $visit->first();
                    $view->with('visit', $visit);
                }
            }

            if (Auth::guard('web')->check() || Auth::guard('substitute_users')->check()) {
                if (count($return_notes) > 0) {
                    foreach ($return_notes as $key => $note) {
                        if (isset($key)) {
                            $shipper_return_note_ticker .= ' ' . PHP_EOL . PHP_EOL . "(Total Shipments " . $note['count'] . " are returned back to you in safe and sound condition today under Return Note Number " . $key . ", In case of any query regarding these shipments you may respond us back in 48 hours)." . PHP_EOL . PHP_EOL;
                        }
                    }
                    $view->with('shipper_return_note_ticker', $shipper_return_note_ticker);
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
