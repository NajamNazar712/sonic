<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Automation\ReattemptShipmentStatusController;
use App\Http\Controllers\Webhook\FinalChargesWebhookController;
use App\Http\Controllers\Webhook\ShipmentStatusWebhookController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\MasterCargo\Bag;
use App\Http\Models\Rider;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\ConsigneeUser;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipperShipmentsSubscription;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipment;
use App\Http\Models\CargoConsignment;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\ReturnNote;
use Vectorface\Whip\Whip;
use Auth;

class ShipmentsJourneyController extends Controller
{
    static public function add($shipment_id, $shipper_status_id, $consignee_status_id, $status_reason_id, $remarks, $user_id, $admin_id, $reference_1_id = NULL, $reference_2_id = NULL,$verification = 1, $received_or_refused_by = NULL, $rider_id = NULL,$cnic= NULL,$relation = NULL) {
      $shipment_journey = new ShipmentsJourney();

      $shipment_journey->shipment_id = $shipment_id;
      $shipment_journey->verification = $verification;
      $shipment_journey->shipper_status_id = $shipper_status_id;
      $shipment_journey->consignee_status_id = $consignee_status_id;
      $shipment_journey->status_reason_id = $status_reason_id;
      $shipment_journey->remarks = $remarks;
      $shipment_journey->user_id = $user_id;
      $shipment_journey->admin_id = $admin_id;
      $shipment_journey->rider_id = $rider_id;
      $shipment_journey->reference_1_id = $reference_1_id;
      $shipment_journey->reference_2_id = $reference_2_id;
      $shipment_journey->received_or_refused_by = $received_or_refused_by;
      $shipment_journey->relation = $relation;
      $shipment_journey->cnic = $cnic;

      

      if($remarks_id != null){
          $shipment_journey->status_sub_reason_id = $remarks_id;
      }

      if($user_id != null){
          $city_id = User::find($user_id)->city_id;
          $shipment_journey->city_id = $city_id;
      }
      else if($admin_id != null){
          $city_id = Admin::find($admin_id)->default_hub_id;
          $shipment_journey->city_id = $city_id;
      }
      else if($rider_id != null){
          $city_id = Rider::find($rider_id)->city_id;
          $shipment_journey->city_id = $city_id;
      }
      else{
          if (in_array($shipper_status_id, [1, 2, 17, 19, 39, 40, 41, 42, 43, 47, 50, 61])) {
              $shipment = Shipment::find($shipment_id);

              if ($shipment) {
                  $shipment_journey->city_id = $shipment->pickup_address->city_id;
              }
          }
          else if (in_array($shipper_status_id, [3, 21, 26, 32])) {
              //$bag = Bag::find($shipment_journey->reference_1_id);
              $bag = CargoManifestBag::find($shipment_journey->reference_1_id);

              if($bag){
                  $cargo_consignment = $bag;
              }
              else{
                  $cargo_consignment = Bag::find($shipment_journey->reference_1_id);

              }

              if ($cargo_consignment) {
                  $shipment_journey->city_id = $cargo_consignment->origin_hub_id;
              }
          }
          else if (in_array($shipper_status_id, [4, 22, 27, 33])) {
              $bag = CargoManifestBag::find($shipment_journey->reference_1_id);
              if($bag){
                  $cargo_consignment = $bag;
              }
              else{
                  $cargo_consignment = Bag::find($shipment_journey->reference_1_id);
              }

              if ($cargo_consignment) {
                  $shipment_journey->city_id = $cargo_consignment->destination_hub_id;
              }
          }
          else if (in_array($shipper_status_id, [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 18])) {
              if ($shipment_journey->reference_1_id) {
                  $delivery_note = DeliveryNote::find($shipment_journey->reference_1_id);

                  if ($delivery_note) {
                      $shipment_journey->city_id = $delivery_note->hub_id;
                  }
                  else if ($shipper_status_id == 13) {
                      $shipment = Shipment::find($shipment_id);

                      if ($shipment) {
                          $shipment_journey->city_id = $shipment->consignee_city_id;
                      }
                  }
              }
          }
          else if (in_array($shipper_status_id, [23, 24, 25, 28, 29, 30, 31, 34, 35, 36, 37, 38, 44, 45, 46, 47, 48])) {
              $return_note = ReturnNote::find($shipment_journey->reference_1_id);

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
          else if (in_array($shipper_status_id, [54, 55])) {
              $shipment = Shipment::find($shipment_id);
              if ($shipment) {
                  $shipment_journey->city_id = $shipment->consignee_city_id;
              }
          }
      }

      $whip = new Whip();
      $client_address = $whip->getValidIpAddress();

      if ($client_address != '') {
        $shipment_journey->ip_address = $client_address;
      }

        if($shipper_status_id == 53){
            $shipment = Shipment::find($shipment_id);
            if ($shipment) {
                $shipment_journey->city_id = $shipment->pickup_address->city_id;
            }
        }

      $shipment_journey->save();
      if($verification == 1){
          if($shipper_status_id != 1){
              ShipmentStatusWebhookController::webhook_subscription($shipment_id, $shipper_status_id);
          }

          if($shipper_status_id == 20)
          {
              FinalChargesWebhookController::webhook_subscription($shipment_id);
          }
      }
        if (in_array($shipper_status_id, [2, 27, 33, 4, 13, 3, 26, 32, 5, 8, 29, 35, 9, 15, 7, 54, 55, 11, 14, 16, 30, 36, 37, 20, 12]) && $verification == 1) {
            $shipment = Shipment::find($shipment_id);
            $consignee_user = ConsigneeUser::where('phone_number_1', $shipment->consignee_phone_number_1)
                ->orwhere('phone_number_2', $shipment->consignee_phone_number_1);
            if ($consignee_user->exists()) {
                $consignee_user = $consignee_user->first();
                $consignee_id = $consignee_user->id;
                NotificationsController::app_notification(8, $consignee_id, 4, $shipment_id, $shipper_status_id);
            }
        }

        if ($verification == 1) {
            $shipment_subscription = ShipperShipmentsSubscription::where('shipment_id',$shipment_id);
            if ($shipment_subscription->exists()) {
                $shipment_subscription = $shipment_subscription->first();
                NotificationsController::app_notification(7, $shipment_subscription->shipper_id, 3, $shipment_id, $shipper_status_id);
            }
        }

        if (in_array($shipper_status_id, [14, 8, 20]) && $verification == 1) {
            ShipperShipmentsSubscription::where('shipment_id', $shipment_id)->delete();
        }

        if($shipper_status_id == 52){
            ReattemptShipmentStatusController::auto_reattempt_status_for_max_delivery_ratio($shipment_id);
        }
    }
}