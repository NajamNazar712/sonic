<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\ShipmentsPickupJourney;

use Auth;

class ShipmentsPickupJourneyController extends Controller
{
    static public function add($shipment_id, $status_id, $admin_id = NULL, $reference_1_id = NULL, $reference_2_id = NULL) {
      $shipment_pickup_journey = new ShipmentsPickupJourney();

      $shipment_pickup_journey->shipment_id = $shipment_id;
      $shipment_pickup_journey->status_id = $status_id;
      $shipment_pickup_journey->admin_id = $admin_id;
      $shipment_pickup_journey->reference_1_id = $reference_1_id;
      $shipment_pickup_journey->reference_2_id = $reference_2_id;

      $shipment_pickup_journey->save();
    }
}