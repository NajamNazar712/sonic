<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\PickupNotesJourney;

class PickupNotesJourneyController extends Controller
{
    static public function add($pickup_id, $status_id, $remarks, $admin_id) {
      $shipment_journey = new PickupNotesJourney();

      $shipment_journey->pickup_id = $pickup_id;
      $shipment_journey->status_id = $status_id;
      $shipment_journey->remarks = $remarks;
      $shipment_journey->admin_id = $admin_id;

      $shipment_journey->save();
    }
}