<?php

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Webhook\ShipmentStatusWebhookController;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\ConsigneeUser;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipperShipmentsSubscription;
use App\Http\Models\Zone;
use App\Http\Models\ZoneCitiesGst;
use Illuminate\Database\Seeder;

class JourneyMissingEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $shipmentId = [34765978,34749674,34735683,34715191,34766064,34735846,34735107,34733669,34729209,34713659,34712703,34701378,34680021,34656644,34596999,34570400,34708125,34666844,34663418,34654551,34644290,34605866,34595605,34587925,34586309,34572306,34559379,34556914,34555066,34508354,34482996,34745352,34710003,34695139,34681841];
        if($shipmentId){
            foreach($shipmentId as $value){
                $shipment = Shipment::find($value);
                if($shipment->shipper_status_id === 5)
                {
                    $shipment->created_at = $shipment->created_at;
                    $shipment->updated_at = $shipment->created_at;
                    $shipment->shipper_status_id = 14;
                    $shipment->consignee_status_id = 14;
                    $shipment->save();
                }
                
                if($shipment->shipper_status_id === 14)
                {
                    $charges = $shipment->weight_charges + $shipment->fuel_surcharge;
                    $deliveryNoteId = DeliveryNoteShipment::where('shipment_id',$shipment->id)->first();
                    $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                    $zone = Zone::find($shipment->pickup_address->city->zone_id);
                    if($zone->gst == '0.16')
                    {
                        $addgst = 16.0;
                    }elseif($zone->gst == '0.13'){
                        $addgst = 13.0;
                    }else{
                        $addgst = $zone->gst;
                    }
                    
                    $gst = ROUND($charges * $zone->gst, 2, PHP_ROUND_HALF_DOWN);
                    $payable = $shipment->amount - $gst;
                    if ($pending_payment->exists()) {
                        $pending_payment = $pending_payment->first();
        
                        $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                        $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;
        
                        // $pending_payment->save();
                    } else {
                        $pending_payment = new PendingPayment();
        
                        $pending_payment->user_id = $shipment->user_id;
                        $pending_payment->total_shipments = 1;
                        $pending_payment->delivered_shipments = 1;
                        $pending_payment->returned_shipments = 0;
                        $pending_payment->adjusted_shipments = 0;
        
                        $pending_payment->save();
                    }
                    $pending_payment_shipment = new PendingPaymentShipment();
                    $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                    $pending_payment_shipment->shipment_id = $shipment->id;
                    $pending_payment_shipment->type = 0;
                    $pending_payment_shipment->amount = $shipment->amount;
                    $pending_payment_shipment->charges = $charges;
                    $pending_payment_shipment->gst = $addgst;
                    $pending_payment_shipment->payable = $payable;
                    $pending_payment_shipment->save();
    
                    // $deliveryNoteId->status = 6;
                    // $deliveryNoteId->save();
                    // ShipmentsJourneyController::add($shipment->id, $shipment->shipper_status_id, $shipment->shipper_status_id, NULL, NULL, $shipment->user_id, NULL, $deliveryNoteId->delivery_note_id);
                    $verification = 1;
                    $shipment_journey = new ShipmentsJourney();

                    $shipment_journey->shipment_id = $shipment->id;
                    $shipment_journey->verification = $verification;
                    $shipment_journey->created_at = $shipment->created_at;
                    $shipment_journey->updated_at = $shipment->created_at;
                    $shipment_journey->shipper_status_id = $shipment->shipper_status_id;
                    $shipment_journey->consignee_status_id = $shipment->consignee_status_id;
                    $shipment_journey->status_reason_id = null;
                    $shipment_journey->remarks =  null;
                    $shipment_journey->user_id = $shipment->user_id;
                    $shipment_journey->admin_id = null;
                    $shipment_journey->rider_id = null;
                    $shipment_journey->reference_1_id = $deliveryNoteId->delivery_note_id;
                    $shipment_journey->reference_2_id = null;
                    $shipment_journey->received_or_refused_by = null;
                    $shipment_journey->relation = null;
                    $shipment_journey->cnic = null;
                    $shipment_journey->save();
                    if($shipment->shipper_status_id != 1){
                        ShipmentStatusWebhookController::webhook_subscription($shipment->id, $shipment->shipper_status_id, null);
                    }
                    if ($verification == 1) {
                        $shipment_subscription = ShipperShipmentsSubscription::where('shipment_id',$shipment->id);
                        if ($shipment_subscription->exists()) {
                            $shipment_subscription = $shipment_subscription->first();
                            NotificationsController::app_notification(7, $shipment_subscription->shipper_id, 3, $shipment->id, $shipment->shipper_status_id);
                        }
                        $consignee_user = ConsigneeUser::where('phone_number_1', $shipment->consignee_phone_number_1)
                        ->orwhere('phone_number_2', $shipment->consignee_phone_number_1);
                        if ($consignee_user->exists()) {
                            $consignee_user = $consignee_user->first();
                            $consignee_id = $consignee_user->id;
                            NotificationsController::app_notification(8, $consignee_id, 4, $shipment->id, $shipment->shipper_status_id);
                        }
                        ShipperShipmentsSubscription::where('shipment_id', $shipment->id)->delete();

                    }
            
                }
            }
        }
       
    }
}
