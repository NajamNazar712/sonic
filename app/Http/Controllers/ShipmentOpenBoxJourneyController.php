<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\ShipmentOpenBoxJourney;

class ShipmentOpenBoxJourneyController extends Controller
{
    static public function add($shipment_id, $status_id, $admin_id) {
		$shipment_openbox_journey = new ShipmentOpenBoxJourney();

		$shipment_openbox_journey->shipment_id = $shipment_id;
		$shipment_openbox_journey->open_box_status_id = $status_id;
		$shipment_openbox_journey->created_by = $admin_id;
		$shipment_openbox_journey->save();
    }
}
