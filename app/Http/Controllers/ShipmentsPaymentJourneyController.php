<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Jobs\ProcessShipmentsPaymentJourney;

class ShipmentsPaymentJourneyController extends Controller
{
    static public function add($shipment_id, $status_id, $admin_id, $payable_remarks = '', $done_payment_id = NULL) {
      $entry = array();

      $entry['shipment_id'] = $shipment_id;
      $entry['status_id'] = $status_id;
      $entry['admin_id'] = $admin_id;
      $entry['payable_remarks'] = $payable_remarks;
      $entry['payment_id'] = $done_payment_id;

      dispatch(new ProcessShipmentsPaymentJourney($entry));
    }
}