<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\PickupsJourney;

class PickupsJourneyController extends Controller
{
    static public function add($pickup_id, $status_id, $remarks, $user_id, $admin_id) {
      $shipment_journey = new PickupsJourney();

      $shipment_journey->pickup_id = $pickup_id;
      $shipment_journey->status_id = $status_id;
      $shipment_journey->remarks = $remarks;
      $shipment_journey->user_id = $user_id;
      $shipment_journey->admin_id = $admin_id;

      $shipment_journey->save();
    }
}