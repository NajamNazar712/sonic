<?php

namespace App\Http\Controllers;

use App\Http\Models\V3Pickup\V3PickupRequest;
use App\Http\Models\V3Pickup\V3RegularPickup;
use Illuminate\Http\Request;

class AddV3PickupController extends Controller
{
    static public function add ($shipper_id, $pickup_type_id,$pickup_address_id, $pickup_date, $city_id, $time_range_id, $shipment_type_id, $estimated_weight, $shipments_count, $pieces, $special_request = NULL, $generate_type = 0, $generated_by = null, $walkin_name = NULL, $walkin_address = NULL, $walkin_contact = NULL, $product_id = NULL, $service_id = NULL){

        $pickup = new V3PickupRequest();
        $pickup->pickup_type = $pickup_type_id;
        $pickup->shipper_id = $shipper_id;
        $pickup->pickup_address_id = $pickup_address_id;
        $pickup->pickup_date = $pickup_date;
        $pickup->booked = $shipments_count;
        $pickup->city_id = $city_id;
        $pickup->time_range_id = $time_range_id;
        $pickup->pickup_shipment_type_id = $shipment_type_id;
        $pickup->weight = $estimated_weight;
        $pickup->pieces = $pieces;
        $pickup->special_request = $special_request;
        $pickup->generated_type = $generate_type;
        $pickup->generated_by = $generated_by;
        $pickup->walkin_name = $walkin_name;
        $pickup->walkin_address = $walkin_address;
        $pickup->walkin_contact = $walkin_contact;
        $pickup->segment_id = $product_id;
        $pickup->sub_segment_id = $service_id;
        $pickup->save();

        return $pickup->id;

    }
    static public function update($pickup_request_id,$shipper_id, $pickup_type_id,$pickup_address_id, $pickup_date, $city_id, $time_range_id, $shipment_type_id, $estimated_weight, $shipments_count, $pieces, $special_request = NULL, $generate_type = 0, $generated_by = null, $walkin_name = NULL, $walkin_address = NULL, $walkin_contact = NULL, $product_id = NULL, $service_id = NULL,$services_count = NULL,$last_updated_by = NULL){

        $pickup = V3PickupRequest::find($pickup_request_id);
        $pickup->pickup_type = $pickup_type_id;
        $pickup->shipper_id = $shipper_id;
        $pickup->pickup_address_id = $pickup_address_id;
        $pickup->pickup_date = $pickup_date;
        $pickup->booked = $shipments_count;
        $pickup->city_id = $city_id;
        $pickup->time_range_id = $time_range_id;
        $pickup->pickup_shipment_type_id = $shipment_type_id;
        $pickup->weight = $estimated_weight;
        $pickup->pieces = $pieces;
        $pickup->special_request = $special_request;
        $pickup->generated_type = $generate_type;
        $pickup->generated_by = $generated_by;
        $pickup->walkin_name = $walkin_name;
        $pickup->walkin_address = $walkin_address;
        $pickup->walkin_contact = $walkin_contact;
        $pickup->segment_id = $product_id;
        $pickup->sub_segment_id = $service_id;
        $pickup->services_count = $services_count;
        $pickup->last_updated_by = $last_updated_by;
        $pickup->save();

        return $pickup->id;

    }

    static public function add_regular_pickup ($shipper_id, $pickup_address_id, $pickup_request_id, $days, $requested_by, $requested_type,$time_range_id=null){
        //0 Shipper, 1 - Admin
        $regular_pickup = V3RegularPickup::where(['shipper_id' => $shipper_id, 'pickup_address_id' => $pickup_address_id,'time_range_id'=>$time_range_id]);

        if($regular_pickup->exists()){
            $regular_pickup = $regular_pickup->first();
            $regular_pickup->pickup_request_id = $pickup_request_id;
            $regular_pickup->time_range_id=$time_range_id;
            $regular_pickup->days = $days;
            $regular_pickup->pickup = 1;
            $regular_pickup->approval = 0;
            $regular_pickup->requested_type = $requested_type;
            $regular_pickup->requested_by = $requested_by;
            $regular_pickup->save();
        }
        else{
            $regular_pickup = new V3RegularPickup();
            $regular_pickup->shipper_id = $shipper_id;
            $regular_pickup->pickup_address_id = $pickup_address_id;
            $regular_pickup->pickup_request_id = $pickup_request_id;
            $regular_pickup->time_range_id=$time_range_id;
            $regular_pickup->days = $days;
            $regular_pickup->pickup = 0;
            $regular_pickup->approval = 0;
            $regular_pickup->requested_type = $requested_type;
            $regular_pickup->requested_by = $requested_by;
            $regular_pickup->save();
        }
    }
}
