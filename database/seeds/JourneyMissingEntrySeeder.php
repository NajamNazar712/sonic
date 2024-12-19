<?php

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Webhook\ShipmentStatusWebhookController;
use App\Http\Models\Admin\DeliveryNote;
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
        //Only use for the marked as delivered....
        $shipmentId = [
            22314446468977,
            27114446413572,
            19514446412700,
            485243431617,
            14422346413845,
            22314446402099,
            15814446466130,
            17414446418619,
            17414446419658,
            19514446451651,
            20214446438897,
            22314446400292,
            22314446409657,
            22314446411586,
            22314446413612,
            22314446468255,
            22314446475657,
            25114446436239,
            28814446415152,
            28814446416660,
            29314446414670,
            38314446485787,
            14414446463423,
            14414446471380
            // 22320237932466, 22320237923449, 22320237912350, 22320237845635, 22320237842962, 22320237841786, 22320237748724, 22320237711689, 22320237600648
        ];
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();
            echo count($shipmentId);
           
            foreach ($shipmentId as $shipment) {
                
                
                // if ($shipment->shipper_status_id === 5) {
                    // $shipment->created_at = $shipment->updated_at;
                    $shipment->shipper_status_id = 14;
                    $shipment->consignee_status_id = 14;
                    $shipment->save();
                // }
                $deliveryNoteId = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest()->first();

                if (in_array($shipment->shipper_status_id, [13, 14])) {
                    $charges = $shipment->weight_charges + $shipment->fuel_surcharge;
                    $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                    $zone = Zone::find($shipment->pickup_address->city->zone_id);
                    if ($zone->gst == '0.16') {
                        $addgst = 16.0;
                    } elseif ($zone->gst == '0.13') {
                        $addgst = 13.0;
                    } else {
                        $addgst = $zone->gst;
                    }

                    $gst = ROUND($charges * $zone->gst, 2, PHP_ROUND_HALF_DOWN);
                    $payable = $shipment->amount - $gst;
                    if ($pending_payment->exists()) {
                        $pending_payment = $pending_payment->first();

                        $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                        $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;

                        $pending_payment->save();
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
                    $pending_payment_shipment->created_at = $deliveryNoteId->updated_at;
                    $pending_payment_shipment->updated_at = $deliveryNoteId->updated_at;
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

                }
                // $shipmentstatus = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 14)->first();
                // if (!$shipmentstatus) {

                
                $verification = 1;
                $shipment_journey = new ShipmentsJourney();
                $status_id = (!in_array($shipment->shipper_status_id, [14]) ? '14' : $shipment->shipper_status_id);
                $shipment_journey->shipment_id = $shipment->id;
                $shipment_journey->verification = $verification;
                $shipment_journey->created_at = $deliveryNoteId->updated_at ?? $shipment->updated_at;
                $shipment_journey->updated_at = $deliveryNoteId->updated_at ?? $shipment->updated_at;
                $shipment_journey->shipper_status_id = $status_id;
                $shipment_journey->consignee_status_id = $status_id;
                $shipment_journey->status_reason_id = null;
                $shipment_journey->city_id =  202;
                $shipment_journey->remarks =  null;
                $shipment_journey->user_id = null;
                $shipment_journey->admin_id = 346;
                $shipment_journey->rider_id = null;
                $shipment_journey->reference_1_id = null;
                $shipment_journey->reference_2_id = null;
                $shipment_journey->received_or_refused_by = null;
                $shipment_journey->relation = null;
                $shipment_journey->cnic = null;
                $shipment_journey->save();

                if ($shipment->shipper_status_id != 1) {
                    ShipmentStatusWebhookController::webhook_subscription($shipment->id, $shipment->shipper_status_id, null);
                }
                if ($verification == 1) {
                    $shipment_subscription = ShipperShipmentsSubscription::where('shipment_id', $shipment->id);
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
                // }
            }
        }
    }
}
