<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Jobs\ProcessShipmentsAirWaybillJourney;

class ShipmentsAirWaybillJourneyController extends Controller
{
    static public function add($shipment_id, $user_type, $user_id) {
      $entry = array();

      $entry['shipment_id'] = $shipment_id;
      $entry['user_type'] = $user_type;
      $entry['user_id'] = $user_id;

      dispatch(new ProcessShipmentsAirWaybillJourney($entry));
    }
}