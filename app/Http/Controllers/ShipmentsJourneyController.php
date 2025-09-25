<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Automation\ReattemptShipmentStatusController;
use App\Http\Controllers\Webhook\FinalChargesWebhookController;
use App\Http\Controllers\Webhook\ShipmentStatusWebhookController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\MasterCargo\Bag;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\Admin\ShipmentJourneyConsigneeRefusedSubReason;
use App\Http\Models\Rider;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Models\ShipmentJourneyRetailUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Models\ConsigneeUser;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipperShipmentsSubscription;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipment;
use App\Http\Models\CargoConsignment;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmRequestTagging;
use Vectorface\Whip\Whip;
use Auth;
use App\Jobs\ShipmentStatusSharingWithWallet;
use App\Models\StatusSharingWithWallet;

class ShipmentsJourneyController extends Controller
{
    static public function add($shipment_id, $shipper_status_id, $consignee_status_id, $status_reason_id, $remarks, $user_id, $admin_id, $reference_1_id = NULL, $reference_2_id = NULL, $verification = 1, $received_or_refused_by = NULL, $rider_id = NULL, $cnic = NULL, $relation = NULL, $remarks_id = NULL, $retail_user_id = NULL) {
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
      else if ($retail_user_id!=null){
          $city_id = RetailUser::find($retail_user_id)->city_id;
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

        if ($retail_user_id!=null) {
            $retail_journey_user = new ShipmentJourneyRetailUser();
            $retail_journey_user->shipment_journey_id = $shipment_journey->id;
            $retail_journey_user->retail_user_id = $retail_user_id;
            $retail_journey_user->save();
        }

        if(in_array($shipper_status_id, [5,8,13,14,18,20,36,37,30])) {
            
            $shipment = Shipment::join('wallet_users as u', function ($join) {
                $join->on('u.user_id', '=', 'shipments.user_id')
                   ->where('u.substitute_user_id', '0');
            })->where('shipments.id', $shipment_id)->first();
            if($shipment) {
                StatusSharingWithWallet::create([
                    'shipment_id' => $shipment_id,
                    'is_send' => 0,
                    'status_id' => $shipper_status_id,
                ]);
                //ShipmentStatusSharingWithWallet::dispatch($data, 1);
            }
        }

        if($remarks_id != null){
            $shipment_journey_id = $shipment_journey->id;

            $ShipmentJourneyConsigneeRefusedSubReason = new ShipmentJourneyConsigneeRefusedSubReason();
            $ShipmentJourneyConsigneeRefusedSubReason->shipment_journey_id = $shipment_journey_id;
            $ShipmentJourneyConsigneeRefusedSubReason->status_sub_reason_id = $remarks_id;
            $ShipmentJourneyConsigneeRefusedSubReason->status = 1;
            $ShipmentJourneyConsigneeRefusedSubReason->updated_by = Auth::id();
            $ShipmentJourneyConsigneeRefusedSubReason->save();
        }

        
       

      if($verification == 1){
          if($shipper_status_id != 1){
              ShipmentStatusWebhookController::webhook_subscription($shipment_id, $shipper_status_id, $status_reason_id);
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

        //if shipper_status_id is
        // 14 => Shipment - Delivered
        // 20 => Return - Confirm
        // 25 => Return - Delivered to Shipper
        // 31 => Replacement - Delivered to Shipper
        //Then Auto Close Complaints

        // Case Nature Types
        // 2 => 'Delay in Delivery',
        // 10 => 'Fake Reason',
        // 14 => 'Urgent Delivery',
        // 37 => 'Delay in Return',
        // 1 => 'Payment',

        // Case Nature Ids
        // 1 => 'Complaints',
        // 2 => 'Service Request',

        // Condition for Delay in Delivery & Fake Reason & Urgent Delivery
        if ($shipper_status_id == 14) {
            $crm_request = CrmRequest::where('shipment_id', $shipment_id)->where('status_id', 2)->first();
            
            if ($crm_request && ($crm_request->case_nature_id == 1 || $crm_request->case_nature_id == 2)) { // 1=>Complaints Or 2=>Service Request
                $shipperName = User::find(Shipment::where('id', $shipment_id)->select('user_id')->first()->user_id)->name;
                
                //if crm request is in_process =>2
                if ($crm_request->status_id == 2 && (in_array($crm_request->case_nature_type_id, [2, 10, 14]))) { // 2=>Delay in Delivery, 10=> Fake Reason, 14=>Urgent Delivery
                    CrmRequest::where('id', $crm_request->id)->update([
                        'status_id' => 4 // Closed status
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $crm_request->id,
                        'status_id' => 4,
                        'agent_id' => Auth::id()
                    ]);
                    
                    CrmRequestTagging::where('crm_request_id', $crm_request->id)->delete();

                    if($crm_request->case_nature_id == 1 && (in_array($crm_request->case_nature_type_id, [2, 10]))) //Complaints
                    {
                        $comment = 'Dear '.$shipperName.',
                                Thank you for reaching us out!
                                Your complaint has been resolved, and the shipment has been delivered. We appreciate your patience and understanding throughout this process. In case of any further query regarding this shipment you may reach us out within 24 hrs.
                                Regards,
                                Team CRM
                                TRAX';
                    }
                    elseif ($crm_request->case_nature_id == 2 && (in_array($crm_request->case_nature_type_id, [14]))) { //Service Request
                        $comment = 'Dear '.$shipperName.',
                            Thank you for reaching us out!
                            Your Service Request has been processed, and the shipment has been delivered. We appreciate your patience and understanding throughout this process. In case of any further query regarding this shipment you may reach us out within 24 hrs
                            Regards,
                            Team CRM
                            TRAX';
                    }
                    
                    CRMCommentController::add($crm_request->id, 306, 0, 0, $comment, 0, 0);
                }
            }
        }

        // Condition for Delay in Return
        if (in_array($shipper_status_id, [25, 31])) {
            $crm_request = CrmRequest::where('shipment_id', $shipment_id)->where('status_id', 2)->first();
            
            if ($crm_request && $crm_request->case_nature_id == 1 && $crm_request->case_nature_type_id == 37) { //against complaint only and case nature type is Delay in return id == 37
                $shipperName = User::find(Shipment::where('id', $shipment_id)->select('user_id')->first()->user_id)->name;
                
                if ($crm_request->status_id == 2) {//if crm request is in_process
                    CrmRequest::where('id', $crm_request->id)->update([
                        'status_id' => 4 // Closed status
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $crm_request->id,
                        'status_id' => 4,
                        'agent_id' => Auth::id()
                    ]);
                    
                    CrmRequestTagging::where('crm_request_id', $crm_request->id)->delete();

                    $comment = 'Dear '.$shipperName.',
                        Thank you for reaching us out!
                        Your complaint has been resolved, and the shipment has been return delivered. We appreciate your patience and understanding throughout this process. In case of any further query regarding this shipment you may reach us out within 48 hrs.
                        Regards,
                        Team CRM
                        TRAX';
                    
                    CRMCommentController::add($crm_request->id, 306, 0, 0, $comment, 0, 0);
                }
            }
        }

        // Condition for Return Confirm (And Delay in Delivery, Fake Reason & Urgent Delivery)
        if ($shipper_status_id == 20) {
            $crm_request = CrmRequest::where('shipment_id', $shipment_id)->where('status_id', 2)->first();
            
            if ($crm_request && in_array($crm_request->case_nature_type_id, [2, 10, 14])) { 
                $shipperName = User::find(Shipment::where('id', $shipment_id)->select('user_id')->first()->user_id)->name;
                
                if ($crm_request->status_id == 2) {//if crm request is in_process
                    CrmRequest::where('id', $crm_request->id)->update([
                        'status_id' => 4 // Closed status
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $crm_request->id,
                        'status_id' => 4,
                        'agent_id' => Auth::id()
                    ]);
                    
                    CrmRequestTagging::where('crm_request_id', $crm_request->id)->delete();

                    $comment = 'Dear '.$shipperName.',
                        Thank you for reaching us out!
                        Please be noted that shipment has been updated on return status after due processing and validations, therefore at this status of shipment the reported ticket has been closed.
                        Regards,
                        Team CRM
                        TRAX';
                    
                    CRMCommentController::add($crm_request->id, 306, 0, 0, $comment, 0, 0);
                }
            }
        }
    }
}
