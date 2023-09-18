<?php

namespace App\Http\Admins\V3Pickup;

use App\Http\Controllers\AddV3PickupController;
use App\Http\Controllers\Admins\V3Pickup\V3AdminPickupsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\V3Pickup\V3PickupRequest;
use App\Http\Models\V3Pickup\V3RegularPickup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class V3PickupCronController extends Controller
{
    static public function regular_pickups_create(){
        $day = Carbon::today()->dayOfWeek;
        $today = Carbon::today();
        $regular_pickups = V3RegularPickup::where('pickup', 1)->where('approval', 1)->get();
        if (count($regular_pickups) > 0){
            $global_rider_id = 346;
            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            }

            foreach ($regular_pickups as $regular_pickup) {
                $pickup_days = $regular_pickup->days;
                $pickup_days = explode(',', $pickup_days);
                if(in_array($day, $pickup_days)){
                    $pickup_request_id = $regular_pickup->pickup_request_id;
                    $pickup_request = V3PickupRequest::find($pickup_request_id);
                    if($pickup_request){
                        $pickup_request_id = AddV3PickupController::add($pickup_request->shipper_id, $pickup_request->pickup_type, $pickup_request->pickup_address_id, $today, $pickup_request->city_id, $pickup_request->time_range_id, $pickup_request->pickup_shipment_type_id, $pickup_request->weight, $pickup_request->booked, $pickup_request->pieces, $pickup_request->special_request, 1, $global_rider_id, NULL, NULL, NULL, $pickup_request->segment_id, $pickup_request->sub_segment_id);

                        V3AdminPickupsController::auto_pickup_assign($pickup_request_id);
                    }
                }
           }
        }
    }
}
