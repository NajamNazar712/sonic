<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Vectorface\Whip\Whip;

use App\Jobs\ProcessShipmentsJourney;

class ShipmentsJourneyController extends Controller
{
    static public function add($shipment_id, $shipper_status_id, $consignee_status_id, $status_reason_id, $remarks, $user_id, $admin_id, $reference_1_id = NULL, $reference_2_id = NULL, $verification = 1, $received_or_refused_by = NULL) {
      $entry = array();

      $entry['shipment_id'] = $shipment_id;
      $entry['verification'] = $verification;
      $entry['shipper_status_id'] = $shipper_status_id;
      $entry['consignee_status_id'] = $consignee_status_id;
      $entry['status_reason_id'] = $status_reason_id;
      $entry['remarks'] = $remarks;
      $entry['user_id'] = $user_id;
      $entry['admin_id'] = $admin_id;
      $entry['reference_1_id'] = $reference_1_id;
      $entry['reference_2_id'] = $reference_2_id;
      $entry['received_or_refused_by'] = $received_or_refused_by;

      $whip = new Whip();
      $client_address = $whip->getValidIpAddress();

      if ($client_address != '') {
        $entry['ip_address'] = $client_address;
      }
      else {
        $entry['ip_address'] = NULL;
      }

      dispatch(new ProcessShipmentsJourney($entry));
    }
}