<?php

namespace App\Http\Controllers\Admins\V3Pickup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\V3Pickup\V3PickupRequestsJourney;

class V3PickupRequestJourneysController extends Controller
{
    static public function add_pickup_request_journey($pickup_request_id,$status,$type,$status_by){
        
        $pickup_request_journey = new V3PickupRequestsJourney();
        $pickup_request_journey->pickup_request_id = $pickup_request_id;
        $pickup_request_journey->status = $status;
        $pickup_request_journey->type = $type; //0 shipper ,1 admins, 2 rider
        $pickup_request_journey->status_by = $status_by;
        $pickup_request_journey->Save();

    }

    static public function add_pickup_request_journey_with_created_at($pickup_request_id,$status,$type,$status_by,$created_at){
        $pickup_request_journey = new V3PickupRequestsJourney();
        $pickup_request_journey->pickup_request_id = $pickup_request_id;
        $pickup_request_journey->status = $status;
        $pickup_request_journey->type = $type; //0 shipper ,1 admins, 2 rider
        $pickup_request_journey->status_by = $status_by;
        $pickup_request_journey->created_at = $created_at;
        $pickup_request_journey->Save();

    }
}
