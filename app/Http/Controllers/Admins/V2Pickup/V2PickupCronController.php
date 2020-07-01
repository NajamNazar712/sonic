<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use DB;
class V2PickupCronController extends Controller
{
    static public function arrival_not_picked(){
        $settings = GlobalSettings::where('type', 'pickup_arrival_cut_off_time');
        $arrival_cut_off_time = '08:00';
        if ($settings->exists()) {
            $settings = $settings->first();
            $arrival_cut_off_time = $settings->setting_value . ':00';
        }
        $arrival_time = Carbon::parse($arrival_cut_off_time)->toTimeString();
        $rider_id = 1837;
        $global_admin_id = 346;
        $setting = GlobalSettings::where('type', 'global_rider_id');
        if($setting->exists()){
            $setting = $setting->first();
            $rider_id = $setting->setting_value;
        }
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::today()->toDateString();
        $pickup_requests = V2PickupRequest::where('status_id', 1)->whereDate('created_at', '<=', $today)->whereTime('created_at', '<=', $arrival_time);
        if($pickup_requests->exists()){
            $pickup_requests = $pickup_requests->get();
            $pickup_request_ids = array();
            foreach ($pickup_requests as $pickup_request) {
                $pickup_request->status_id = 3;
                $pickup_request->save();
                if($pickup_request->current_rider_id != null){
                    $pickup_request_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id);
                    if($pickup_request_attempt->exists()){
                        $pickup_request_attempt = $pickup_request_attempt->latest()->first();
                        $pickup_request_attempt->reason_id = 7;
                        $pickup_request_attempt->save();
                        $pickup_note_request = V2PickupNoteRequest::where('pickup_request_id', $pickup_request->id)->latest('id')->first();
                        if($pickup_note_request){
                            if($pickup_note_request->status == 0){
                                $pickup_note_request->status = 1;
                                $pickup_note_request->save();
                            }
                        }
                        if(!in_array($pickup_request->id, $pickup_request_ids)){
                            $pickup_request_ids[] = $pickup_request->id;
                        }
                    }
                }else{
                    $pickup_request_attempt = new V2PickupRequestAttempt();
                    $pickup_request_attempt->pickup_request_id = $pickup_request->id;
                    $pickup_request_attempt->rider_id = $rider_id;
                    $pickup_request_attempt->reason_id = 7;
                    $pickup_request_attempt->attempt_date = $yesterday;
                    $pickup_request_attempt->assigned_by = $global_admin_id;
                    $pickup_request_attempt->save();

                }

            }
            V2PickupNote::where('status', 0)->whereDate('created_at', '<=', $today)->update(['status' => 1]);

            self::remove_riders();
        }
    }
    static public function remove_riders(){
        V2PickupRequest::whereIn('status_id', [1,3])->where('rider_status', 2)->update(['last_rider_id' => DB::raw('current_rider_id'), 'current_rider_id' => NULL, 'rider_status' => 1]);
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
        $pickup_requests = V2PickupRequest::where('status_id', 2)->where('regenerate', 0);
        if($pickup_requests->exists()) {
            $pickup_requests = $pickup_requests->get();
            foreach ($pickup_requests as $pickup_request) {
                $pickup_request->regenerate = 1;
                $pickup_request->save();
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
