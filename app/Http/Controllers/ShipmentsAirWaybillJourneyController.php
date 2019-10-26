<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\ShipmentsAirWaybillJourney;

class ShipmentsAirWaybillJourneyController extends Controller
{
    static public function add($shipment_id, $user_type, $user_id) {
		$shipment_air_waybill_journey = new ShipmentsAirWaybillJourney();

		$shipment_air_waybill_journey->shipment_id = $shipment_id;
		$shipment_air_waybill_journey->user_type = $user_type;
		$shipment_air_waybill_journey->user_id = $user_id;

		$shipment_air_waybill_journey->save();
    }
}