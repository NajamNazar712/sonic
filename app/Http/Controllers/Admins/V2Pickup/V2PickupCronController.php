<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        
    }
}
