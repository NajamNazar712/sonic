<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Jobs\ProcessShipmentsPickupJourney;

class ShipmentsPickupJourneyController extends Controller
{
    static public function add($shipment_id, $status_id, $admin_id = NULL, $reference_1_id = NULL, $reference_2_id = NULL) {
      $entry = array();

      $entry['shipment_id'] = $shipment_id;
      $entry['status_id'] = $status_id;
      $entry['admin_id'] = $admin_id;
      $entry['reference_1_id'] = $reference_1_id;
      $entry['reference_2_id'] = $reference_2_id;

      dispatch(new ProcessShipmentsPickupJourney($entry));
    }
}