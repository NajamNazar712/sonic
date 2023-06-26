<?php

namespace App\Http\Controllers;

use App\Http\Models\V3Pickup\V3PickupRequest;
use App\Http\Models\V3Pickup\V3RegularPickup;
use Illuminate\Http\Request;

class AddV3PickupController extends Controller
{
    static public function add ($shipper_id, $pickup_address_id, $pickup_date, $city_id, $time_range, $pickup_type, $estimated_weight, $shipments_count, $remarks = NULL, $generate_type = 0, $generated_by = null, $vendor = NULL){

        $pickup = new V3PickupRequest();
        $pickup->shipper_id = $shipper_id;
        $pickup->pickup_address_id = $pickup_address_id;
        $pickup->pickup_date = $pickup_date;
        $pickup->booked = $shipments_count;
        $pickup->city_id = $city_id;
        $pickup->time_range_id = $time_range;
        $pickup->pickup_type_id = $pickup_type;
        $pickup->estimated_weight = $estimated_weight;
        $pickup->remarks = $remarks;
        $pickup->vendor = $vendor;
        $pickup->generated_type = $generate_type;
        $pickup->generated_by = $generated_by;
        $pickup->save();
    }

    static public function add_regular_pickup ($shipper_id, $pickup_address_id){
        $regular_pickup = V3RegularPickup::where(['shipper_id' => $shipper_id, 'pickup_address_id' => $pickup_address_id]);

        if($regular_pickup->exists()){
            $regular_pickup = $regular_pickup->first();
            $regular_pickup->pickup = 1;
            $regular_pickup->approval = 0;
            $regular_pickup->save();
        }
        else{
            $regular_pickup = new V3RegularPickup();
            $regular_pickup->shipper_id = $shipper_id;
            $regular_pickup->pickup_address_id = $pickup_address_id;
            $regular_pickup->pickup = 1;
            $regular_pickup->approval = 0;
            $regular_pickup->save();
        }
    }
}
