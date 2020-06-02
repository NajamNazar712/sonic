<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class V2PickupCronController extends Controller
{
    static public function arrival_not_picked(){
        $pickup_requests = V2PickupRequest::where('status_id', 1);
        if($pickup_requests->exists()){
            $pickup_requests = $pickup_requests->get();
            foreach ($pickup_requests as $pickup_request) {
                $pickup_request->status_id = 3;
                if($pickup_request->current_rider_id != null){
                    $pickup_request_attempt = $pickup_request->pickup_attempt_latest;
                    $pickup_request_attempt->reason_id = 7;
                    $pickup_request_attempt->save();
                }else{
                    $setting = GlobalSettings::where('type', 'global_rider_id');
                    if($setting->exists()){
                        $setting = $setting->first();
                        $rider_id = $setting->setting_value;
                        $pickup_request_attempt = new V2PickupRequestAttempt();
                        $pickup_request_attempt->pickup_request_id = $pickup_request->id;
                        $pickup_request_attempt->rider_id = $rider_id;
                        $pickup_request_attempt->attempt_date = Carbon::now();
                        $pickup_request_attempt->assigned_by = 6;
                        $pickup_request_attempt->save();

                        $pickup_request_attempt->fresh();

                        $pickup_request_attempt->reason_id = 7;
                        $pickup_request_attempt->save();
                    }
                }
            }
        }
    }
    static public function cancel_if_not_valid(){
        $pickup_requests = V2PickupRequest::where('status_id', 1);
        if($pickup_requests->exists()) {
            $pickup_requests = $pickup_requests->get();
            foreach ($pickup_requests as $pickup_request) {
                $count = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id)->whereIn('reason_id', [1,2,3,4,5,6])->count();
                if($count >= 3){
                    $pickup_request->status_id = 4;
                    $pickup_request->save();
                }
            }
        }
    }
    static public function pickup_re_generate(){
        $pickup_requests = V2PickupRequest::where('status_id', 1);
        if($pickup_requests->exists()) {
            $pickup_requests = $pickup_requests->get();
            foreach ($pickup_requests as $pickup_request) {
                $count = $pickup_request->pickup_request_shipments->where('status', 0)->count();
                if($count > 0){
                    $pickup_request_shipments = $pickup_request->pickup_request_shipments->where('status', 0)->pluck('shipment_id')->toArray();
                    foreach ($pickup_request_shipments as $shipment_id){
                        AdminPickupsController::generate($shipment_id);
                    }
                }
            }
        }
    }
}
