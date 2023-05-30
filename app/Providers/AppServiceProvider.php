<?php

namespace App\Providers;


use App\DailyVisit;
use App\Http\Models\Admin\AdminsScreenList;
use App\Http\Models\Admin\NotificationReturnedDeliveredToShipper;
use App\ReturnDeliveredToShipperSms;
use Carbon\Carbon;
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
            $visit = NULL;
            $shipper_return_note_ticker = NULL;
            $return_notes = array();

            if (Auth::guard('admin')->check()) {
                $settings = GlobalSettings::where('type', 'admin_ticker');
                if (session('role_id') !== 1) {
                    $search_sonic = AdminsScreenList::whereIn('permission_id', session('permissions'))->select('id', 'name', 'url');
                } else {
                    $search_sonic = AdminsScreenList::select('id', 'name', 'url');
                }

            }
            else if (Auth::guard('web')->check() || Auth::guard('substitute_users')->check()) {
                $settings = GlobalSettings::where('type', 'shipper_ticker');
                $visit = DailyVisit::where('shipper_id', session('user_id'))->where('rated', 0);

                $from =  Carbon::now()->startOfDay()->toDateTimeString();
                $to = Carbon::parse($from)->endOfDay()->toDateTimeString();

                $shipper_return_notes = ReturnDeliveredToShipperSms::where('user_id',session('user_id'))->where('status',1)
                ->whereBetween('return_delivered_to_shipper_sms.created_at',[$from,$to]);
                if($shipper_return_notes->exists()){
                    $shipper_return_notes = $shipper_return_notes->get();

                    foreach($shipper_return_notes as $value){

                        if(!isset($return_notes[$value->return_note_id]['count'])){
                            $return_notes[$value->return_note_id]['count'] = 1;
                        } 
                        else{
                            $return_notes[$value->return_note_id]['count'] += 1; 
                        }
                    }
                }
            } else {
                $settings = NULL;
                $search_sonic = NULL;
            }

            if ($settings && $settings->exists()) {
                $settings = $settings->first();

                $ticker = $settings->text;

                 if (!empty($ticker)) {
                    $view->with('ticker',$ticker);
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
                      foreach($return_notes as $key => $note){
                        if(isset($key)){
                            $shipper_return_note_ticker .= "(Total Shipments " . $note['count'] . " are returned back to you in safe and sound condition today under Return Note Number " . $key . ", In case of any query regarding these shipments you may respond us back in 48 hours)." .PHP_EOL.PHP_EOL;
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