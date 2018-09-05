<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipment;
use App\Http\Models\CargoConsignment;
use App\Http\Models\CargoConsignment\Admin\DeliveryNote;
use App\Http\Models\CargoConsignment\Admin\ReturnNote;

use Auth;

class ShipmentsJourneyController extends Controller
{
    static public function add($shipment_id, $shipper_status_id, $consignee_status_id, $status_reason_id, $remarks, $user_id, $admin_id, $reference_1_id = NULL, $reference_2_id = NULL) {
      $shipment_journey = new ShipmentsJourney();

      $shipment_journey->shipment_id = $shipment_id;
      $shipment_journey->shipper_status_id = $shipper_status_id;
      $shipment_journey->consignee_status_id = $consignee_status_id;
      $shipment_journey->status_reason_id = $status_reason_id;
      $shipment_journey->remarks = $remarks;
      $shipment_journey->user_id = $user_id;
      $shipment_journey->admin_id = $admin_id;
      $shipment_journey->reference_1_id = $reference_1_id;
      $shipment_journey->reference_2_id = $reference_2_id;

      if (in_array($shipper_status_id, [1, 2, 17, 19, 39, 40, 41, 42, 43, 47])) {
        $shipment = Shipment::find($shipment_id);

        if ($shipment) {
          $shipment_journey->city_id = $shipment->pickup_address->city_id;
        }
      }
      else if (in_array($shipper_status_id, [3, 21, 26, 32])) {
        $cargo_consignment = CargoConsignment::find($journey->reference_1_id);

        if ($cargo_consignment) {
          $shipment_journey->city_id = $cargo_consignment->origin_hub_id;
        }
      }
      else if (in_array($shipper_status_id, [4, 22, 27, 33])) {
        $cargo_consignment = CargoConsignment::find($journey->reference_1_id);

        if ($cargo_consignment) {
          $shipment_journey->city_id = $cargo_consignment->destination_hub_id;
        }
      }
      else if (in_array($shipper_status_id, [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 18, 28, 29, 30, 34, 35, 36, 37, 45, 46])) {
        $delivery_note = DeliveryNote::find($journey->reference_1_id);

        if ($delivery_note) {
          $shipment_journey->city_id = $delivery_note->hub_id;
        }
      }
      else if (in_array($shipper_status_id, [23, 24, 25, 31, 38, 44])) {
        $return_note = ReturnNote::find($journey->reference_1_id);

        if ($return_note) {
          $shipment_journey->city_id = $return_note->hub_id;
        }
      }
      else if ($shipper_status_id == 20) {
        $shipment = Shipment::find($shipment_id);

        if ($shipment) {
          $shipment_journey->city_id = $shipment->consignee_city_id;
        }
      }

      $shipment_journey->save();
    }
}