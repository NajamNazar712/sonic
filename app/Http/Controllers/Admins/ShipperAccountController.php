<?php

namespace App\Http\Controllers\Admins;

use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipment;
use App\Http\Controllers\NotificationsController;
use Auth;
use DB;

use Carbon\Carbon;

class ShipperAccountController extends Controller
{
    static public function disable()
    {

        $settings = GlobalSettings::where('type', 'auto_account_disabled_days')->first();
        $days = $settings->setting_value;
        $date = Carbon::now()->subDays($days);
        $today = Carbon::now();
        //     $active_users = User::where('status', 3)->where('activated_at', '<', $date)->pluck('id')->toArray();

        //     if (count($active_users) > 0) {
        //         $shipments = Shipment::where('created_at', '>', $date)->groupBy('user_id')->pluck('user_id')->toArray();

        //         $result = array_diff($active_users, $shipments);
        //         if (count($result) > 0) {
        //             foreach ($result as $status) {
        //                 User::where('id', $status)->Update(['status' => 4, 'disable_remarks' => 'Auto Disabled after ' . $days . ' Day(s)']);
        //                 NotificationsController::send(57, $status);
        //                 NotificationsController::send(58, $status);
        //             }
        //         }
        //     }



        $users = User::where('status', 3)->whereNull('reactivated_at')->pluck('id')->toArray();;

        if (count($users) > 0) {
            $shipments = Shipment::where('created_at', '>', $date)->groupBy('user_id')->pluck('user_id')->toArray();

            $result = array_diff($users, $shipments);
            if (count($result) > 0) {
                foreach ($result as $status) {
                    User::where('id', $status)->Update(['status' => 4, 'reactivated_at' => '', 'disable_at' => $today , 'disable_remarks' => 'Auto Disabled after ' . $days . ' Day(s)']);

                    NotificationsController::send(57, $status);
                    NotificationsController::send(58, $status);

                    // sending survey form notification
                    $disabled_shippers = User::where('id', $status)->select(['id','email','name','phone'])->get();

                    // via email
                    NotificationsController::send(179, $disabled_shippers);
                    //via sms
                    NotificationsController::send(180, $disabled_shippers);
                }
            }
        } 
        $active_users = User::where('status', 3)->where('reactivated_at', '<', $date)->pluck('id')->toArray();

        if (count($active_users) > 0) {
            $shipments = Shipment::where('created_at', '>', $date)->groupBy('user_id')->pluck('user_id')->toArray();

            $result = array_diff($active_users, $shipments);
            if (count($result) > 0) {
                foreach ($result as $status) {
                    User::where('id', $status)->Update(['status' => 4, 'reactivated_at' => '', 'disable_at' => $today , 'disable_remarks' => 'Auto Disabled after ' . $days . ' Day(s)']);
                    NotificationsController::send(57, $status);
                    NotificationsController::send(58, $status);
                }
            }
        }





    }
    //            $settings = GlobalSettings::where('type', 'auto_account_disabled_days')->first();
    //            $days = $settings->setting_value;
    //            $date = Carbon::now()->subDays($days);
    //            $active_users = User::where('status', 3)->where('created_at', '<', $date)->pluck('id')->toArray();
    //            $shipments = Shipment::where('created_at', '>', $date)->groupBy('user_id')->pluck('user_id')->toArray();
    //            $result = array_diff($active_users,$shipments);
    //
    //            if (count($result) > 0)
    //            {
    //                foreach ($result as $status) {
    //                    User::where('id', $status)->Update(['status' => 4,'disable_remarks' => 'Auto Disabled after ' . $days . ' Day(s)']);
    //                    NotificationsController::send(57, $status);
    //                    NotificationsController::send(58, $status);
    //                }
    //            }
    //            $active_users = User::where('status', 3)->get();
    //            if(count($active_users) > 0){
    //                foreach($active_users as $active_user){
    //                    if($active_user->auto_account_disabled_days != null){
    //                        $days = $active_user->auto_account_disabled_days;
    //                        $date = Carbon::now()->subDays($days);
    //                    }
    //                    else{
    //                        $settings = GlobalSettings::where('type', 'auto_account_disabled_days')->first();
    //                        $days = $settings->setting_value;
    //                        $date = Carbon::now()->subDays($days);
    //                    }
    //                    $shipments = Shipment::where('created_at', '>', $date)->where('user_id', $active_user->id)->groupBy('user_id');
    //
    //                    if(!$shipments->exists()){
    //                        $active_user->status = 4;
    //                        $active_user->disable_remarks = 'Auto Disabled after ' . $days . ' Day(s)';
    //                        $active_user->save();
    //                        NotificationsController::send(57, $active_user->id);
    //                        NotificationsController::send(58, $active_user->id);
    //                    }
    //                }
    //            }
    //        }

}
