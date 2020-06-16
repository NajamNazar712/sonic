<?php

namespace App\Http\Controllers\Admins\Handover;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Handover\HandoverShipmentsJourney;

class HandoverShipmentJourneyController extends Controller
{
    static public function add($shipment_id, $handover_id, $status) {
		$handover_shipment_journey = new HandoverShipmentsJourney();

		    $handover_shipment_journey->shipment_id = $shipment_id;
        $handover_shipment_journey->handover_id = $handover_id;
        $handover_shipment_journey->status = $status;

		$handover_shipment_journey->save();
    }
}
