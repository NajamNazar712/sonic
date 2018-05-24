<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\ShipmentsJourney;

use Auth;

class ShipmentsJourneyController extends Controller
{
    static public function add($shipment_id, $shipper_status_id, $consignee_status_id, $remarks, $user_id, $admin_id) {
      $shipment_journey = new ShipmentsJourney();

      $shipment_journey->shipment_id = $shipment_id;
      $shipment_journey->shipper_status_id = $shipper_status_id;
      $shipment_journey->consignee_status_id = $consignee_status_id;
      $shipment_journey->remarks = $remarks;
      $shipment_journey->user_id = $user_id;
      $shipment_journey->admin_id = $admin_id;

      $shipment_journey->save();
    }
}