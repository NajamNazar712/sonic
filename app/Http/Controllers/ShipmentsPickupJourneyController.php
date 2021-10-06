<?php

namespace App\Http\Controllers;

use App\Http\Models\V2Pickup\ShipmentsV2PickupJourney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\ShipmentsPickupJourney;

class ShipmentsPickupJourneyController extends Controller
{
    static public function add($shipment_id, $status_id, $admin_id = NULL, $referene_1_id = NULL, $reason_id = NULL) {
		$shipment_pickup_journey = new ShipmentsV2PickupJourney();

		$shipment_pickup_journey->shipment_id = $shipment_id;
		$shipment_pickup_journey->status_id = $status_id;
		$shipment_pickup_journey->admin_id = $admin_id;
		$shipment_pickup_journey->reference_1_id = $referene_1_id;
		$shipment_pickup_journey->reason_id = $reason_id;

		$shipment_pickup_journey->save();
    }
}