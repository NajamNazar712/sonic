<?php

namespace App\Providers;


use App\Http\Models\Admin\NotificationReturnedDeliveredToShipper;
use App\DailyVisit;
use App\Http\Models\Admin\AdminsScreenList;
use App\Http\Models\Notification;
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
            $flag = 0;
            $shipper_ticker = null;
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
                $visit = DailyVisit::where('shipper_id',session('user_id'))->where('rated',0);

                //todo
                $current_user = session('user_id');

                $global = GlobalSettings::where('type','returned_shipment_notification')
                    ->select('text as users');

                if($global->exists())
                {
                    $global = $global->first();
                    $return_users = explode(',', $global->users);
                    if(in_array($current_user,$return_users))
                    {
                        $return_users = NotificationReturnedDeliveredToShipper::where('user_id',$current_user)->where('status',1);

                        if ($return_users->exists())
                        {

                            $return_users = $return_users->get();
                            $notification = Notification::find(216);

                            if ($notification) {
                                if ($notification->status) {
                                    $body = $notification->body;
                                    foreach ($return_users as $key => $return_noted) {
                                        $old_body = $body;
                                        if (strpos($old_body, '[return_notes_id]') !== FALSE) {
                                            $old_body = str_replace('[return_notes_id]', $return_noted->return_note_id, $old_body);
                                        }
                                        if (strpos($old_body, '[shipments_count]') !== FALSE) {
                                            $old_body = str_replace('[shipments_count]', $return_noted->shipment_count, $old_body);
                                        }
                                        $shipper_ticker .=  $old_body . "\n\n";
                                        $flag = 1;
                                    }
                                }
                            }
                        }
                    }
                }

//                die();

                //todo end
            }
            else {
                $settings = NULL;
                $search_sonic = NULL;
            }

            if($flag == 0)
            {
                if ($settings && $settings->exists()) {
                    $settings = $settings->first();

                    $ticker = $settings->text;

                    if (!empty($ticker)) {
                        $view->with('ticker', $ticker);
                    }

                }
            }
            else
            {
                if (!empty($shipper_ticker)) {
                    $view->with('ticker', $shipper_ticker);
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
            if (Auth::guard('web')->check() || Auth::guard('substitute_users')->check()) {
                if ($visit && $visit->exists()){
                    $visit = $visit->first();
                    $view->with('visit', $visit);
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

