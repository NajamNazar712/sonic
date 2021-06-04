<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use DB;
class V2PickupCronController extends Controller
{
    static public function ready_pickups() {
        V2PickupNote::where('status', 0)->update(['status' => 1]);

        V2PickupRequest::whereIn('status_id', [1, 3])->where('rider_status', 2)->update(['last_rider_id' => DB::raw('current_rider_id'), 'current_rider_id' => NULL, 'rider_status' => 1]);

        V2PickupRequest::whereIn('status_id', [1, 3])->where('rider_status', 1)->where('current_rider_id', '!=', NULL)->update(['last_rider_id' => DB::raw('current_rider_id'), 'current_rider_id' => NULL]);
    }

    static public function auto_pickup_assign() {
        $pickup_requests = V2PickupRequest::whereIn('status_id', [1, 3])->where('rider_status', 1);

        if ($pickup_requests->exists()) {
            $pickup_requests = $pickup_requests->get();

            foreach ($pickup_requests as $pickup_request) {
                AdminPickupsController::auto_pickup_assign($pickup_request->id);
            }
        }
    }

    static public function arrival_not_picked(){
        $settings = GlobalSettings::where('type', 'pickup_arrival_cut_off_time');
        $arrival_cut_off_time = '6';
        if ($settings->exists()) {
            $settings = $settings->first();
            $arrival_cut_off_time = $settings->setting_value;
        }
        // $arrival_time = Carbon::parse($arrival_cut_off_time)->toTimeString();
        $rider_id = 1837;
        $global_admin_id = 346;
        $auto_generate_pickup_ids = array();
        $setting = GlobalSettings::where('type', 'global_rider_id');
        if($setting->exists()){
            $setting = $setting->first();
            $rider_id = $setting->setting_value;
        }
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $today->setTime($arrival_cut_off_time,0,0);
        $yesterday->setTime($arrival_cut_off_time,0,1);

        $pickup_requests = V2PickupRequest::whereIn('status_id', [1,3])->where('created_at', '<=', $today);
        if($pickup_requests->exists()){
            $pickup_requests = $pickup_requests->get();
            foreach ($pickup_requests as $pickup_request) {
                $pickup_request_shipments = $pickup_request->pickup_request_shipments->pluck('shipment_id')->toArray();
                $cancelled_shipments_count = Shipment::whereIn('id', $pickup_request_shipments)->where('shipper_status_id', 17)->count();

                if ($cancelled_shipments_count == $pickup_request->booked) {
                    $pickup_request->status_id = 4;
                    $pickup_request->save();
                    continue;
                }

                $now = Carbon::now();
                if($pickup_request->status_id != 3){
                    $pickup_request->status_id = 3;
                    $pickup_request->save();
                }
                $pickup_request_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id)->whereBetween('attempt_date', [$yesterday,$today]);
                if($pickup_request_attempt->exists()){
                    $pickup_request_attempt = $pickup_request_attempt->latest('id')->first();
                    if($pickup_request_attempt->reason_id == null){
                        $pickup_request_attempt->reason_id = 7;
                        $pickup_request_attempt->save();
                    }
                    $pickup_note_request = V2PickupNoteRequest::where('pickup_request_id', $pickup_request->id);
                    if($pickup_note_request->exists()){
                        $pickup_note_request = $pickup_note_request->latest('id')->first();
                        if($pickup_note_request->status == 0){
                            $pickup_note_request->status = 1;
                            $pickup_note_request->save();
                        }
                    }
                }else{
                    $pickup_request->attempts = $pickup_request->attempts + 1;
                    $pickup_request->save();

                    $pickup_request_attempt = new V2PickupRequestAttempt();
                    $pickup_request_attempt->pickup_request_id = $pickup_request->id;
                    $pickup_request_attempt->rider_id = $rider_id;
                    $pickup_request_attempt->reason_id = 7;
                    $pickup_request_attempt->attempt_date = $now;
                    $pickup_request_attempt->assigned_by = $global_admin_id;
                    $pickup_request_attempt->save();
                }
            }
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
                    $shipments = Shipment::where('shipper_status_id', 1)->whereIn('id', $pickup_request_shipments)->pluck('id')->toArray();
                    if(count($shipments) > 0){
                        foreach ($shipments as $shipment_id){
                            AdminPickupsController::generate($shipment_id);
                        }
                    }
                }
            }
        }
    }

    static public function clean_data(){
        $pickup_requests = V2PickupRequest::where('attempts', '>',1)->whereIn('status_id',[1,2,3])->select('id')->get();
        $current = Carbon::now()->day(5)->month(7)->startOfDay();
        $past = Carbon::now()->day(18)->month(6)->startOfDay();

        $shipments = array();
        $shipments['dates'] = array();
        while ($current->greaterThanOrEqualTo($past)) {
            $shipments['dates'][] = $past->toDateString();
            $past = $past->addDay(1);
        }
        $attempt_ids = array();
        if(count($pickup_requests) > 0){
            foreach ($pickup_requests as $pickup_request) {
                foreach ($shipments['dates'] as $date){
                    $attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id)->whereDate('attempt_date', $date)->whereNull('reason_id')->latest('id')->first();
                    if($attempt){
                        $to_delete_attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id)->whereDate('attempt_date', $date)->whereNull('reason_id')->where('id', '!=', $attempt->id)->pluck('id')->toArray();
                        if(count($to_delete_attempts) > 0){
                            foreach ($to_delete_attempts as $to_delete_attempt) {
                                $attempt_ids[] = $to_delete_attempt;
                            }
                        }
                    }
                }
            }
        }
        if(count($attempt_ids) > 0){
            V2PickupRequestAttempt::whereIn('id', $attempt_ids)->delete();

            self::reset_pickup_count();
        }
    }
    static public function reset_pickup_count(){
        $pickup_requests = V2PickupRequest::where('attempts', '>',1)->whereIn('status_id',[1,2,3])->get();
        foreach ($pickup_requests as $pickup_request){
            $pickup_request->attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id)->count();
            $pickup_request->save();
        }
        self::pickup_delete_duplicate_shipments();
    }

    static public function pickup_delete_duplicate_shipments(){
        $pickup_requests = V2PickupRequest::whereIn('status_id',[1,3]);
            if ($pickup_requests->exists()) {
                $pickup_requests = $pickup_requests->get();

                foreach ($pickup_requests as $pickup_request) {
                    $shipment_ids = V2PickupRequestShipment::where('pickup_request_id', $pickup_request->id)->distinct('shipment_id')->pluck('shipment_id')->toArray();

                    if (count($shipment_ids) > 0) {
                        foreach ($shipment_ids as $shipment_id) {
                            $id = V2PickupRequestShipment::where('pickup_request_id', $pickup_request->id)->where('shipment_id', $shipment_id)->first()->id;
                            if($id){
                                V2PickupRequestShipment::where('pickup_request_id', $pickup_request->id)->where('shipment_id', $shipment_id)->where('id', '!=', $id)->delete();
                            }
                         }
                    }
               }
           }
        self::pickup_shipment_reset_count();
    }
    static public function pickup_shipment_reset_count(){
        $pickup_requests = V2PickupRequest::whereIn('status_id',[1,3]);
        if ($pickup_requests->exists()) {
            $pickup_requests = $pickup_requests->get();
            foreach ($pickup_requests as $pickup_request) {
                $pickup_request->booked = V2PickupRequestShipment::where('pickup_request_id', $pickup_request->id)->count();
                $pickup_request->save();
            }
        }
    }

    static public function arrival_not_picked_old(){
        $settings = GlobalSettings::where('type', 'pickup_arrival_cut_off_time');
        $arrival_cut_off_time = '8';
        if ($settings->exists()) {
            $settings = $settings->first();
            $arrival_cut_off_time = $settings->setting_value;
        }
        $arrival_time = Carbon::parse($arrival_cut_off_time)->toTimeString();
        $rider_id = 1837;
        $global_admin_id = 346;
        $setting = GlobalSettings::where('type', 'global_rider_id');
        if($setting->exists()){
            $setting = $setting->first();
            $rider_id = $setting->setting_value;
        }
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $today->setTime($arrival_cut_off_time,0,0);
        $yesterday->setTime($arrival_cut_off_time,0,1);
        $pickup_requests = V2PickupRequest::whereIn('status_id', [1,3])->whereBetween('created_at', [$yesterday,$today]);
        if($pickup_requests->exists()){
            $pickup_requests = $pickup_requests->get();
            foreach ($pickup_requests as $pickup_request) {
                $now = Carbon::now();
                if($pickup_request->status_id == 3){
                    $check_pickup_request_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id)->whereBetween('attempt_date', [$yesterday,$today])->whereNotNull('reason_id');
                    if($check_pickup_request_attempt->exists()){
                        continue;
                    }
                }
                $pickup_request->status_id = 3;
                $pickup_request->save();
                if($pickup_request->current_rider_id != null){
                    $pickup_request_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id)->whereDate('attempt_date', '>=' ,$yesterday)->whereTime('attempt_date', '>=', $arrival_time);
                    if($pickup_request_attempt->exists()){
                        $pickup_request_attempt = $pickup_request_attempt->latest('id')->first();
                        $pickup_request_attempt->reason_id = 7;
                        $pickup_request_attempt->save();
                        $pickup_note_request = V2PickupNoteRequest::where('pickup_request_id', $pickup_request->id);
                        if($pickup_note_request->exists()){
                            $pickup_note_request = $pickup_note_request->latest('id')->first();
                            if($pickup_note_request->status == 0){
                                $pickup_note_request->status = 1;
                                $pickup_note_request->save();
                            }
                        }
                    }else{
                        $pickup_request->attempts = $pickup_request->attempts + 1;
                        $pickup_request->save();
                        $pickup_request_attempt = new V2PickupRequestAttempt();
                        $pickup_request_attempt->pickup_request_id = $pickup_request->id;
                        $pickup_request_attempt->rider_id = $rider_id;
                        $pickup_request_attempt->reason_id = 7;
                        $pickup_request_attempt->attempt_date = $now;
                        $pickup_request_attempt->assigned_by = $global_admin_id;
                        $pickup_request_attempt->save();
                    }
                }else{
                    $pickup_request->attempts = $pickup_request->attempts + 1;
                    $pickup_request->save();
                    $pickup_request_attempt = new V2PickupRequestAttempt();
                    $pickup_request_attempt->pickup_request_id = $pickup_request->id;
                    $pickup_request_attempt->rider_id = $rider_id;
                    $pickup_request_attempt->reason_id = 7;
                    $pickup_request_attempt->attempt_date = $now;
                    $pickup_request_attempt->assigned_by = $global_admin_id;
                    $pickup_request_attempt->save();

                }

            }
            V2PickupNote::where('status', 0)->update(['status' => 1]);

            self::remove_riders();
        }
    }
}
